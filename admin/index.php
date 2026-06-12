<?php
  session_start();
  include_once '../koneksi.php';
  if (isset($_SESSION['kode_admin'])) {
    $kode_admin_aktif = $_SESSION['kode_admin'];
    $admin_aktif = mysqli_query($koneksi, "SELECT * FROM admin WHERE kode_admin = '$kode_admin_aktif'");
    if (mysqli_num_rows($admin_aktif) == 1) {
      $data_admin_aktif = mysqli_fetch_array($admin_aktif);
      $id_admin = $data_admin_aktif['id_admin'];
      $nama_admin = $data_admin_aktif['nama_admin'];
      $nama_pengguna_admin = $data_admin_aktif['nama_pengguna_admin'];
      $kata_sandi_admin = $data_admin_aktif['kata_sandi_admin'];
      $pin_admin = $data_admin_aktif['pin_admin'];
    } else {
      echo '
        <script>
          alert("Terjadi kesalahan, harap masuk kembali!");
          window.location.replace("'.$alamat_admin.'keluar.php");
        </script>
      ';
    }
  } else {
    echo '
      <script>
        window.location.replace("'.$alamat_admin.'masuk");
      </script>
    ';
  }
  if (isset($_GET['halaman'])) {
    $halaman_aktif = $_GET['halaman'];
  } else {
    echo '
      <script>
        window.location.replace("'.$alamat_admin.'dasbor");
      </script>
    ';
  }
