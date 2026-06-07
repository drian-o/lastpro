<?php
include_once '../koneksi.php';

// Pastikan session admin ada, jika tidak, alihkan ke halaman keluar
if (!isset($_SESSION['kode_staff'])) {
    echo '
        <script>
            alert("Terjadi kesalahan, harap masuk kembali!");
            window.location.replace("' . $alamat_admin . 'keluar.php");
        </script>
    ';
}

// Query untuk mendapatkan data jumlah deposit
$deposit = mysqli_query($koneksi, "SELECT SUM(jumlah_deposit) AS total_jumlah_deposit FROM deposit");
$data_deposit = mysqli_fetch_array($deposit);
$total_jumlah_deposit = $data_deposit['total_jumlah_deposit'];
if ($total_jumlah_deposit == 0) {
    $total_jumlah_deposit = 0;
}

// Query untuk mendapatkan data jumlah withdraw
$withdraw = mysqli_query($koneksi, "SELECT SUM(jumlah_withdraw) AS total_jumlah_withdraw FROM withdraw");
$data_withdraw = mysqli_fetch_array($withdraw);
$total_jumlah_withdraw = $data_withdraw['total_jumlah_withdraw'];
if ($total_jumlah_withdraw == 0) {
    $total_jumlah_withdraw = 0;
}

// Query untuk mendapatkan jumlah anggota
$anggota = mysqli_query($koneksi, "SELECT * FROM anggota");
$jumlah_anggota = mysqli_num_rows($anggota);

// Query untuk mendapatkan jumlah promosi
$promosi = mysqli_query($koneksi, "SELECT * FROM promosi");
$jumlah_promosi = mysqli_num_rows($promosi);

// Query untuk mendapatkan jumlah staff
$staff = mysqli_query($koneksi, "SELECT * FROM staff");
$jumlah_staff = mysqli_num_rows($staff);

// Simpan jumlah saat ini ke dalam session
$_SESSION['lastDepositCount'] = $total_jumlah_deposit;
$_SESSION['lastWithdrawCount'] = $total_jumlah_withdraw;
$_SESSION['lastAnggotaCount'] = $jumlah_anggota;
$_SESSION['lastPromosiCount'] = $jumlah_promosi;
$_SESSION['lastStaffCount'] = $jumlah_staff;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags dan link stylesheet lainnya... -->
</head>
<body>
    <!-- Tambahkan elemen audio untuk suara notifikasi -->
    <audio id="notificationSound" preload="auto"></audio>

    <!-- Tambahkan script untuk memutar suara notifikasi -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Set interval untuk memeriksa perubahan data setiap 5 detik
            setInterval(checkDataChanges, 5000);
            setInterval(checkNewData, 5000);

            // Inisialisasi objek Audio
            var notificationSound = new Audio('assets/sounds/notification_sound.mp3');

            function checkDataChanges() {
                $.ajax({
                    url: 'check_data_changes.php?action=check_data_changes', // File PHP untuk memeriksa perubahan data
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.dataChanges) {
                            // Jika ada perubahan data, mainkan suara notifikasi
                            playNotificationSound();
                            // Tampilkan popup notifikasi
                            showNotification("Ada perubahan pada data!", "info");
                            // Auto reload halaman setelah 3 detik
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status, error);
                    }
                });
            }

            function checkNewData() {
                $.ajax({
                    url: 'check_new_data.php?action=check_new_data', // File PHP untuk memeriksa data baru
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.newData) {
                            // Jika ada data baru, mainkan suara notifikasi
                            playNotificationSound();
                            // Perbarui tabel atau elemen lainnya di dasbor
                            updateDashboard(response.data);
                            // Tampilkan popup notifikasi
                            showNotification("Data baru diterima!", "success");
                            // Auto reload halaman setelah 3 detik
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status, error);
                    }
                });
            }

            function playNotificationSound() {
                // Mainkan suara notifikasi
                notificationSound.play();
            }

            function updateDashboard(data) {
                // Fungsi untuk memperbarui dasbor dengan data baru
                // Anda bisa memperbarui tabel atau elemen lainnya di sini
                console.log("Data baru diterima:", data);
                // Misalnya, perbarui tabel deposit dengan data baru
            }

            function showNotification(message, type) {
                var notification = document.createElement('div');
                notification.className = 'notification ' + type;
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(function() {
                    notification.style.opacity = '0';
                    setTimeout(function() {
                        document.body.removeChild(notification);
                    }, 400);
                }, 2000);
            }
        });
    </script>

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
<?php
// Query untuk total jumlah deposit yang diproses
$deposit = mysqli_query($koneksi, "SELECT SUM(jumlah_deposit) AS total_jumlah_deposit FROM deposit WHERE status_deposit = 'diproses'");
$data_deposit = mysqli_fetch_array($deposit);
$total_jumlah_deposit = $data_deposit['total_jumlah_deposit'];
if ($total_jumlah_deposit === null) {
  $total_jumlah_deposit = 0;
}

