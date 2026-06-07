<?php
// Mengamankan Sesi
// Mencegah XSS melalui ID Sesi
ini_set('session.cookie_httponly', 1);
// Memastikan sesi hanya ditransfer melalui HTTPS
ini_set('session.cookie_secure', 1);
// Mencegah ID sesi terpapar di URL
ini_set('session.use_only_cookies', 1);
// Mengaktifkan atau melanjutkan sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Sertakan koneksi database
include 'koneksi.php';

/**
 * Membersihkan data input untuk mencegah XSS.
 * @param mixed $data Data input.
 * @return mixed Data yang sudah dibersihkan.
 */
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Proses login hanya jika permintaan adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Verifikasi Token CSRF (Jika Anda memiliki token di form login)
    // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    //     // Jika token tidak cocok, hentikan proses
    //     header("Location: " . $alamat_website . "home");
    //     exit();
    // }

    // Ambil dan bersihkan data dari form login
    $username = clean_input($_POST['nama_pengguna_anggota'] ?? '');
    $password = $_POST['kata_sandi_anggota'] ?? ''; // TIDAK perlu dibersihkan dengan clean_input karena akan diverifikasi hash, tetapi harus divalidasi

    // --- Validasi Input Sisi Server ---
    if (empty($username) || empty($password)) {
        echo '<script>alert("Nama Pengguna dan Kata Sandi wajib diisi."); window.location.replace("' . $alamat_website . 'home");</script>';
        exit();
    }
    
    // Batas percobaan login (Throttle/Brute-Force Protection)
    // Implementasi ini memerlukan penyimpanan (misalnya, tabel database atau Redis) untuk menghitung percobaan gagal per IP/Username.
    // Tanpa implementasi penyimpanan, kita hanya bisa memberikan pesan error generik.
    // Untuk tujuan ini, kita akan menggunakan pesan generik.

    // Query menggunakan Prepared Statement untuk mencegah SQL Injection
    $query = "SELECT id_anggota, nama_pengguna_anggota, saldo_anggota, kata_sandi_anggota, status_anggota, ext_pengguna_anggota FROM anggota WHERE nama_pengguna_anggota = ?";
    
    // Inisialisasi dan eksekusi prepared statement
    if ($stmt = mysqli_prepare($koneksi, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // 1. Cek Status Anggota
            if ($row['status_anggota'] === 'terkunci') {
                echo '<script>alert("User ID Anda terkunci. Silakan hubungi layanan pelanggan."); window.location.replace("' . $alamat_website . 'home");</script>';
                exit();
            }

            // 2. Verifikasi Password Menggunakan Hashing Kuat (password_verify)
            if (password_verify($password, $row['kata_sandi_anggota'])) {
                
                // --- Keamanan Sesi Tambahan (Session Hijacking & Fixation) ---
                session_regenerate_id(true); // Ganti ID sesi lama dengan yang baru
                
                // 3. Set Session dengan Data Minimal
                $_SESSION['loggedin'] = true;
                $_SESSION['id_anggota'] = $row['id_anggota'];
                $_SESSION['nama_pengguna_anggota'] = $row['nama_pengguna_anggota'];
                $_SESSION['ext_pengguna_anggota'] = $row['ext_pengguna_anggota'];
                
                // JANGAN simpan saldo di sesi, karena saldo sering berubah. 
                // Saldo harus diambil dari database SETIAP KALI dibutuhkan untuk mencegah "Tampered Data".
                // $_SESSION['saldo_anggota'] = $row['saldo_anggota']; // Dihapus!
                
                // 4. Redirect Sukses
                echo '<script>
                        alert("Login berhasil! Selamat datang, ' . $row['nama_pengguna_anggota'] . '."); 
                        window.location.replace("' . $alamat_website . 'home");
                      </script>';
                exit();
                
            } else {
                // Password salah - Pesan error generik untuk mencegah Brute-Force
                echo '<script>alert("Nama Pengguna atau Kata Sandi salah."); window.location.replace("' . $alamat_website . 'home");</script>';
                // LOG: Catat percobaan login gagal di sini (IP, Username, Waktu)
            }
        } else {
            // Username tidak ditemukan - Pesan error generik
            echo '<script>alert("Nama Pengguna atau Kata Sandi salah."); window.location.replace("' . $alamat_website . 'home");</script>';
            // LOG: Catat percobaan login gagal di sini (IP, Username, Waktu)
        }
    } else {
        // Gagal menyiapkan pernyataan SQL
        error_log("Gagal menyiapkan pernyataan SQL: " . mysqli_error($koneksi));
        echo '<script>alert("Terjadi kesalahan sistem. Silakan coba lagi nanti."); window.location.replace("' . $alamat_website . 'home");</script>';
    }

} else {
    // Jika diakses tidak melalui POST (mencegah akses langsung)
    header("Location: " . $alamat_website . "home");
    exit();
}
?>