?>
<!DOCTYPE html>
<html lang="en" class="dark-style layout-navbar-fixed layout-menu-fixed layout-footer-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-starter">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title><?php echo ucwords(str_replace('_', ' ', $halaman_aktif)); ?> | Panel Admin</title>
    <base href="<?php echo $alamat_admin; ?>">
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/materialdesignicons.css">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css" class="template-customizer-core-css">
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css">
    <link rel="stylesheet" href="assets/css/demo.css">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendor/libs/node-waves/node-waves.css">
    <link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="assets/vendor/libs/summernote/summernote-bs4.css" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css" />
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
  </head>
  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php
          include_once "sidebar.php";
        ?>
        <div class="layout-page">
          <?php
            include_once "navbar.php";
          ?>
          <div class="content-wrapper">
            
            <?php
              if ($halaman_aktif == "dasbor") {
                include_once "dasbor.php";
              } else if ($halaman_aktif == "pemberitahuan") {
                include_once "pemberitahuan.php";
              } else if ($halaman_aktif == "anggota") {
                include_once "anggota.php";
              } else if ($halaman_aktif == "ubah_saldo") {
                include_once "ubah_saldo.php";
              } else if ($halaman_aktif == "saldo") {
                include_once "saldo.php";
              } else if ($halaman_aktif == "tambah_anggota") {
                include_once "tambah_anggota.php";
              } else if ($halaman_aktif == "ubah_anggota") {
                include_once "ubah_anggota.php";
              } else if ($halaman_aktif == "deposit") {
                include_once "deposit.php";
              } else if ($halaman_aktif == "ubah_deposit") {
                include_once "ubah_deposit.php";
              } else if ($halaman_aktif == "withdraw") {
                include_once "withdraw.php";
              } else if ($halaman_aktif == "ubah_withdraw") {
                include_once "ubah_withdraw.php";
              } else if ($halaman_aktif == "rekening") {
                include_once "rekening.php";
              } else if ($halaman_aktif == "tambah_rekening") {
                include_once "tambah_rekening.php";
              } else if ($halaman_aktif == "ubah_rekening") {
                include_once "ubah_rekening.php";
              } else if ($halaman_aktif == "bukti_jp") {
                include_once "bukti_jp.php";
              } else if ($halaman_aktif == "tambah_bukti_jp") {
                include_once "tambah_bukti_jp.php";
              } else if ($halaman_aktif == "ubah_bukti_jp") {
                include_once "ubah_bukti_jp.php";
              } else if ($halaman_aktif == "promosi") {
                include_once "promosi.php";
              } else if ($halaman_aktif == "tambah_promosi") {
                include_once "tambah_promosi.php";
              }
              else if ($halaman_aktif == "refferal") {
                include_once "refferal.php";
              } else if ($halaman_aktif == "ubah_promosi") {
                include_once "ubah_promosi.php";
              } else if ($halaman_aktif == "staff") {
                include_once "staff.php";
              } else if ($halaman_aktif == "tambah_staff") {
                include_once "tambah_staff.php";
              } else if ($halaman_aktif == "ubah_staff") {
                include_once "ubah_staff.php";
              } else if ($halaman_aktif == "bonus") {
                include_once "bonus.php";
              } else if ($halaman_aktif == "tambah_bonus") {
                include_once "tambah_bonus.php";
              } else if ($halaman_aktif == "ubah_bonus") {
                include_once "ubah_bonus.php";
              } else if ($halaman_aktif == "ikon_mengambang") {
                include_once "ikon_mengambang.php";
              } else if ($halaman_aktif == "tambah_ikon_mengambang") {
                include_once "tambah_ikon_mengambang.php";
              } else if ($halaman_aktif == "ubah_ikon_mengambang") {
                include_once "ubah_ikon_mengambang.php";
              } else if ($halaman_aktif == "profil") {
                include_once "profil.php";
              } else if ($halaman_aktif == "pengaturan") {
                include_once "pengaturan.php";
              }
                else if ($halaman_aktif == "add_domain") {
                include_once "add_domain.php";
              }
                else if ($halaman_aktif == "rekap") {
                include_once "rekap.php";
              }
               else if ($halaman_aktif == "provider") {
                include_once "provider.php";
              }
                else if ($halaman_aktif == "log_active.php") {
                include_once "log_active.php";
              }
              include_once "footer.php";
            ?>
          </div>
        </div>
      </div>
      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <script src="assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="assets/vendor/libs/cleavejs/cleave-phone.js"></script>
    <script src="assets/vendor/libs/moment/moment.js"></script>
    <script src="assets/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="assets/vendor/libs/select2/select2.js"></script>
    <script src="assets/vendor/libs/summernote/summernote-bs4.js"></script>
    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
    <!-- Page JS -->
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="assets/js/form-layouts.js"></script>
    <script>
      $(document).ready(function() {
        $("#<?php echo $halaman_aktif; ?>").addClass("active");
        <?php
          if ($halaman_aktif == "tambah_anggota" || $halaman_aktif == "ubah_anggota") {
        ?>
        $("#anggota").addClass("active");
        <?php
          } else if ($halaman_aktif == "pemberitahuan") {
        ?>
        $("#pemberitahuan").addClass("active");
        <?php
          } else if ($halaman_aktif == "ubah_deposit") {
        ?>
        $("#deposit").addClass("active");
        <?php
          } else if ($halaman_aktif == "ubah_withdraw") {
        ?>
        $("#withdraw").addClass("active");
        <?php
          } else if ($halaman_aktif == "tambah_rekening" || $halaman_aktif == "ubah_rekening") {
        ?>
        $("#rekening").addClass("active");
        <?php
          } else if ($halaman_aktif == "tambah_promosi" || $halaman_aktif == "ubah_promosi") {
        ?>
        $("#promosi").addClass("active");
        <?php
          } else if ($halaman_aktif == "tambah_staff" || $halaman_aktif == "ubah_staff") {
        ?>
        $("#staff").addClass("active");
        <?php
          } else if ($halaman_aktif == "tambah_bonus" || $halaman_aktif == "ubah_bonus") {
        ?>
        $("#bonus").addClass("active");
        <?php
          } else if ($halaman_aktif == "tambah_ikon_mengambang" || $halaman_aktif == "ubah_ikon_mengambang") {
        ?>
        $("#ikon_mengambang").addClass("active");
        <?php
          } else if ($halaman_aktif == "tambah_bukti_jp" || $halaman_aktif == "ubah_bukti_jp") {
        ?>
        $("#bukti_jp").addClass("active");
        <?php
          }
        ?>
        function updateLiveTime() {
          var currentTime = new Date();
          var hours = currentTime.getHours();
          var minutes = currentTime.getMinutes();
          var seconds = currentTime.getSeconds();
          // Formatting the time (add leading zero if needed)
          hours = (hours < 10 ? "0" : "") + hours;
          minutes = (minutes < 10 ? "0" : "") + minutes;
          seconds = (seconds < 10 ? "0" : "") + seconds;
          // Menampilkan waktu di dalam elemen dengan id "jam_sekarang"
          $("#jam_sekarang").html("Jam " + hours + ":" + minutes + ":" + seconds);
        }
        // Memanggil fungsi updateLiveTime setiap detik (1000 milidetik)
        setInterval(updateLiveTime, 1000);

        // DataTable
        $("#example").DataTable();

        // Select2
        $(".select2").select2();

        // Summernote
        $(".summernote").summernote({
          tabsize: 2,
          height: 100
        });
      });
    </script>
  </body>
</html>
