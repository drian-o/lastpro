<?php
// 1. Include koneksi (di dalam koneksi.php udah otomatis jalanin session_start)
include 'koneksi.php';
require_once 'classes/class.exa.php'; 

// 2. Verifikasi Data Session
if (!isset($_SESSION['ext_pengguna_anggota']) || empty($_SESSION['ext_pengguna_anggota'])) {
    echo "Pengguna belum login.";
    exit();
}

$player_id = $_SESSION['ext_pengguna_anggota']; 
$username = $_SESSION['nama_pengguna_anggota'] ?? ''; 

// ⚡ KUNCI JAWABANNYA DI SINI: Lepas kuncian session PHP sebelum nembak API GxG!
session_write_close();

$api = new GameXaAPI();

try {
    // Karena session udah dilepas, website nggak bakal nge-freeze walau API GxG lagi lemot
    $saldo_response = $api->getPlayerBalance($player_id);
    
    if ($saldo_response['success'] === true && isset($saldo_response['data']['balance'])) {
        $saldo_baru = floatval($saldo_response['data']['balance']);
        $saldo_formatted = number_format($saldo_baru, 0, ',', '.');
        
        // Update Saldo di Database Lokal
        $query = "UPDATE anggota SET saldo_anggota = ? WHERE nama_pengguna_anggota = ?";
        $stmt = $koneksi->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("ds", $saldo_baru, $username);
            
            if ($stmt->execute()) {
                // Opsional: Kalau lu butuh banget update session untuk nampilin di halaman lain
                // Kita buka sebentar aja, update datanya, trus tutup lagi.
                session_start();
                $_SESSION['saldo_anggota'] = $saldo_baru;
                session_write_close();
                
                // Tampilkan hasil buat ditangkep Javascript
                echo "IDR " . $saldo_formatted;
            } else {
                echo "Gagal memperbarui database.";
            }
            $stmt->close();
        }
    } else {
        $error_message = $saldo_response['data']['message'] ?? "Error API";
        echo "Gagal mengambil saldo: " . $error_message;
    }
} catch (Exception $e) {
    error_log("Error update saldo: " . $e->getMessage());
    echo "Terjadi kesalahan sistem.";
}
?>
