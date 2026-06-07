<?php
  include_once '../koneksi.php';
  include_once '../classes/class.exa.php';
  
  $GXA = new GameXaAPI();
  
  if (!isset($_SESSION['kode_staff'])) {
    echo '
      <script>
        alert("Terjadi kesalahan, harap masuk kembali!");
        window.location.replace("'.$alamat_staff.'keluar.php");
      </script>
    ';
  }
  
  if (isset($_POST['tambah_data'])) {
    $nama_pengguna_anggota = strtolower($_POST['nama_pengguna_anggota']);
    $kata_sandi_anggota = $_POST['kata_sandi_anggota'];
    $email_anggota = $_POST['email_anggota'];
    $telepon_anggota = $_POST['telepon_anggota'];
    $bank_anggota = $_POST['bank_anggota'];
    $nama_rekening_anggota = $_POST['nama_rekening_anggota'];
    $nomor_rekening_anggota = $_POST['nomor_rekening_anggota'];
    $opsi = ['cost' => 12];
    $kata_sandi_hash_anggota = password_hash($kata_sandi_anggota, PASSWORD_BCRYPT, $opsi);
    
    if (preg_match('/^[a-zA-Z0-9\s]+$/', $nama_pengguna_anggota)) {
      $cek_nama_pengguna_anggota = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nama_pengguna_anggota = '$nama_pengguna_anggota'");
      
      if (mysqli_num_rows($cek_nama_pengguna_anggota) == 0) {
        
        // 1. Panggil API untuk membuat Player
        $register_api = $GXA->createPlayer(
          $nama_pengguna_anggota,
          $email_anggota,
          $kata_sandi_anggota,
          $nama_rekening_anggota, // Asumsi: Nama Rekening digunakan sebagai Full Name
          $telepon_anggota,
          "IDR"
        );
        
        if ($register_api['success'] === true && isset($register_api['data']['player']['id'])) {
          $ext_pengguna_anggota_api = $register_api['data']['player']['id']; // Ambil Player ID dari API
          
          // 2. Simpan data ke database lokal, termasuk ext_pengguna_anggota
          $tambah_data = mysqli_query($koneksi, "INSERT INTO anggota (nama_pengguna_anggota, ext_pengguna_anggota, kata_sandi_anggota, email_anggota, telepon_anggota, bank_anggota, nama_rekening_anggota, nomor_rekening_anggota) VALUES ('$nama_pengguna_anggota', '$ext_pengguna_anggota_api', '$kata_sandi_hash_anggota', '$email_anggota', '$telepon_anggota', '$bank_anggota', '$nama_rekening_anggota', '$nomor_rekening_anggota')");
          
          if ($tambah_data) {
            echo '
              <script>
                alert("Berhasil tambah data anggota dan registrasi ke API.");
                window.location.replace("'.$alamat_staff.'anggota");
              </script>
            ';
          } else {
            // Jika gagal simpan ke DB, Anda mungkin perlu memanggil API untuk menghapus akun yang baru dibuat (rollback), tetapi untuk saat ini kita hanya menampilkan error.
            echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
          }
        } else {
          // Gagal membuat akun di API
          $message = isset($register_api['message']) ? $register_api['message'] : 'Gagal membuat akun di API GameXa.';
          echo '
            <script>
              alert("'.$message.'");
              window.location.replace("'.$alamat_staff.'tambah_anggota");
            </script>
          ';
        }
      } else {
        echo '
          <script>
            alert("Nama pengguna sudah ada!");
            window.location.replace("'.$alamat_staff.'tambah_anggota");
          </script>
        ';
      }
    } else {
      echo '
        <script>
          alert("Nama pengguna hanya boleh mengandung huruf dan angka!");
          window.location.replace("'.$alamat_staff.'tambah_anggota");
        </script>
      ';
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
        <a href="<?php echo $alamat_staff.'anggota'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-chevron-double-left me-1"></span>
          Kembali
        </a>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Tambah Data Anggota</h5>
    <form method="post" class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="nama_pengguna_anggota" class="form-control" placeholder="Nama Pengguna" required>
            <label>Nama Pengguna</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="password" name="kata_sandi_anggota" class="form-control" placeholder="Kata Sandi" required>
            <label>Kata Sandi</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="email" name="email_anggota" class="form-control" placeholder="Email" required>
            <label>Email</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="telepon_anggota" class="form-control" placeholder="Telepon" required>
            <label>Telepon</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline mb-4">
            <select name="bank_anggota" class="form-select select2" required>
              <option value="BCA">BCA</option>
              <option value="BNI">BNI</option>
              <option value="BRI">BRI</option>
              <option value="CIMB">CIMB</option>
              <option value="DANAMON">DANAMON</option>
              <option value="GOPAY">GOPAY</option>
              <option value="MANDIRI">MANDIRI</option>
              <option value="MEGA">MEGA</option>
              <option value="MYBANK">MYBANK</option>
              <option value="OVO">OVO</option>
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
            <input type="text" name="nama_rekening_anggota" class="form-control" placeholder="Nama Rekening" required>
            <label>Nama Rekening</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="nomor_rekening_anggota" class="form-control" placeholder="Nomor Rekening" required>
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