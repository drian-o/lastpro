<?php
// Mulai sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
// Ganti class lama dengan class GameXaAPI
require_once 'classes/class.exa.php'; 

// Inisialisasi GameXaAPI
$api = new GameXaAPI(); 

// Fungsi untuk membersihkan input agar lebih aman (XSS)
function clean_input($koneksi, $data) {
    if (is_array($data)) {
        return array_map(function($d) use ($koneksi) { return clean_input($koneksi, $d); }, $data);
    }
    // Gabungkan sanitasi umum dengan real escape string
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return mysqli_real_escape_string($koneksi, $data);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil dan bersihkan data dari form
    $username = clean_input($koneksi, $_POST['nama_pengguna_anggota'] ?? '');
    $password_raw = $_POST['kata_sandi_anggota'] ?? ''; 
    $email = clean_input($koneksi, $_POST['email_anggota'] ?? '');
    $bank = clean_input($koneksi, $_POST['bank_anggota'] ?? '');
    $nama_rekening = clean_input($koneksi, $_POST['nama_rekening_anggota'] ?? '');
    $nomor_rekening = clean_input($koneksi, $_POST['nomor_rekening_anggota'] ?? '');
    $telepon = clean_input($koneksi, $_POST['telepon_anggota'] ?? '');
    $refferal = isset($_POST["refferal_anggota"]) ? clean_input($koneksi, $_POST["refferal_anggota"]) : NULL;

    // --- 1. Validasi Input Sederhana ---
    if (empty($username) || empty($password_raw) || empty($email) || empty($bank) || empty($nama_rekening) || empty($nomor_rekening) || empty($telepon)) {
        echo "<script>alert('Mohon isi semua bidang yang diperlukan.'); window.location.replace('" . $alamat_website . "auth-register');</script>";
        exit;
    }
    
    // --- 2. Validasi Keberadaan User Lokal (Pre-check) ---
    $query_check = "SELECT id_anggota, nama_pengguna_anggota, email_anggota, telepon_anggota, nomor_rekening_anggota FROM anggota WHERE nama_pengguna_anggota = ? OR email_anggota = ? OR telepon_anggota = ? OR nomor_rekening_anggota = ?";
    
    $check_existing = mysqli_prepare($koneksi, $query_check);
    mysqli_stmt_bind_param($check_existing, "ssss", $username, $email, $telepon, $nomor_rekening);
    mysqli_stmt_execute($check_existing);
    mysqli_stmt_store_result($check_existing);
    
    if (mysqli_stmt_num_rows($check_existing) > 0) {
        mysqli_stmt_bind_result($check_existing, $db_id, $db_username, $db_email, $db_telepon, $db_nomor_rekening);
        mysqli_stmt_fetch($check_existing);
        
        $error_message = '';
        if ($db_username == $username) {
            $error_message = 'Username sudah terdaftar. Silakan gunakan username lain.';
        } elseif ($db_email == $email) {
            $error_message = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } elseif ($db_telepon == $telepon) {
            $error_message = 'Nomor telepon sudah terdaftar. Silakan gunakan nomor telepon lain.';
        } elseif ($db_nomor_rekening == $nomor_rekening) {
            $error_message = 'Nomor rekening sudah terdaftar. Silakan gunakan nomor rekening lain.';
        }
        
        echo "<script>alert('{$error_message}'); window.location.replace('" . $alamat_website . "auth-register');</script>";
        mysqli_stmt_close($check_existing);
        exit;
    }
    mysqli_stmt_close($check_existing);

    // --- 3. Proses API GxG untuk membuat member ---
    $process_api = $api->createPlayer(
        $username,
        $email,
        $password_raw, 
        $nama_rekening, 
        $telepon,
        'IDR' 
    ); 

    if ($process_api['success'] === true && isset($process_api['data']['player']['id'])) {
        
        $gxg_id = $process_api['data']['player']['id']; 
        $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);
        
        // --- 4. Query INSERT ke Database Lokal ---
        $query_insert = "
            INSERT INTO anggota 
            (refferal, nama_pengguna_anggota, ext_pengguna_anggota, kata_sandi_anggota, email_anggota, bank_anggota, nama_rekening_anggota, nomor_rekening_anggota, telepon_anggota) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        
        $stmt = mysqli_prepare($koneksi, $query_insert);
        mysqli_stmt_bind_param($stmt, "ssissssss", 
            $refferal, 
            $username, 
            $gxg_id, 
            $hashed_password, 
            $email, 
            $bank, 
            $nama_rekening, 
            $nomor_rekening, 
            $telepon
        );
        
        if (mysqli_stmt_execute($stmt)) {
            
            // --- MODIFIKASI: OTOMATIS LOGIN SETELAH PENDAFTARAN SUKSES ---
            
            // 1. Dapatkan ID anggota lokal yang baru dibuat
            $new_local_id = mysqli_insert_id($koneksi);
            
            // 2. Regenerasi ID sesi untuk mencegah Session Fixation
            session_regenerate_id(true); 
            
            // 3. Set Session untuk status Login
            $_SESSION['loggedin'] = true;
            $_SESSION['id_anggota'] = $new_local_id; // Menggunakan ID lokal
            $_SESSION['nama_pengguna_anggota'] = $username;
            $_SESSION['ext_pengguna_anggota'] = $row['ext_pengguna_anggota'];
            echo "<script>
                    alert('Pendaftaran Berhasil! Selamat datang, {$username}.'); 
                    window.location.replace('" . $alamat_website . "home');
                  </script>";
            
        } else {
            // Gagal menyimpan ke DB lokal
            error_log("DB INSERT FAILED: " . mysqli_error($koneksi) . " for GxG ID: " . $gxg_id);
            echo "<script>alert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'); window.location.replace('" . $alamat_website . "auth-register');</script>";
        }
        mysqli_stmt_close($stmt);

    } else {
        // Pendaftaran API gagal
        $error_message = $process_api['data']['message'] ?? 'Kesalahan API tidak diketahui.';
        echo "<script>alert('Pendaftaran gagal: " . $error_message . "'); window.location.replace('" . $alamat_website . "auth-register');</script>";
    }
} else {
    // Mencegah akses langsung melalui GET
    header("Location: " . $alamat_website . "auth-register");
}

mysqli_close($koneksi);
?>