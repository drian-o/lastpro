<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../koneksi.php';

// Proteksi Admin
if (!isset($_SESSION['kode_admin'])) {
    echo '<script>window.location.replace("keluar.php");</script>';
    exit();
}

// --- SAPU OTOMATIS: Hapus log yang usianya lebih dari 7 hari ---
// Ditaruh di sini agar database tetap enteng
mysqli_query($koneksi, "DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL 7 DAY");

// Fitur Hapus Manual
if(isset($_GET['aksi']) && $_GET['aksi'] == 'bersihkan') {
    mysqli_query($koneksi, "TRUNCATE TABLE activity_logs");
    echo '<script>alert("Log dibersihkan!"); window.location.replace("?halaman=log_aktifitas");</script>';
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4 text-white"><span class="text-muted fw-light">Menu Utama /</span> Log Aktivitas</h4>

  <div class="card mb-4" style="background-color: #2b2c40; color: #cbcbd6;">
    <div class="card-header border-bottom border-secondary d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white fw-bold mb-0">Monitor Aktivitas Sistem</h5>
            <small class="text-muted">Menampilkan 1000 log terbaru (Log lama > 7 hari otomatis dihapus)</small>
        </div>
        <a href="?halaman=log_aktifitas&aksi=bersihkan" class="btn btn-sm text-white fw-bold" style="background-color: #ff3e1d;" onclick="return confirm('Hapus semua log?')">
            <i class="bx bx-trash me-1"></i> Bersihkan Log
        </a>
    </div>
    
    <div class="table-responsive">
      <table class="table mb-0 table-hover">
        <thead style="background-color: #17a2b8;">
          <tr>
            <th style="color: white; width: 50px;" class="text-center">NO</th>
            <th style="color: white;">USER / ROLE</th>
            <th style="color: white;">WAKTU</th>
            <th style="color: white;">IP</th>
            <th style="color: white;">AKTIVITAS</th>
            <th style="color: white;" class="text-center">SOURCE</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          <?php
          // Query pakai index id biar cepat
          $query_logs = mysqli_query($koneksi, "SELECT * FROM activity_logs ORDER BY id DESC LIMIT 1000");
          $no = 1;
          
          if ($query_logs && mysqli_num_rows($query_logs) > 0) {
              while ($row = mysqli_fetch_assoc($query_logs)) {
                  // Logika Warna Role
                  $role_db = strtolower($row['role']);
                  $badge_role = ($role_db == 'admin') ? 'bg-danger' : (($role_db == 'staff') ? 'bg-warning' : (($role_db == 'user') ? 'bg-primary' : 'bg-secondary'));
                  ?>
                  <tr style="border-bottom: 1px solid #3c3d56;">
                    <td class="text-center text-white"><?= $no++; ?></td>
                    <td>
                        <strong class="text-white"><?= htmlspecialchars($row['username']); ?></strong><br>
                        <span class="badge <?= $badge_role; ?>" style="font-size: 10px;"><?= strtoupper(htmlspecialchars($row['role'])); ?></span>
                    </td>
                    <td class="text-muted" style="font-size: 12px;"><?= date('d-m-Y H:i:s', strtotime($row['created_at'])); ?></td>
                    <td class="text-info"><?= htmlspecialchars($row['ip_address']); ?></td>
                    <td class="text-white" style="font-size: 13px;"><?= htmlspecialchars($row['activity']); ?></td>
                    <td class="text-center text-muted"><?= htmlspecialchars($row['device']); ?></td>
                  </tr>
                  <?php 
              } 
          } else {
              echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada aktivitas.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
