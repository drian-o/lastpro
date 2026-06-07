<?php include_once 'header.php'; ?>

<?php
// --- Logic Penentuan Provider Awal ---
// Tentukan Provider Aktif Awal dari URL atau Default
$default_provider = 'pragmatiplay_slot'; 
$provider_slug = isset($_GET['provider']) ? $_GET['provider'] : $default_provider;

// --- Daftar CUID yang Diprioritaskan ---
$priority_cuids = [70, 39, 36, 32]; 
$priority_list = implode(',', $priority_cuids); 
$query_provider_list = "
    SELECT providerid, providername, providerimage, status 
    FROM tb_provider 
    WHERE type = 'SL' AND status = 1 
    ORDER BY 
        CASE 
            WHEN cuid IN ({$priority_list}) THEN 0  -- Kelompok Prioritas
            ELSE 1                               -- Kelompok Non-Prioritas
        END ASC, 
        FIELD(cuid, {$priority_list}),           -- Urutan dalam Kelompok Prioritas
        cuid ASC                                 -- Urutan dalam Kelompok Non-Prioritas
";

$result_provider_list = mysqli_query($koneksi, $query_provider_list);

// Ambil Provider Name untuk display header
$provider_name_display = 'Slot Games';
$provider_data = mysqli_query($koneksi, "SELECT providername FROM tb_provider WHERE providerid = '{$provider_slug}' LIMIT 1");
if (mysqli_num_rows($provider_data) > 0) {
    $provider_name_display = mysqli_fetch_assoc($provider_data)['providername'];
}
?>

