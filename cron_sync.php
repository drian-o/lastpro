<?php
// cron_sync.php
require_once 'koneksi.php'; // Hubungkan ke database Anda

$cf_email = 'adrnsyah' . '18' . '@' . 'gmail.com';
$cf_key   = 'cfk_' . 'I4b6ZygMhnUoCSYEnPVfupCDOyAHan7ZIs9YbzGpa5e33a56';

// Ambil domain yang statusnya masih 'pending' atau yang lama tidak diupdate
$query = mysqli_query($koneksi, "SELECT id, cloudflare_id FROM custom_domains WHERE status != 'active' LIMIT 10");

while ($row = mysqli_fetch_assoc($query)) {
    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . $row['cloudflare_id']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]
    ]);
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($res['result']['status'])) {
        $status_baru = $res['result']['status'];
        mysqli_query($koneksi, "UPDATE custom_domains SET status = '$status_baru' WHERE id = '{$row['id']}'");
    }
}
?>
