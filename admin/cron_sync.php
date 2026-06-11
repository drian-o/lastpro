<?php
// cron_sync.php
require_once __DIR__ . '/../koneksi.php'; // Pastikan path ini benar sesuai letak file koneksi.php

$auth_email = getenv('CF_EMAIL');
$auth_key   = getenv('CF_KEY');

// Ambil domain yang statusnya masih 'pending' atau yang lama tidak diupdate
$query = mysqli_query($koneksi, "SELECT id, cloudflare_id FROM custom_domains WHERE status != 'active' LIMIT 10");

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . $row['cloudflare_id']);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5, // Mencegah server hang jika Cloudflare lambat
            CURLOPT_HTTPHEADER     => [
                'X-Auth-Email: ' . $auth_email, 
                'X-Auth-Key: ' . $auth_key,
                'Content-Type: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $res = json_decode($response, true);
        curl_close($ch);

        // Update status jika respons valid
        if (isset($res['result']['status'])) {
            $status_baru = mysqli_real_escape_string($koneksi, $res['result']['status']);
            mysqli_query($koneksi, "UPDATE custom_domains SET status = '$status_baru' WHERE id = '{$row['id']}'");
        }
    }
}
?>