<section class="container mx-auto p-3">
    <nav class="flex mb-1 lg:mb-2">
        <ol class="flex items-center pb-1 overflow-x-scroll whitespace-nowrap opacity-scroll">
            <li class="inline-flex items-end pr-1">
                <a class="text-xs border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out undefined" href="home">Beranda</a>
			</li>
            <li class="inline-flex items-end pr-1 group">
                <div class="flex items-center">
                    <svg width="17" height="17" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="17">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
					</svg><a class="text-xs pl-1 border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out group-last:text-primary undefined" href="slot">Slot</a>
				</div>
			</li>
		</ol>
	</nav>
    <div class="lg:flex w-full">
        <div id="provider-list-container" class="w-full lg:w-80 lg:pr-3 grid lg:inline-block grid-rows-2 grid-flow-col gap-x-2 md:gap-x-2 gap-y-3 md:gap-y-4 py-2 lg:pt-0 lg:mb-6 overflow-x-scroll lg:overflow-x-hidden overflow-y-hidden whitespace-nowrap lg:whitespace-normal opacity-scroll">
			
			<?php
            if (mysqli_num_rows($result_provider_list) > 0) {
                while ($row_provider = mysqli_fetch_assoc($result_provider_list)) {
                    $nama_provider = $row_provider['providername'];
                    $gambar_provider = $row_provider['providerimage'];
                    $status_provider = $row_provider['status'];
                    $provider_id = $row_provider['providerid'];
                ?>
                <a 
                    href="#" 
                    data-provider-id="<?php echo urlencode($provider_id); ?>" 
                    class="provider-link w-20 md:w-24 lg:w-full max-h-[70px] md:max-h-28 min-w-max py-2 lg:px-3 md:py-4 lg:py-3 lg:mb-3 inline-block lg:flex lg:items-center rounded-md lg:overflow-hidden transition-all duration-300 ease-in-out bg-background-secondary lg:hover:bg-background-tertiary relative cursor-pointer group <?php echo ($provider_slug == $provider_id) ? 'active-provider border border-primary lg:bg-background-tertiary' : ''; ?>"
                >
                    <?php if (in_array($provider_id, ['PR', 'PG', 'HB'])) { ?>
                        <div class="absolute h-[15px] lg:h-[20px] right-3 lg:right-0 left-3 lg:left-auto -top-2 lg:top-4 lg:bottom-4 overflow-hidden">
                            <span class="flex justify-center px-2 lg:pl-5 mb-1 text-center rounded-full lg:rounded-none transition-all duration-300 ease-in-out" style="background-color: rgb(59, 165, 244);">
                                <div>
                                    <span class="block uppercase text-[10px] md:text-xs font-semibold h-[15px] lg:h-[20px] lg:leading-[20px] text-black">
                                        PROMO
                                    </span>
                                </div>
                                <span class="hidden lg:block absolute -left-2 rotate-45 top-0 bottom-0 h-5 w-5 transition-all duration-300 ease-in-out bg-background-secondary group-hover:bg-background-tertiary"></span>
                            </span>
                        </div>
                    <?php } ?>
                    <figure class="w-7 md:w-9 lg:w-auto md:h-auto lg:h-7 mx-auto lg:mx-0">
                        <img alt="<?php echo $nama_provider; ?>" loading="lazy" width="0" height="0" decoding="async" data-nimg="1" class="rounded-lg w-full h-full mt-1 lg:mt-0" src="<?php echo $gambar_provider; ?>" style="color: transparent;">
                    </figure>
                    <p class="text-[10px] lg:text-sm mt-1 lg:mt-0 text-center lg:pl-2"><?php echo $nama_provider; ?></p>
                </a>
                <?php
                }
            } else {
                echo '<p>Tidak ada provider yang tersedia saat ini.</p>';
            }
            ?>
		</div>
		
        <div class="lg:inline-block lg:w-full">
            
            <div class="hidden lg:flex justify-between pl-3 mb-4">
                <div class="flex items-center">
                    <p class="text-xs" id="provider-name-display"><?php echo $provider_name_display; ?></p>
				</div>
                
                <div class="relative flex items-center w-96">
                    <span class="absolute left-1 cursor-pointer bg-background-secondary px-2">
                        <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="6" stroke="var(--base)"></circle>
                            <path d="M11 8a3 3 0 0 0-3 3M20 20l-3-3" stroke="var(--base)" stroke-linecap="round"></path>
						</svg>
					</span>
                    <input 
                        type="text" 
                        id="search-input" 
                        placeholder="Cari Game..." 
                        class="bg-background-secondary w-full text-sm rounded-md py-2 pl-10 pr-2 mx-1 focus:outline-none"
                    >
				</div>
			</div>
            
            <div class="lg:hidden mb-4">
                <div class="relative flex items-center w-full">
                    <span class="absolute left-1 cursor-pointer bg-background-secondary px-2">
                        <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="6" stroke="var(--base)"></circle>
                            <path d="M11 8a3 3 0 0 0-3 3M20 20l-3-3" stroke="var(--base)" stroke-linecap="round"></path>
						</svg>
					</span>
                    <input 
                        type="text" 
                        id="search-input-mobile" 
                        placeholder="Cari Game..." 
                        class="bg-background-secondary w-full text-sm rounded-md py-2 pl-10 pr-2 mx-1 focus:outline-none"
                    >
				</div>
            </div>

            <div class="lg:pl-3">
                <div id="games-container" class="grid grid-cols-4 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4 lg:gap-5">
                    </div>
                
                <div id="loader" class="text-center p-8 hidden">
                    <p class="text-primary font-semibold">Memuat game, harap tunggu...</p>
                </div>

                <div id="load-more-wrapper" class="text-center mt-6 hidden">
                    <button id="load-more-btn" class="bg-primary hover:opacity-90 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        Muat Lebih Banyak Game...
                    </button>
                </div>
            </div>
            
		</div>
	</div>
</section>

