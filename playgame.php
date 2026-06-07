<?php
    // 1. Mulai Output Buffering DI PALING ATAS
    ob_start();
    
    // 2. Memulai Sesi
    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    
    // 3. Sertakan file yang diperlukan
    include 'koneksi.php';
    require_once 'classes/class.exa.php'; 
    
    // PERHATIAN: Tentukan path log
    $log_file = __DIR__ . '/logs/playgame.log';

    // Logika Autentikasi: Periksa ext_pengguna_anggota
    if (!isset($_SESSION['ext_pengguna_anggota']) || empty($_SESSION['ext_pengguna_anggota'])) {
        header("Location: auth-login"); 
        exit();
    }
    
    // --- Ambil Data Pemain dan Game ---
    
    // 1. Validasi parameter game_code
    if (!isset($_GET['game_code'])) {
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'home'));
        exit();
    }
    
    $game_code = $_GET['game_code'];
    $player_id = $_SESSION['ext_pengguna_anggota']; 
    $game_uid = $game_code; 
    
    try {
        // Inisialisasi API Class
        $api = new GameXaAPI();
        
        // Panggil fungsi launchGame
        $proses = $api->launchGame(
            $player_id, 
            $game_uid, 
            'IDR', // Asumsi mata uang default
            $alamat_website . 'home' // URL kembali setelah selesai bermain
        );
        
        // --- Tangani Respon API Exa ---
        if ($proses['success'] === true && isset($proses['data']['game_launch_url'])) {
            
            $launch_url = $proses['data']['game_launch_url'];
            
            // Redirect ke URL peluncuran game
            header("Location: " . $launch_url);
            exit();
            
        } else {
            // Jika API merespons dengan 'success: false' atau tidak ada URL
            $error_message = $proses['data']['message'] ?? "Respon API Gagal: Tidak ada URL peluncuran game.";
            
            // Tambahkan detail error API ke log
            $log_data = date('[Y-m-d H:i:s]') . " [API Error] Player: {$player_id}, GameCode: {$game_code}. ";
            $log_data .= "Pesan API: {$error_message}. Respon Lengkap: " . json_encode($proses) . "\n";
            file_put_contents($log_file, $log_data, FILE_APPEND);
            
            throw new Exception($error_message);
        }
        
    } catch (Exception $e) {
        // --- Tangani Error PHP / Exception ---
        
        // Log error lengkap ke file
        $log_data = date('[Y-m-d H:i:s]') . " [Fatal Error] Player: {$player_id}, GameCode: {$game_code}. ";
        $log_data .= "Pesan Exception: " . $e->getMessage() . ". File: " . $e->getFile() . ", Line: " . $e->getLine() . "\n";
        file_put_contents($log_file, $log_data, FILE_APPEND);
        
        // Beri tahu pengguna dan redirect
        $_SESSION['error_message'] = "Gagal memulai permainan. Detail error telah dicatat.";
        header("Location: error.php");
        exit();
    }
    
    // Kirim output buffer dan hentikan buffering
    ob_end_flush();
?>