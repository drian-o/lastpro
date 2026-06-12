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

              // Query disesuaikan untuk memasukkan kolom total_games
              $stmt = $koneksi->prepare("INSERT INTO game_providers (provider_code, provider_name, status, logo_url, description, total_games, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE provider_name = VALUES(provider_name), status = VALUES(status), logo_url = VALUES(logo_url), description = VALUES(description), total_games = VALUES(total_games), updated_at = NOW()");

              if ($stmt === false) {
                  $database_message = '<div class="alert alert-danger"><strong>Kesalahan SQL:</strong> ' . htmlspecialchars($koneksi->error) . '</div>';
              } else {
                  foreach ($response['data']['providers'] as $api_provider) {
                      $code = $api_provider['provider_code'] ?? '';
                      $name = $api_provider['provider_name'] ?? 'Unknown Provider';
                      $status = $api_provider['status'] ?? 'inactive';
                      $image_url = $api_provider['logo_url'] ?? null;
                      
                      // Coba tangkap Tipe Game dari berbagai kemungkinan nama key API
                      $type = $api_provider['type'] ?? $api_provider['category'] ?? $api_provider['provider_type'] ?? 'slot';
                      
                      // Coba tangkap Jumlah Game dari berbagai kemungkinan nama key API
                      $game_count = (int)($api_provider['game_count'] ?? $api_provider['total_games'] ?? $api_provider['games'] ?? 0);

                      if (!empty($code)) {
                          // bind_param pakai "sssssi" karena game_count adalah integer (i)
                          $stmt->bind_param("sssssi", $code, $name, $status, $image_url, $type, $game_count);
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
                  $database_message = '<div class="alert alert-success alert-dismissible fade show"><strong>Proses Selesai!</strong><br>Data Baru: <strong>'.$inserted_count.'</strong><br>Data Diperbarui: <strong>'.$updated_count.'</strong><br>Gagal: <strong>'.$error_db_count.'</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
              }
          } else {
              $pesan_error = $response['message'] ?? 'Tidak ada data di API.';
              $database_message = '<div class="alert alert-danger alert-dismissible fade show"><strong>API Error:</strong> Gagal mengambil daftar provider dari API GameXa. ('.$pesan_error.')<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
          }
      } catch (Throwable $e) { 
          $database_message = '<div class="alert alert-danger alert-dismissible fade show"><strong>Sistem Terhenti:</strong> ' . htmlspecialchars($e->getMessage()) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
      }
  }

  // --- Ambil Data untuk Tampilan Tabel ---
  $all_db_providers = [];
  $all_db_providers_error = '';
  $game_summary_data = [];
  $total_semua_game = 0; // Buat ngitung grand total 6656

  try {
      // Ambil daftar provider beserta jumlah gamenya
      $query_all_providers = $koneksi->query("SELECT id, provider_code, provider_name, description AS provider_type, status AS provider_status, logo_url AS provider_image, total_games FROM game_providers ORDER BY provider_name ASC");
      if ($query_all_providers) {
          while ($row = $query_all_providers->fetch_assoc()) {
              $all_db_providers[] = $row;
              $total_semua_game += (int)$row['total_games']; // Hitung total ke keseluruhan
          }
          $query_all_providers->free();
      }

      // Ambil ringkasan (Menghitung jumlah provider DAN total game per tipe)
      $query_summary = $koneksi->query("SELECT description AS game_type, COUNT(*) AS total_providers, SUM(total_games) AS total_games_count FROM game_providers GROUP BY description ORDER BY description");
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
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Menu Utama /</span> Daftar Provider GameXa</h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4" style="background-color: #2b2c40; color: #cbcbd6;">
        <div class="card-body">
          <p class="text-white">Klik tombol di bawah untuk menyinkronkan daftar provider game dari API GameXa ke database Anda secara otomatis.</p>

          <form method="POST" action="">
              <button type="submit" name="update_providers_and_db" class="btn text-white fw-bold mb-3" style="background-color: #696cff;">
                  <i class="bx bx-sync me-1"></i> Sinkronisasi API ke Database
              </button>
          </form>

          <?php echo $database_message; ?>

          <hr class="border-secondary my-4" />

          <!-- BAGIAN RINGKASAN DATA (Dipindah ke atas biar gampang dibaca) -->
          <h5 class="text-white fw-bold d-flex justify-content-between align-items-center">
            Ringkasan Database
            <span class="badge bg-primary fs-6">Grand Total Game: <?php echo $total_semua_game; ?></span>
          </h5>
          <?php if (!empty($game_summary_data)): ?>
            <div class="table-responsive text-nowrap mb-4" style="max-width: 600px;">
              <table class="table mb-0">
                <thead>
                  <tr style="border-bottom: 1px solid #444;">
                    <th style="color: #a3a4cc;">TIPE GAME</th>
                    <th style="color: #a3a4cc;" class="text-center">JUMLAH PROVIDER</th>
                    <th style="color: #a3a4cc;" class="text-center">TOTAL GAME</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <?php foreach ($game_summary_data as $summary_row): ?>
                    <tr style="border-bottom: 1px solid #3c3d56;">
                      <td><strong class="text-white"><?php echo strtoupper(htmlspecialchars($summary_row['game_type'])); ?></strong></td>
                      <td class="text-center text-white"><?php echo htmlspecialchars($summary_row['total_providers']); ?></td>
                      <td class="text-center text-success fw-bold"><?php echo htmlspecialchars($summary_row['total_games_count'] ?? 0); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">Belum ada data ringkasan.</p>
          <?php endif; ?>

          <hr class="border-secondary my-4" />

          <!-- TABEL DAFTAR PROVIDER -->
          <h5 class="text-white fw-bold">Daftar Semua Provider dari Database:</h5>
          <?php if (!empty($all_db_providers_error)): ?>
            <div class="alert alert-danger mt-3" role="alert"><strong>Error:</strong> <?php echo htmlspecialchars($all_db_providers_error); ?></div>
          <?php elseif (!empty($all_db_providers)): ?>
            <div class="table-responsive text-nowrap">
              <table class="table mb-0">
                <thead>
                  <tr style="border-bottom: 1px solid #444;">
                    <th style="color: #a3a4cc;">KODE</th>
                    <th style="color: #a3a4cc;">NAMA PROVIDER</th>
                    <th style="color: #a3a4cc;">TIPE</th>
                    <th style="color: #a3a4cc;" class="text-center">JUMLAH GAME</th>
                    <th style="color: #a3a4cc;">STATUS</th>
                    <th style="color: #a3a4cc;" class="text-center">LOGO</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <?php foreach ($all_db_providers as $provider): ?>
                    <tr style="border-bottom: 1px solid #3c3d56;">
                      <td><strong class="text-white"><?php echo htmlspecialchars($provider['provider_code']); ?></strong></td>
                      <td class="text-white"><?php echo htmlspecialchars($provider['provider_name']); ?></td>
                      <td class="text-muted"><?php echo strtoupper(htmlspecialchars($provider['provider_type'])); ?></td>
                      <td class="text-center text-info fw-bold"><?php echo htmlspecialchars($provider['total_games'] ?? 0); ?></td>
                      <td>
                        <?php
                          $status_text = $provider['provider_status'] ?? 'unknown';
                          $status_color = ($status_text === 'active') ? 'bg-label-success' : 'bg-label-danger';
                        ?>
                        <span class="badge <?php echo $status_color; ?> fw-bold"><?php echo strtoupper(htmlspecialchars($status_text)); ?></span>
                      </td>
                      <td class="text-center">
                        <?php if (!empty($provider['provider_image'])): ?>
                          <img src="<?php echo htmlspecialchars($provider['provider_image']); ?>" alt="Logo" style="max-width: 60px; height: auto; border-radius: 4px;">
                        <?php else: ?>
                          <span class="text-muted"><small>Tidak Ada</small></span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">Tidak ada provider di database. Klik tombol Sinkronisasi di atas untuk mengisinya.</p>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  </div>
</div>
