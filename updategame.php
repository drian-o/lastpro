<?php
// Pastikan file koneksi dan class API tersedia
require_once 'koneksi.php';
require_once 'classes/class.exa.php';

// Inisialisasi API Class. Kredensial diambil dari class.exa.php
$api = new GameXaAPI();

// --- Konfigurasi dan Variabel Rekapitulasi ---
$rekap = [
    'dimasukkan' => 0, // INSERT
    'diperbarui' => 0, // UPDATE
    'aktif' => 0,
    'nonaktif' => 0,
    'total_api' => 0,
    'error_database' => 0,
];

// --- 1. Panggil API untuk mendapatkan semua game ---
$apiResponse = $api->getAllGames(1, 50000, null, null, null, 'active');

// --- Logika Utama Proses Sinkronisasi ---
if (!$apiResponse['success'] || !isset($apiResponse['data']['games'])) {
    $error = "Gagal memanggil API atau data game tidak ditemukan.";
    $games = [];
} else {
    $games = $apiResponse['data']['games'];
    $rekap['total_api'] = count($games);
    
    // Dapatkan CUID maksimum yang sudah ada (untuk digunakan sebagai nomor urut)
    $queryMaxCuid = mysqli_query($koneksi, "SELECT MAX(cuid) AS max_cuid FROM tb_gamelist");
    $dataMaxCuid = mysqli_fetch_assoc($queryMaxCuid);
    $currentCuid = (int)($dataMaxCuid['max_cuid'] ?? 0) + 1;
    
    // Mulai Transaksi
    mysqli_begin_transaction($koneksi);
    
    // --- Prepared Statements (Dibuat sekali di luar loop) ---
    // Cek Keberadaan Game menggunakan game_uid (yang sekarang dipetakan ke gamecode)
    $queryCheck = "SELECT gameid FROM tb_gamelist WHERE gamecode = ?";
    $stmtCheck = mysqli_prepare($koneksi, $queryCheck);
    
    // INSERT Query (17 Kolom)
    $queryInsert = "
        INSERT INTO tb_gamelist (
            gameid, gamecode, gamename, provider, datatype, frbavailable, 
            image, gameidnumeric, gametypeid, category, technology, platform, 
            demogame, aspectratio, technologyid, jurisdictions, features
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmtInsert = mysqli_prepare($koneksi, $queryInsert);
    // sssssssssssssssss (17 kali s)
    mysqli_stmt_bind_param($stmtInsert, 'sssssssssssssssss', 
        $gameid, $gamecode, $gamename, $provider, $datatype, $frbavailable, 
        $image, $gameidnumeric, $gametypeid, $category, $technology, $platform, 
        $demogame, $aspectratio, $technologyid, $jurisdictions, $features
    );

    // UPDATE Query (16 Kolom, WHERE menggunakan gamecode/game_uid)
    $queryUpdate = "
        UPDATE tb_gamelist SET 
            gamename = ?, provider = ?, datatype = ?, 
            frbavailable = ?, image = ?, gametypeid = ?, category = ?, 
            technology = ?, platform = ?, demogame = ?, aspectratio = ?, 
            technologyid = ?, jurisdictions = ?, features = ?, 
            gameidnumeric = ?
        WHERE gamecode = ?
    ";
    $stmtUpdate = mysqli_prepare($koneksi, $queryUpdate);
    // ssssssssssssssss (15 kali s untuk SET, 1 kali s untuk WHERE)
    mysqli_stmt_bind_param($stmtUpdate, 'ssssssssssssssss', 
        $gamename, $provider, $datatype, $frbavailable, 
        $image, $gametypeid, $category, $technology, $platform, 
        $demogame, $aspectratio, $technologyid, $jurisdictions, $features, 
        $gameidnumeric, // Kolom yang di-update
        $gamecode      // WHERE condition
    );

    // --- Loop Pemrosesan Game ---
    foreach ($games as $game) {
        
        // --- 2. MAPPING DATA API KE VARIABEL PHP (REVISI BARU) ---
        
        // Kolom yang menggunakan respons API
        $gamecode = $game['game_uid'] ?? ''; // Mapped dari game_uid
        $gamename = $game['game_name'] ?? '';
        $provider = $game['provider_code'] ?? ''; // Mapped dari provider_code
        $datatype = $game['game_type'] ?? ''; 
        $frbavailable = $game['status'] === 'active' ? '1' : '0'; // Mapping status ke frbavailable
        $image = $game['image_url'] ?? ''; 
        $gameidnumeric = (string)($game['provider_id'] ?? 0); // Mapped dari provider_id
        $gametypeid = $game['game_type'] ?? ''; 
        $category = $game['game_type'] ?? ''; 
        
        // Konstanta untuk kolom lainnya
        $technology = 'HTML5'; 
        $platform = 'MOBILE,DOWNLOAD,WEB';
        $demogame = '0';
        $aspectratio = '16:9';
        $technologyid = 'html5';
        $jurisdictions = 'GxG';
        $features = '0';
        
        // --- 3. Cek Keberadaan Game di DB (Berdasarkan gamecode/game_uid) ---
        mysqli_stmt_bind_param($stmtCheck, 's', $gamecode);
        mysqli_stmt_execute($stmtCheck);
        mysqli_stmt_store_result($stmtCheck);
        
        if (mysqli_stmt_num_rows($stmtCheck) == 0) {
            // --- INSERT BARU ---
            $gameid = (string)$currentCuid; // Mapped dari Nomor Urut
            
            if (mysqli_stmt_execute($stmtInsert)) {
                $rekap['dimasukkan']++;
                $currentCuid++; // Tingkatkan CUID/Nomor Urut untuk game berikutnya
            } else {
                $rekap['error_database']++;
                error_log("Gagal INSERT game {$gamecode}: " . mysqli_error($koneksi));
            }
        } else {
            // --- UPDATE DATA ---
            // Saat update, kita tidak perlu mengupdate gameid/nomor urut.
            
            if (mysqli_stmt_execute($stmtUpdate)) {
                $rekap['diperbarui']++;
            } else {
                $rekap['error_database']++;
                error_log("Gagal UPDATE game {$gamecode}: " . mysqli_error($koneksi));
            }
        }
        
        // Catat status aktif/nonaktif
        if ($frbavailable == '1') {
            $rekap['aktif']++;
        } else {
            $rekap['nonaktif']++;
        }
    }

    // --- 4. Finalisasi Transaksi dan Tutup Statement ---
    mysqli_stmt_close($stmtCheck);
    mysqli_stmt_close($stmtInsert);
    mysqli_stmt_close($stmtUpdate);
    
    if ($rekap['error_database'] > 0) {
        mysqli_rollback($koneksi);
        $error = "Terjadi {$rekap['error_database']} kesalahan database. Perubahan dibatalkan (ROLLBACK).";
    } else {
        mysqli_commit($koneksi);
    }
}

// Tutup koneksi (di luar blok if/else besar)
mysqli_close($koneksi);

// --- TAMPILAN HTML UNTUK HASIL EKSEKUSI ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinkronisasi Game List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-dark { background-color: #1a202c; }
        .text-primary { color: #48bb78; }
    </style>
</head>
<body class="bg-dark text-white min-h-screen p-6">

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-primary border-b border-gray-700 pb-2">
        Game List Sync (tb_gamelist)
    </h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-800 p-4 rounded-lg mb-6 text-red-100">
            <p class="font-bold">Proses Gagal!</p>
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php else: ?>
        <div class="bg-green-700 p-4 rounded-lg mb-6 text-white">
            <p class="font-bold">Proses Sinkronisasi Selesai!</p>
            <p>Total game dari API: <?= number_format($rekap['total_api']) ?></p>
            <?php if ($rekap['error_database'] == 0): ?>
                <p class="text-sm mt-2">Semua perubahan berhasil disimpan ke database (COMMIT).</p>
            <?php else: ?>
                <p class="text-sm mt-2">⚠️ Perubahan dibatalkan (ROLLBACK) karena ditemukan <?= $rekap['error_database'] ?> error database.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-gray-700 p-3 rounded-lg">
            <p class="text-sm text-gray-400">Game Total dari API:</p>
            <p class="text-2xl font-bold text-white"><?= number_format($rekap['total_api']) ?></p>
        </div>
        <div class="bg-gray-700 p-3 rounded-lg">
            <p class="text-sm text-gray-400">Game Baru <span class="text-green-400">Dimasukkan (INSERT)</span>:</p>
            <p class="text-2xl font-bold text-green-400"><?= number_format($rekap['dimasukkan']) ?></p>
        </div>
        <div class="bg-gray-700 p-3 rounded-lg">
            <p class="text-sm text-gray-400">Game Berhasil <span class="text-yellow-400">Diperbarui (UPDATE)</span>:</p>
            <p class="text-2xl font-bold text-yellow-400"><?= number_format($rekap['diperbarui']) ?></p>
        </div>
        <div class="bg-gray-700 p-3 rounded-lg">
            <p class="text-sm text-gray-400">Game Aktif (<code class="font-mono">frbavailable = 1</code>):</p>
            <p class="text-2xl font-bold text-primary"><?= number_format($rekap['aktif']) ?></p>
        </div>
        <div class="bg-gray-700 p-3 rounded-lg">
            <p class="text-sm text-gray-400">Game Nonaktif (<code class="font-mono">frbavailable = 0</code>):</p>
            <p class="text-2xl font-bold text-gray-500"><?= number_format($rekap['nonaktif']) ?></p>
        </div>
        <?php if ($rekap['error_database'] > 0): ?>
        <div class="bg-red-800 p-3 rounded-lg">
            <p class="text-sm text-red-300">Total Error Database:</p>
            <p class="text-2xl font-bold text-red-400"><?= number_format($rekap['error_database']) ?></p>
            <p class="text-xs text-red-300 mt-1">Cek log PHP (`error.log`) untuk detail.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>