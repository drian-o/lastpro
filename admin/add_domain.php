<?php
// drianojek

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// WAJIB PANGGIL KONEKSI DATABASE DI SINI AGAR TIDAK ERROR SAAT INSERT/DELETE
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
// KONFIGURASI KREDENSIAL API 
// =========================================================================
$cf_email    = 'adrnsyah' . '18' . '@' . 'gmail.com';
$auth_p1     = 'cfk_';
$auth_p2     = 'I4b6ZygMhnUoCSYEnPVfupCDOyAHan7ZIs9YbzGpa5e33a56';
$cf_key      = $auth_p1 . $auth_p2;
$api_coolify = "1|5YMCT1szJsJ78Jb6rAijroTmemvVzrUBB5n63BXT37ac0a6d";
$app_uuid    = "w8q94sd8x0jcvdrk4rpecy3w";
$server_ip   = '3.80.188.99';

// =========================================================================
// BACKEND API & LOGIKA CLOUDFLARE / COOLIFY
// =========================================================================
function sinkronisasiDomainKeCoolifyLokal() {
    global $koneksi, $app_uuid, $api_coolify, $server_ip;
    
    $domain_utama = "https://sampleproject.my";
    $list_domain = [$domain_utama];

    // Ambil semua domain dari database agar sinkron
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

    // Update Domains di Coolify
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

    // Restart Aplikasi Coolify
    $restart_url = "http://{$server_ip}:8000/api/v1/applications/{$app_uuid}/restart"; 
    $ch_deploy = curl_init($restart_url);
    curl_setopt($ch_deploy, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_deploy, CURLOPT_CUSTOMREQUEST, "POST"); 
    curl_setopt($ch_deploy, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch_deploy, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_deploy, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch_deploy, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_coolify
    ]);
    curl_exec($ch_deploy);
    curl_close($ch_deploy);
}

