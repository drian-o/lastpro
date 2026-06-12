<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../koneksi.php';

// Proteksi akses
if (!isset($_SESSION['kode_admin'])) {
    echo '<script>window.location.replace("keluar.php");</script>';
    exit();
}

// 1. SAPU OTOMATIS (7 hari)
mysqli_query($koneksi, "DELETE FROM activity_logs WHERE created_at < NOW() - INTERVAL 7 DAY");

// 2. Tombol manual
if(isset($_GET['aksi']) && $_GET['aksi'] == 'bersihkan') {
    mysqli_query($koneksi, "TRUNCATE TABLE activity_logs");
    echo '<script>alert("Log dibersihkan!"); window.location.replace("?halaman=log_aktifitas");</script>';
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4 text-white">Log Aktivitas Sistem</h4>

  <div class="card mb-4" style="background-color: #2b2c40; color: #cbcbd6;">
    <div class="card-header border-bottom border-secondary d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white fw-bold mb-0">Monitor Aktivitas (Real-time)</h5>
            <small class="text-muted">Data otomatis dihapus setiap 7 hari</small>
        </div>
        <a href="?halaman=log_aktifitas&aksi=bersihkan" class="btn btn-sm btn-danger fw-bold">
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
            <th style="color: white;">IP ADDRESS</th> <th style="color: white;">AKTIVITAS</th>
            <th style="color: white;" class="text-center">DEVICE</th> </tr>
        </thead>
        <tbody>
          <?php
          $query_logs = mysqli_query($koneksi, "SELECT * FROM activity_logs ORDER BY id DESC LIMIT 500");
          $no = 1;
          while ($row = mysqli_fetch_assoc($query_logs)) {
              $badge = ($row['role'] == 'Admin') ? 'bg-danger' : (($row['role'] == 'Staff') ? 'bg-warning' : 'bg-primary');
              ?>
              <tr style="border-bottom: 1px solid #3c3d56;">
                <td class="text-center text-white"><?= $no++; ?></td>
                <td>
                    <strong class="text-white"><?= htmlspecialchars($row['username']); ?></strong><br>
                    <span class="badge <?= $badge; ?>"><?= strtoupper(htmlspecialchars($row['role'])); ?></span>
                </td>
                <td class="text-muted" style="font-size: 12px;"><?= $row['created_at']; ?></td>
                <td class="text-info fw-bold"><?= htmlspecialchars($row['ip_address']); ?></td>
                <td class="text-white"><?= htmlspecialchars($row['activity']); ?></td>
                <td class="text-center text-muted">
                    <?php if(strtolower($row['device']) == 'mobile'): ?>
                        <i class="bx bx-mobile-alt"></i> Mobile
                    <?php else: ?>
                        <i class="bx bx-laptop"></i> Desktop
                    <?php endif; ?>
                </td>
              </tr>
              <?php 
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