// Query untuk menampilkan detail deposit yang diproses
$query_deposit_diproses = mysqli_query($koneksi, "SELECT * FROM deposit WHERE status_deposit = 'diproses'");
?>

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

                            // Ambil nama pengguna anggota deposit
                            $anggota_deposit = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota_deposit'");
                            if (mysqli_num_rows($anggota_deposit) == 0) {
                                $nama_pengguna_anggota_deposit = "Data anggota tidak ada atau telah dihapus";
                            } else {
                                $data_anggota_deposit = mysqli_fetch_assoc($anggota_deposit);
                                $nama_pengguna_anggota_deposit = $data_anggota_deposit['nama_pengguna_anggota'];
                            }

                            // Tentukan kelas status untuk warna latar belakang
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
                            <th scope="col" class="text-center" style="width: 15%;">Jumlah</th>
                            <th scope="col" class="text-center" style="width: 15%;">Tanggal</th>
                            <th scope="col" class="text-center" style="width: 10%;">Status</th>
                            <th scope="col" class="text-center" style="width: 5%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $nomor_withdraw = 1;
                        // Query untuk mendapatkan data queued withdraw
                        $query_withdraw_diproses = mysqli_query($koneksi, "SELECT * FROM withdraw WHERE status_withdraw = 'diproses' ORDER BY tanggal_withdraw DESC");

                        if (!$query_withdraw_diproses) {
                            die("Query failed: " . mysqli_error($koneksi));
                        }

                        // Cek apakah query mengembalikan hasil atau tidak
                        if (mysqli_num_rows($query_withdraw_diproses) > 0) {
                            // Tampilkan data withdraw
                            while ($data_withdraw = mysqli_fetch_assoc($query_withdraw_diproses)) {
                                $id_withdraw = $data_withdraw['id_withdraw'];
                                $id_anggota_withdraw = $data_withdraw['id_anggota_withdraw'];
                                $kode_withdraw = $data_withdraw['kode_withdraw'];
                                $tujuan_withdraw = $data_withdraw['tujuan_withdraw'];
                                $jumlah_withdraw = $data_withdraw['jumlah_withdraw'];
                                $tanggal_withdraw = $data_withdraw['tanggal_withdraw'];
                                $status_withdraw = $data_withdraw['status_withdraw'];

                                // Ambil nama pengguna anggota withdraw
                                $anggota_withdraw = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota_withdraw'");
                                if (!$anggota_withdraw) {
                                    die("Query failed: " . mysqli_error($koneksi));
                                }

                                if (mysqli_num_rows($anggota_withdraw) == 0) {
                                    $nama_pengguna_anggota_withdraw = "Data anggota tidak ada atau telah dihapus";
                                } else {
                                    $data_anggota_withdraw = mysqli_fetch_assoc($anggota_withdraw);
                                    $nama_pengguna_anggota_withdraw = $data_anggota_withdraw['nama_pengguna_anggota'];
                                }

                                // Tentukan kelas status untuk warna latar belakang
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
                            } // tutup while
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