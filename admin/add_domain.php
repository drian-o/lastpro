<?php
// zuzulo/tambah_domain.php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['kode_admin'])) { exit('Akses ditolak.'); }

// KONFIGURASI
$cf_email = 'adrnsyah' . '18' . '@' . 'gmail.com';
$cf_key   = 'cfk_' . 'I4b6ZygMhnUoCSYEnPVfupCDOyAHan7ZIs9YbzGpa5e33a56';
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
    callAPI("http://137.184.155.151:8000/api/v1/applications/$app_uuid", "PATCH", ["fqdn" => implode(",", $list)], ['Authorization: Bearer ' . $api_coolify]);
    callAPI("http://137.184.155.151:8000/api/v1/applications/$app_uuid/restart", "POST", null, ['Authorization: Bearer ' . $api_coolify]);
}

// PROSES TAMBAH
if (isset($_POST['submit_domain'])) {
    $domain = strtolower(trim($_POST['nama_domain']));
    $hasil = callAPI("https://api.cloudflare.com/client/v4/zones", "POST", ["name" => $domain, "jump_start" => true], ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
    
    if ($hasil['success']) {
        $zid = $hasil['result']['id'];
        $ns = $hasil['result']['name_servers'];
        
        callAPI("https://api.cloudflare.com/client/v4/zones/$zid/dns_records", "POST", ["type"=>"A", "name"=>"@", "content"=>"137.184.155.151", "proxied"=>true], ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
        
        mysqli_query($koneksi, "INSERT INTO custom_domains (domain_name, cloudflare_id, status) VALUES ('$domain', '$zid', 'pending')");
        sinkronisasiCoolify();
        
        $_SESSION['pesan'] = "<div class='alert alert-success'><strong>🎉 Domain Berhasil Terdaftar!</strong><br>Silakan ganti NameServer domain user Anda ke:<br><code>1. {$ns[0]}</code><br><code>2. {$ns[1]}</code></div>";
    } else {
        $_SESSION['pesan'] = "<div class='alert alert-danger'>Gagal: " . ($hasil['errors'][0]['message'] ?? 'Error Cloudflare') . "</div>";
    }
    header("Location: ?halaman=tambah_domain"); exit;
}

// PROSES HAPUS
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    callAPI("https://api.cloudflare.com/client/v4/zones/" . $_GET['cf_id'], "DELETE", null, ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
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
            // Update status otomatis dari Cloudflare
            $status_cf = callAPI("https://api.cloudflare.com/client/v4/zones/{$row['cloudflare_id']}", "GET", null, ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key]);
            $status_sekarang = $status_cf['result']['status'] ?? 'pending';
            if ($status_sekarang !== $row['status']) {
                mysqli_query($koneksi, "UPDATE custom_domains SET status = '$status_sekarang' WHERE id = '{$row['id']}'");
                $row['status'] = $status_sekarang;
            }

            $badge = ($row['status'] == 'active') ? 'bg-label-success' : 'bg-label-warning';
            echo "<tr>
                <td>{$row['domain_name']}</td>
                <td><span class='badge {$badge}'>".strtoupper($row['status'])."</span></td>
                <td><a href='?halaman=tambah_domain&aksi=hapus&id={$row['id']}&cf_id={$row['cloudflare_id']}' class='btn btn-danger btn-sm'>Hapus</a></td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
