<?php
// zuzulo/tambah_domain.php
if (session_status() == PHP_SESSION_NONE) session_start();

// Matikan error reporting agar header redirect tidak bentrok
ini_set('display_errors', 0);
error_reporting(0);

if (!isset($_SESSION['kode_admin'])) { exit('Akses ditolak.'); }

require_once dirname(__DIR__) . '/koneksi.php';

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
        CURLOPT_TIMEOUT => 10,
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
    $list = ["https://exampleproject.my.id"];
    while ($row = mysqli_fetch_array($query)) { $list[] = "https://" . $row['domain_name']; }
    $headers = ['Authorization: Bearer ' . $api_coolify];
    callAPI("http://137.184.155.151:8000/api/v1/applications/$app_uuid", "PATCH", ["fqdn" => implode(",", $list)], $headers);
    callAPI("http://137.184.155.151:8000/api/v1/applications/$app_uuid/restart", "POST", null, $headers);
}

// PROSES TAMBAH
if (isset($_POST['submit_domain'])) {
    $domain = strtolower(trim($_POST['nama_domain']));
    $hasil = callAPI("https://api.cloudflare.com/client/v4/zones", "POST", ["name" => $domain, "jump_start" => true], ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
    
    if (isset($hasil['success']) && $hasil['success']) {
        $zid = $hasil['result']['id'];
        $ns = $hasil['result']['name_servers'];
        callAPI("https://api.cloudflare.com/client/v4/zones/$zid/dns_records", "POST", ["type"=>"A", "name"=>"@", "content"=>"137.184.155.151", "proxied"=>true], ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
        mysqli_query($koneksi, "INSERT INTO custom_domains (domain_name, cloudflare_id, status, user_id) VALUES ('$domain', '$zid', 'pending', '0')");
        sinkronisasiCoolify();
        $_SESSION['pesan'] = "<div class='alert alert-success'><strong>🎉 Berhasil!</strong><br>NS: <code>{$ns[0]}</code> & <code>{$ns[1]}</code></div>";
    } else {
        $_SESSION['pesan'] = "<div class='alert alert-danger'>Gagal: " . ($hasil['errors'][0]['message'] ?? 'Error Cloudflare') . "</div>";
    }
    header("Location: ?halaman=tambah_domain"); exit;
}

// PROSES HAPUS
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $cf_id = mysqli_real_escape_string($koneksi, $_GET['cf_id']);
    callAPI("https://api.cloudflare.com/client/v4/zones/" . $cf_id, "DELETE", null, ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
    mysqli_query($koneksi, "DELETE FROM custom_domains WHERE id = '$id'");
    sinkronisasiCoolify();
    header("Location: ?halaman=tambah_domain"); exit;
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
        <thead><tr><th>Domain</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php
        $q = mysqli_query($koneksi, "SELECT * FROM custom_domains ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($q)) {
            $badge = ($row['status'] == 'active') ? 'bg-label-success' : 'bg-label-warning';
            echo "<tr>
                <td>{$row['domain_name']}</td>
                <td><span class='badge {$badge}'>".strtoupper($row['status'])."</span></td>
                <td><a href='?halaman=tambah_domain&aksi=hapus&id={$row['id']}&cf_id={$row['cloudflare_id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin hapus?\")'>Hapus</a></td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
