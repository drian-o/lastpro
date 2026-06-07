<?php
include_once 'header.php';

if (empty($_GET)) {
?>

<section class="relative bg-[#000134] min-h-screen">
	<div class="container mx-auto p-3 lg:pb-8 relative z-10">
		<nav class="flex mb-1 lg:mb-2">
			<ol class="flex items-center pb-1 overflow-x-scroll whitespace-nowrap opacity-scroll">
				<li class="inline-flex items-end pr-1">
					<a class="text-xs border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out undefined" href="<?php echo $alamat_website . 'home'; ?>">Home</a>
				</li>
				<li class="inline-flex items-end pr-1 group">
					<div class="flex items-center">
						<svg width="17" height="17" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="17">
							<path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
						</svg><a class="text-xs pl-1 border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out group-last:text-primary undefined" href="sports">sports</a>
					</div>
				</li>
			</ol>
		</nav>
		<img alt="sports Page Banner" fetchpriority="high" width="1500" height="0" decoding="async" data-nimg="1" class="rounded-lg w-full h-36 md:h-auto object-center object-cover" src="https://cdn.databerjalan.com/cdn-cgi/image/width=auto,quality=75,fit=contain,format=auto//assets/images/static/v3/banner/sports-new.webp" style="color: transparent;">
		<div class="flex flex-wrap -mx-2 mt-4 lg:mt-8">
			<?php
			// Query untuk mengambil daftar provider sports dari database
			$query_provider = "SELECT * FROM tb_provider WHERE jenis IN (2) AND status = 1 ORDER BY cuid ASC";
			$result_provider = mysqli_query($koneksi, $query_provider);

			while ($row_provider = mysqli_fetch_assoc($result_provider)) {
				$nama_provider = $row_provider['providername'];
				$gambar_provider = $row_provider['slug'];
				$kode_provider = $row_provider['providerid'];
			?>
			<div class="w-1/3 sm:w-1/4 lg:w-1/6 px-[6px] lg:px-3 mb-3 lg:mb-4">
				<?php $href = $alamat_website . 'sports?provider=' . strtolower($gambar_provider); ?>
				<a href="<?php echo $href; ?>" class="relative pb-3 w-full group overflow-hidden">
					<div class="w-full rounded-xl lg:rounded-2xl" style="box-shadow: rgba(204, 210, 227, 0.5) 0px 0px 28.8428px inset;">
						<figure class="relative overflow-hidden rounded-xl lg:rounded-2xl"><img alt="sports-<?php echo $nama_provider; ?>" fetchpriority="high" width="0" height="0" decoding="async" data-nimg="1" class="w-full group-hover:lg:scale-110 transition-all duration-300 ease-in-out" src="uploads/provider/sports/<?= $gambar_provider; ?>.png" style="color: transparent;"></figure>
					</div>
					<div class="absolute left-2 right-2 md:left-6 md:right-6 bottom-1 z-50">
						<p class="text-xs md:text-sm text-center truncate z-10 relative px-2 py-[2px] md:py-1 bg-background-secondary rounded-full"><?php echo $nama_provider; ?></p>
						<div class="rainbow left-[-1px] right-[-1px] top-[-1px] bottom-[-1px] rounded-full"></div>
					</div>
				</a>
			</div>
			<?php
			}
			?>
		</div>
	</div>
</section>

<?php
include_once 'footer.php';
} else {
$provider = isset($_GET['provider']) ? $_GET['provider'] : '';

// ambil data provider
$query_provider = "SELECT * FROM tb_provider WHERE slug = ? AND jenis IN (2)";
$stmt = mysqli_prepare($koneksi, $query_provider);
mysqli_stmt_bind_param($stmt, 's', $provider);
mysqli_stmt_execute($stmt);
$result_provider = mysqli_stmt_get_result($stmt);
$row_provider = mysqli_fetch_assoc($result_provider);
$nama_provider = $row_provider['providername'];
$providerid = $row_provider['providerid'];
$providertype = $row_provider['type'];
mysqli_stmt_close($stmt);
?>

<div class="container mx-auto p-3 lg:pb-8">
    <nav class="flex mb-1 lg:mb-2">
        <ol class="flex items-center pb-1 overflow-x-scroll whitespace-nowrap opacity-scroll">
            <li class="inline-flex items-end pr-1"><a class="text-xs border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out" href="<?php echo $alamat_website . 'home'; ?>">Home</a></li>
            <li class="inline-flex items-end pr-1 group">
                <div class="flex items-center"><svg width="17" height="17" viewBox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="17">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                    </svg><a class="text-xs pl-1 border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out group-last:text-primary" href="<?php echo $alamat_website . 'sports'; ?>">sports</a></div>
            </li>
            <li class="inline-flex items-end pr-1 group">
                <div class="flex items-center"><svg width="17" height="17" viewBox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="17">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                    </svg><a class="text-xs pl-1 border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out group-last:text-primary"><?php echo $nama_provider; ?></a></div>
            </li>
        </ol>
    </nav>
    <div class="flex flex-wrap mt-3 mb-5">
        <div class="flex pb-1 overflow-x-scroll whitespace-nowrap">
            <h5 class="border-b border-primary text-sm cursor-pointer transition-all duration-300 ease-in-out">All</h5>
            <h5 class="ml-4 text-sm capitalize cursor-pointer transition-all duration-300 ease-in-out hover:lg:border-primary">LIVE GAMES</h5>
            <h5 class="ml-4 text-sm capitalize cursor-pointer transition-all duration-300 ease-in-out hover:lg:border-primary">HOT</h5>
        </div>
    </div>

    <div class="flex flex-wrap -mx-3 mt-3">
        <?php
        $query_games = "SELECT * FROM tb_gamelist WHERE datatype = '$providertype' AND provider = '$providerid'";
        $stmt_games = mysqli_prepare($koneksi, $query_games);
        mysqli_stmt_execute($stmt_games);
        $result_games = mysqli_stmt_get_result($stmt_games);
        
        while ($data_games = mysqli_fetch_array($result_games)) {
            $angka_rtp = rand($isi_1_rtp_web, $isi_2_rtp_web);
            $gambar_games = $data_games['image'];
            $nama_games = $data_games['gamename'];
            $warna_rtp = ($angka_rtp <= 30) ? "red" : (($angka_rtp <= 60) ? "yellow" : "green");
        ?>
            <div class="w-1/3 sm:w-1/4 lg:w-1/6 px-[6px] lg:px-3 mb-3 lg:mb-4">
                <div class="relative">
                    <div class="relative z-10 rounded-xl overflow-hidden cursor-pointer group">
                        <figure class="w-full overflow-hidden aspect-square">
                            <img alt="<?php echo htmlspecialchars($nama_games); ?>" fetchpriority="high" width="300" height="300" decoding="async" data-nimg="1" class="rounded-xl w-full h-full object-cover group-hover:lg:scale-110 group-hover:lg:blur-sm transition-all duration-500 ease-in-out" src="<?php echo htmlspecialchars($gambar_games); ?>" style="color: transparent;">
                        </figure>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/80 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
                            <div>
                                <?php
                                if (isset($_SESSION['id_anggota'])) {
                                    // Pengguna sudah login
                                    $href = "playgame.php?game_code=" . $data_games['gamecode'];
                                } else {
                                    // Pengguna belum login
                                    $href = "javascript:registerPopup({ content:'" . htmlspecialchars($isi_1_popup_teks_belum_login_web, ENT_QUOTES, 'UTF-8') . "' });";
                                }
                                ?>

                                <a href="<?php echo $href; ?>" class="text-xs rounded-full py-2 px-6 justify-center bg-primary hover:brightness-90 transition-all duration-300 ease-in-out">Play Game</a>

                            </div>
                        </div>
                        <p class="text-[11px] md:text-xs mt-1 lg:mt-2 truncate text-center"><?php echo htmlspecialchars($nama_games); ?></p>
                    </div>
                </div>
            </div>
        <?php
        }
        mysqli_stmt_close($stmt_games);
        ?>
    </div>
</div>
<?php
}
?>