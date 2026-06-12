<?php
  // Aktifkan pelaporan kesalahan PHP untuk debugging
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }

  require_once '../koneksi.php';

  if (!isset($alamat_admin)) {
      $current_dir_url_path = dirname($_SERVER['SCRIPT_NAME']);
      $alamat_admin = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . $current_dir_url_path . '/';
  }

  // Panggil class API
  if(file_exists('../classes/class.exa.php')){
      require_once '../classes/class.exa.php';
      $gameXaAPI = new GameXaAPI(); 
  } else {
      die("<div style='color:red; padding:20px;'><b>FATAL ERROR:</b> File class.exa.php tidak ditemukan di folder classes!</div>");
  }

  if (!isset($_SESSION['kode_admin'])) {
    echo '<script>alert("Terjadi kesalahan, harap masuk kembali!"); window.location.replace("'.$alamat_admin.'keluar.php");</script>';
    exit();
  }

  $database_message = ''; 
  
  // --- Logika Pemanggilan API dan Update Database ---
  if (isset($_POST['update_providers_and_db'])) {
      try {
          $response = $gameXaAPI->getGameProviders();

          if ($response['success'] && isset($response['data']['providers'])) {
              $inserted_count = 0; 
              $updated_count = 0; 
              $error_db_count = 0;

              // SESUAIKAN DENGAN TABEL tb_provider
              $stmt = $koneksi->prepare("INSERT INTO tb_provider (providerid, providername, slug, type, status, providerimage, jenis, providerapi) VALUES (?, ?, ?, ?, ?, ?, ?, 'GxG') ON DUPLICATE KEY UPDATE providername = VALUES(providername), slug = VALUES(slug), type = VALUES(type), status = VALUES(status), providerimage = VALUES(providerimage), jenis = VALUES(jenis)");

              if ($stmt === false) {
                  $database_message = '<div class="alert alert-danger"><strong>Kesalahan SQL:</strong> ' . htmlspecialchars($koneksi->error) . '</div>';
              } else {
                  foreach ($response['data']['providers'] as $api_provider) {
                      $providerid = $api_provider['provider_code'] ?? '';
                      $providername = $api_provider['provider_name'] ?? 'Unknown';
                      $slug = strtolower($providerid); // Bikin slug dari ID
                      $api_image = $api_provider['logo_url'] ?? '';
                      
                      // Status mapping (API 'active' = DB 1)
                      $api_status = $api_provider['status'] ?? 'inactive';
                      $status_int = ($api_status === 'active') ? 1 : 0;

                      // Tangkap tipe mentah dari API
                      $raw_type = strtolower($api_provider['type'] ?? $api_provider['category'] ?? $api_provider['provider_type'] ?? 'slot');
                      
                      // Mapping Jenis (angka) dan Type (teks) sesuai format tb_provider
                      $jenis_int = 1; // Default Slot
                      $type_teks = 'SL';
                      
                      if (strpos($raw_type, 'sport') !== false) {
                          $jenis_int = 2; $type_teks = 'sports';
                      } elseif (strpos($raw_type, 'casino') !== false) {
                          $jenis_int = 3; $type_teks = 'casino';
                      } elseif (strpos($raw_type, 'fish') !== false || strpos($raw_type, 'arcade') !== false) {
                          $jenis_int = 4; $type_teks = 'arcade';
                      } elseif (strpos($raw_type, 'lottery') !== false || strpos($raw_type, 'togel') !== false) {
                          $jenis_int = 6; $type_teks = 'Lottery';
                      } elseif (strpos($raw_type, 'e-game') !== false) {
                          $jenis_int = 5; $type_teks = 'egames';
                      }

                      if (!empty($providerid)) {
                          // bind_param: s (string), i (integer)
                          // "ssssisi" = string, string, string, string, integer, string, integer
                          $stmt->bind_param("ssssisi", $providerid, $providername, $slug, $type_teks, $status_int, $api_image, $jenis_int);
                          
                          if ($stmt->execute()) {
                              if ($stmt->affected_rows === 1) {
                                  $inserted_count++; 
                              } elseif ($stmt->affected_rows === 2) {
                                  $updated_count++; 
                              }
                          } else {
                              $error_db_count++;
                          }
                      }
                  }
                  $stmt->close();
                  $database_message = '<div class="alert alert-success alert-dismissible fade show" style="background-color: #28a745; color: white; padding: 15px; border-radius: 5px;"><strong>Proses Selesai!</strong><br>Data Baru: <strong>'.$inserted_count.'</strong><br>Data Diperbarui: <strong>'.$updated_count.'</strong><br>Gagal: <strong>'.$error_db_count.'</strong></div>';
              }
          } else {
              $database_message = '<div class="alert alert-danger" style="background-color: #dc3545; color: white; padding: 15px;"><strong>API Error:</strong> Gagal mengambil daftar provider.</div>';
          }
      } catch (Throwable $e) { 
          $database_message = '<div class="alert alert-danger" style="background-color: #dc3545; color: white; padding: 15px;"><strong>Sistem Terhenti:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
      }
  }

  // --- Ambil Data untuk Tampilan Tabel ---
  $all_db_providers = [];
  $all_db_providers_error = '';
  $game_summary_data = [];

  try {
      // Tarik dari tb_provider
      $query_all_providers = $koneksi->query("SELECT cuid, providerid, providername, type, status, providerimage FROM tb_provider ORDER BY providername ASC");
      if ($query_all_providers) {
          while ($row = $query_all_providers->fetch_assoc()) {
              $all_db_providers[] = $row;
          }
          $query_all_providers->free();
      }

      // Ringkasan Provider
      $query_summary = $koneksi->query("SELECT type, COUNT(*) AS total_providers FROM tb_provider GROUP BY type ORDER BY type");
      if ($query_summary) {
          while ($row = $query_summary->fetch_assoc()) {
              $game_summary_data[] = $row;
          }
          $query_summary->free();
      }

  } catch (Throwable $e) {
      $all_db_providers_error = "Gagal memuat data dari database: " . htmlspecialchars($e->getMessage());
  }
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4" style="color: white;"><span class="text-muted fw-light">Menu Utama /</span> Daftar Provider GameXa</h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4" style="background-color: #2b2c40; color: #cbcbd6;">
        <div class="card-body" style="padding: 20px;">
          <p class="text-white">Klik tombol di bawah untuk menyinkronkan daftar provider game dari API GameXa ke database Anda secara otomatis.</p>

          <form method="POST" action="">
              <button type="submit" name="update_providers_and_db" class="btn text-white fw-bold mb-3" style="background-color: #696cff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                  Sinkronisasi API ke Database
              </button>
          </form>

          <?php echo $database_message; ?>

          <hr style="border-color: #444; margin: 30px 0;" />

          <h5 class="text-white fw-bold">Ringkasan Tipe Provider Database</h5>
          <?php if (!empty($game_summary_data)): ?>
            <div class="table-responsive text-nowrap mb-4">
              <table class="table mb-0" style="width: 100%; text-align: left; color: white;">
                <thead>
                  <tr style="border-bottom: 1px solid #444;">
                    <th style="padding: 10px;">TIPE GAME</th>
                    <th style="padding: 10px;">JUMLAH PROVIDER</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($game_summary_data as $summary_row): ?>
                    <tr style="border-bottom: 1px solid #3c3d56;">
                      <td style="padding: 10px;"><strong><?php echo strtoupper(htmlspecialchars($summary_row['type'])); ?></strong></td>
                      <td style="padding: 10px;"><?php echo htmlspecialchars($summary_row['total_providers']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">Belum ada data ringkasan.</p>
          <?php endif; ?>

          <hr style="border-color: #444; margin: 30px 0;" />

          <h5 class="text-white fw-bold">Daftar Semua Provider dari Database:</h5>
          <?php if (!empty($all_db_providers_error)): ?>
            <div class="alert alert-danger mt-3" style="background-color: #dc3545; color: white; padding: 15px;"><strong>Error:</strong> <?php echo htmlspecialchars($all_db_providers_error); ?></div>
          <?php elseif (!empty($all_db_providers)): ?>
            <div class="table-responsive text-nowrap">
              <table class="table mb-0" style="width: 100%; text-align: left; color: white; border-collapse: collapse;">
                <thead>
                  <tr style="border-bottom: 2px solid #444;">
                    <th style="padding: 10px;">KODE</th>
                    <th style="padding: 10px;">NAMA PROVIDER</th>
                    <th style="padding: 10px;">TIPE</th>
                    <th style="padding: 10px;">STATUS</th>
                    <th style="padding: 10px;">LOGO</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($all_db_providers as $provider): ?>
                    <tr style="border-bottom: 1px solid #3c3d56;">
                      <td style="padding: 10px;"><strong><?php echo htmlspecialchars($provider['providerid']); ?></strong></td>
                      <td style="padding: 10px;"><?php echo htmlspecialchars($provider['providername']); ?></td>
                      <td style="padding: 10px;"><?php echo strtoupper(htmlspecialchars($provider['type'])); ?></td>
                      <td style="padding: 10px;">
                        <?php if ($provider['status'] == 1): ?>
                            <span style="background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">ACTIVE</span>
                        <?php else: ?>
                            <span style="background-color: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">INACTIVE</span>
                        <?php endif; ?>
                      </td>
                      <td style="padding: 10px;">
                        <?php if (!empty($provider['providerimage'])): ?>
                          <img src="<?php echo htmlspecialchars($provider['providerimage']); ?>" alt="Logo" style="max-width: 60px; height: auto; border-radius: 4px;">
                        <?php else: ?>
                          <span style="color: #888; font-size: 12px;">Tidak Ada</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">Tidak ada provider di database. Klik tombol Sinkronisasi di atas.</p>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  </div>
</div>
