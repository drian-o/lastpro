<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 0);
  include_once '../koneksi.php';

  $BASE_UPLOAD_DIR = realpath(__DIR__ . '/../assets/img/');
  $BANK_UPLOAD_DIR = realpath(__DIR__ . '/../assets/img/bank_admin/');
  $DS = DIRECTORY_SEPARATOR;
  
  if ($BASE_UPLOAD_DIR === false || $BANK_UPLOAD_DIR === false) {
    echo '
      <script>
        alert("Terjadi kesalahan path: Direktori assets/img/ atau assets/img/bank_admin/ tidak ditemukan oleh server!");
        window.location.replace("'.$alamat_admin.'keluar.php");
      </script>
    ';
    exit();
  }
  
  if (!isset($_SESSION['kode_admin'])) {
    echo '
      <script>
        alert("Terjadi kesalahan, harap masuk kembali!");
        window.location.replace("'.$alamat_admin.'keluar.php");
      </script>
    ';
    exit();
  }

  $isi_1_logo_web = isset($isi_1_logo_web) ? $isi_1_logo_web : ''; 
  $isi_1_favicon_web = isset($isi_1_favicon_web) ? $isi_1_favicon_web : ''; 
  $isi_1_popup_pengumuman_web = isset($isi_1_popup_pengumuman_web) ? $isi_1_popup_pengumuman_web : ''; 
  $isi_1_qris_web = isset($isi_1_qris_web) ? $isi_1_qris_web : ''; 
  
  if (isset($_POST['ubah_judul_deskripsi_kata_kunci'])) {
    $judul_web = $_POST['judul_web'];
    $deskripsi_web = $_POST['deskripsi_web'];
    $kata_kunci_web = $_POST['kata_kunci_web'];
    $perbarui_1 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$judul_web' WHERE nama_pengaturan = 'judul_web'");
    if ($perbarui_1) {
      $perbarui_2 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$deskripsi_web' WHERE nama_pengaturan = 'deskripsi_web'");
      if ($perbarui_2) {
        $perbarui_3 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$kata_kunci_web' WHERE nama_pengaturan = 'kata_kunci_web'");
        if ($perbarui_3) {
          echo '
            <script>
              alert("Berhasil ubah data.");
              window.location.replace("'.$alamat_admin.'pengaturan");
            </script>
          ';
        } else {
          echo "Proses Gagal<br>Error : ".$perbarui_3."<br>".mysqli_error($koneksi);
        }
      } else {
        echo "Proses Gagal<br>Error : ".$perbarui_2."<br>".mysqli_error($koneksi);
      }
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui_1."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_warna_tema'])) {
    $bg_1_web = $_POST['bg_1_web'];
    $bg_2_web = $_POST['bg_2_web'];
    $bg_3_web = $_POST['bg_3_web'];
    $perbarui_1 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$bg_1_web' WHERE nama_pengaturan = 'bg_1_web'");
    if ($perbarui_1) {
      $perbarui_2 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$bg_2_web' WHERE nama_pengaturan = 'bg_2_web'");
      if ($perbarui_2) {
        $perbarui_3 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$bg_3_web' WHERE nama_pengaturan = 'bg_3_web'");
        if ($perbarui_3) {
          echo '
            <script>
              alert("Berhasil ubah data.");
              window.location.replace("'.$alamat_admin.'pengaturan");
            </script>
          ';
        } else {
          echo "Proses Gagal<br>Error : ".$perbarui_3."<br>".mysqli_error($koneksi);
        }
      } else {
        echo "Proses Gagal<br>Error : ".$perbarui_2."<br>".mysqli_error($koneksi);
      }
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui_1."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_gradient_footer'])) {
    $warna_atas = $_POST['warna_atas'];
    $warna_tengah = $_POST['warna_tengah'];
    $warna_bawah = $_POST['warna_bawah'];
    $perbarui = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$warna_atas', isi_2_pengaturan = '$warna_tengah', isi_3_pengaturan = '$warna_bawah' WHERE nama_pengaturan = 'bg_gradient_1_web'");
    if ($perbarui) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_gradient_menu'])) {
    $warna_atas = $_POST['warna_atas'];
    $warna_tengah = $_POST['warna_tengah'];
    $warna_bawah = $_POST['warna_bawah'];
    $perbarui = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$warna_atas', isi_2_pengaturan = '$warna_tengah', isi_3_pengaturan = '$warna_bawah' WHERE nama_pengaturan = 'bg_gradient_2_web'");
    if ($perbarui) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_gradient_jackpot'])) {
    $warna_atas = $_POST['warna_atas'];
    $warna_tengah = $_POST['warna_tengah'];
    $warna_bawah = $_POST['warna_bawah'];
    $perbarui = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$warna_atas', isi_2_pengaturan = '$warna_tengah', isi_3_pengaturan = '$warna_bawah' WHERE nama_pengaturan = 'bg_gradient_3_web'");
    if ($perbarui) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_gradient_tombol_masuk'])) {
    $warna_atas = $_POST['warna_atas'];
    $warna_tengah = $_POST['warna_tengah'];
    $warna_bawah = $_POST['warna_bawah'];
    $perbarui = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$warna_atas', isi_2_pengaturan = '$warna_tengah', isi_3_pengaturan = '$warna_bawah' WHERE nama_pengaturan = 'bg_gradient_4_web'");
    if ($perbarui) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_gradient_tombol_daftar'])) {
    $warna_atas = $_POST['warna_atas'];
    $warna_tengah = $_POST['warna_tengah'];
    $warna_bawah = $_POST['warna_bawah'];
    $perbarui = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$warna_atas', isi_2_pengaturan = '$warna_tengah', isi_3_pengaturan = '$warna_bawah' WHERE nama_pengaturan = 'bg_gradient_5_web'");
    if ($perbarui) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui."<br>".mysqli_error($koneksi);
    }
  } 

  else if (isset($_POST['ubah_logo'])) {
    $random = rand(1000000000, 9999999999);
    $tmp_file = $_FILES['logo_web']['tmp_name'];
    $nama_file = $_FILES['logo_web']['name'];
    
    $file_input = $isi_1_logo_web;
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
        $lokasi_simpan = $BASE_UPLOAD_DIR . $DS . $file_input;
        
        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
          $file_uploaded = true;
          if (!empty($isi_1_logo_web) && $isi_1_logo_web != $file_input && file_exists($BASE_UPLOAD_DIR . $DS . $isi_1_logo_web)) {
              @unlink($BASE_UPLOAD_DIR . $DS . $isi_1_logo_web);
          }
        } else {
          $error_message = "Gagal upload gambar, usahakan nama file gambar pendek, atau cek izin direktori!";
        }
      }
    } else {
        $error_message = "Pilih gambar baru terlebih dahulu!";
    }

    if ($error_message) {
      echo '
        <script>
          alert("' . $error_message . '");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
      exit();
    }
    
    if ($file_uploaded) {
        $query = "UPDATE pengaturan SET isi_1_pengaturan = ? WHERE nama_pengaturan = 'logo_web'";
        $stmt = mysqli_prepare($koneksi, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $file_input);
            $perbarui = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($perbarui) {
                echo '
                  <script>
                    alert("Berhasil ubah data.");
                    window.location.replace("'.$alamat_admin.'pengaturan");
                  </script>
                ';
            } else {
                @unlink($BASE_UPLOAD_DIR . $DS . $file_input);
                echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
            }
        } else {
            echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
        }
    }
    exit();
  } 

  else if (isset($_POST['ubah_favicon'])) {
    $random = rand(1000000000, 9999999999);
    $tmp_file = $_FILES['favicon_web']['tmp_name'];
    $nama_file = $_FILES['favicon_web']['name'];
    
    $file_input = $isi_1_favicon_web;
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
        $lokasi_simpan = $BASE_UPLOAD_DIR . $DS . $file_input;
        
        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
          $file_uploaded = true;
          if (!empty($isi_1_favicon_web) && $isi_1_favicon_web != $file_input && file_exists($BASE_UPLOAD_DIR . $DS . $isi_1_favicon_web)) {
              @unlink($BASE_UPLOAD_DIR . $DS . $isi_1_favicon_web);
          }
        } else {
          $error_message = "Gagal upload gambar, usahakan nama file gambar pendek, atau cek izin direktori!";
        }
      }
    } else {
        $error_message = "Pilih gambar baru terlebih dahulu!";
    }

    if ($error_message) {
      echo '
        <script>
          alert("' . $error_message . '");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
      exit();
    }
    
    if ($file_uploaded) {
        $query = "UPDATE pengaturan SET isi_1_pengaturan = ? WHERE nama_pengaturan = 'favicon_web'";
        $stmt = mysqli_prepare($koneksi, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $file_input);
            $perbarui = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($perbarui) {
                echo '
                  <script>
                    alert("Berhasil ubah data.");
                    window.location.replace("'.$alamat_admin.'pengaturan");
                  </script>
                ';
            } else {
                @unlink($BASE_UPLOAD_DIR . $DS . $file_input);
                echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
            }
        } else {
            echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
        }
    }
    exit();
  } 
  
  else if (isset($_POST['ubah_sosial_media'])) {
    $link_apk_web = $_POST['link_apk_web'];
    $facebook_web = $_POST['facebook_web'];
    $telegram_web = $_POST['telegram_web'];
    $telegram_web_2 = $_POST['telegram_web_2'];
    $perbarui_1 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$link_apk_web' WHERE nama_pengaturan = 'link_apk_web'");
    if ($perbarui_1) {
      $perbarui_2 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$facebook_web' WHERE nama_pengaturan = 'facebook_web'");
      if ($perbarui_2) {
        $perbarui_3 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$telegram_web', isi_2_pengaturan = '$telegram_web_2' WHERE nama_pengaturan = 'telegram_web'");
        if ($perbarui_3) {
          echo '
            <script>
              alert("Berhasil ubah data.");
              window.location.replace("'.$alamat_admin.'pengaturan");
            </script>
          ';
        } else {
          echo "Proses Gagal<br>Error : ".$perbarui_3."<br>".mysqli_error($koneksi);
        }
      } else {
        echo "Proses Gagal<br>Error : ".$perbarui_2."<br>".mysqli_error($koneksi);
      }
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui_1."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_whatsapp_livechat'])) {
    $whatsapp_web = mysqli_real_escape_string($koneksi, $_POST['whatsapp_web']);
    $link_livechat_web = mysqli_real_escape_string($koneksi, $_POST['link_livechat_web']);
    $script_livechat_web = mysqli_real_escape_string($koneksi, $_POST['script_livechat_web']);
    $perbarui_1 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$whatsapp_web' WHERE nama_pengaturan = 'whatsapp_web'");
    if ($perbarui_1) {
      $perbarui_2 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$link_livechat_web' WHERE nama_pengaturan = 'link_livechat_web'");
      if ($perbarui_2) {
        $perbarui_3 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$script_livechat_web' WHERE nama_pengaturan = 'script_livechat_web'");
        if ($perbarui_3) {
          echo '
            <script>
              alert("Berhasil ubah data.");
              window.location.replace("'.$alamat_admin.'pengaturan");
            </script>
          ';
        }
      } else {
        echo "Proses Gagal<br>Error : ".$perbarui_2."<br>".mysqli_error($koneksi);
      }
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui_1."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_teks_berjalan'])) {
    $teks_berjalan_web = $_POST['teks_berjalan_web'];
    $teks_berjalan_web_2 = $_POST['teks_berjalan_web_2'];
    $teks_berjalan_web_3 = $_POST['teks_berjalan_web_3'];
    $perbarui_1 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$teks_berjalan_web', isi_2_pengaturan = '$teks_berjalan_web_2', isi_3_pengaturan = '$teks_berjalan_web_3' WHERE nama_pengaturan = 'teks_berjalan_web'");
    if ($perbarui_1) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui_1."<br>".mysqli_error($koneksi);
    }
  } 

  else if (isset($_POST['ubah_popup_pengumuman'])) {
    $popup_pengumuman_web_2 = mysqli_real_escape_string($koneksi, $_POST['popup_pengumuman_web_2']);
    $random = rand(1000000000, 9999999999);
    $tmp_file = $_FILES['popup_pengumuman_web']['tmp_name'];
    $nama_file = $_FILES['popup_pengumuman_web']['name'];
    
    $file_input = $isi_1_popup_pengumuman_web;
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
        $lokasi_simpan = $BASE_UPLOAD_DIR . $DS . $file_input;
        
        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
          $file_uploaded = true;
          if (!empty($isi_1_popup_pengumuman_web) && $isi_1_popup_pengumuman_web != $file_input && file_exists($BASE_UPLOAD_DIR . $DS . $isi_1_popup_pengumuman_web)) {
              @unlink($BASE_UPLOAD_DIR . $DS . $isi_1_popup_pengumuman_web);
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
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
      exit();
    }
    
    $query = "UPDATE pengaturan SET isi_2_pengaturan = ? " . ($file_uploaded ? ", isi_1_pengaturan = ?" : "") . " WHERE nama_pengaturan = 'popup_pengumuman_web'";
    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
        if ($file_uploaded) {
            mysqli_stmt_bind_param($stmt, "ss", $popup_pengumuman_web_2, $file_input);
        } else {
            mysqli_stmt_bind_param($stmt, "s", $popup_pengumuman_web_2);
        }
        
        $perbarui_1 = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($perbarui_1) {
            echo '
              <script>
                alert("Berhasil ubah data.");
                window.location.replace("'.$alamat_admin.'pengaturan");
              </script>
            ';
        } else {
            if ($file_uploaded) {
                @unlink($BASE_UPLOAD_DIR . $DS . $file_input);
            }
            echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
        }
    } else {
        echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
    }
    exit();
  } 
  
  else if (isset($_POST['ubah_rtp'])) {
    $rtp_web = $_POST['rtp_web'];
    $rtp_web_2 = $_POST['rtp_web_2'];
    $perbarui_1 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$rtp_web', isi_2_pengaturan = '$rtp_web_2' WHERE nama_pengaturan = 'rtp_web'");
    if ($perbarui_1) {
      echo '
        <script>
          alert("Berhasil ubah data.");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui_1."<br>".mysqli_error($koneksi);
    }
  } else if (isset($_POST['ubah_popup_teks'])) {
    $popup_teks_belum_login_web = $_POST['popup_teks_belum_login_web'];
    $popup_teks_tidak_ada_saldo_web = $_POST['popup_teks_tidak_ada_saldo_web'];
    $popup_teks_ada_saldo_web = $_POST['popup_teks_ada_saldo_web'];
    $popup_teks_setelah_deposit_web = $_POST['popup_teks_setelah_deposit_web'];
    $popup_teks_setelah_withdraw_web = $_POST['popup_teks_setelah_withdraw_web'];
    $perbarui_1 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$popup_teks_belum_login_web' WHERE nama_pengaturan = 'popup_teks_belum_login_web'");
    if ($perbarui_1) {
      $perbarui_2 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$popup_teks_tidak_ada_saldo_web' WHERE nama_pengaturan = 'popup_teks_tidak_ada_saldo_web'");
      if ($perbarui_2) {
        $perbarui_3 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$popup_teks_ada_saldo_web' WHERE nama_pengaturan = 'popup_teks_ada_saldo_web'");
        if ($perbarui_3) {
          $perbarui_4 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$popup_teks_setelah_deposit_web' WHERE nama_pengaturan = 'popup_teks_setelah_deposit_web'");
          if ($perbarui_4) {
            $perbarui_5 = mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$popup_teks_setelah_withdraw_web' WHERE nama_pengaturan = 'popup_teks_setelah_withdraw_web'");
            if ($perbarui_5) {
              echo '
                <script>
                  alert("Berhasil ubah data.");
                  window.location.replace("'.$alamat_admin.'pengaturan");
                </script>
              ';
            } else {
              echo "Proses Gagal<br>Error : ".$perbarui_5."<br>".mysqli_error($koneksi);
            }
          } else {
            echo "Proses Gagal<br>Error : ".$perbarui_4."<br>".mysqli_error($koneksi);
          }
        } else {
          echo "Proses Gagal<br>Error : ".$perbarui_3."<br>".mysqli_error($koneksi);
        }
      } else {
        echo "Proses Gagal<br>Error : ".$perbarui_2."<br>".mysqli_error($koneksi);
      }
    } else {
      echo "Proses Gagal<br>Error : ".$perbarui_1."<br>".mysqli_error($koneksi);
    }
  } 

  else if (isset($_POST['ubah_qris'])) {
    $random = rand(1000000000, 9999999999);
    $tmp_file = $_FILES['qris_web']['tmp_name'];
    $nama_file = $_FILES['qris_web']['name'];
    
    $file_input = $isi_1_qris_web;
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
        $lokasi_simpan = $BANK_UPLOAD_DIR . $DS . $file_input;
        
        if (move_uploaded_file($tmp_file, $lokasi_simpan)) {
          $file_uploaded = true;
          if (!empty($isi_1_qris_web) && $isi_1_qris_web != $file_input && file_exists($BANK_UPLOAD_DIR . $DS . $isi_1_qris_web)) {
              @unlink($BANK_UPLOAD_DIR . $DS . $isi_1_qris_web);
          }
        } else {
          $error_message = "Gagal upload gambar, usahakan nama file gambar pendek, atau cek izin direktori!";
        }
      }
    } else {
        $error_message = "Pilih gambar baru terlebih dahulu!";
    }

    if ($error_message) {
      echo '
        <script>
          alert("' . $error_message . '");
          window.location.replace("'.$alamat_admin.'pengaturan");
        </script>
      ';
      exit();
    }
    
    if ($file_uploaded) {
        $query = "UPDATE pengaturan SET isi_1_pengaturan = ? WHERE nama_pengaturan = 'qris_web'";
        $stmt = mysqli_prepare($koneksi, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $file_input);
            $perbarui = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($perbarui) {
                echo '
                  <script>
                    alert("Berhasil ubah data.");
                    window.location.replace("'.$alamat_admin.'pengaturan");
                  </script>
                ';
            } else {
                @unlink($BANK_UPLOAD_DIR . $DS . $file_input);
                echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
            }
        } else {
            echo "Proses Gagal<br>Error : ".mysqli_error($koneksi);
        }
    }
    if (isset($_POST['ubah_seo_redirect'])) {
    $google_verif = mysqli_real_escape_string($koneksi, $_POST['google_verif']);
    $amp_url = mysqli_real_escape_string($koneksi, $_POST['amp_url']);
    $redirect_domain = mysqli_real_escape_string($koneksi, $_POST['redirect_domain']);
    $status_redirect = isset($_POST['status_redirect']) ? 1 : 0;

    mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$google_verif' WHERE nama_pengaturan = 'google_verif'");
    mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$amp_url' WHERE nama_pengaturan = 'amp_url'");
    mysqli_query($koneksi, "UPDATE pengaturan SET isi_1_pengaturan = '$redirect_domain', isi_2_pengaturan = '$status_redirect' WHERE nama_pengaturan = 'redirect_domain'");
    
    echo '<script>alert("Berhasil update SEO."); window.location.replace("'.$alamat_admin.'pengaturan");</script>';
  }
    exit();
  }
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row gy-4 mb-4">
    <div class="col-md-6">
      <div class="fw-bold fs-4 text-center text-md-start">Pengaturan</div>
    </div>
    <div class="col-md-6">
      <div class="text-center text-md-end">
        <span><?php echo ucapan().', '.tanggalIndonesia(date('Y-m-d'), true).', '; ?></span>
        <span id="jam_sekarang">Jam </span>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <h5 class="card-header">Ubah Data Pengaturan</h5>
    <form method="post" class="card-body">
      <h6> Judul - Deskripsi - Kata Kunci</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="judul_web" class="form-control" value="<?php echo $isi_1_judul_web; ?>" required>
            <label>Judul</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="deskripsi_web" class="form-control" value="<?php echo $isi_1_deskripsi_web; ?>" required>
            <label>Deskripsi</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="kata_kunci_web" class="form-control" value="<?php echo $isi_1_kata_kunci_web; ?>" required>
            <label>Kata Kunci</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_judul_deskripsi_kata_kunci" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
    <form method="post" class="card-body">
      <hr class="my-4 mx-n4">
      <h6> Warna Tema</h6>
        <div class="col-12">
          <div class="mb-3">
          <div class="form-floating form-floating-outline mb-4">
            <input type="color" id="bg_1_web" name="bg_1_web" class="form-control" value="<?php echo $isi_1_bg_1_web; ?>">
            <input type="hidden" id="bg_1_web_hsl" name="bg_1_web_hsl">
            <label for="bg_1_web">Background 1</label>
          </div>
        </div>
       <div class="pt-4 text-end">
        <button type="submit" name="ubah_warna_tema" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>

