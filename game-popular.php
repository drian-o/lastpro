<div class="w-full px-3 mt-3 lg:mt-5 order-6">
	<div class="flex justify-between items-center">
		<p class="md:text-lg font-medium">Game Populer</p>
		<a class="text-primary text-sm md:text-base transition-all duration-300 ease-in-out border-b border-transparent hover:lg:border-primary" href="<?php echo $alamat_website . 'slot'; ?>">
			Tampilkan Semua
			<svg width="20" height="20" viewBox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="20">
				<path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--primary)"></path>
			</svg>
		</a>
	</div>
	<div class="mt-3 -mx-[5px] lg:-mx-2 pb-2 whitespace-nowrap overflow-x-scroll overflow-y-hidden lg:overflow-x-hidden opacity-scroll">
		<?php
		// Query untuk mengambil daftar game populer dari database
		$query_game_populer = "
	SELECT * 
	FROM tb_gamelist 
	WHERE provider = 'pragmatiplay_slot' 
	AND datatype = 'slot' 
	ORDER BY RAND() 
	LIMIT 6
";

		$result_game_populer = mysqli_query($koneksi, $query_game_populer);

		while ($row_game = mysqli_fetch_assoc($result_game_populer)) {
			$nama_game = $row_game['gamename'];
			$gambar_game = $row_game['image'];
			$provider_game = $row_game['provider'];
			$kode_game = $row_game['gamecode'];
		?>
		<div class="w-[40%] md:w-[26%] lg:w-1/6 px-[5px] lg:px-2 inline-block">
			<div class="relative">
				<?php
				if (isset($_SESSION['id_anggota'])) {
				?>
					<a href="playgame.php?game_code=<?php echo $kode_game; ?>" class="absolute h-full w-full z-50 lg:hidden"></a>
				<?php
				} else {
				?>
					<a href="javascript:registerPopup({ content:'<?php echo htmlspecialchars($isi_1_popup_teks_belum_login_web, ENT_QUOTES, 'UTF-8'); ?>' });" class="absolute h-full w-full z-50 lg:hidden"></a>
				<?php
				}
				?>
				<div class="relative z-10 rounded-xl overflow-hidden cursor-pointer group">
					<div class="absolute top-1 left-1 z-10 h-8 lg:h-9 overflow-hidden">
						<div class="">
							<span class=" text-[9px] text-center px-1 mb-1 rounded-full block font-semibold text-white" style="background-color:#ee0000;animation-delay:-1s">HOT</span>
						</div>
					</div>
					<figure class="w-full overflow-hidden">
						<img alt="<?php echo $nama_game; ?>-<?php echo $provider_game; ?>" fetchPriority="high" width="300" height="300" decoding="async" data-nimg="1" class="rounded-xl w-full group-hover:lg:scale-110 group-hover:lg:blur-sm transition-all duration-500 ease-in-out" style="background-size:cover;background-position:50% 50%;background-repeat:no-repeat;background-color:transparent;" src="<?php echo $gambar_game; ?>" />
					</figure>
					<div class="hidden lg:flex items-center justify-center translate-y-full group-hover:translate-y-0 absolute z-[15] left-0 right-0 top-0 bottom-0 bg-black/80 transition-all duration-500 ease-out">
						<div>
							<?php
							if (isset($_SESSION['id_anggota'])) {
							?>
								<a href="playgame.php?game_code=<?php echo $kode_game; ?>" class="w-full text-xs rounded-full py-2 px-6 justify-center bg-primary hover:brightness-90 transition-all duration-300 ease-in-out">Main Game</a>
							<?php
							} else {
							?>
								<a href="javascript:registerPopup({ content:'<?php echo htmlspecialchars($isi_1_popup_teks_belum_login_web, ENT_QUOTES, 'UTF-8'); ?>' });" class="w-full text-xs rounded-full py-2 px-6 justify-center bg-primary hover:brightness-90 transition-all duration-300 ease-in-out">Main Game</a>
							<?php
							}
							?>
						</div>
					</div>
				</div>
				<p class="text-[11px] md:text-xs mt-1 lg:mt-2 truncate"><?php echo $nama_game; ?></p>
			</div>
		</div>
		<?php
		}
		?>
	</div>
</div>
