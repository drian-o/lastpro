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

  if (isset($_GET['id_promosi'])) {
    $id_promosi = mysqli_real_escape_string($koneksi, $_GET['id_promosi']);
    $promosi = mysqli_query($koneksi, "SELECT * FROM promosi WHERE id_promosi = '$id_promosi'");
    
    if (!$promosi || mysqli_num_rows($promosi) == 0) {
        echo '
          <script>
            alert("Promosi tidak ditemukan!");
            window.location.replace("'.$alamat_admin.'promosi");
          </script>
        ';
        exit();
    }
    
    $data_promosi = mysqli_fetch_array($promosi);
    $gambar_promosi = $data_promosi['gambar_promosi'];
    $judul_promosi = $data_promosi['judul_promosi'];
    $kategori_promosi = $data_promosi['kategori_promosi'];
    $deskripsi_promosi = $data_promosi['deskripsi_promosi'];
  } else {
    echo '
      <script>
        alert("Pilih promosi yang ingin diubah!");
        window.location.replace("'.$alamat_admin.'promosi");
      </script>
    ';
    exit();
  }

  if (isset($_POST['ubah_data'])) {
    $judul_promosi_2 = $_POST['judul_promosi'];
    $kategori_promosi_2 = $_POST['kategori_promosi'];
    $deskripsi_promosi_2 = $_POST['deskripsi_promosi'];
    $random = rand(1000000000, 9999999999);
    $tmp_file = $_FILES['gambar_promosi']['tmp_name'];
    $nama_file = $_FILES['gambar_promosi']['name'];
    
    $tambah_promosi = false;
    $file_input = $gambar_promosi;
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
          @unlink("../assets/img/".$gambar_promosi);
        } else {
          $error_message = "Gagal upload gambar, usahakan nama file gambar pendek, atau cek koneksi internet anda!";
        }
      }
    }

    if ($error_message) {
      echo '
        <script>
          alert("' . $error_message . '");
          window.location.replace("'.$alamat_admin.'ubah_promosi?id_promosi='.$id_promosi.'");
        </script>
      ';
      exit();
    }
    
    if ($file_uploaded) {
      $query = "UPDATE promosi SET gambar_promosi = ?, judul_promosi = ?, kategori_promosi = ?, deskripsi_promosi = ? WHERE id_promosi = ?";
      $stmt = mysqli_prepare($koneksi, $query);
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $file_input, $judul_promosi_2, $kategori_promosi_2, $deskripsi_promosi_2, $id_promosi);
        $ubah_promosi = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }
    } else {
      $query = "UPDATE promosi SET judul_promosi = ?, kategori_promosi = ?, deskripsi_promosi = ? WHERE id_promosi = ?";
      $stmt = mysqli_prepare($koneksi, $query);
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $judul_promosi_2, $kategori_promosi_2, $deskripsi_promosi_2, $id_promosi);
        $ubah_promosi = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
      }
    }
    
    if ($ubah_promosi) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'promosi");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
    }
    
  } else if (isset($_POST['hapus_data'])) {
    
    $query = "DELETE FROM promosi WHERE id_promosi = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_promosi);
        $hapus_data = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($hapus_data) {
            @unlink("../assets/img/".$gambar_promosi);
            echo '
              <script>
                alert("Berhasil hapus data.");
                window.location.replace("'.$alamat_admin.'promosi");
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
      <div class="fw-bold fs-4 text-center text-md-start">Promosi</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <a href="<?php echo $alamat_admin.'promosi'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-chevron-double-left me-1"></span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Ubah Data Promosi</h5>
    <form method="post" enctype="multipart/form-data" class="card-body">
      <h6>1. Gambar</h6>
      <div class="row g-3">
        <div class="col-12">
          <div class="mb-3">
            <div class="bg-secondary rounded text-center p-3 mb-3">
              <img src="<?php echo '../assets/img/'.$gambar_promosi; ?>" alt="<?php echo $judul_promosi; ?>" class="img-fluid">
            </div>
            <input type="file" name="gambar_promosi" class="form-control" id="formFile">
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
            <input type="text" name="judul_promosi" class="form-control" value="<?php echo htmlspecialchars($judul_promosi); ?>" required>
            <label>Judul</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="kategori_promosi" class="form-control" value="<?php echo htmlspecialchars($kategori_promosi); ?>" required>
            <label>Kategori</label>
          </div>
        </div>
        <div class="col-12">
          <textarea name="deskripsi_promosi" class="summernote"><?php echo htmlspecialchars($deskripsi_promosi); ?></textarea>
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