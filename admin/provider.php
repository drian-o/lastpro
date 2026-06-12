<?php
  // Aktifkan pelaporan kesalahan PHP untuk debugging
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }

  require_once '../koneksi.php';

  // PASTIKAN FILE INI BENAR-BENAR ADA DI SERVER! JIKA TIDAK, AKAN HTTP 500
  if(file_exists('../classes/class.exa.php')){
      require_once '../classes/class.exa.php';
      $gameXaAPI = new GameXaAPI(); 
  } else {
      die("<b>FATAL ERROR:</b> File class.exa.php tidak ditemukan di folder classes!");
  }

  if (!isset($_SESSION['kode_admin'])) {
    echo '<script>alert("Terjadi kesalahan, harap masuk kembali!"); window.location.replace("'.$alamat_admin.'keluar.php");</script>';
    exit();
  }

  $database_message = ''; 
  $gamelist_entries_for_insert = []; 
  $game_summary_error = ''; 

  if (isset($koneksi) && $koneksi instanceof mysqli) {
      // Ambil DISTINCT provider_code dari srg_gamelist
      $query_gamelist_data = "SELECT DISTINCT provider_code, game_type FROM srg_gamelist ORDER BY provider_code, game_type";
      $result_gamelist_data = $koneksi->query($query_gamelist_data);
      if ($result_gamelist_data) {
          while ($row = $result_gamelist_data->fetch_assoc()) {
              $gamelist_entries_for_insert[] = [
                  'provider_code' => $row['provider_code'],
                  'provider_type' => $row['game_type'] 
              ];
          }
          $result_gamelist_data->free();
      }

      // Ambil data ringkasan
      $game_summary_data = []; 
      $query_summary_for_display = "SELECT game_type, provider_code, COUNT(*) AS total_games FROM srg_gamelist GROUP BY game_type, provider_code ORDER BY game_type, provider_code";
      $result_summary_for_display = $koneksi->query($query_summary_for_display);
      if ($result_summary_for_display) {
          while ($row = $result_summary_for_display->fetch_assoc()) {
              $game_summary_data[] = $row;
          }
          $result_summary_for_display->free();
      }
  }

  // --- Logika Pemanggilan API dan Update Database ---
  if (isset($_POST['update_providers_and_db'])) {
      try {
          $response = $gameXaAPI->getGameProviders();
          $api_providers_map = []; 

          if ($response['success'] && isset($response['data']['providers'])) {
              foreach ($response['data']['providers'] as $api_provider) {
                  if (isset($api_provider['provider_code'])) {
                      $api_providers_map[$api_provider['provider_code']] = $api_provider;
                  }
              }
          } else {
              $database_message = '<div class="alert alert-danger">Gagal mengambil daftar provider dari API GameXa.</div>';
          }

          if (isset($koneksi) && $koneksi instanceof mysqli && empty($database_message)) {
              $inserted_count = 0; $updated_count = 0; $error_db_count = 0;

              // DISESUAIKAN DENGAN NAMA TABEL DI PHPMyAdmin KAMU (game_providers)
              $stmt = $koneksi->prepare("INSERT INTO game_providers (provider_code, provider_name, status, logo_url, description, updated_at) VALUES (?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE provider_name = VALUES(provider_name), status = VALUES(status), logo_url = VALUES(logo_url), description = VALUES(description), updated_at = NOW()");

              if ($stmt === false) {
                  $database_message = '<div class="alert alert-danger"><strong>Kesalahan SQL:</strong> ' . htmlspecialchars($koneksi->error) . '</div>';
              } else {
                  foreach ($gamelist_entries_for_insert as $entry) {
                      $code = $entry['provider_code'];
                      $type = $entry['provider_type']; 

                      $name = 'Unknown Provider'; 
                      $status = 'inactive'; 
                      $image_url = null; 

                      if (isset($api_providers_map[$code])) {
                          $api_data = $api_providers_map[$code];
                          $name = $api_data['provider_name'] ?? $name;
                          $status = $api_data['status'] ?? $status;
                          $image_url = $api_data['logo_url'] ?? $image_url;
                      }

                      $stmt->bind_param("sssss", $code, $name, $status, $image_url, $type);
                      if ($stmt->execute()) {
                          if ($stmt->affected_rows === 1) $inserted_count++; 
                          elseif ($stmt->affected_rows === 2) $updated_count++; 
                      } else {
                          $error_db_count++;
                      }
                  }
                  $stmt->close();
                  $database_message = '<div class="alert alert-success">Proses selesai. Insert: <strong>'.$inserted_count.'</strong>, Update: <strong>'.$updated_count.'</strong>, Gagal: <strong>'.$error_db_count.'</strong>.</div>';
              }
          }
      } catch (Exception $e) {
          $database_message = '<div class="alert alert-danger">Terjadi kesalahan pada proses API: ' . htmlspecialchars($e->getMessage()) . '</div>';
      }
  }

  // --- Ambil Semua Data Provider dari Database untuk Tampilan Tabel ---
  $all_db_providers = [];
  $all_db_providers_error = '';
  if (isset($koneksi) && $koneksi instanceof mysqli) {
      // DISESUAIKAN DENGAN TABEL game_providers
      $query_all_providers = $koneksi->query("SELECT id, provider_code, provider_name, description AS provider_type, status AS provider_status, logo_url AS provider_image FROM game_providers ORDER BY provider_name ASC");
      if ($query_all_providers) {
          while ($row = $query_all_providers->fetch_assoc()) {
              $all_db_providers[] = $row;
          }
          $query_all_providers->free();
      } else {
          $all_db_providers_error = "Gagal mengambil daftar provider dari database: " . htmlspecialchars($koneksi->error);
      }
  }
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Menu Utama /</span> Daftar Provider GameXa</h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <p>Klik tombol di bawah untuk mengambil daftar provider game dari API GameXa dan menyimpannya ke database.</p>

          <form method="POST" action="">
              <button type="submit" name="update_providers_and_db" class="btn btn-primary mb-3">Update & Insert Providers From API to Database</button>
          </form>

          <?php echo $database_message; ?>

          <hr class="my-4" />

          <h4>Daftar Semua Provider dari Database:</h4>
          <?php if (!empty($all_db_providers_error)): ?>
            <div class="alert alert-danger mt-3" role="alert"><strong>Error:</strong> <?php echo htmlspecialchars($all_db_providers_error); ?></div>
          <?php elseif (!empty($all_db_providers)): ?>
            <div class="table-responsive text-nowrap">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Kode Provider</th>
                    <th>Nama Provider</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Gambar Lokal</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <?php foreach ($all_db_providers as $provider): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($provider['id']); ?></td>
                      <td><strong><?php echo htmlspecialchars($provider['provider_code']); ?></strong></td>
                      <td><?php echo htmlspecialchars($provider['provider_name']); ?></td>
                      <td><?php echo htmlspecialchars($provider['provider_type']); ?></td>
                      <td>
                        <?php
                          $status_text = $provider['provider_status'] ?? 'unknown';
                          $status_color = ($status_text === 'active') ? 'badge bg-label-success' : 'badge bg-label-danger';
                        ?>
                        <span class="<?php echo $status_color; ?> me-1"><?php echo htmlspecialchars($status_text); ?></span>
                      </td>
                      <td>
                        <?php if (!empty($provider['provider_image'])): ?>
                          <img src="<?php echo htmlspecialchars($provider['provider_image']); ?>" alt="Gambar Provider" style="max-width: 80px; height: auto;">
                        <?php else: ?>
                          Tidak Ada
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p>Tidak ada provider di database. Klik tombol 'Update & Insert Providers From API to Database' di atas untuk mengisinya.</p>
          <?php endif; ?>

          <hr class="my-4" />
          <h4>Ringkasan Game per Provider</h4>
          <?php if (!empty($game_summary_error)): ?>
            <div class="alert alert-danger mt-3" role="alert"><strong>Error:</strong> <?php echo htmlspecialchars($game_summary_error); ?></div>
          <?php elseif (!empty($game_summary_data)): ?>
            <div class="table-responsive text-nowrap">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Tipe Game</th>
                    <th>Kode Provider</th>
                    <th>Jumlah Game</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  <?php foreach ($game_summary_data as $summary_row): ?>
                    <tr>
                      <td><strong><?php echo htmlspecialchars($summary_row['game_type']); ?></strong></td>
                      <td><?php echo htmlspecialchars($summary_row['provider_code']); ?></td>
                      <td><?php echo htmlspecialchars($summary_row['total_games']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p>Tidak ada data ringkasan game yang ditemukan di tabel `srg_gamelist`.</p>
          <?php endif; ?>
          </div>
      </div>
    </div>
  </div>
</div>
