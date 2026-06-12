<?php
// admin/log_aktifitas.php (Sesuaikan dengan nama file lu)

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Panggil koneksi database
require_once '../koneksi.php';

// Proteksi halaman, hanya admin yang boleh lihat log
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
        <div>
            <h5 class="text-white fw-bold mb-0">Monitor Aktivitas (Anti-Manipulasi)</h5>
            <small class="text-muted">Melacak seluruh jejak Admin, Staff, dan User (Pemain)</small>
        </div>
        <a href="?halaman=log_aktifitas&aksi=bersihkan" class="btn btn-sm text-white fw-bold" style="background-color: #ff3e1d;" onclick="return confirm('Yakin ingin menghapus seluruh log? Tindakan ini tidak bisa dibatalkan.')">
            <i class="bx bx-trash me-1"></i> Bersihkan Log
        </a>
    </div>
    
    <div class="table-responsive text-nowrap">
      <table class="table mb-0 table-striped">
        <thead style="background-color: #17a2b8;"> <tr>
            <th style="color: white; width: 50px;" class="text-center">NO</th>
            <th style="color: white; width: 200px;">USER / ROLE</th>
            <th style="color: white;">WAKTU (TANGGAL)</th>
            <th style="color: white;">IP ADDRESS</th>
            <th style="color: white;">AKTIVITAS (HALAMAN / URL)</th>
            <th style="color: white;" class="text-center">DEVICE</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          <?php
          // Ambil 1000 log terakhir agar loading tidak berat
          $query_logs = mysqli_query($koneksi, "SELECT * FROM activity_logs ORDER BY id DESC LIMIT 1000");
          $no = 1;
          
          if ($query_logs && mysqli_num_rows($query_logs) > 0) {
              while ($row = mysqli_fetch_assoc($query_logs)) {
                  
                  // LOGIKA WARNA ROLE BIAR GAK KETUKER
                  $role_db = strtolower($row['role']);
                  if ($role_db == 'admin') {
                      $badge_role = 'bg-danger';     // Merah
                  } elseif ($role_db == 'staff') {
                      $badge_role = 'bg-warning';    // Kuning
                  } elseif ($role_db == 'user') {
                      $badge_role = 'bg-primary';    // Biru
                  } else {
                      $badge_role = 'bg-secondary';  // Abu-abu (Guest)
                  }

                  // Sanitasi output untuk menghindari XSS
                  $username_aman = htmlspecialchars($row['username']);
                  $role_aman     = strtoupper(htmlspecialchars($row['role']));
                  $ip_aman       = htmlspecialchars($row['ip_address']);
                  $aktivitas_aman= htmlspecialchars($row['activity']);
                  $device_aman   = htmlspecialchars($row['device']);
                  ?>
                  <tr style="border-bottom: 1px solid #3c3d56;">
                    <td class="text-center fw-bold text-white"><?= $no++; ?></td>
                    
                    <td>
                        <strong class="text-white" style="font-size: 15px;"><?= $username_aman; ?></strong><br>
                        <span class="badge <?= $badge_role; ?>" style="font-size: 10px; padding: 4px 8px; margin-top: 4px;">
                            <i class="bx bx-user-circle me-1" style="font-size: 10px;"></i> <?= $role_aman; ?>
                        </span>
                    </td>
                    
                    <td class="text-muted"><?= date('d-m-Y H:i:s', strtotime($row['created_at'])); ?></td>
                    
                    <td class="text-info fw-bold"><?= $ip_aman; ?></td>
                    
                    <td class="text-white" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= $aktivitas_aman; ?>">
                        <?= $aktivitas_aman; ?>
                    </td>
                    
                    <td class="text-center">
                        <?php if(strtolower($device_aman) == 'mobile'): ?>
                            <span class="text-success"><i class="bx bx-mobile-alt"></i> Mobile</span>
                        <?php else: ?>
                            <span class="text-muted"><i class="bx bx-laptop"></i> Desktop</span>
                        <?php endif; ?>
                    </td>
                  </tr>
                  <?php 
              } 
          } else {
              echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada aktivitas tercatat di database.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
