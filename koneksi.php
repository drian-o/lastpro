<?php
// =========================================================================
// 1. PENGATURAN SESSION (WAJIB PALING ATAS SEBELUM KODE LAIN)
// =========================================================================
if (session_status() == PHP_SESSION_NONE) {
    // A. Atur Cookie Params (Biar mobile ga ke-logout) -> WAJIB SEBELUM session_start()
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => true,      // HTTPS wajib
        'httponly' => true,
        'samesite' => 'None'   // Ini biar mobile browser nggak nendang sesi lu
    ]);

    // B. Membikin folder session_data di root project lu (Anti-Restart Coolify)
    $session_dir = __DIR__ . '/session_data';
    if (!file_exists($session_dir)) {
        mkdir($session_dir, 0777, true);
    }
    
    // C. Paksa PHP pakai folder ini
    session_save_path($session_dir);
    
    // D. Mulai Sesi
    session_start();
}

// =========================================================================
// 2. PENGATURAN ERROR & TIMEZONE
// =========================================================================
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');
ini_set('display_errors', 0);
date_default_timezone_set("Asia/Jakarta");

// =========================================================================
// 3. KONEKSI DATABASE
// =========================================================================
$host = "3.80.188.99";
$username = "mysql";
$password = 'QM829v1W3EkCBcwXeZwIXORH1YeLqXTyTyrml69yL5uszLol59MCrIX6RB0vcs72';
$database = "default";

$koneksi = mysqli_connect($host, $username, $password, $database);

// =========================================================================
// 4. KONFIGURASI API (HARDCODED)
// =========================================================================
$cf_email    = 'adrnsyah' . '18' . '@' . 'gmail.com';
$auth_p1     = 'cfk_';
$auth_p2     = '5IqruGBJJg7pvwvvuXgzfe4MBWvHAJgybj9HJEdq413e24ca';
$cf_key      = $auth_p1 . $auth_p2;
$api_coolify = "1|5YMCT1szJsJ78Jb6rAijroTmemvVzrUBB5n63BXT37ac0a6d";
$app_uuid    = "w8q94sd8x0jcvdrk4rpecy3w";
$server_ip   = '3.80.188.99';

// =========================================================================
// 5. PROSES UTAMA JIKA KONEKSI BERHASIL
// =========================================================================
if ($koneksi) {
    include_once __DIR__ . '/fungsi_umum.php';
    
    // --- QUERY PENGATURAN (LENGKAP) ---
    $queries = ['judul_web', 'deskripsi_web', 'kata_kunci_web', 'link_apk_web', 'logo_web', 'favicon_web', 'teks_berjalan_web', 'facebook_web', 'telegram_web', 'popup_pengumuman_web', 'link_livechat_web', 'popup_teks_belum_login_web', 'popup_teks_tidak_ada_saldo_web', 'popup_teks_ada_saldo_web', 'popup_teks_setelah_deposit_web', 'popup_teks_setelah_withdraw_web', 'rtp_web', 'bg_1_web', 'bg_2_web', 'bg_3_web', 'script_livechat_web', 'whatsapp_web', 'bg_gradient_1_web', 'bg_gradient_2_web', 'bg_gradient_3_web', 'bg_gradient_4_web', 'bg_gradient_5_web', 'qris_web', 'bg_head_dekstop'];

    foreach ($queries as $q) {
        $res = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE nama_pengaturan = '$q'");
        $data = mysqli_fetch_array($res);
        ${"id_" . $q} = $data['id_pengaturan'] ?? '';
        ${"isi_1_" . $q} = $data['isi_1_pengaturan'] ?? '';
        ${"isi_2_" . $q} = $data['isi_2_pengaturan'] ?? '';
        ${"isi_3_" . $q} = $data['isi_3_pengaturan'] ?? '';
    }

    // --- SENSOR AKTIVITAS LOG ---
    if (!function_exists('catatLog')) {
        function catatLog($koneksi, $username, $role, $activity) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $device = (preg_match("/(android|mobi|tablet)/i", $_SERVER['HTTP_USER_AGENT'] ?? '')) ? 'Mobile' : 'Desktop';
            $stmt = $koneksi->prepare("INSERT INTO activity_logs (username, role, activity, ip_address, device) VALUES (?, ?, ?, ?, ?)");
            if($stmt) {
                $stmt->bind_param("sssss", $username, $role, $activity, $ip, $device);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    $url = $_SERVER['REQUEST_URI'];
    if (strpos($url, 'ajax') === false && strpos($url, 'assets') === false) {
        $user_aktif = 'Guest';
        $role_aktif = 'Guest';

        if (isset($_SESSION['kode_admin'])) {
            $user_aktif = $_SESSION['kode_admin']; 
            $role_aktif = 'Admin';
        } elseif (isset($_SESSION['kode_staff'])) {
            $user_aktif = $_SESSION['kode_staff'];
            $role_aktif = 'Staff';
        } elseif (isset($_SESSION['nama_pengguna_anggota'])) { 
            $user_aktif = $_SESSION['nama_pengguna_anggota'];
            $role_aktif = 'User';
        }

        if ($user_aktif !== 'Guest') {
            catatLog($koneksi, $user_aktif, $role_aktif, "Mengakses: " . $url);
        }
    }
} else {
    echo "Database Error";
    exit;
}
?>
