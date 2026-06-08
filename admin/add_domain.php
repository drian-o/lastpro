<?php
// zuzulo/tambah_domain.php
ob_start();
if (session_status() == PHP_SESSION_NONE) session_start();

ini_set('display_errors', 0);
error_reporting(0);

if (!isset($_SESSION['kode_admin'])) { exit('Akses ditolak.'); }

require_once dirname(__DIR__) . '/koneksi.php';

// KONFIGURASI - GANTI DENGAN YANG BARU
$cf_email = 'adrnsyah' . '18' . '@' . 'gmail.com';
$auth_p1    = 'cfk_';
$auth_p2    = 'I4b6ZygMhnUoCSYEnPVfupCDOyAHan7ZIs9YbzGpa5e33a56';
$cf_key     = $auth_p1 . $auth_p2;
$api_coolify = "1|oKcpXvShtMkxgo19ftMWq5TISsBin4CaC5Ozh10jca69c54f";
$app_uuid = "hii3cbzqugws8nhg7zvaba1a";

function callAPI($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $data ? json_encode($data) : null,
        CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json'], $headers)
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

function sinkronisasiCoolify() {
    global $koneksi, $api_coolify, $app_uuid;
    $query = mysqli_query($koneksi, "SELECT domain_name FROM custom_domains");
    $domains = [];
    while ($row = mysqli_fetch_array($query)) { $domains[] = trim($row['domain_name']); }
    
    $headers = ['Authorization: Bearer ' . $api_coolify, 'Content-Type: application/json'];
    $payload = ["fqdn" => implode(",", $domains)];
    
    callAPI("http://167.71.163.131:8000/api/v1/applications/$app_uuid", "PATCH", $payload, $headers);
    callAPI("http://167.71.163.131:8000/api/v1/applications/$app_uuid/deploy", "POST", null, $headers);
}

// PROSES TAMBAH
if (isset($_POST['submit_domain'])) {
    $domain = strtolower(trim($_POST['nama_domain']));
    $hasil = callAPI("https://api.cloudflare.com/client/v4/zones", "POST", ["name" => $domain, "jump_start" => true], ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
    
    if (isset($hasil['success']) && $hasil['success']) {
        $zid = $hasil['result']['id'];
        $ns = $hasil['result']['name_servers'];
        callAPI("https://api.cloudflare.com/client/v4/zones/$zid/dns_records", "POST", ["type"=>"A", "name"=>"@", "content"=>"167.71.163.131", "proxied"=>true], ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
        mysqli_query($koneksi, "INSERT INTO custom_domains (domain_name, cloudflare_id, status, user_id) VALUES ('$domain', '$zid', 'active', '0')");
        sinkronisasiCoolify();
        $_SESSION['pesan'] = "<div class='alert alert-success'><strong>🎉 Berhasil!</strong><br>Setting ke Namecheap:<br><code>{$ns[0]}</code><br><code>{$ns[1]}</code></div>";
    } else {
        $_SESSION['pesan'] = "<div class='alert alert-danger'>Gagal: " . ($hasil['errors'][0]['message'] ?? 'Error Cloudflare') . "</div>";
    }
    if (ob_get_length()) ob_end_clean();
    header("Location: " . $_SERVER['REQUEST_URI']); exit;
}

// PROSES HAPUS
if (isset($_POST['submit_hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $cf_id = mysqli_real_escape_string($koneksi, $_POST['cf_id']);
    callAPI("https://api.cloudflare.com/client/v4/zones/" . $cf_id, "DELETE", null, ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
    mysqli_query($koneksi, "DELETE FROM custom_domains WHERE id = '$id'");
    sinkronisasiCoolify();
    if (ob_get_length()) ob_end_clean();
    header("Location: " . $_SERVER['REQUEST_URI']); exit;
}
?>

<h4 class="fw-bold py-3">Manajemen Domain</h4>
<?php if(isset($_SESSION['pesan'])) { echo $_SESSION['pesan']; unset($_SESSION['pesan']); } ?>

<div class="card p-4">
    <form method="POST">
        <input type="text" name="nama_domain" class="form-control mb-3" placeholder="Contoh: domainkamu.com" required>
        <button type="submit" name="submit_domain" class="btn btn-primary">SIMPAN DOMAIN</button>
    </form>
</div>

<div class="card mt-4">
    <table class="table">
        <thead><tr><th>Domain</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php
        $q = mysqli_query($koneksi, "SELECT * FROM custom_domains ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($q)) {
            echo "<tr>
                <td>{$row['domain_name']}</td>
                <td>
                    <form method='POST' style='display:inline;' onsubmit='return confirm(\"Yakin hapus?\")'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <input type='hidden' name='cf_id' value='{$row['cloudflare_id']}'>
                        <button type='submit' name='submit_hapus' class='btn btn-danger btn-sm'>Hapus</button>
                    </form>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<?php ob_end_flush(); ?>
