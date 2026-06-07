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
      <div class="fw-bold fs-4 text-center text-md-start">Deposit</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <span><?php echo ucapan().', '.tanggalIndonesia(date('Y-m-d'), true).', '; ?></span>
        <span id="jam_sekarang">Jam </span>
      </div>
    </div>
  </div>
  <?php
// Fungsi untuk memisahkan string dan mengambil dua bagian pertama
function tampilkanTujuan($string) {
    $bagian = explode(' - ', $string);
    if (count($bagian) >= 3) {
        return $bagian[0] . ' - ' . $bagian[1]; // Mengambil nama bank dan nama rekening saja
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
          $nomor_deposit = 1;
          $deposit = mysqli_query($koneksi, "SELECT * FROM deposit");
          while ($data_deposit = mysqli_fetch_array($deposit)) {
            if (mysqli_num_rows($deposit) >= 1) {
              $id_deposit = $data_deposit['id_deposit'];
              $id_anggota_deposit = $data_deposit['id_anggota_deposit'];
              $kode_deposit = $data_deposit['kode_deposit'];
              $tujuan_deposit = $data_deposit['tujuan_deposit'];
              $jumlah_deposit = $data_deposit['jumlah_deposit'];
              $tanggal_deposit = $data_deposit['tanggal_deposit'];
              $status_deposit = $data_deposit['status_deposit'];
              $anggota_deposit = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota_deposit'");
              if (mysqli_num_rows($anggota_deposit) == 0) {
                $nama_pengguna_anggota_deposit = "Data anggota tidak ada atau telah dihapus";
              } else {
                $data_anggota_deposit = mysqli_fetch_array($anggota_deposit);
                $nama_pengguna_anggota_deposit = $data_anggota_deposit['nama_pengguna_anggota'];
              }
              $tujuan_deposit_tampilan = tampilkanTujuan($tujuan_deposit);

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
          <th scope="row"><?php echo $nomor_deposit++; ?></th>
          <td class="text-center"><?php echo $kode_deposit; ?></td>
          <td class="text-center"><?php echo $nama_pengguna_anggota_deposit; ?></td>
          <td class="text-center"><?php echo $tujuan_deposit_tampilan; ?></td>
          <td class="text-center"><?php echo number_format($jumlah_deposit); ?></td>
          <td class="text-center"><?php echo jamTanggalIndonesia($tanggal_deposit); ?></td>
          <td class="<?php echo $status_class; ?> text-center"><?php echo $status_deposit; ?></td>
          <td>
            <a href="<?php echo $alamat_staff.'ubah_deposit/'.$id_deposit; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
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
