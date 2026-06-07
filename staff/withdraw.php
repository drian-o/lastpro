<?php
  include_once '../koneksi.php';
  if (!isset($_SESSION['kode_staff'])) {
    echo '
      <script>
        alert("Terjadi kesalahan, harap masuk kembali!");
        window.location.replace("'.$alamat_staff.'keluar.php");
      </script>
    ';
  }
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row gy-4 mb-4">
    <div class="col-md-6">
      <div class="fw-bold fs-4 text-center text-md-start">Withdraw</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <span><?php echo ucapan().', '.tanggalIndonesia(date('Y-m-d'), true).', '; ?></span>
        <span id="jam_sekarang">Jam </span>
      </div>
    </div>
  </div>
  <?php
// Fungsi untuk memisahkan string dan mengambil dua bagian pertama (nama bank dan nama rekening)
function tampilkanTujuan($string) {
    $bagian = explode(' - ', $string);
    if (count($bagian) >= 3) {
        return $bagian[0] . ' - ' . $bagian[2]; // Mengambil nama bank dan nama rekening saja
    }
    return $string; // Jika format tidak sesuai, kembalikan string asli
}
?>

<div class="card table-responsive p-3">
    <table class="table" id="example">
      <thead>
        <tr>
          <th scope="col" class="text-center">#</th>
          <th scope="col" class="text-center">Kode</th>
          <th scope="col" class="text-center">Nama Pengguna</th>
          <th scope="col" class="text-center">Tujuan</th>
          <th scope="col" class="text-center">Jumlah</th>
          <th scope="col" class="text-center">Tanggal</th>
          <th scope="col" class="text-center">Status</th>
          <th scope="col" class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $nomor_withdraw = 1;
          $withdraw = mysqli_query($koneksi, "SELECT * FROM withdraw");
          while ($data_withdraw = mysqli_fetch_array($withdraw)) {
            if (mysqli_num_rows($withdraw) >= 1) {
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
                $data_anggota_withdraw = mysqli_fetch_array($anggota_withdraw);
                $nama_pengguna_anggota_withdraw = $data_anggota_withdraw['nama_pengguna_anggota'];
              }
              $tujuan_withdraw_tampilan = tampilkanTujuan($tujuan_withdraw);
              
              // Tentukan warna berdasarkan status withdraw
              $status_class = '';
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
          <th scope="row"><?php echo $nomor_withdraw++; ?></th>
          <td class="text-center"><?php echo $kode_withdraw; ?></td>
          <td class="text-center"><?php echo $nama_pengguna_anggota_withdraw; ?></td>
          <td class="text-center"><?php echo $tujuan_withdraw_tampilan; ?></td>
          <td class="text-center"><?php echo number_format($jumlah_withdraw); ?></td>
          <td class="text-center"><?php echo jamTanggalIndonesia($tanggal_withdraw); ?></td>
          <td class="<?php echo $status_class; ?> text-center"><?php echo $status_withdraw; ?></td>
          <td>
            <a href="<?php echo $alamat_staff.'ubah_withdraw/'.$id_withdraw; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
              <span class="tf-icons mdi mdi-pencil me-1"></span>
              Ubah
            </a>
          </td>
        </tr>
        <?php
            } else {
        ?>
        <tr>
          <td class="text-center" colspan="8">Tidak Ada Data</td>
        </tr>
        <?php
            }
          }
        ?>
      </tbody>
    </table>
  </div>
</div>
