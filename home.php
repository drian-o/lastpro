<?php
// 1. Panggil Koneksi dulu (Jangan index.php)
require_once 'koneksi.php';

// 2. CEK LOGIN (Pastiin sesi user udah ada)
if (!isset($_SESSION['nama_pengguna_anggota'])) {
    header("Location: auth-login"); // Arahin ke login kalau belum ada sesi
    exit();
}

// 3. TAMPILAN (Jangan include index.php, tapi include file layout atau menu)
// include 'index.php'; // <--- INI HAPUS ATAU KOMENTARIN
?>

<?php include 'header.php'; ?>
<?php include 'footer.php'; ?>
