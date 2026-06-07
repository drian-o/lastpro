<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 0);
  include_once '../koneksi.php';

  if (!isset($_SESSION['kode_admin'])) {
    echo '
      <script>
        alert("Terjadi kesalahan, harap masuk kembali!");
        window.location.replace("'.$alamat_admin.'keluar.php");
      </script>
    ';
    exit();
  }
  
  if (isset($_POST['tambah_data'])) {
    // Sanitasi input
    $jenis_rekening = mysqli_real_escape_string($koneksi, $_POST['jenis_bank']);
    $atas_nama_rekening = mysqli_real_escape_string($koneksi, $_POST['atas_nama_bank']);
    $nomor_rekening_rekening = mysqli_real_escape_string($koneksi, $_POST['nomor_rekening_bank']);
    
    $random = rand(1000000000, 9999999999);
    $tmp_file = $_FILES['gambar_bank']['tmp_name'];
    $nama_file = $_FILES['gambar_bank']['name'];
    
    $error_message = null;
    
    if (empty($nama_file)) {
      $error_message = "Harap unggah gambar untuk rekening!";
    } else {
      $format =  array('png', 'PNG', 'jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'svg', 'SVG');
      $extensi = pathinfo($nama_file, PATHINFO_EXTENSION);
      
      if (!in_array($extensi, $format)) {
        $error_message = "Format gambar salah, format gambar yang diperbolehkan adalah PNG, JPG, JPEG, GIF, dan SVG!";
      } else {
        $file = strtolower(str_replace(" ", "_", $nama_file));
        $file_input = $random.'_'.$file;
        $lokasi_simpan = "../assets/img/bank_admin/".$file_input;
        
        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
          // Upload berhasil
        } else {
          $error_message = "Gagal upload gambar, usahakan nama file gambar pendek, atau cek izin direktori!";
        }
      }
    }
    
    if ($error_message) {
      echo '
        <script>
          alert("' . $error_message . '");
          window.location.replace("'.$alamat_admin.'tambah_rekening");
        </script>
      ';
      exit();
    }
    
    // Jika tidak ada error message, lanjutkan ke database
    $query = "INSERT INTO bank (gambar_bank, jenis_bank, atas_nama_bank, nomor_rekening_bank) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $file_input, $jenis_rekening, $atas_nama_rekening, $nomor_rekening_rekening);
        $tambah_rekening = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($tambah_rekening) {
          echo '
            <script>
              alert("Berhasil tambah data.");
              window.location.replace("'.$alamat_admin.'rekening");
            </script>
          ';
        } else {
          // Jika gagal simpan ke DB, hapus file yang baru diupload
          @unlink($lokasi_simpan); 
          echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
        }
    } else {
      // Jika prepared statement gagal dibuat
      @unlink($lokasi_simpan);
      echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
    }
  }
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row gy-4 mb-4">
    <div class="col-md-6">
      <div class="fw-bold fs-4 text-center text-md-start">Rekening</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <a href="<?php echo $alamat_admin.'rekening'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-chevron-double-left me-1"></span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Tambah Data Rekening</h5>
    <form method="post" enctype="multipart/form-data" class="card-body">
      <h6>1. Gambar</h6>
      <div class="row g-3">
        <div class="col-12">
          <div class="mb-3">
            <input type="file" name="gambar_bank" class="form-control" required>
            <div class="form-text">
              Format gambar harus PNG, JPG, JPEG, GIF, atau SVG.
            </div>
          </div>
        </div>
      </div>
      <hr class="my-4 mx-n4">
      <h6>2. Detail</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline mb-4">
            <select name="jenis_bank" class="form-select select2" required>
              <option value="" selected>-- Pilih Bank --</option>
              <option value="AXIS">AXIS</option>
              <option value="ARTHAGRAHA">ARTHA GRAHA</option>
              <option value="BCA">BCA</option>
              <option value="BJB">BJB</option>
              <option value="BNI">BNI</option>
              <option value="BRI">BRI</option>
              <option value="BTPN">BTPN</option>
              <option value="BUKOPIN">BUKOPIN</option>
              <option value="CIMB">CIMB</option>
              <option value="DANA">DANA</option>
              <option value="DANAMON">DANAMON</option>
              <option value="DKI">DKI</option>
              <option value="GOPAY">GOPAY</option>
              <option value="INDOSAT">INDOSAT</option>
              <option value="LINKAJA">LINKAJA</option>
              <option value="MANDIRI">MANDIRI</option>
              <option value="MEGA">MEGA</option>
              <option value="MYBANK">MYBANK</option>
              <option value="OVO">OVO</option>
              <option value="QRIS">QRIS</option>
              <option value="SAKUKU">SAKUKU</option>
              <option value="SHOPEEPAY">SHOPEEPAY</option>
              <option value="SIMPATI">SIMPATI</option>
              <option value="SINARMAS">SINARMAS</option>
              <option value="SYARIAHINDONESIA">SYARIAH INDONESIA</option>
              <option value="XL">XL</option>
            </select>
            <label>Bank</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="atas_nama_bank" class="form-control" placeholder="Atas Nama" required>
            <label>Atas Nama</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="nomor_rekening_bank" class="form-control" placeholder="Nomor Rekening" required>
            <label>Nomor Rekening</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="tambah_data" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>