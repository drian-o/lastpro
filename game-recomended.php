<div class="w-full px-3 mt-1 lg:mt-4 order-6">
	<p class="md:text-lg font-medium">Game Rekomendasi</p>
	<div class="mt-3 -mx-[5px] lg:-mx-2 pb-2 whitespace-nowrap overflow-x-scroll overflow-y-hidden lg:overflow-x-hidden opacity-scroll">
		<?php
		// Query untuk mengambil daftar game rekomendasi dari database
		$query_game_rekomendasi = "SELECT * FROM tb_gamelist WHERE provider = 'pgsoft_slot' AND datatype = 'slot' ORDER BY RAND() LIMIT 6";
		$result_game_rekomendasi = mysqli_query($koneksi, $query_game_rekomendasi);

		while ($row_game = mysqli_fetch_assoc($result_game_rekomendasi)) {
			$nama_game = $row_game['gamename'];
			$gambar_game = $row_game['image'];
			$provider_game = $row_game['provider'];
			$kode_game = $row_game['gamecode'];
		?>
		<div class="w-[40%] md:w-[26%] lg:w-1/6 px-[5px] lg:px-2 inline-block">
			<?php
				// Periksa apakah pengguna sudah login
				if (isset($_SESSION['id_anggota'])) {
					// Pengguna sudah login
					$href = "playgame.php?game_code=" . $kode_game;
				} else {
					$href = "javascript:registerPopup({ content:'" . htmlspecialchars($isi_1_popup_teks_belum_login_web, ENT_QUOTES, 'UTF-8') . "' });";
				}
			?>
			<a href="<?php echo $href; ?>" class="relative pb-3 w-full group overflow-hidden">
				<div class="w-full rounded-xl lg:rounded-2xl" style="box-shadow: rgba(204, 210, 227, 0.5) 0px 0px 28.8428px inset;">
					<figure class="relative overflow-hidden rounded-xl lg:rounded-2xl aspect-square">
						<img alt="<?php echo $nama_game; ?>-<?php echo $provider_game; ?>" loading="lazy" width="300" height="300" decoding="async" data-nimg="1" class="w-full h-full object-cover group-hover:lg:scale-110 transition-all duration-300 ease-in-out" src="<?php echo $gambar_game; ?>" style="color: transparent;">
					</figure>
				</div>
				<div class="absolute left-2 right-2 md:left-6 md:right-6 bottom-1 z-50">
					<p class="text-xs md:text-sm text-center truncate z-10 relative px-2 py-[2px] md:py-1 bg-background-secondary rounded-full"><?php echo $nama_game; ?></p>
					<div class="rainbow left-[-1px] right-[-1px] top-[-1px] bottom-[-1px] rounded-full"></div>
				</div>
			</a>
		</div>
		<?php
		}
		?>
	</div>
</div>
