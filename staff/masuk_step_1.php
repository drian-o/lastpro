<?php
  session_start();
  include_once "koneksi.php";
  $nama_pengguna_staff = $_POST['nama_pengguna_staff'];
  $kata_sandi_staff = $_POST['kata_sandi_staff'];
  $staff = mysqli_query($koneksi, "SELECT * FROM staff WHERE nama_pengguna_staff = '$nama_pengguna_staff'");
  if (mysqli_num_rows($staff) >= 1) {
    $data_staff = mysqli_fetch_array($staff);
    $id_staff = $data_staff['id_staff'];
    $kata_sandi_staff_hash = $data_staff['kata_sandi_staff'];
    if (password_verify($kata_sandi_staff, $kata_sandi_staff_hash)) {
      $perbarui_posisi_staff = mysqli_query($koneksi, "UPDATE staff SET posisi_staff = 'masuk' WHERE id_staff = '$id_staff'");
      $posisi_staff = $data_staff['posisi_staff'];
      if ($perbarui_posisi_staff) {
        echo 'success';
        $_SESSION['posisi_staff'] == $posisi_staff;
        $_SESSION['nama_pengguna_staff'] == $nama_pengguna_staff;
      } else {
        echo 'error';
      }
    } else {
      echo 'error';
    }
  } else {
    echo 'error';
  }
?>