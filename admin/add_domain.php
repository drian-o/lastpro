<?php
// drianojek

// Kunci API udah otomatis ditarik dari koneksi.php
require_once '../koneksi.php';

if (!isset($alamat_admin)) {
    $current_dir_url_path = dirname($_SERVER['SCRIPT_NAME']);
    $alamat_admin = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . $current_dir_url_path . '/';
}

if (!isset($_SESSION['kode_admin'])) {
    echo '<script>alert("Terjadi kesalahan, harap masuk kembali!"); window.location.replace("'.rtrim($alamat_admin, '/').'/keluar.php");</script>';
    exit();
}

$pesan = "";

// =========================================================================
// BACKEND API & LOGIKA CLOUDFLARE / COOLIFY
// =========================================================================
function sinkronisasiDomainKeCoolifyLokal() {
    // Cukup panggil nama variabelnya aja, nilainya udah ada di koneksi.php
    global $koneksi, $app_uuid, $api_coolify, $server_ip;
    
    // Domain utama yang baru
    $domain_utama = "https://exampleproject.my.id";
    $list_domain = [$domain_utama];

    $query_domains = mysqli_query($koneksi, "SELECT domain_name FROM custom_domains");
    if ($query_domains) {
        while ($row = mysqli_fetch_array($query_domains)) {
            if (!empty($row['domain_name'])) {
                $list_domain[] = "https://" . trim($row['domain_name']);
            }
        }
    }
    
    $string_domains = implode(",", $list_domain);
    $url = "http://{$server_ip}:8000/api/v1/applications/{$app_uuid}";
    $data_payload = json_encode(array("domains" => $string_domains));

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_payload);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_coolify
    ]);
    curl_exec($ch);
    curl_close($ch); 

    // Restart Aplikasi Coolify (TIMEOUT 1 DETIK BIAR GA NUNGGU LAMA)
    $restart_url = "http://{$server_ip}:8000/api/v1/applications/{$app_uuid}/restart"; 
    $ch_deploy = curl_init($restart_url);
    curl_setopt($ch_deploy, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_deploy, CURLOPT_CUSTOMREQUEST, "POST"); 
    curl_setopt($ch_deploy, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch_deploy, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_deploy, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch_deploy, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_coolify
    ]);
    curl_exec($ch_deploy);
    curl_close($ch_deploy);
}

function tambahSiteBaruCloudflareLokal($domainBaru) {
    global $cf_key;
    $data = ["name" => $domainBaru, "jump_start" => true];

    $ch = curl_init("https://api.cloudflare.com/client/v4/zones");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $cf_key,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function deleteSiteDariCloudflareLokal($zone_id) {
    global $cf_key;
    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $cf_key,
        'Content-Type: application/json'
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function cekStatusZoneCloudflareLokal($zone_id) {
    global $cf_key;
    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $cf_key,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    $res_data = json_decode($response, true);
    curl_close($ch);
    return $res_data['result']['status'] ?? 'pending'; 
}

// =========================================================================
// LOGIKA PROSES FORM (CRUD DOMAIN & REDIRECT)
// =========================================================================

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id']) && isset($_GET['cf_id'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['id']);
    $zone_id_hapus = mysqli_real_escape_string($koneksi, $_GET['cf_id']);
    
    deleteSiteDariCloudflareLokal($zone_id_hapus);
    mysqli_query($koneksi, "DELETE FROM custom_domains WHERE id = '$id_hapus'");
    sinkronisasiDomainKeCoolifyLokal();
    
    $pesan = "<div class='alert alert-success alert-dismissible fade show'>Domain berhasil dihapus permanen!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

if (isset($_POST['submit_domain'])) {
    $domain_input = strtolower(trim($_POST['nama_domain']));
    $domain_clean = mysqli_real_escape_string($koneksi, $domain_input);

    if (preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/', $domain_clean)) {
        $hasil = tambahSiteBaruCloudflareLokal($domain_clean);

        if (isset($hasil['success']) && $hasil['success'] == true) {
            $zone_id = $hasil['result']['id']; 
            $ns1 = $hasil['result']['name_servers'][0] ?? 'ns1.cloudflare.com';
            $ns2 = $hasil['result']['name_servers'][1] ?? 'ns2.cloudflare.com';
            
            $dns_data = ["type" => "A", "name" => "@", "content" => $server_ip, "ttl" => 1, "proxied" => true];
            $ch_dns = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id . "/dns_records");
            curl_setopt($ch_dns, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch_dns, CURLOPT_POST, true); 
            curl_setopt($ch_dns, CURLOPT_POSTFIELDS, json_encode($dns_data));
            curl_setopt($ch_dns, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $cf_key,
                'Content-Type: application/json'
            ]); 
            curl_exec($ch_dns); 
            curl_close($ch_dns);

            $ssl_payload = ["id" => "ssl", "value" => "full"];
            $ch_ssl = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id . "/settings/ssl");
            curl_setopt($ch_ssl, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch_ssl, CURLOPT_CUSTOMREQUEST, "PATCH"); 
            curl_setopt($ch_ssl, CURLOPT_POSTFIELDS, json_encode($ssl_payload));
            curl_setopt($ch_ssl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $cf_key,
                'Content-Type: application/json'
            ]); 
            curl_exec($ch_ssl); 
            curl_close($ch_ssl);

            try {
                $query_simpan = "INSERT INTO custom_domains (user_id, domain_name, cloudflare_id, status, created_at, updated_at) 
                                 VALUES (1, '$domain_clean', '$zone_id', 'pending',
