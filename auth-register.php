<?php include_once 'header.php'; ?>

<section class="lg:bg-background-tertiary min-h-content pb-16 lg:pb-0">
	<div class="container mx-auto md:py-4 lg:py-8 lg:px-3">
		<div class="bg-background-default rounded-2xl p-4 lg:px-10 mx-auto md:w-4/5 lg:w-3/5">
			<section class="flex justify-center rounded-full bg-separator w-full lg:w-3/5 mx-auto overflow-hidden lg:mt-3 mb-6">
				<a class="w-1/2 justify-center bg-inverse rounded-full text-primary font-semibold py-2 text-center" href="<?php echo $alamat_website . 'auth-register'; ?>">Daftar</a>
				<a class="w-1/2 justify-center opacity-70 text-center py-2" href="<?php echo $alamat_website . 'auth-login'; ?>">Masuk</a>
			</section>
			
			<form method="POST" action="<?php echo $alamat_website . 'process_register'; ?>">
				
				<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
					<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
						<label for="user_name" class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Nama Pengguna</label>
					</div>
					<div class="relative">
						<input name="nama_pengguna_anggota" id="user_name" placeholder="6-14 karakter huruf atau angka" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="text" minlength="6" maxlength="14" required>
					</div>
				</div>
				
				<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
					<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
						<label for="password" class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Kata Sandi</label>
					</div>
					<div class="relative">
						<input name="kata_sandi_anggota" id="password" placeholder="6-14 karakter huruf, angka, atau simbol" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="password" minlength="6" maxlength="14" required>
						<span class="absolute px-2 flex items-center rounded-md opacity-70 cursor-pointer right-[1px] top-[1px] bottom-[1px]" id="togglePassword">
							<svg id="eyeClosed" width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" size="20">
								<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								<circle cx="12" cy="12" r="3" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
								<path d="M5 2 19 16" stroke="var(--base)" stroke-width="2"></path>
							</svg>
							<svg id="eyeOpen" class="hidden" width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" size="20">
								<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								<circle cx="12" cy="12" r="3" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
							</svg>
						</span>
					</div>
				</div>
				
				<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
					<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
						<label for="confirm_password" class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Konfirmasi Kata Sandi</label>
					</div>
					<div class="relative">
						<input id="confirm_password" placeholder="Ulangi kata sandi di atas" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="password" required>
						<span class="absolute px-2 flex items-center rounded-md opacity-70 cursor-pointer right-[1px] top-[1px] bottom-[1px]" id="toggleConfirmPassword">
							<svg id="eyeClosedConfirm" width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" size="20">
								<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								<circle cx="12" cy="12" r="3" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
								<path d="M5 2 19 16" stroke="var(--base)" stroke-width="2"></path>
							</svg>
							<svg id="eyeOpenConfirm" class="hidden" width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" size="20">
								<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								<circle cx="12" cy="12" r="3" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
							</svg>
						</span>
					</div>
				</div>
				
				<div class="flex -mx-1">
					<div class="w-4/12 lg:w-1/4 px-1">
						<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
							<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
								<label class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Kode Negara</label>
							</div>
							<div class="relative">
								<input class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" value="+62" readonly>
								<span class="absolute px-2 flex items-center rounded-md opacity-70 cursor-pointer right-[1px] top-[1px] bottom-[1px]">
									<svg width="24" height="24" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="24">
										<path d="m11.808 14.77-3.715-4.458A.8.8 0 0 1 8.708 9h6.584a.8.8 0 0 1 .614 1.312l-3.714 4.458a.25.25 0 0 1-.384 0Z" fill="var(--base)"></path>
									</svg>
								</span>
							</div>
						</div>
					</div>
					<div class="w-8/12 lg:w-3/4 px-1">
						<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
							<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
								<label for="telepon_anggota" class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Nomor Telepon</label>
							</div>
							<div class="relative">
								<input id="telepon_anggota" placeholder="Contoh: 628123456789" name="telepon_anggota" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="number" required>
							</div>
						</div>
					</div>
				</div>
				
				<section class="my-4">
					<section class="p-3 rounded-xl cursor-pointer border border-caption relative lg:bg-background-default">
						<div class="absolute -top-2 left-2 px-1 bg-background-default">
							<p class="text-[10px] lg:text-xs opacity-70">-- Nama Bank --</p>
						</div>
						<div id="pilihKustom" class="flex items-center">
							<p id="opsiTerpilih" class="text-xs flex-auto mt-2">-- Pilih --</p>
							<div class="flex-none">
								<svg width="25" height="25" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="25">
									<path d="m11.808 14.77-3.715-4.458A.8.8 0 0 1 8.708 9h6.584a.8.8 0 0 1 .614 1.312l-3.714 4.458a.25.25 0 0 1-.384 0Z" fill="var(--base)"></path>
								</svg>
							</div>
						</div>
						<div id="lapisan" class="lg:hidden fixed z-[1000] transition duration-500 ease-in-out w-0"></div>
						<div id="menuDropdown" class="fixed lg:absolute z-[9999] left-0 right-0 bottom-0 lg:bottom-[unset] lg:mt-5 overflow-hidden bg-background-secondary rounded-tl-3xl rounded-tr-3xl lg:rounded-xl transition-all duration-500 ease-out max-h-0">
							<div class="flex justify-between px-4 pt-5 mb-3 lg:hidden">
								<button id="tutupDropdown" class="ml-auto">
									<svg width="24" height="24" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="24">
										<path d="M18 6 6 18M6 6l12 12" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
									</svg>
								</button>
							</div>
							<div class="max-h-[70vh] lg:max-h-[30vh] px-5 pb-8 lg:pb-4 overflow-auto">
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">BCA</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">BNI</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">BRI</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">BSI</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">CIMB Niaga</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">DANA</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">OVO</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">GOPAY</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">JENIUS</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">LINKAJA</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">Bank Mandiri</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">Maybank</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">MEGA</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">PANIN</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">Bank Permata</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">KOSPIN-PERMATA</p>
								</div>
								<div class="itemDropdown border-b border-separator last:border-transparent py-3 hover:lg:bg-background-tertiary transition duration-300 ease-in-out">
									<p class="text-sm px-1">SINARMAS</p>
								</div>
							</div>
						</div>
						<input type="hidden" name="bank_anggota" id="inputBankAnggota" required>
					</section>
				</section>
				
				<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
					<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
						<label for="nama_rekening_anggota" class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Nama Lengkap Sesuai Rekening Bank</label>
					</div>
					<div class="relative">
						<input id="nama_rekening_anggota" placeholder="Nama pemilik rekening" name="nama_rekening_anggota" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="text" required>
					</div>
				</div>
				
				<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
					<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
						<label for="nomor_rekening_anggota" class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Nomor Rekening Bank</label>
					</div>
					<div class="relative">
						<input id="nomor_rekening_anggota" placeholder="Nomor rekening" name="nomor_rekening_anggota" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="number" required>
					</div>
				</div>
				
				<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
					<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
						<label for="email_anggota" class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Email</label>
					</div>
					<div class="relative">
						<input id="email_anggota" name="email_anggota" placeholder="Masukkan alamat email Anda" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="email" required>
					</div>
				</div>

				<div class="relative">
					<div class="relative mt-4 lg:mt-5 rounded-xl group border border-separator focus-within:border-primary focus-within:ring-1">
						<div class="absolute left-0 -top-4 lg:-top-[14px] mx-2 z-20 bg-background-default">
							<label class="text-[10px] lg:text-xs opacity-70 px-1 bg-background-default rounded-full">Kode Referral (Opsional)</label>
						</div>
						<div class="relative">
							<?php
								$ref = isset($_GET['refferal']) ? $_GET['refferal'] : '';
							?>
							<input name="refferal_anggota" value="<?php echo htmlspecialchars($ref); ?>" placeholder="Masukkan kode referral" class="p-3 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="text">
						</div>
					</div>
				</div>
				
				<p class="mt-6 mb-8 text-xs lg:text-sm lg:text-center text-gray-400">
					Dengan mendaftar, Anda menyetujui <a target="_blank" class="text-error inline-block font-semibold" href="#">Syarat dan Ketentuan</a>
				</p>
				
				<div class="flex justify-center my-4">
					<button type="submit" aria-label="Tombol Daftar" class="bg-primary lg:hover:brightness-95 text-white rounded-xl text-sm lg:text-base font-semibold w-full lg:w-1/2 justify-center py-3">Daftar</button>
				</div>
			</form>
		</div>
	</div>
