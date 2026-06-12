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

// --- KONFIGURASI CLOUDFLARE & COOLIFY ---
// 
define('CF_EMAIL', getenv('CF_EMAIL'));
define('CF_KEY', getenv('CF_GLOBAL_KEY'));
define('CF_ZONE_ID', getenv('CF_ZONE_ID'));
define('API_COOLIFY', getenv('API_COOLIFY'));
define('APP_UUID', getenv('APP_UUID'));
define('COOLIFY_URL', 'http://3.80.188.99:8000');
// ---------------------------------------------------

// FUNGSI SAKTI ADD DOMAIN KE CLOUDFLARE
function tambahDomainKeCloudflare($domainBaru) {
    $data = ["hostname" => $domainBaru, "ssl" => ["method" => "http", "type" => "dv"]];
    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . CF_ZONE_ID . "/custom_hostnames");
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($data), CURLOPT_HTTPHEADER => ['X-Auth-Email: ' . CF_EMAIL, 'X-Auth-Key: ' . CF_KEY, 'Content-Type: application/json']]);
    $res = curl_exec($ch); curl_close($ch); return json_decode($res, true);
}

if ($koneksi) {
    include_once __DIR__ . '/fungsi_umum.php';
    
    $protocol = (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $current_domain = $protocol . $_SERVER['HTTP_HOST'];
    $alamat_website = $current_domain . '/';
    $alamat_admin   = $current_domain . '/admin/';
    $alamat_staff   = $current_domain . '/staff/';
    
    // --- QUERY PENGATURAN ---
    $queries = [
        'judul_web', 'deskripsi_web', 'kata_kunci_web', 'link_apk_web', 'logo_web', 'favicon_web', 
        'teks_berjalan_web', 'facebook_web', 'telegram_web', 'popup_pengumuman_web', 'link_livechat_web',
        'popup_teks_belum_login_web', 'popup_teks_tidak_ada_saldo_web', 'popup_teks_ada_saldo_web',
        'popup_teks_setelah_deposit_web', 'popup_teks_setelah_withdraw_web', 'rtp_web', 'bg_1_web',
        'bg_2_web', 'bg_3_web', 'script_livechat_web', 'whatsapp_web', 'bg_gradient_1_web',
        'bg_gradient_2_web', 'bg_gradient_3_web', 'bg_gradient_4_web', 'bg_gradient_5_web',
        'qris_web', 'bg_head_dekstop'
    ];

    foreach ($queries as $q) {
        $res = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE nama_pengaturan = '$q'");
        $data = mysqli_fetch_array($res);
        $var_id = "id_" . $q;
        $var_i1 = "isi_1_" . $q;
        $var_i2 = "isi_2_" . $q;
        $var_i3 = "isi_3_" . $q;
        $$var_id = $data['id_pengaturan'] ?? '';
        $$var_i1 = $data['isi_1_pengaturan'] ?? '';
        $$var_i2 = $data['isi_2_pengaturan'] ?? '';
        $$var_i3 = $data['isi_3_pengaturan'] ?? '';
    }

    // ==========================================================
    // SENSOR AKTIVITAS LOG (ADMIN, STAFF, & USER)
    // ==========================================================
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!function_exists('deteksiDevice')) {
        function deteksiDevice() {
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $is_mobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $user_agent);
            return $is_mobile ? 'Mobile' : 'Desktop';
        }
    }

    if (!function_exists('catatLog')) {
        function catatLog($koneksi, $username, $role, $activity) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $device = deteksiDevice();

            $stmt = $koneksi->prepare("INSERT INTO activity_logs (username, role, activity, ip_address, device) VALUES (?, ?, ?, ?, ?)");
            if($stmt) {
                $stmt->bind_param("sssss", $username, $role, $activity, $ip, $device);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Mengabaikan pemanggilan background / auto-refresh agar database tidak kelebihan beban
    if (strpos($_SERVER['REQUEST_URI'], 'ajax') === false) {
        $user_aktif = 'Guest';
        $role_aktif = 'guest';

        // 1. DETEKSI ADMIN
        if (isset($_SESSION['kode_admin'])) {
            $user_aktif = $_SESSION['kode_admin']; 
            $role_aktif = 'Admin';
        } 
        // 2. DETEKSI STAFF (Memeriksa kemungkinan nama session staff yang Anda gunakan)
        elseif (isset($_SESSION['kode_staff'])) {
            $user_aktif = $_SESSION['kode_staff'];
            $role_aktif = 'Staff';
        } elseif (isset($_SESSION['username_staff'])) {
            $user_aktif = $_SESSION['username_staff'];
            $role_aktif = 'Staff';
        } 
        // 3. DETEKSI USER / PEMAIN
        elseif (isset($_SESSION['username'])) {
            $user_aktif = $_SESSION['username'];
            $role_aktif = 'User';
        } elseif (isset($_SESSION['user'])) {
            $user_aktif = $_SESSION['user'];
            $role_aktif = 'User';
        }

        $halaman_dibuka = "Mengakses: " . $_SERVER['REQUEST_URI'];
        catatLog($koneksi, $user_aktif, $role_aktif, $halaman_dibuka);
    }
    // ==========================================================

} else {
    echo "Kesalahan : Tidak dapat terhubung ke database." . PHP_EOL;
    exit;
}
?>
