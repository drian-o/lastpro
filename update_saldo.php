<?php
    // Sertakan file koneksi database
	include 'koneksi.php';
	
    // Ganti class lama dengan class baru
	require_once 'classes/class.exa.php'; 

	// Mulai session jika belum dimulai (memastikan ini selalu aktif untuk AJAX)
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
    // --- 1. Verifikasi Login dan Ambil ID Eksternal ---
	if (!isset($_SESSION['ext_pengguna_anggota']) || empty($_SESSION['ext_pengguna_anggota'])) {
		echo "Pengguna belum login atau ID pemain tidak ditemukan.";
		exit();
	}
	
    $player_id = $_SESSION['ext_pengguna_anggota']; // ID Pemain GxG
    $username = $_SESSION['nama_pengguna_anggota'] ?? ''; // Username lokal (untuk WHERE condition)
    
    // Inisialisasi API Class
    $api = new GameXaAPI();

	try {
        // --- 2. Ambil Saldo dari API GxG ---
        // Panggil fungsi getPlayerBalance($playerId)
		$saldo_response = $api->getPlayerBalance($player_id);
		
		// --- 3. Validasi Respon API GxG ---
		if ($saldo_response['success'] === true && isset($saldo_response['data']['balance'])) {
			
			// Ambil saldo dari data response API
			$saldo_baru = floatval($saldo_response['data']['balance']);
			$saldo_formatted = number_format($saldo_baru, 0, ',', '.');
			
            // --- 4. Update Saldo di Database Lokal ---
			
            // Update berdasarkan username lokal untuk keamanan dan konsistensi
            // Karena kita menggunakan Prepared Statement, kolom saldo_anggota harus double (d)
			$query = "UPDATE anggota SET saldo_anggota = ? WHERE nama_pengguna_anggota = ?";
			$stmt = $koneksi->prepare($query);
			
			if ($stmt === false) {
				throw new Exception("Gagal mempersiapkan query: " . $koneksi->error);
			}
			
			// Ikat parameter dan eksekusi statement: d (double/float), s (string)
			$stmt->bind_param("ds", $saldo_baru, $username);
			
			if ($stmt->execute()) {
				// Update saldo di session
				$_SESSION['saldo_anggota'] = $saldo_baru;
				
                // Tampilkan saldo yang sudah diformat ke halaman (respons AJAX)
				echo "IDR " . $saldo_formatted;
			} else {
				throw new Exception("Gagal memperbarui saldo di database: " . $stmt->error);
			}
			
			$stmt->close();
		} else {
			// Jika API GxG merespons gagal
			$error_message = $saldo_response['data']['message'] ?? "Kesalahan API GxG tidak diketahui.";
			echo "Gagal mengambil saldo: " . $error_message;
		}
	} catch (Exception $e) {
		// Jika ada error fatal (koneksi, prepared statement gagal, dll)
		error_log("Error saat mengambil atau memperbarui saldo: " . $e->getMessage());
		echo "Terjadi kesalahan saat memproses saldo. Silakan coba lagi.";
	}
?>