</section>

<?php include_once 'footer.php'; ?>

<style>
	.open {
	max-height: 500px;
	transition: max-height 0.5s ease-in-out;
	}

	.w-0 {
	width: 0;
	}

	.w-full {
	width: 100%;
	}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fungsionalitas Tampilkan/Sembunyikan Kata Sandi
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');
	
	const confirmPasswordInput = document.getElementById('confirm_password');
	const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
	const eyeOpenConfirm = document.getElementById('eyeOpenConfirm');
	const eyeClosedConfirm = document.getElementById('eyeClosedConfirm');

    function setupPasswordToggle(input, toggle, openIcon, closedIcon) {
        toggle.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            openIcon.classList.toggle('hidden');
            closedIcon.classList.toggle('hidden');
        });
    }

    setupPasswordToggle(passwordInput, togglePassword, eyeOpen, eyeClosed);
	setupPasswordToggle(confirmPasswordInput, toggleConfirmPassword, eyeOpenConfirm, eyeClosedConfirm);
	
	// Fungsionalitas Custom Dropdown Bank
    const pilihKustom = document.getElementById('pilihKustom');
    const menuDropdown = document.getElementById('menuDropdown');
    const lapisan = document.getElementById('lapisan');
    const opsiTerpilih = document.getElementById('opsiTerpilih');
    const inputBankAnggota = document.getElementById('inputBankAnggota');
    const tutupDropdown = document.getElementById('tutupDropdown');
    const itemDropdown = document.querySelectorAll('.itemDropdown');

    pilihKustom.addEventListener('click', function() {
        menuDropdown.classList.toggle('open');
        lapisan.classList.toggle('w-0');
        lapisan.classList.toggle('w-full');
    });

    tutupDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        menuDropdown.classList.remove('open');
        lapisan.classList.add('w-0');
        lapisan.classList.remove('w-full');
    });

    itemDropdown.forEach(item => {
        item.addEventListener('click', function() {
            const trimmedText = this.textContent.trim();
            opsiTerpilih.textContent = trimmedText;
            inputBankAnggota.value = trimmedText;
            menuDropdown.classList.remove('open');
            lapisan.classList.add('w-0');
            lapisan.classList.remove('w-full');
        });
    });

    document.addEventListener('click', function(e) {
        if (!pilihKustom.contains(e.target) && !menuDropdown.contains(e.target)) {
            menuDropdown.classList.remove('open');
            lapisan.classList.add('w-0');
            lapisan.classList.remove('w-full');
        }
    });
});
</script>