<script>
function rgbToHsl(r, g, b) {
    r /= 255;
    g /= 255;
    b /= 255;
    let max = Math.max(r, g, b);
    let min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;

    if (max === min) {
        h = s = 0;
    } else {
        let d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r:
                h = (g - b) / d + (g < b ? 6 : 0);
                break;
            case g:
                h = (b - r) / d + 2;
                break;
            case b:
                h = (r - g) / d + 4;
                break;
        }
        h /= 6;
    }

    return `hsl(${Math.round(h * 360)}, ${Math.round(s * 100)}%, ${Math.round(l * 100)}%)`;
}

function updateHslValues() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        const hex = input.value;
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        const hsl = rgbToHsl(r, g, b);
        document.getElementById(`${input.id}_hsl`).value = hsl;
    });
}

document.querySelectorAll('input[type="color"]').forEach(input => {
    input.addEventListener('input', updateHslValues);
});

updateHslValues();
</script>

    <form method="post" enctype="multipart/form-data" class="card-body">
      <hr class="my-4 mx-n4">
      <h6> Logo</h6>
      <div class="row g-3">
        <div class="col-12">
          <div class="mb-3">
            <div class="bg-secondary rounded text-center p-3 mb-3">
              <img src="<?php echo '../assets/img/'.$isi_1_logo_web; ?>" alt="<?php echo $jenis_promosi; ?>" class="img-fluid">
            </div>
            <input type="file" name="logo_web" class="form-control" id="formFile">
            <div class="form-text">
              Format gambar harus PNG, JPG, JPEG, GIF, atau SVG.
            </div>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_logo" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
    <form method="post" enctype="multipart/form-data" class="card-body">
      <hr class="my-4 mx-n4">
      <h6> Favicon</h6>
      <div class="row g-3">
        <div class="col-12">
          <div class="mb-3">
            <div class="bg-secondary rounded text-center p-3 mb-3">
              <img src="<?php echo '../assets/img/'.$isi_1_favicon_web; ?>" alt="<?php echo $jenis_promosi; ?>" class="img-fluid">
            </div>
            <input type="file" name="favicon_web" class="form-control" id="formFile">
            <div class="form-text">
              Format gambar harus PNG, JPG, JPEG, GIF, atau SVG.
            </div>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_favicon" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
   <form method="post" class="card-body">
      <h6> WhatsApp - LiveChat</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="whatsapp_web" class="form-control" value="<?php echo $isi_1_whatsapp_web; ?>" required>
            <label>WhatsApp</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="link_livechat_web" class="form-control" value="<?php echo $isi_1_link_livechat_web; ?>" required>
            <label>Link LiveChat</label>
          </div>
        </div>
        <div class="col-12">
          <div class="form-floating form-floating-outline mb-4">
            <textarea name="script_livechat_web" class="form-control h-px-100"><?php echo $isi_1_script_livechat_web; ?></textarea>
            <label for="exampleFormControlTextarea1">Script LiveChat</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_whatsapp_livechat" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
    <form method="post" class="card-body">
      <h6> Teks Berjalan</h6>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="teks_berjalan_web" class="form-control" value="<?php echo $isi_1_teks_berjalan_web; ?>" required>
            <label>Teks 1</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="teks_berjalan_web_2" class="form-control" value="<?php echo $isi_2_teks_berjalan_web; ?>" required>
            <label>Teks 2</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="teks_berjalan_web_3" class="form-control" value="<?php echo $isi_3_teks_berjalan_web; ?>" required>
            <label>Teks 3</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_teks_berjalan" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
    <form method="post" enctype="multipart/form-data" class="card-body">
      <hr class="my-4 mx-n4">
      <h6> Popup Pengumuman</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="mb-3">
            <div class="bg-secondary rounded text-center p-3 mb-3">
              <img src="<?php echo '../assets/img/'.$isi_1_popup_pengumuman_web; ?>" alt="<?php echo $jenis_promosi; ?>" class="img-fluid">
            </div>
            <input type="file" name="popup_pengumuman_web" class="form-control" id="formFile">
            <div class="form-text">
              Format gambar harus PNG, JPG, JPEG, GIF, atau SVG.
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="popup_pengumuman_web_2" class="form-control" value="<?php echo $isi_2_popup_pengumuman_web; ?>" required>
            <label>Teks Popup Pengumuman</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_popup_pengumuman" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
    <form method="post" class="card-body">
      <h6> RTP</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="rtp_web" class="form-control" value="<?php echo $isi_1_rtp_web; ?>" required>
            <label>Angka Awal (Minimal : 0)</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="rtp_web_2" class="form-control" value="<?php echo $isi_2_rtp_web; ?>" required>
            <label>Angka Akhir (Maksimal : 100)</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_rtp" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
    <form method="post" class="card-body">
      <h6> Popup Teks</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="popup_teks_belum_login_web" class="form-control" value="<?php echo $isi_1_popup_teks_belum_login_web; ?>" required>
            <label>Popup Teks Belum Login</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="popup_teks_tidak_ada_saldo_web" class="form-control" value="<?php echo $isi_1_popup_teks_tidak_ada_saldo_web; ?>" required>
            <label>Popup Teks Tidak Ada Saldo [ GAME ON/OFF]</label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="popup_teks_ada_saldo_web" class="form-control" value="<?php echo $isi_1_popup_teks_ada_saldo_web; ?>" required>
            <label>Popup Ada Saldo [ GAME LOCK ]</label>
          </div>
        </div>
       <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <input type="text" name="popup_teks_setelah_withdraw_web" class="form-control" value="<?php echo $isi_1_popup_teks_setelah_withdraw_web; ?>" required>
            <label>Popup Teks Error [ GAME LOCK ]</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_popup_teks" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span>
          Simpan
        </button>
      </div>
    </form>
    <div class="card mb-4">
    <h5 class="card-header">Pengaturan SEO & Redirect</h5>
    <form method="post" class="card-body">
      <div class="row g-3">
        <div class="col-12">
          <div class="form-floating form-floating-outline">
            <textarea name="google_verif" class="form-control"><?php echo $isi_1_google_verif; ?></textarea>
            <label>Google Verification Meta Tag</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="amp_url" class="form-control" value="<?php echo $isi_1_amp_url; ?>">
            <label>AMP URL</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-floating form-floating-outline">
            <input type="text" name="redirect_domain" class="form-control" value="<?php echo $isi_1_redirect_domain; ?>">
            <label>Target Redirect Domain</label>
          </div>
        </div>
        <div class="col-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="status_redirect" id="status_redirect" <?php echo ($isi_2_redirect_domain == 1) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="status_redirect">Aktifkan Redirect (301 Permanent)</label>
          </div>
        </div>
      </div>
      <div class="pt-4 text-end">
        <button type="submit" name="ubah_seo_redirect" class="btn btn-primary waves-effect waves-light">
          <span class="tf-icons mdi mdi-content-save me-1"></span> Simpan SEO
        </button>
      </div>
    </form>
  </div>
</div>
