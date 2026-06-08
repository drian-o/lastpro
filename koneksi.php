<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', 0);
date_default_timezone_set("Asia/Jakarta");

// Database
$koneksi = mysqli_connect("167.71.163.131", "mysql", "UMGunsTrHAsjjOxaFiO8f0xoRzyA2qFCx7XxJlEs6kIsISSGNPINa6r8B2vSfVHY", "default");
if (!$koneksi) die("Koneksi gagal: " . mysqli_connect_error());

// Konfigurasi dari Environment Variable (Atur di dashboard Coolify!)
define('CF_EMAIL', getenv('CF_EMAIL'));
define('CF_KEY', getenv('CF_GLOBAL_KEY'));
define('API_COOLIFY', getenv('API_COOLIFY'));
define('APP_UUID', getenv('APP_UUID'));
define('COOLIFY_URL', 'http://167.71.163.131:8000'); // URL API Coolify

// Optimasi Pengaturan (Hanya 1 Query)
$SETTING = [];
$res = mysqli_query($koneksi, "SELECT * FROM pengaturan");
while ($row = mysqli_fetch_array($res)) { $SETTING[$row['nama_pengaturan']] = $row['isi_1_pengaturan']; }
function get_set($key) { global $SETTING; return $SETTING[$key] ?? ''; }
?>
