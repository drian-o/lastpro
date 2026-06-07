<?php
session_start();
// Pastikan path ke koneksi.php benar
require_once '../../koneksi.php'; 

// Batas game per halaman
$limit = 16;

// --- 1. Validasi dan Sanitasi Input ---
$provider_id = $_POST['provider'] ?? '';
$page = (int)($_POST['page'] ?? 1);
$search_query = $_POST['search'] ?? '';

// Bersihkan input dan pastikan tipe data valid
$provider_id = mysqli_real_escape_string($koneksi, trim($provider_id));
$page = max(1, $page); // Pastikan halaman minimal 1
$offset = ($page - 1) * $limit;

if (empty($provider_id)) {
    header('Content-Type: text/html');
    echo '<p class="p-4 text-center text-gray-400 col-span-full">Provider ID tidak valid.</p>';
    exit;
}

// Persiapkan kueri WHERE dan BINDING
$where_clauses = "provider = ? AND datatype = 'slot' AND frbavailable = 1";
$bind_types = 's';
$bind_params = [&$provider_id];

$search_sql = '';
if (!empty($search_query) && strlen($search_query) >= 3) {
    // Gunakan wildcard untuk pencarian
    $search_param = '%' . mysqli_real_escape_string($koneksi, trim($search_query)) . '%';
    $where_clauses .= " AND gamename LIKE ?";
    $bind_types .= 's';
    $bind_params[] = &$search_param;
}

// --- 2. Hitung Total Game (Menggunakan Prepared Statement) ---
$count_query = "SELECT COUNT(*) AS total FROM tb_gamelist WHERE " . $where_clauses;
$stmt_count = mysqli_prepare($koneksi, $count_query);

// Bind parameter untuk kueri COUNT
if (!empty($bind_params)) {
    mysqli_stmt_bind_param($stmt_count, $bind_types, ...$bind_params);
}

mysqli_stmt_execute($stmt_count);
$count_result = mysqli_stmt_get_result($stmt_count);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
mysqli_stmt_close($stmt_count);

// --- 3. Kueri Utama untuk Mendapatkan Game (Menggunakan Prepared Statement) ---
$main_game_query = "
    SELECT * FROM tb_gamelist 
    WHERE " . $where_clauses . "
    ORDER BY CAST(features AS UNSIGNED) DESC, gamename ASC 
    LIMIT ? OFFSET ?
";

$stmt_games = mysqli_prepare($koneksi, $main_game_query);

// Bind parameter untuk kueri utama (tambahkan limit dan offset)
$limit_ref = $limit;
$offset_ref = $offset;
$bind_types .= 'ii'; // i untuk integer (limit dan offset)
$bind_params[] = &$limit_ref;
$bind_params[] = &$offset_ref;

// Re-bind (harus re-bind karena kita menambahkan parameter limit/offset)
mysqli_stmt_bind_param($stmt_games, $bind_types, ...$bind_params);
mysqli_stmt_execute($stmt_games);
$games_result = mysqli_stmt_get_result($stmt_games);

// --- 4. TAMPILAN GAME (HTML) ---
$num_games_fetched = mysqli_num_rows($games_result);

if ($num_games_fetched > 0) {
    while ($data_games = mysqli_fetch_array($games_result)) {
        // Logika RTP (Asumsi variabel $isi_1_rtp_web dan $isi_2_rtp_web tersedia dari koneksi.php)
        $angka_rtp = rand($isi_1_rtp_web, $isi_2_rtp_web);
        $gambar_games = $data_games['image'];
        $nama_games = $data_games['gamename']; 
        $game_code = $data_games['gamecode'];
        
        if ($angka_rtp < 30) {
            $warna_rtp = "red";
        } else if ($angka_rtp < 70) {
            $warna_rtp = "yellow";
        } else {
            $warna_rtp = "green";
        }
?>
        <div class="relative game-item">
            <div class="relative z-10 rounded-xl overflow-hidden cursor-pointer group">
                <div class="absolute top-1 left-1 z-30 h-8 lg:h-9 overflow-hidden">
                    <div>
                        <span class="text-[9px] text-center px-1 mb-1 rounded-full block font-semibold text-white" style="background-color: rgb(238, 0, 0); animation-delay: -1s;">HOT</span>
                    </div>
                </div>
                
                <div class="relative group aspect-square flex items-center justify-center">
                    
                    <figure class="absolute inset-0 w-full h-full overflow-hidden">
                        <img 
                            alt="<?php echo $nama_games; ?> (Background)" 
                            src="<?php echo $gambar_games; ?>" 
                            class="w-full h-full object-cover rounded-xl scale-105 transition-all duration-300 group-hover:blur-md" 
                            style="color: transparent; filter: blur(16px);" 
                        >
                    </figure>
                    
                    <figure class="relative z-10 w-3/4 h-3/4 overflow-hidden shadow-xl rounded-lg transform transition-all duration-300 group-hover:scale-105">
                        <img 
                            alt="<?php echo $nama_games; ?>" 
                            src="<?php echo $gambar_games; ?>" 
                            class="w-full h-full object-cover" 
                            style="color: transparent;"
                        >
                    </figure>

                    <div class="absolute inset-0 flex items-center justify-center bg-black/80 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out z-20">
                        <?php if (isset($_SESSION['id_anggota'])) { ?>
                            <a href="playgame?game_code=<?php echo urlencode($game_code); ?>" class="bg-primary px-4 py-2 text-xs font-semibold rounded-lg">Mainkan Sekarang</a>
                        <?php } else { ?>
                            $href = "javascript:registerPopup({ content:'" . htmlspecialchars($isi_1_popup_teks_belum_login_web, ENT_QUOTES, 'UTF-8') . "' });";
                            
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <div class="absolute top-1 right-1 z-10">
                <div class="flex justify-center rounded-md px-1 py-0.5 mb-1 text-center font-bold text-[9px] text-white" style="background-color: <?php echo $warna_rtp; ?>;">
                    RTP <?php echo $angka_rtp; ?>%
                </div>
            </div>
            
            <p class="mt-1 text-[10px] md:text-xs lg:text-sm text-center font-medium"><?php echo $nama_games; ?></p>
        </div>
<?php
    }
} else {
    $search_message = !empty($search_query) ? 'dengan kata kunci "' . htmlspecialchars($search_query) . '"' : '';
    echo '<p class="p-4 text-center text-gray-400 col-span-full">Tidak ada game yang tersedia ' . $search_message . '.</p>';
}

$has_next = ($offset + $num_games_fetched < $total_rows) ? 'true' : 'false';
echo '<div id="load-more-indicator" data-has-next="' . $has_next . '" class="col-span-full hidden"></div>'; 

mysqli_stmt_close($stmt_games);
mysqli_close($koneksi);
?>