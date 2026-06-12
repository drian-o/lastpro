<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');
ini_set('display_errors', 0);
date_default_timezone_set("Asia/Jakarta");

$host = "3.80.188.99";
$username = "mysql";
$password = 'QM829v1W3EkCBcwXeZwIXORH1YeLqXTyTyrml69yL5uszLol59MCrIX6RB0vcs72';
$database = "default";

$koneksi = mysqli_connect($host, $username, $password, $database);

// --- KONFIGURASI HARDCODED (STABIL) ---
define('CF_EMAIL', getenv('CF_EMAIL'));
define('CF_KEY', getenv('CF_GLOBAL_KEY'));
define('CF_ZONE_ID', getenv('CF_ZONE_ID'));
define('API_COOLIFY', getenv('API_COOLIFY'));
define('APP_UUID', getenv('APP_UUID'));
define('COOLIFY_URL', 'http://3.80.188.99:8000');

if ($koneksi) {
    include_once __DIR__ . '/fungsi_umum.php';
    
    // --- FIX BUG MOBILE: Sesi nempel terus ---
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => true,      // HTTPS wajib
        'httponly' => true,
        'samesite' => 'None'   // Ini biar mobile browser nggak nendang sesi lu
    ]);

    if (session_status() == PHP_SESSION_NONE) { session_start(); }

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