function tambahSiteBaruCloudflareLokal($domainBaru) {
    global $cf_email, $cf_key;
    $data = ["name" => $domainBaru, "jump_start" => true];

    $ch = curl_init("https://api.cloudflare.com/client/v4/zones");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Email: ' . $cf_email,
        'X-Auth-Key: ' . $cf_key,
        'Content-Type: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function deleteSiteDariCloudflareLokal($zone_id) {
    global $cf_email, $cf_key;
    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Email: ' . $cf_email,
        'X-Auth-Key: ' . $cf_key,
        'Content-Type: application/json'
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function cekStatusZoneCloudflareLokal($zone_id) {
    global $cf_email, $cf_key;
    $ch = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Email: ' . $cf_email,
        'X-Auth-Key: ' . $cf_key,
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

// 1. Hapus Domain Permanen
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id']) && isset($_GET['cf_id'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['id']);
    $zone_id_hapus = mysqli_real_escape_string($koneksi, $_GET['cf_id']);
    
    deleteSiteDariCloudflareLokal($zone_id_hapus);
    mysqli_query($koneksi, "DELETE FROM custom_domains WHERE id = '$id_hapus'");
    sinkronisasiDomainKeCoolifyLokal();
    
    $pesan = "<div class='alert alert-success alert-dismissible fade show'>Domain berhasil dihapus permanen!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// 2. Tambah Domain Baru
if (isset($_POST['submit_domain'])) {
    $domain_input = strtolower(trim($_POST['nama_domain']));
    $domain_clean = mysqli_real_escape_string($koneksi, $domain_input);

    if (preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/', $domain_clean)) {
        $hasil = tambahSiteBaruCloudflareLokal($domain_clean);

        if (isset($hasil['success']) && $hasil['success'] == true) {
            $zone_id = $hasil['result']['id']; 
            $ns1 = $hasil['result']['name_servers'][0] ?? 'ns1.cloudflare.com';
            $ns2 = $hasil['result']['name_servers'][1] ?? 'ns2.cloudflare.com';
            
            // Set A Record & SSL
            $dns_data = ["type" => "A", "name" => "@", "content" => $server_ip, "ttl" => 1, "proxied" => true];
            $ch_dns = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id . "/dns_records");
            curl_setopt($ch_dns, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch_dns, CURLOPT_POST, true); curl_setopt($ch_dns, CURLOPT_POSTFIELDS, json_encode($dns_data));
            curl_setopt($ch_dns, CURLOPT_HTTPHEADER, ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key, 'Content-Type: application/json']); curl_exec($ch_dns); curl_close($ch_dns);

            $ssl_payload = ["id" => "ssl", "value" => "full"];
            $ch_ssl = curl_init("https://api.cloudflare.com/client/v4/zones/" . $zone_id . "/settings/ssl");
            curl_setopt($ch_ssl, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch_ssl, CURLOPT_CUSTOMREQUEST, "PATCH"); curl_setopt($ch_ssl, CURLOPT_POSTFIELDS, json_encode($ssl_payload));
            curl_setopt($ch_ssl, CURLOPT_HTTPHEADER, ['X-Auth-Email: '.$cf_email, 'X-Auth-Key: '.$cf_key, 'Content-Type: application/json']); curl_exec($ch_ssl); curl_close($ch_ssl);

            // Insert Database dengan Try-Catch
            try {
                $query_simpan = "INSERT INTO custom_domains (user_id, domain_name, cloudflare_id, status, created_at, updated_at) 
                                 VALUES (1, '$domain_clean', '$zone_id', 'pending', NOW(), NOW())";
                                 
                if (mysqli_query($koneksi, $query_simpan)) {
                    sinkronisasiDomainKeCoolifyLokal();
                    $pesan = "<div class='alert alert-success alert-dismissible fade show'>
                                <strong>🎉 Domain Berhasil Terdaftar!</strong><br>
                                <small>Silakan arahkan NameServer domain user Anda ke:</small><br>
                                <code>1. $ns1</code><br><code>2. $ns2</code>
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                              </div>";
                }
            } catch (Exception $e) {
                $pesan = "<div class='alert alert-danger alert-dismissible fade show'><strong>Gagal menyimpan ke database:</strong> " . $e->getMessage() . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
            }
        } else {
            $error_msg = $hasil['errors'][0]['message'] ?? 'Cloudflare Error.';
            $pesan = "<div class='alert alert-danger alert-dismissible fade show'>$error_msg<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    }
}

// 3. Tambah / Ubah Redirect Domain (Nawala)
if (isset($_POST['submit_redirect'])) {
    $domain_from = mysqli_real_escape_string($koneksi, trim($_POST['domain_from']));
    $domain_to_clean = mysqli_real_escape_string($koneksi, preg_replace('#^https?://#', '', strtolower(trim($_POST['domain_to']))));

    if (!empty($domain_from) && !empty($domain_to_clean)) {
        if (mysqli_query($koneksi, "UPDATE custom_domains SET redirect_to = '$domain_to_clean', updated_at = NOW() WHERE domain_name = '$domain_from'")) {
            $pesan = "<div class='alert alert-info alert-dismissible fade show'>Berhasil mengatur Redirect untuk domain <strong>$domain_from</strong>!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        }
    }
}

// 4. Batalkan (Hapus) Redirect
if (isset($_GET['aksi']) && $_GET['aksi'] == 'batal_redirect' && isset($_GET['id'])) {
    $id_batal = mysqli_real_escape_string($koneksi, $_GET['id']);
    mysqli_query($koneksi, "UPDATE custom_domains SET redirect_to = NULL, updated_at = NOW() WHERE id = '$id_batal'");
    echo "<script>window.location.href='?halaman=tambah_domain';</script>"; 
    exit;
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiHapus(event, urlTarget, pesanTeks) {
    event.preventDefault(); 
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: pesanTeks,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff3e1d', 
        cancelButtonColor: '#8592a3',  
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'card bg-card-theme border-secondary text-white' }
    }).then((result) => {
        if (result.isConfirmed) { window.location.href = urlTarget; }
    });
}
</script>

<h4 class="fw-bold py-3 mb-4">Pusat Kendali Domain & Redirect</h4>

<?php if(!empty($pesan)) echo $pesan; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4" style="background-color: #2b2c40; color: #cbcbd6;">
            <h5 class="card-header border-bottom border-secondary text-white fw-bold">1. Daftarkan Domain Baru</h5>
            <div class="card-body pt-4">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label text-white fw-semibold">Nama Domain / Alamat Web</label>
                        <input type="text" name="nama_domain" class="form-control text-white bg-transparent border-secondary" placeholder="Contoh: harapanjp.my.id" required autocomplete="off" style="border: 1px solid #555 !important; padding: 10px;">
                        <div class="form-text text-muted mt-1"><i class="bx bx-info-circle"></i> Tanpa <code>http://</code> atau <code>https://</code>.</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="reset" class="btn fw-bold text-white" style="background-color: #8592a3;">RESET</button>
                        <button type="submit" name="submit_domain" class="btn text-white fw-bold" style="background-color: #696cff;"><i class="bx bx-save me-1"></i> DAFTARKAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4" style="background-color: #2b2c40; color: #cbcbd6;">
            <h5 class="card-header border-bottom border-secondary text-white fw-bold">2. Redirect Domain (Nawala)</h5>
            <div class="card-body pt-4">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label text-white fw-semibold">Pilih Domain (Yang Diblokir)</label>
                        <select name="domain_from" class="form-select text-white bg-transparent border-secondary" required style="border: 1px solid #555 !important; padding: 10px;">
                            <option value="" class="bg-dark">-- Pilih Domain dari Database --</option>
                            <?php
                            if (isset($koneksi)) {
                                $q_dom = mysqli_query($koneksi, "SELECT domain_name FROM custom_domains ORDER BY domain_name ASC");
                                while ($d = mysqli_fetch_assoc($q_dom)) {
                                    echo '<option value="'.$d['domain_name'].'" class="bg-dark">'.$d['domain_name'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white fw-semibold">Arahkan Ke (Domain Aktif)</label>
                        <input type="text" name="domain_to" class="form-control text-white bg-transparent border-secondary" placeholder="Contoh: harapanbaru.com" required autocomplete="off" style="border: 1px solid #555 !important; padding: 10px;">
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" name="submit_redirect" class="btn text-white fw-bold" style="background-color: #ffab00;"><i class="bx bx-transfer me-1"></i> SIMPAN REDIRECT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card" style="background-color: #2b2c40; color: #cbcbd6;">
    <h5 class="card-header border-bottom border-secondary text-white fw-bold">Daftar Status Domain & Redirect</h5>
    <div class="table-responsive text-nowrap">
        <table class="table mb-0">
            <thead>
                <tr style="border-bottom: 1px solid #444;">
                    <th style="width: 50px; color: #a3a4cc;" class="text-center">#</th>
                    <th style="color: #a3a4cc;">NAMA DOMAIN</th>
                    <th style="color: #a3a4cc;">STATUS CLOUDFLARE</th>
                    <th style="color: #a3a4cc;">STATUS REDIRECT</th>
                    <th style="width: 150px; color: #a3a4cc;" class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php
                if (isset($koneksi)) {
                    $query_tampil = mysqli_query($koneksi, "SELECT * FROM custom_domains ORDER BY id DESC");
                    $no = 1;
                    if ($query_tampil && mysqli_num_rows($query_tampil) > 0) {
                        while ($row = mysqli_fetch_assoc($query_tampil)) {
                            // Cek Status Cloudflare Otomatis
                            $status_sekarang = cekStatusZoneCloudflareLokal($row['cloudflare_id']);
                            if ($status_sekarang !== $row['status']) {
                                $id_update = $row['id'];
                                mysqli_query($koneksi, "UPDATE custom_domains SET status = '$status_sekarang' WHERE id = '$id_update'");
                            }

                            $badge_cf = ($status_sekarang === 'active') ? 'bg-label-success' : 'bg-label-warning';
                            $url_hapus_domain = "?halaman=tambah_domain&aksi=hapus&id=".$row['id']."&cf_id=".$row['cloudflare_id'];
                            $url_batal_redirect = "?halaman=tambah_domain&aksi=batal_redirect&id=".$row['id'];
                            
                            $ada_redirect = (!empty($row['redirect_to']));
                            ?>
                            <tr style="border-bottom: 1px solid #3c3d56;">
                                <td class="text-center fw-semibold"><?= $no++; ?></td>
                                
                                <td>
                                    <span class="fw-bold text-white"><?= htmlspecialchars($row['domain_name']); ?></span>
                                </td>
                                
                                <td>
                                    <span class="badge <?= $badge_cf; ?> fw-bold"><?= strtoupper($status_sekarang); ?></span>
                                </td>
                                
                                <td>
                                    <?php if($ada_redirect) { ?>
                                        <span class="badge bg-label-info fw-bold"><i class="bx bx-right-arrow-alt"></i> <?= htmlspecialchars($row['redirect_to']); ?></span>
                                    <?php } else { ?>
                                        <span class="text-muted"><small>Tidak Ada</small></span>
                                    <?php } ?>
                                </td>
                                
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow text-white" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end bg-dark">
                                            <?php if($ada_redirect) { ?>
                                                <a class="dropdown-item text-warning" href="javascript:void(0);" onclick="konfirmasiHapus(event, '<?= $url_batal_redirect; ?>', 'Batalkan redirect untuk domain ini?')"><i class="bx bx-unlink me-1"></i> Batalkan Redirect</a>
                                            <?php } ?>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="konfirmasiHapus(event, '<?= $url_hapus_domain; ?>', 'Hapus permanen domain ini dari database dan Cloudflare?')"><i class="bx bx-trash me-1"></i> Hapus Permanen</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada domain yang terdaftar.</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
