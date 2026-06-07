<?php
include_once '../koneksi.php';

if (!isset($_SESSION['kode_admin'])) {
  echo '
        <script>
            alert("Terjadi kesalahan, harap masuk kembali!");
            window.location.replace("' . $alamat_admin . 'keluar.php");
        </script>
    ';
  exit();
}

if (isset($_GET['id_anggota'])) {
  $id_anggota = $_GET['id_anggota'];
  $anggota = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota'");
  
  if (!$anggota || mysqli_num_rows($anggota) == 0) {
      echo '
        <script>
            alert("Anggota tidak ditemukan!");
            window.location.replace("' . $alamat_admin . 'anggota");
        </script>
    ';
    exit();
  }
  
  $data_anggota = mysqli_fetch_array($anggota);

  $nama_pengguna_anggota = $data_anggota['nama_pengguna_anggota'];
  $kata_sandi_anggota = $data_anggota['kata_sandi_anggota'];
  $email_anggota = $data_anggota['email_anggota'];
  $telepon_anggota = $data_anggota['telepon_anggota'];
  $bank_anggota = $data_anggota['bank_anggota'];
  $nama_rekening_anggota = $data_anggota['nama_rekening_anggota'];
  $nomor_rekening_anggota = $data_anggota['nomor_rekening_anggota'];
  // Kolom saldo_anggota Dihapus
  $status_anggota = $data_anggota['status_anggota'];
  $status_game = $data_anggota['status_game'];
} else {
  echo '
    <script>
      alert("Pilih anggota yang ingin diubah!");
      window.location.replace("' . $alamat_admin . 'anggota");
    </script>
  ';
  exit();
}

if (isset($_POST['ubah_data'])) {
  $kata_sandi_anggota_2 = $_POST['kata_sandi_anggota'];
  $email_anggota_2 = $_POST['email_anggota'];
  $telepon_anggota_2 = $_POST['telepon_anggota'];
  $bank_anggota_2 = $_POST['bank_anggota'];
  $nama_rekening_anggota_2 = $_POST['nama_rekening_anggota'];
  $nomor_rekening_anggota_2 = $_POST['nomor_rekening_anggota'];
  $status_anggota_2 = $_POST['status_anggota'];
  $status_game_2 = $_POST['status_game'];

  if ($kata_sandi_anggota_2 == "") {
    $ubah_data = mysqli_query($koneksi, "UPDATE anggota SET email_anggota = '$email_anggota_2', telepon_anggota = '$telepon_anggota_2', bank_anggota = '$bank_anggota_2', nama_rekening_anggota = '$nama_rekening_anggota_2', nomor_rekening_anggota = '$nomor_rekening_anggota_2', status_anggota = '$status_anggota_2', status_game = '$status_game_2' WHERE id_anggota = '$id_anggota'");
  } else {
    $opsi = ['cost' => 12];
    $kata_sandi_hash_anggota = password_hash($kata_sandi_anggota_2, PASSWORD_BCRYPT, $opsi);
    $ubah_data = mysqli_query($koneksi, "UPDATE anggota SET kata_sandi_anggota = '$kata_sandi_hash_anggota', email_anggota = '$email_anggota_2', telepon_anggota = '$telepon_anggota_2', bank_anggota = '$bank_anggota_2', nama_rekening_anggota = '$nama_rekening_anggota_2', nomor_rekening_anggota = '$nomor_rekening_anggota_2', status_anggota = '$status_anggota_2', status_game = '$status_game_2' WHERE id_anggota = '$id_anggota'");
  }

  if ($ubah_data) {
    echo '
      <script>
        alert("Berhasil ubah data.");
        window.location.replace("' . $alamat_admin . 'anggota");
      </script>
    ';
  } else {
    echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
  }
} elseif (isset($_POST['hapus_data'])) {
  $hapus_data = mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota = '$id_anggota'");
  if ($hapus_data) {
    echo '
      <script>
        alert("Berhasil hapus data.");
        window.location.replace("' . $alamat_admin . 'anggota");
      </script>
    ';
  } else {
    echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
  }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row gy-4 mb-4">
    <div class="col-md-6">
      <div class="fw-bold fs-4 text-center text-md-start">Anggota</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <a href="<?php echo $alamat_admin . 'anggota'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-chevron-double-left me-1"></span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Ubah Data Anggota</h5>
    <form method="post" class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" class="form-control" value="<?php echo $nama_pengguna_anggota; ?>" readonly disabled>
            <label>Nama Pengguna</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="password" name="kata_sandi_anggota" class="form-control" placeholder="Kosongkan jika tidak diubah">
            <label>Kata Sandi</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="email" name="email_anggota" class="form-control" value="<?php echo $email_anggota; ?>" placeholder="Email" required>
            <label>Email</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="telepon_anggota" class="form-control" value="<?php echo $telepon_anggota; ?>" placeholder="Telepon" required>
            <label>Telepon</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline mb-4">
            <select name="bank_anggota" class="form-select select2" required>
              <?php
              $list_bank = array("BCA", "BNI", "BRI", "CIMB", "DANAMON", "GOPAY", "MANDIRI", "MEGA", "MYBANK", "OVO", "SAKUKU", "SHOPEEPAY", "SIMPATI", "SINARMAS", "SYARIAHINDONESIA", "XL");
              foreach ($list_bank as $bank) {
                echo '<option value="' . $bank . '" ' . (($bank_anggota == $bank) ? 'selected' : '') . '>' . $bank . '</option>';
              }
              ?>
            </select>
            <label>Bank</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="nama_rekening_anggota" class="form-control" value="<?php echo $nama_rekening_anggota; ?>" placeholder="Nama Rekening" required>
            <label>Nama Rekening</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="nomor_rekening_anggota" class="form-control" value="<?php echo $nomor_rekening_anggota; ?>" placeholder="Nomor Rekening" required>
            <label>Nomor Rekening</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline mb-4">
            <select name="status_anggota" class="form-select select2" required>
              <?php
              if ($status_anggota == "aktif") {
                echo '
                <option value="aktif" selected>Aktif</option>
                <option value="terkunci">Terkunci</option>
                ';
              } else {
                echo '
                <option value="aktif">Aktif</option>
                <option value="terkunci" selected>Terkunci</option>
                ';
              }
              ?>
            </select>
            <label>Status Anggota</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline mb-4">
            <select name="status_game" class="form-select select2" required>
              <?php
              if ($status_game == "Aktif") {
                echo '
                <option value="Aktif" selected>Aktif</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
                ';
              } else {
                echo '
                <option value="Tidak Aktif" selected>Tidak Aktif</option>
                <option value="Aktif">Aktif</option>
                ';
              }
              ?>
            </select>
            <label>Status Game</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="button" class="btn btn-danger waves-effect waves-light me-sm-3 me-1" data-bs-toggle="modal" data-bs-target="#hapus_data">
          <span class="tf-icons mdi mdi-delete me-1"></span>
          Hapus
        </button>
        <button type="submit" name="ubah_data" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="hapus_data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Hapus Data</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          Yakin ingin menghapus data ini?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="hapus_data" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>