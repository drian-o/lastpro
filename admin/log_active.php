<?php
// admin/log_aktifitas.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../koneksi.php';

if (!isset($_SESSION['kode_admin'])) {
    echo '<script>window.location.replace("keluar.php");</script>';
    exit();
}

// Fitur Hapus Semua Log (Biar database nggak bengkak)
if(isset($_GET['aksi']) && $_GET['aksi'] == 'bersihkan') {
    mysqli_query($koneksi, "TRUNCATE TABLE activity_logs");
    echo '<script>alert("Semua log berhasil dibersihkan!"); window.location.replace("?halaman=log_aktifitas");</script>';
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4 text-white"><span class="text-muted fw-light">Menu Utama /</span> Log Aktivitas Sistem</h4>

  <div class="card mb-4" style="background-color: #2b2c40; color: #cbcbd6;">
    <div class="card-header border-bottom border-secondary d-flex justify-content-between align-items-center">
        <h5 class="text-white fw-bold mb-0">Daftar Aktivitas (Online Log)</h5>
        <a href="?halaman=log_aktifitas&aksi=bersihkan" class="btn btn-sm btn-danger fw-bold" onclick="return confirm('Yakin ingin menghapus seluruh log? Tindakan ini tidak bisa dibatalkan.')">
            <i class="bx bx-trash me-1"></i> Bersihkan Log
        </a>
    </div>
    
    <div class="table-responsive text-nowrap">
      <table class="table mb-0 table-striped">
        <thead style="background-color: #17a2b8;"> <tr>
            <th style="color: white;" class="text-center">NO</th>
            <th style="color: white;">USER / ROLE</th>
            <th style="color: white;">WAKTU (TANGGAL)</th>
            <th style="color: white;">IP ADDRESS</th>
            <th style="color: white;">AKTIVITAS (GAME/URL)</th>
            <th style="color: white;" class="text-center">SOURCE</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          <?php
          // Ambil 500 log terakhir agar loading tidak berat
          $query_logs = mysqli_query($koneksi, "SELECT * FROM activity_logs ORDER BY id DESC LIMIT 500");
          $no = 1;
          
          if ($query_logs && mysqli_num_rows($query_logs) > 0) {
              while ($row = mysqli_fetch_assoc($query_logs)) {
                  // Warna badge untuk role
                  $badge_role = ($row['role'] == 'Admin') ? 'bg-danger' : (($row['role'] == 'User') ? 'bg-primary' : 'bg-secondary');
                  ?>
                  <tr style="border-bottom: 1px solid #3c3d56;">
                    <td class="text-center fw-bold text-white"><?= $no++; ?></td>
                    <td>
                        <strong class="text-white"><?= htmlspecialchars($row['username']); ?></strong><br>
                        <span class="badge <?= $badge_role; ?>" style="font-size: 10px; padding: 3px 6px;"><?= strtoupper(htmlspecialchars($row['role'])); ?></span>
                    </td>
                    <td class="text-muted"><?= date('d-m-Y H:i:s', strtotime($row['created_at'])); ?></td>
                    <td class="text-info fw-bold"><?= htmlspecialchars($row['ip_address']); ?></td>
                    <td class="text-white" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['activity']); ?>">
                        <?= htmlspecialchars($row['activity']); ?>
                    </td>
                    <td class="text-center text-muted"><?= htmlspecialchars($row['device']); ?></td>
                  </tr>
                  <?php 
              } 
          } else {
              echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada aktivitas tercatat.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
