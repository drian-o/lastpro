<?php
include_once '../koneksi.php';

// Pastikan admin terautentikasi
if (!isset($_SESSION['kode_admin'])) {
    echo '
      <script>
        alert("Terjadi kesalahan, harap masuk kembali!");
        window.location.replace("'.$alamat_admin.'keluar.php");
      </script>
    ';
}

// Ambil semua anggota
$anggota_query = mysqli_query($koneksi, "
    SELECT id_anggota, nama_pengguna_anggota FROM anggota
");

?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4 mb-4">
        <div class="col-md-6">
            <div class="fw-bold fs-4 text-center text-md-start">Referral</div>
        </div>
       <!--  <div class="col-md-6">
            <div class="text-center text-md-end">
                <a href="<?php echo $alamat_admin.'tambah_anggota'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
                    <span class="tf-icons mdi mdi-plus me-1"></span>
                    Tambah Anggota
                </a>
            </div>
        </div> -->
    </div>
    <div class="card table-responsive p-3">
        <table class="table" id="example">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">ID Anggota</th>
                    <th scope="col">Nama Pengguna</th>
                    <th scope="col">Deposit Reff</th>
                    <th scope="col">Turnover Reff</th>
                    <th scope="col">Total Referral</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($anggota_query) > 0) {
                    $nomor_anggota = 1;
                    while ($data_anggota = mysqli_fetch_array($anggota_query)) {
                        $id_anggota = $data_anggota['id_anggota'];
                        $nama_pengguna = $data_anggota['nama_pengguna_anggota'];

                        // Ambil total deposit untuk anggota ini berdasarkan nama pengguna referral
                        $deposit_query = mysqli_query($koneksi, "
                            SELECT SUM(d.jumlah_deposit) AS total_deposit 
                            FROM deposit d
                            JOIN anggota a ON d.nama_pengguna_anggota_deposit = a.nama_pengguna_anggota 
                            WHERE a.refferal = '$nama_pengguna' AND d.status_deposit = 'disetujui'
                        ");
                        $deposit_data = mysqli_fetch_array($deposit_query);
                        $total_deposit = $deposit_data['total_deposit'] ? $deposit_data['total_deposit'] : 0;

                        // Hitung total turnover
                        $total_turnover = $total_deposit * 0.10; // 10% dari total deposit

                        // Hitung total referral
                        $referral_query = mysqli_query($koneksi, "
                            SELECT COUNT(*) AS total_referral 
                            FROM anggota 
                            WHERE refferal = '$nama_pengguna'
                        ");
                        $referral_data = mysqli_fetch_array($referral_query);
                        $total_referral = $referral_data['total_referral'];
                ?>
                <tr>
                    <th scope="row"><?php echo $nomor_anggota++; ?></th>
                    <td><?php echo $id_anggota; ?></td>
                    <td><?php echo $nama_pengguna; ?></td>
                    <td><?php echo number_format($total_deposit, 2, ',', '.'); ?></td>
                    <td><?php echo number_format($total_turnover, 2, ',', '.'); ?></td>
                    <td><?php echo $total_referral; ?></td>
                  
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td class="text-center" colspan="7">Tidak Ada Data</td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
