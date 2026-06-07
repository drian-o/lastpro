<?php
include_once '../koneksi.php';
session_start(); // Pastikan session dimulai

if (!isset($_SESSION['kode_admin'])) {
    echo '
      <script>
        alert("Terjadi kesalahan, harap masuk kembali!");
        window.location.replace("'.$alamat_admin.'keluar.php");
      </script>
    ';
    exit; // Pastikan skrip berhenti jika session tidak ada
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row gy-4 mb-4">
    <div class="col-md-6">
      <div class="fw-bold fs-4 text-center text-md-start">Anggota</div>
    </div>
    <div class="card table-responsive p-3">
      <table class="table" id="example">
        <thead>
          <tr>
            <th scope="col" class="text-center">#</th>
            <th scope="col" class="text-center">Nama Pengguna</th>
            <th scope="col" class="text-center">Bank</th>
            <th scope="col" class="text-center">Nama Rekening</th>
            <th scope="col" class="text-center">Nomor Rekening</th>
            <th scope="col" class="text-center">Saldo</th>
            <th scope="col" class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $nomor_anggota = 1;
          $anggota = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY id_anggota DESC");

          if (mysqli_num_rows($anggota) > 0) {
              while ($data_anggota = mysqli_fetch_array($anggota)) {
                  $id_anggota = $data_anggota['id_anggota'];
                  $nama_pengguna_anggota = $data_anggota['nama_pengguna_anggota'];
                  $bank_anggota = $data_anggota['bank_anggota'];
                  $nama_rekening_anggota = $data_anggota['nama_rekening_anggota'];
                  $nomor_rekening_anggota = $data_anggota['nomor_rekening_anggota'];
                  $saldo_anggota = $data_anggota['saldo_anggota'];
          ?>
              <tr>
                <th scope="row" class="text-center"><?php echo $nomor_anggota++; ?></th>
                <td class="text-center"><?php echo htmlspecialchars($nama_pengguna_anggota); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($bank_anggota); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($nama_rekening_anggota); ?></td>
                <td class="text-center"><?php echo htmlspecialchars($nomor_rekening_anggota); ?></td>
                <td class="text-center"><?php echo 'Rp.' . number_format($saldo_anggota, 0, ',', '.'); ?></td>
                <td class="text-center">
                  <a href="<?php echo htmlspecialchars($alamat_admin . 'ubah_saldo/' . $id_anggota); ?>" class="btn btn-sm btn-primary waves-effect waves-light">
                    <span class="tf-icons mdi mdi-pencil me-1"></span>
                    Ubah
                  </a>
                </td>
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
</div>
