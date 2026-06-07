<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'header.php'; ?>
<section class="lg:bg-background-tertiary min-h-content">
	<div class="container mx-auto md:py-4 lg:py-8 lg:px-3">
		<div class="bg-background-default rounded-2xl p-4 lg:px-10 mx-auto md:w-4/5 lg:w-3/5">
			<section class="flex justify-center rounded-full bg-separator w-full lg:w-3/5 mx-auto overflow-hidden lg:mt-3 mb-6">
				<a class="w-1/2 justify-center opacity-70 text-center py-2" href="<?php echo $alamat_website . 'auth-register'; ?>">Daftar</a>
				<a class="w-1/2 justify-center bg-inverse rounded-full text-primary font-semibold py-2 text-center" href="<?php echo $alamat_website . 'auth-login'; ?>">Masuk</a>
			</section>
			<form method="post" action="<?php echo $alamat_website . 'process_login'; ?>" id="loginForm">
				
				<div id="username-group" class="relative mt-4 lg:mt-5 rounded-xl group border lg:bg-background-default border-caption focus-within:border-primary focus-within:ring-1">
					<div class="relative flex items-center top-0 pt-3 px-3">
						<svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M19.727 20.447c-.455-1.276-1.46-2.403-2.857-3.207C15.473 16.436 13.761 16 12 16c-1.761 0-3.473.436-4.87 1.24-1.397.804-2.402 1.931-2.857 3.207" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></path>
							<circle cx="12" cy="8" r="4" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></circle>
						</svg>
						<label for="username" class="text-xs opacity-70 pl-2 bg-background-default rounded-full">Nama Pengguna</label>
					</div>
					<div class="relative">
						<input id="username" placeholder="Masukkan nama pengguna Anda" name="nama_pengguna_anggota" class="px-3 pt-2 pb-3 focus:border-transparent focus:ring-0 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="text" value="">
					</div>
				</div>
				<p id="error-username" class="text-xs text-red-500 mt-1 ml-3 hidden">Nama Pengguna wajib diisi.</p>

				<div id="password-group" class="relative mt-4 lg:mt-5 rounded-xl group border lg:bg-background-default border-caption focus-within:border-primary focus-within:ring-1">
					<div class="relative flex items-center top-0 pt-3 px-3">
						<svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4 13c0-1.886 0-2.828.586-3.414C5.172 9 6.114 9 8 9h8c1.886 0 2.828 0 3.414.586C20 10.172 20 11.114 20 13v2c0 2.828 0 4.243-.879 5.121C18.243 21 16.828 21 14 21h-4c-2.828 0-4.243 0-5.121-.879C4 19.243 4 17.828 4 15v-2Z" stroke="var(--primary)" stroke-width="2"></path>
							<path d="M16 8V7a4 4 0 0 0-4-4v0a4 4 0 0 0-4 4v1" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></path>
							<circle cx="12" cy="15" r="2" fill="var(--primary)"></circle>
						</svg>
						<label for="password" class="text-xs opacity-70 pl-2 bg-background-default rounded-full">Kata Sandi</label>
					</div>
					<div class="relative">
						<input id="password" placeholder="Masukkan kata sandi Anda" name="kata_sandi_anggota" class="px-3 pt-2 pb-3 focus:border-transparent focus:ring-0 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" type="password" value="">
						<span class="absolute px-2 flex items-center rounded-md opacity-70 cursor-pointer right-[1px] top-[1px] bottom-[1px]" id="togglePassword">
							<svg id="eyeClosed" width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" size="20">
								<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								<circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
								<path d="M5 2 19 16" stroke="#fff" stroke-width="2"></path>
							</svg>
							<svg id="eyeOpen" class="hidden" width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" size="20">
								<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								<circle cx="12" cy="12" r="3" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
							</svg>
						</span>
					</div>
				</div>
				<p id="error-password" class="text-xs text-red-500 mt-1 ml-3 hidden">Kata Sandi wajib diisi.</p>

				<div class="flex justify-center mt-6 mb-4">
					<button type="submit" aria-label="Tombol Masuk" class="bg-primary lg:hover:brightness-95 rounded-xl text-sm lg:text-base font-semibold w-full lg:w-1/2 min-h-[44px] justify-center py-3 text-white">Masuk</button>
				</div>
				<p class="mt-10 mb-8 text-sm text-center text-gray-400">
					Belum punya akun? <a class="text-primary inline-block font-semibold" href="<?php echo $alamat_website . 'auth-register'; ?>">Daftar Sekarang</a>
				</p>
			</form>
		</div>
	</div>
</section>

<?php include_once 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const usernameGroup = document.getElementById('username-group');
    const passwordGroup = document.getElementById('password-group');
    const errorUsername = document.getElementById('error-username');
    const errorPassword = document.getElementById('error-password');

    // Fungsi untuk menampilkan/menyembunyikan indikator error
    function toggleError(inputGroup, errorTextElement, isError) {
        if (isError) {
            // Tampilkan pesan error
            errorTextElement.classList.remove('hidden');
            // Tambahkan indikator visual (border merah)
            inputGroup.classList.add('border-red-500', 'ring-red-500');
            // Hapus fokus primary saat ada error
            inputGroup.classList.remove('focus-within:border-primary');
        } else {
            // Sembunyikan pesan error
            errorTextElement.classList.add('hidden');
            // Hapus indikator visual, biarkan fokus primary bekerja
            inputGroup.classList.remove('border-red-500', 'ring-red-500');
            inputGroup.classList.add('focus-within:border-primary');
        }
    }

    // Listener saat form disubmit
    form.addEventListener('submit', function(event) {
        let isValid = true;

        // 1. Cek Username
        if (usernameInput.value.trim() === '') {
            toggleError(usernameGroup, errorUsername, true);
            isValid = false;
        } else {
            toggleError(usernameGroup, errorUsername, false);
        }

        // 2. Cek Password
        if (passwordInput.value.trim() === '') {
            toggleError(passwordGroup, errorPassword, true);
            isValid = false;
        } else {
            toggleError(passwordGroup, errorPassword, false);
        }

        if (!isValid) {
            event.preventDefault(); // Mencegah form dikirim jika ada error
        }
    });
    
    // Opsional: Hapus indikator error saat pengguna mulai mengetik
    usernameInput.addEventListener('input', () => toggleError(usernameGroup, errorUsername, false));
    passwordInput.addEventListener('input', () => toggleError(passwordGroup, errorPassword, false));

    
    // --- Fungsionalitas Tampilkan/Sembunyikan Kata Sandi (Dipertahankan) ---
    const togglePassword = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeOpen.classList.toggle('hidden');
        eyeClosed.classList.toggle('hidden');
    });

});
</script>