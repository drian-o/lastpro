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
  
  if (isset($_GET['id_ikon_mengambang'])) {
    $id_ikon_mengambang = mysqli_real_escape_string($koneksi, $_GET['id_ikon_mengambang']);
    $ikon_mengambang = mysqli_query($koneksi, "SELECT * FROM floating WHERE id_floating = '$id_ikon_mengambang'");
    
    if (!$ikon_mengambang || mysqli_num_rows($ikon_mengambang) == 0) {
        echo '
          <script>
            alert("Ikon mengambang tidak ditemukan!");
            window.location.replace("'.$alamat_admin.'ikon_mengambang");
          </script>
        ';
        exit();
    }
    
    $data_ikon_mengambang = mysqli_fetch_array($ikon_mengambang);
    $nama_ikon_mengambang = $data_ikon_mengambang['nama_floating'];
    $link_ikon_mengambang = $data_ikon_mengambang['link_floating'];
    $gambar_ikon_mengambang = $data_ikon_mengambang['gambar_floating'];
  } else {
    echo '
      <script>
        alert("Pilih ikon mengambang yang ingin diubah!");
        window.location.replace("'.$alamat_admin.'ikon_mengambang");
      </script>
    ';
    exit();
  }
  
  if (isset($_POST['ubah_data'])) {
    $nama_ikon_mengambang_2 = mysqli_real_escape_string($koneksi, $_POST['nama_floating']);
    $link_ikon_mengambang_2 = mysqli_real_escape_string($koneksi, $_POST['link_floating']);
    
    $random = rand(1000000000, 9999999999);
    $tmp_file = $_FILES['gambar_floating']['tmp_name'];
    $nama_file = $_FILES['gambar_floating']['name'];

    $file_input = $gambar_ikon_mengambang;
    $file_uploaded = false;
    $error_message = null;

    if (!empty($nama_file)) {
      $format =  array('png', 'PNG', 'jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF', 'svg', 'SVG');
      $extensi = pathinfo($nama_file, PATHINFO_EXTENSION);
      
      if (!in_array($extensi, $format)) {
        $error_message = "Format gambar salah, format gambar yang diperbolehkan adalah PNG, JPG, JPEG, GIF, dan SVG!";
      } else {
        $file = strtolower(str_replace(" ", "_", $nama_file));
        $file_input = $random.'_'.$file;
        $lokasi_simpan = "../assets/img/".$file_input;
        
        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
          $file_uploaded = true;
          // Hapus gambar lama HANYA JIKA gambar lama ada dan nama gambar baru berbeda
          if (!empty($gambar_ikon_mengambang) && $gambar_ikon_mengambang != $file_input) {
              @unlink("../assets/img/".$gambar_ikon_mengambang);
          }
        } else {
          $error_message = "Gagal upload gambar, usahakan nama file gambar pendek, atau cek izin direktori!";
        }
      }
    }

    if ($error_message) {
      echo '
        <script>
          alert("' . $error_message . '");
          window.location.replace("'.$alamat_admin.'ubah_ikon_mengambang/'.$id_ikon_mengambang.'");
        </script>
      ';
      exit();
    }
    
    $ubah_ikon_mengambang = false;

    if ($file_uploaded) {
      // Dengan Gambar Baru
      $query = "UPDATE floating SET nama_floating = ?, link_floating = ?, gambar_floating = ? WHERE id_floating = ?";
      $stmt = mysqli_prepare($koneksi, $query);
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $nama_ikon_mengambang_2, $link_ikon_mengambang_2, $file_input, $id_ikon_mengambang);
        $ubah_ikon_mengambang = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }
    } else {
      // Tanpa Gambar Baru (Hanya Update Detail)
      $query = "UPDATE floating SET nama_floating = ?, link_floating = ? WHERE id_floating = ?";
      $stmt = mysqli_prepare($koneksi, $query);
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $nama_ikon_mengambang_2, $link_ikon_mengambang_2, $id_ikon_mengambang);
        $ubah_ikon_mengambang = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }
    }
    
    if (isset($ubah_ikon_mengambang) && $ubah_ikon_mengambang) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'ikon_mengambang");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
    }

  } else if (isset($_POST['hapus_data'])) {
    
    $query = "DELETE FROM floating WHERE id_floating = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_ikon_mengambang);
        $hapus_data = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($hapus_data) {
            // Hapus gambar terkait
            if (!empty($gambar_ikon_mengambang)) {
                @unlink("../assets/img/".$gambar_ikon_mengambang);
            }
            echo '
              <script>
                alert("Berhasil hapus data.");
                window.location.replace("'.$alamat_admin.'ikon_mengambang");
              </script>
            ';
        } else {
            echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
        }
    } else {
        echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
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
    <h5 class="card-header">Ubah Data Ikon Mengambang</h5>
    <form method="post" enctype="multipart/form-data" class="card-body">
      <h6>1. Gambar</h6>
      <div class="row g-3">
        <div class="col-12">
          <div class="mb-3">
            <div class="bg-secondary rounded text-center p-3 mb-3">
              <img src="<?php echo '../assets/img/'.$gambar_ikon_mengambang; ?>" alt="<?php echo htmlspecialchars($nama_ikon_mengambang); ?>" class="img-fluid">
            </div>
            <input type="file" name="gambar_floating" class="form-control" id="formFile">
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
            <input type="text" name="nama_floating" class="form-control" value="<?php echo htmlspecialchars($nama_ikon_mengambang); ?>" required>
            <label>Nama</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="link_floating" class="form-control" value="<?php echo htmlspecialchars($link_ikon_mengambang); ?>">
            <label>Link</label>
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