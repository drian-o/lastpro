<?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) : ?>
    <section class="container mx-auto pb-10 lg:pb-20 flex flex-wrap">
        <div class="w-full px-3 mt-3 order-2">
            <div class="bg-background-secondary h-full lg:h-auto rounded-xl pt-4 lg:py-4 overflow-hidden relative flex flex-wrap">
                <div class="w-7/12 lg:w-[45%] lg:px-8 items-center lg:pt-3 flex flex-wrap px-4">
                    <article class="w-full flex items-center mb-1 lg:mb-3">
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.879 3.879C2 4.757 2 6.172 2 9v6c0 2.828 0 4.243.879 5.121C3.757 21 5.172 21 8 21h10c.93 0 1.395 0 1.776-.102a3 3 0 0 0 2.122-2.122C22 18.395 22 17.93 22 17h-6a3 3 0 1 1 0-6h6V9c0-2.828 0-4.243-.879-5.121C20.243 3 18.828 3 16 3H8c-2.828 0-4.243 0-5.121.879ZM7 7a1 1 0 0 0 0 2h3a1 1 0 1 0 0-2H7Z" fill="var(--primary)"></path>
                            <path d="M17 14h-1" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                        <span class="text-xs lg:text-sm text-caption px-2">Account Balance</span>
                        <button>
                            <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="3.5" stroke="var(--caption)"></circle>
                                <path d="M20.188 10.934c.388.472.582.707.582 1.066 0 .359-.194.594-.582 1.066C18.768 14.79 15.636 18 12 18c-3.636 0-6.768-3.21-8.188-4.934-.388-.472-.582-.707-.582-1.066 0-.359.194-.594.582-1.066C5.232 9.21 8.364 6 12 6c3.636 0 6.768 3.21 8.188 4.934Z" stroke="var(--caption)"></path>
                            </svg>
                        </button>
                    </article>
                    <div class="w-full flex lg:gap-x-5">
                        <div class="lg:w-2/3 flex items-center">
                            <section class="w-full flex items-center h-7">
                                <span class="text-sm lg:text-xl font-semibold">IDR&nbsp;<?php echo number_format($_SESSION['saldo_anggota'], 0, ',', '.'); ?></span>
                                <button class="rounded-full bg-background-default cursor-pointer rotate-270 w-7 h-7 ml-2 items-center justify-center flex" onclick="window.location.reload();">
                                    <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="m10 19-.707-.707-.707.707.707.707L10 19Zm3.293-4.707-4 4 1.414 1.414 4-4-1.414-1.414Zm-4 5.414 4 4 1.414-1.414-4-4-1.414 1.414Z" fill="var(--caption)"></path>
                                        <path d="M5.938 15.5A7 7 0 1 1 12 19" stroke="var(--caption)" stroke-width="2" stroke-linecap="round"></path>
                                    </svg>
                                </button>
                            </section>
                        </div>
                    </div>
                </div>
                <div class="w-5/12 lg:w-[20%] lg:pt-3 px-4 lg:px-8 border-l border-separator lg:order-last">
                    <div class="w-full flex items-center mb-2">
                        <img alt="VIP Icon" loading="lazy" width="0" height="0" decoding="async" data-nimg="1" class="w-6 lg:w-8 h-auto" src="assets/img/pemainbaru1.png" style="color: transparent;">
                        <a class="flex items-center ml-2 lg:ml-4 lg:font-medium">
                            <span class="text-xs lg:text-sm lg:mr-3 text-caption border-b border-transparent hover:lg:border-primary transition-all duration-300 ease-out">Level</span>
                            <svg width="18" height="18" viewbox="0 0 24 24" fill="var(--caption)" xmlns="http://www.w3.org/2000/svg" size="18">
                                <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--caption)"></path>
                            </svg>
                        </a>
                    </div>
                    <p class="font-semibold mt-1 lg:mt-4">Pemain Baru</p>
                </div>
                <section class="flex flex-wrap w-full lg:w-[35%] py-3 lg:pt-3 lg:pb-0 px-4 lg:px-8 mt-4 mb-2 lg:my-0 border-l border-transparent lg:border-l-separator border-t border-t-separator lg:border-t-transparent">
                    <article class="w-full flex flex-wrap justify-between lg:items-center">
                        <div class="flex items-center lg:mb-3">
                            <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                                <path d="M5 12H4v8a2 2 0 0 0 2 2h5V12H5Zm13 0h-5v10h5a2 2 0 0 0 2-2v-8h-2Zm.791-5c.147-.486.217-.992.209-1.5C19 3.57 17.43 2 15.5 2c-1.622 0-2.705 1.482-3.404 3.085C11.407 3.57 10.269 2 8.5 2 6.57 2 5 3.57 5 5.5c0 .596.079 1.089.209 1.5H2v4h9V9h2v2h9V7h-3.209ZM7 5.5C7 4.673 7.673 4 8.5 4c.888 0 1.714 1.525 2.198 3H8c-.374 0-1 0-1-1.5ZM15.5 4c.827 0 1.5.673 1.5 1.5C17 7 16.374 7 16 7h-2.477c.51-1.576 1.251-3 1.977-3Z" fill="var(--primary)"></path>
                            </svg>
                            <p class="text-xs lg:text-sm text-caption pl-2">Bonus Balance</p>
                        </div>
                        <div class="flex w-full">
                            <div class="w-2/3 lg:w-[70%] flex flex-wrap">
                                <p class="flex items-center text-sm lg:text-xl mt-1 lg:mt-0 w-full">IDR&nbsp;0</p>
                            </div>
                            <div class="w-1/3 lg:w-[30%] flex items-center justify-end">
                                <button class="px-5 py-1 lg:py-2 text-sm lg:text-base justify-center font-semibold rounded-lg w-full h-8 lg:h-auto border border-white opacity-50 cursor-no-drop hover:lg:brightness-[0.9]">Claim</button>
                            </div>
                        </div>
                    </article>
                </section>
                <section class="fixed z-[9999] flex items-center justify-center overflow-hidden transition duration-300 ease-in-out w-0 ">
                    <div class="bg-background-secondary rounded-lg">
                        <div class="flex justify-between px-4 lg:px-7 pt-5 mb-3">
                            <button class="ml-auto">
                                <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="24">
                                    <path d="M18 6 6 18M6 6l12 12" stroke="var(--base)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="max-h-[calc(100vh-60px-130px)] lg:max-h-[calc(100vh-100px-130px)] lg:max-w-2xl px-4 lg:px-7 pb-3 overflow-auto">
                            <p class="mt-4 font-light text-center">
                                Claim <span class="font-medium">-</span> Bonus balance to main balance?
                            </p>
                        </div>
                        <div class="flex justify-center px-4 lg:px-20 pt-4 pb-8 gap-3">
                            <button class="bg-primary justify-center text-sm w-24 py-2 rounded-lg transition-all duration-200 ease-in-out hover:lg:brightness-90">Yes</button>
                            <button class="text-sm justify-center w-24 py-2 rounded-lg border border-primary text-primary transition-all duration-200 ease-in-out hover:lg:brightness-75">No</button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    <?php else : ?>
        <section class="container mx-auto pb-10 lg:pb-20 flex flex-wrap">
        <?php endif; ?>

        <div class="w-full lg:px-3 mt-4 order-3">
            <div aria-label="listbox" class="carousel-root slide homebanner">
                <div class="carousel carousel-slider" style="width:100%">
                    <ul class="control-dots dots">
                        <?php
                        // Ambil data promosi dari database
                        $promosi = mysqli_query($koneksi, "SELECT * FROM promosi");
                        $slideIndex = 0;
                        $promosi_data = [];
                        while ($data_promosi = mysqli_fetch_array($promosi)) {
                            $gambar_promosi = $data_promosi['gambar_promosi'];
                            $judul_promosi = $data_promosi['judul_promosi'];
                            if ($gambar_promosi && $judul_promosi) {
                                $promosi_data[] = $data_promosi;
                        ?>
                                <li aria-label="slide item <?php echo $slideIndex; ?>" role="listitem" class="dot w-2 lg:w-3 h-2 lg:h-3 ml-2 lg:ml-3 rounded-full inline-block lg:hover:opacity-80 border-[0.5px] border-base" style="background-color: var(--secondaryBackground)" data-index="<?php echo $slideIndex; ?>"></li>
                        <?php
                                $slideIndex++;
                            }
                        }
                        ?>
                    </ul>
                    <button type="button" aria-label="previous slide / item" id="prevSlide" class="control-arrow control-prev"></button>
                    <div class="slider-wrapper axis-horizontal">
                        <ul class="slider animated" style="-webkit-transform:translate3d(-92.5%,0,0);-ms-transform:translate3d(-92.5%,0,0);-o-transform:translate3d(-92.5%,0,0);transform:translate3d(-92.5%,0,0);-webkit-transition-duration:500ms;-moz-transition-duration:500ms;-o-transition-duration:500ms;transition-duration:500ms;-ms-transition-duration:500ms">
                            <?php
                            foreach ($promosi_data as $data_promosi) {
                                $gambar_promosi = $data_promosi['gambar_promosi'];
                                $judul_promosi = $data_promosi['judul_promosi'];
                            ?>
                                <li class="slide" style="min-width: 95%">
                                    <a target="_blank" class="w-full px-1 lg:px-2" href="">
                                        <figure class="h-[calc(100vw/2.67)] lg:h-[420px] w-full rounded-lg overflow-hidden">
                                            <img alt="<?php echo htmlspecialchars($judul_promosi, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" width="0" height="0" decoding="async" data-nimg="1" class="w-full h-full object-cover object-center" style="color: transparent;" src="<?php echo htmlspecialchars($alamat_website . 'assets/img/' . $gambar_promosi, ENT_QUOTES, 'UTF-8'); ?>">
                                        </figure>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                            <li class="slide" style="min-width: 95%">
                                <a target="_blank" class="w-full px-1 lg:px-2" href="">
                                    <figure class="h-[calc(100vw/2.67)] lg:h-[420px] w-full rounded-lg overflow-hidden">
                                        <img alt="<?php echo htmlspecialchars($judul_promosi, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" width="0" height="0" decoding="async" data-nimg="1" class="w-full h-full object-cover object-center" style="color: transparent;" src="<?php echo htmlspecialchars($alamat_website . 'assets/img/' . $gambar_promosi, ENT_QUOTES, 'UTF-8'); ?>">
                                    </figure>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <button type="button" aria-label="next slide / item" id="nextSlide" class="control-arrow control-next"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slider = document.querySelector('.slider'); // Memilih elemen slider
                const slides = document.querySelectorAll('.slide'); // Mengambil semua elemen slide
                let currentIndex = 0; // Indeks slide saat ini
                const slideIncrement = 95; // Penambahan nilai setiap slide dalam persentase
                const initialOffset = -92.5; // Offset awal dalam persentase
                const maxSlides = slides.length - 4; // Batas jumlah slide sebelum kembali ke awal (totalSlides - 1)

                const dots = document.querySelectorAll('.dot');

                function updateSlide() {
                    // Menghitung offset untuk slide saat ini
                    let offset = initialOffset + (-currentIndex * slideIncrement);

                    // Jika sudah mencapai slide ke-3, kembali ke posisi awal
                    if (currentIndex > maxSlides) {
                        currentIndex = 0;
                        offset = initialOffset;
                    }

                    slider.style.transform = `translate3d(${offset}%, 0px, 0px)`; // Menerapkan transformasi
                    slider.style.transitionDuration = '500ms'; // Durasi transisi

                    // Memperbarui dots yang aktif
                    dots.forEach(dot => dot.classList.remove('active'));
                    if (dots[currentIndex]) {
                        dots[currentIndex].classList.add('active');
                    }
                }

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % (maxSlides + 1); // Pindah ke slide berikutnya, kembali ke awal setelah slide terakhir
                    updateSlide();
                }

                function prevSlide() {
                    currentIndex = (currentIndex - 1 + (maxSlides + 1)) % (maxSlides + 1); // Pindah ke slide sebelumnya, kembali ke slide terakhir setelah slide pertama
                    updateSlide();
                }

                function startAutoSlide() {
                    setInterval(nextSlide, 5000); // Set interval untuk perubahan slide otomatis setiap 5 detik
                }

                // Kontrol manual
                const nextButton = document.getElementById('nextSlide'); // Tombol untuk slide berikutnya
                const prevButton = document.getElementById('prevSlide'); // Tombol untuk slide sebelumnya

                if (nextButton) {
                    nextButton.addEventListener('click', nextSlide); // Menambahkan event listener untuk tombol berikutnya
                }

                if (prevButton) {
                    prevButton.addEventListener('click', prevSlide); // Menambahkan event listener untuk tombol sebelumnya
                }

                // Inisialisasi slide pertama
                updateSlide();

                // Membuat dots navigasi
                dots.forEach((dot, index) => {
                    dot.dataset.index = index; // Menyimpan indeks pada atribut data
                    dot.addEventListener('click', function() {
                        currentIndex = parseInt(dot.dataset.index);
                        updateSlide();
                    });
                });

                // Memulai slide otomatis
                startAutoSlide();

                // Slide manual dengan swap (geser)
                let startX = 0;
                let endX = 0;

                slider.addEventListener('touchstart', function(event) {
                    startX = event.touches[0].clientX; // Mengambil posisi awal sentuhan
                });

                slider.addEventListener('touchend', function(event) {
                    endX = event.changedTouches[0].clientX; // Mengambil posisi akhir sentuhan
                    handleSwipe();
                });

                slider.addEventListener('mousedown', function(event) {
                    startX = event.clientX; // Mengambil posisi awal mouse
                });

                slider.addEventListener('mouseup', function(event) {
                    endX = event.clientX; // Mengambil posisi akhir mouse
                    handleSwipe();
                });

                function handleSwipe() {
                    const distance = endX - startX;
                    if (Math.abs(distance) > 50) { // Ambang batas untuk swipe
                        if (distance < 0) {
                            nextSlide(); // Geser ke kiri berarti slide berikutnya
                        } else {
                            prevSlide(); // Geser ke kanan berarti slide sebelumnya
                        }
                    }
                }
            });
        </script>

        <style>
            .control-dots li {
                width: 10px;
                height: 10px;
                background-color: var(--secondaryBackground);
                /* Warna dot default */
                margin: 0 5px;
                border-radius: 50%;
                cursor: pointer;
            }

            .control-dots li.active {
                background-color: red !important;
                /* Warna dot aktif menjadi merah */
            }

            .carousel .control-arrow,
            .carousel.carousel-slider .control-arrow {
                background: none;
                border: 0;
                cursor: pointer;
                filter: alpha(opacity=40);
                font-size: 32px;
                opacity: .4;
                position: absolute;
                top: 20px;
                transition: all .25s ease-in;
                z-index: 2;
            }

            .carousel .control-arrow:focus,
            .carousel .control-arrow:hover {
                filter: alpha(opacity=100);
                opacity: 1;
            }

            .carousel .control-arrow:before,
            .carousel.carousel-slider .control-arrow:before {
                border-bottom: 8px solid transparent;
                border-top: 8px solid transparent;
                content: "";
                display: inline-block;
                margin: 0 5px;
            }

            .carousel .control-disabled.control-arrow {
                cursor: inherit;
                display: none;
                filter: alpha(opacity=0);
                opacity: 0;
            }

            .carousel .control-prev.control-arrow {
                left: 0;
            }

            .carousel .control-prev.control-arrow:before {
                border-right: 8px solid #fff;
            }

            .carousel .control-next.control-arrow {
                right: 0;
            }

            .carousel .control-next.control-arrow:before {
                border-left: 8px solid #fff;
            }

            .carousel-root {
                outline: none;
            }

            .carousel {
                position: relative;
                width: 100%;
            }

            .carousel * {
                box-sizing: border-box;
            }

            .carousel img {
                display: inline-block;
                pointer-events: none;
                width: 100%;
            }

            .carousel .carousel {
                position: relative;
            }

            .carousel .control-arrow {
                background: none;
                border: 0;
                font-size: 18px;
                margin-top: -13px;
                outline: 0;
                top: 50%;
            }

            .carousel .thumbs-wrapper {
                margin: 20px;
                overflow: hidden;
            }

            .carousel .thumbs {
                list-style: none;
                position: relative;
                transform: translateZ(95);
                transition: all .15s ease-in;
                white-space: nowrap;
            }

            .carousel .thumb {
                border: 3px solid #fff;
                display: inline-block;
                margin-right: 6px;
                overflow: hidden;
                padding: 2px;
                transition: border .15s ease-in;
                white-space: nowrap;
            }

            .carousel .thumb:focus {
                border: 3px solid #ccc;
                outline: none;
            }

            .carousel .thumb.selected,
            .carousel .thumb:hover {
                border: 3px solid #333;
            }

            .carousel .thumb img {
                vertical-align: top;
            }

            .carousel.carousel-slider {
                margin: 0;
                overflow: hidden;
                position: relative;
            }

            .carousel.carousel-slider .control-arrow {
                bottom: 0;
                color: #fff;
                font-size: 26px;
                margin-top: 0;
                padding: 5px;
                top: 0;
            }

            .carousel.carousel-slider .control-arrow:hover {
                background: rgba(0, 0, 0, .2);
            }

            .carousel .slider-wrapper {
                margin: auto;
                overflow: hidden;
                transition: height .15s ease-in;
                width: 100%;
            }

            .carousel .slider-wrapper.axis-horizontal .slider {
                -ms-box-orient: horizontal;
                display: -moz-flex;
                display: flex;
            }

            .carousel .slider-wrapper.axis-horizontal .slider .slide {
                flex-direction: column;
                flex-flow: column;
            }

            .carousel .slider-wrapper.axis-vertical {
                -ms-box-orient: horizontal;
                display: -moz-flex;
                display: flex;
            }

            .carousel .slider-wrapper.axis-vertical .slider {
                flex-direction: column;
            }

            .carousel .slider {
                list-style: none;
                margin: 0;
                padding: 0;
                position: relative;
                width: 100%;
            }

            .carousel .slider.animated {
                transition: all .35s ease-in-out;
            }

            .carousel .slide {
                margin: 0;
                min-width: 100%;
                position: relative;
                text-align: center;
            }

            .carousel .slide img {
                border: 0;
                vertical-align: top;
                width: 100%;
            }

            .carousel .slide iframe {
                border: 0;
                display: inline-block;
                margin: 0 40px 40px;
                width: calc(100% - 80px);
            }

            .carousel .slide .legend {
                background: #000;
                border-radius: 10px;
                bottom: 40px;
                color: #fff;
                font-size: 12px;
                left: 50%;
                margin-left: -45%;
                opacity: .25;
                padding: 10px;
                position: absolute;
                text-align: center;
                transition: all .5s ease-in-out;
                transition: opacity .35s ease-in-out;
                width: 90%;
            }

            .carousel .control-dots {
                bottom: 0;
                margin: 10px 0;
                padding: 0;
                position: absolute;
                text-align: center;
                width: 100%;
                z-index: 1;
            }

            @media (min-width: 960px) {
                .carousel .control-dots {
                    bottom: 0;
                }
            }

            .carousel .control-dots .dot {
                background: #fff;
                border-radius: 50%;
                box-shadow: 1px 1px 2px rgba(0, 0, 0, .9);
                cursor: pointer;
                display: inline-block;
                filter: alpha(opacity=30);
                height: 8px;
                margin: 0 8px;
                opacity: .3;
                transition: opacity .25s ease-in;
                width: 8px;
            }

            .carousel .control-dots .dot.selected,
            .carousel .control-dots .dot:hover {
                filter: alpha(opacity=100);
                opacity: 1;
            }

            .carousel .carousel-status {
                color: #fff;
                font-size: 10px;
                padding: 5px;
                position: absolute;
                right: 0;
                text-shadow: 1px 1px 1px rgba(0, 0, 0, .9);
                top: 0;
            }

            .carousel:hover .slide .legend {
                opacity: 1;
            }
        </style>