<?php include_once 'footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. VARIABEL GLOBAL & KONFIGURASI ---
        const AJAX_URL = 'ajax/loadgame/fetch_games.php';
        const GAMES_CONTAINER = document.getElementById('games-container');
        const LOADER = document.getElementById('loader');
        const LOAD_MORE_BTN = document.getElementById('load-more-btn');
        const LOAD_MORE_WRAPPER = document.getElementById('load-more-wrapper');
        const PROVIDER_LINKS = document.querySelectorAll('.provider-link');
        const SEARCH_INPUT_DESKTOP = document.getElementById('search-input');
        const SEARCH_INPUT_MOBILE = document.getElementById('search-input-mobile');
        const PROVIDER_NAME_DISPLAY = document.getElementById('provider-name-display');

        let currentPage = 1;
        // Ambil nilai provider awal dari URL (untuk deep linking/refresh)
        const urlParams = new URLSearchParams(window.location.search);
        let currentProvider = urlParams.get('provider') || '<?php echo $provider_slug; ?>'; 
        let currentSearch = urlParams.get('search') || '';
        let isLoading = false;
        
        // Sinkronkan input field dengan URL parameter awal
        SEARCH_INPUT_DESKTOP.value = currentSearch;
        SEARCH_INPUT_MOBILE.value = currentSearch;

        // --- Debounce Function ---
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
        
        // --- Fungsi untuk Mengatur Kelas Provider Aktif ---
        function setActiveProviderClass(providerId) {
            document.querySelectorAll('.provider-link').forEach(link => {
                const isActive = link.getAttribute('data-provider-id') === providerId;
                link.classList.toggle('active-provider', isActive);
                link.classList.toggle('border', isActive);
                link.classList.toggle('border-primary', isActive);
                link.classList.toggle('lg:bg-background-tertiary', isActive);
                
                if (isActive) {
                     // Update header display name
                    const providerName = link.querySelector('p').textContent;
                    PROVIDER_NAME_DISPLAY.textContent = providerName;
                }
            });
        }
        
        // --- 2. FUNGSI UTAMA: FETCH GAMES VIA AJAX ---
        function fetchGames(provider, page, search, isAppend = false) {
            if (isLoading) return;
            
            isLoading = true;
            LOADER.classList.remove('hidden');
            LOAD_MORE_WRAPPER.classList.add('hidden');

            if (!isAppend) {
                GAMES_CONTAINER.innerHTML = ''; 
                // Hanya scroll ke atas jika bukan load more (halaman baru/search baru)
//                window.scrollTo({ top: GAMES_CONTAINER.offsetTop, behavior: 'smooth' }); 
            }

            const formData = new FormData();
            formData.append('provider', provider);
            formData.append('page', page);
            formData.append('search', search);

            fetch(AJAX_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                GAMES_CONTAINER.insertAdjacentHTML('beforeend', html); // Selalu tambahkan ke akhir
                
                const indicator = document.getElementById('load-more-indicator');
                const hasNextPage = indicator ? indicator.getAttribute('data-has-next') === 'true' : false;
                if (indicator) indicator.remove(); 
                
                if (hasNextPage) {
                    LOAD_MORE_WRAPPER.classList.remove('hidden');
                } else {
                    LOAD_MORE_WRAPPER.classList.add('hidden');
                }

                // Update URL di browser (History API)
                const newUrl = window.location.pathname + 
                               '?provider=' + currentProvider + 
                               (currentSearch ? '&search=' + currentSearch : '');
                history.pushState(null, '', newUrl);
                
            })
            .catch(error => {
                console.error('Fetch error:', error);
                GAMES_CONTAINER.innerHTML = '<p class="p-4 text-center text-red-500 col-span-full">Gagal memuat data game. Silakan coba lagi.</p>';
            })
            .finally(() => {
                isLoading = false;
                LOADER.classList.add('hidden');
            });
        }

        // --- 3. EVENT HANDLERS ---
        
        // A. Load More Button
        LOAD_MORE_BTN.addEventListener('click', function() {
            currentPage++;
            fetchGames(currentProvider, currentPage, currentSearch, true);
        });

        // B. Provider Link Click
        PROVIDER_LINKS.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const newProviderId = this.getAttribute('data-provider-id');
                if (newProviderId === currentProvider && currentSearch === '') return; // Cek jika tidak ada perubahan

                currentProvider = newProviderId;
                currentPage = 1;
                
                setActiveProviderClass(currentProvider);
                fetchGames(currentProvider, currentPage, currentSearch, false); 
            });
        });

        // C. Live Search (menggunakan Debounce)
        const handleSearch = debounce(function(e) {
            const searchText = e.target.value.trim();
            
            // Logika filter minimal 3 karakter
            if (searchText.length >= 3 || searchText.length === 0) {
                // Sinkronkan kedua input field
                SEARCH_INPUT_DESKTOP.value = searchText;
                SEARCH_INPUT_MOBILE.value = searchText; 
                
                if (searchText === currentSearch) return; // Cek jika tidak ada perubahan
                
                currentSearch = searchText;
                currentPage = 1;
                fetchGames(currentProvider, currentPage, currentSearch, false);
            }
        }, 600); // Penundaan 600ms untuk menyeimbangkan beban

        SEARCH_INPUT_DESKTOP.addEventListener('input', handleSearch);
        SEARCH_INPUT_MOBILE.addEventListener('input', handleSearch);


        // --- 4. MUAT GAME PERTAMA SAAT HALAMAN DIMUAT ---
        setActiveProviderClass(currentProvider);
        fetchGames(currentProvider, currentPage, currentSearch, false);
    });
</script>