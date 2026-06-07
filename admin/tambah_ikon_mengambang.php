<?php
// Non-aktifkan error reporting setelah debugging (opsional, disarankan untuk produksi)
error_reporting(E_ALL);
ini_set('display_errors', 0);

include_once '../koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['kode_admin'])) {
    echo '
      <script>
        alert("Terjadi kesalahan, harap masuk kembali!");
        window.location.replace("'.$alamat_admin.'keluar.php");
      </script>
    ';
    exit();
}

// Cek apakah form sudah disubmit
if (isset($_POST['tambah_data'])) {
    // Sanitasi input
    $nama_ikon_mengambang = mysqli_real_escape_string($koneksi, $_POST['nama_floating']);
    $link_ikon_mengambang = mysqli_real_escape_string($koneksi, $_POST['link_floating']);
    
    // Ambil data file gambar
    $tmp_file = $_FILES['gambar_floating']['tmp_name'];
    $nama_file = $_FILES['gambar_floating']['name'];
    
    $error_message = null;
    $file_input = null;
    $lokasi_simpan = null;
    
    // Validasi file
    if (empty($nama_file)) {
      $error_message = "Harap unggah gambar untuk ikon mengambang!";
    } else {
      $format =  array('png', 'jpg', 'jpeg', 'gif', 'svg');
      $extensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
      
      if (!in_array($extensi, $format)) {
        $error_message = "Format gambar salah, format gambar yang diperbolehkan adalah PNG, JPG, JPEG, GIF, dan SVG!";
      } else {
        // Proses penamaan dan lokasi simpan
        $random = rand(1000000000, 9999999999);
        $file = strtolower(str_replace(" ", "_", $nama_file));
        $file_input = $random.'_'.$file;
        $lokasi_simpan = "../assets/img/".$file_input;
        
        // Proses upload gambar
        if (!move_uploaded_file($tmp_file, $lokasi_simpan)) {
          $error_message = "Gagal upload gambar, usahakan nama file gambar pendek, atau cek izin direktori!";
        }
      }
    }
    
    if ($error_message) {
      echo '
        <script>
          alert("' . $error_message . '");
          window.location.replace("'.$alamat_admin.'tambah_ikon_mengambang");
        </script>
      ';
      exit();
    }
    
    // Gunakan Prepared Statement untuk INSERT
    $query = "INSERT INTO floating (nama_floating, link_floating, gambar_floating) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $nama_ikon_mengambang, $link_ikon_mengambang, $file_input);
        $tambah_ikon_mengambang = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($tambah_ikon_mengambang) {
          echo '
            <script>
              alert("Berhasil tambah data.");
              window.location.replace("'.$alamat_admin.'ikon_mengambang");
            </script>
          ';
        } else {
          // Jika gagal simpan ke DB, hapus file yang baru diupload
          if ($lokasi_simpan && file_exists($lokasi_simpan)) {
              @unlink($lokasi_simpan); 
          }
          echo "Proses Gagal<br>Error: " . mysqli_error($koneksi);
        }
    } else {
      // Jika prepared statement gagal dibuat
      if ($lokasi_simpan && file_exists($lokasi_simpan)) {
          @unlink($lokasi_simpan);
      }
      echo "Proses Gagal<br>Error: " . mysqli_error($koneksi);
    }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row gy-4 mb-4">
    <div class="col-md-6">
      <div class="fw-bold fs-4 text-center text-md-start">Ikon Mengambang</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <a href="<?php echo $alamat_admin.'ikon_mengambang'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-chevron-double-left me-1"></span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Tambah Data Ikon Mengambang</h5>
    <form method="post" enctype="multipart/form-data" class="card-body">
      <h6>1. Gambar</h6>
      <div class="row g-3">
        <div class="col-12">
          <div class="mb-3">
            <input type="file" name="gambar_floating" class="form-control" required>
            <div class="form-text">
              Format gambar harus PNG, JPG, JPEG, GIF, atau SVG.
            </div>
          </div>
        </div>
      </div>
      <hr class="my-4 mx-n4">
      <h6>2. Detail</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="nama_floating" class="form-control" placeholder="Nama" required>
            <label>Nama</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="link_floating" class="form-control" placeholder="Link" required>
            <label>Link</label>
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