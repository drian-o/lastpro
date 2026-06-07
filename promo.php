<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'header.php';
$saldo_anggota = isset($_SESSION['saldo_anggota']) ? $_SESSION['saldo_anggota'] : 0;
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Query untuk mengambil data promosi
$promosi_query = mysqli_query($koneksi, "SELECT * FROM promosi");
if (!$promosi_query) {
    die('Error: ' . mysqli_error($koneksi));
}
?>

<section class="container mx-auto pt-3 pb-10 lg:pb-12 px-3">
    <nav class="flex mb-1 lg:mb-2">
        <ol class="flex items-center pb-1 overflow-x-scroll whitespace-nowrap opacity-scroll">
            <li class="inline-flex items-end pr-1">
                <a class="text-xs border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out undefined" href="<?php echo $alamat_website . 'home'; ?>">Home</a>
            </li>
            <li class="inline-flex items-end pr-1 group">
                <div class="flex items-center">
                    <svg width="17" height="17" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="17">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                    </svg><a class="text-xs pl-1 border-b border-transparent hover:lg:border-primary transition-all duration-200 ease-in-out group-last:text-primary undefined" href="<?php echo $alamat_website . 'promo'; ?>">Promo</a>
                </div>
            </li>
        </ol>
    </nav>
    <h3 class="md:text-lg font-medium w-full">Available Promo</h3>
    <div class="flex flex-wrap -mx-2 lg:-mx-3 mt-4">
        <?php
        // Mulai loop untuk menampilkan data promosi
        while ($data_promosi = mysqli_fetch_array($promosi_query)) {
            $id_promosi = $data_promosi['id_promosi'];
            $gambar_promosi = $data_promosi['gambar_promosi'];
            $judul_promosi = $data_promosi['judul_promosi'];
            $kategori_promosi = $data_promosi['kategori_promosi'];
            $deskripsi_promosi = $data_promosi['deskripsi_promosi'];
            $link_kategori_promosi = strtolower(str_replace(' ', ' ', $kategori_promosi));
        ?>
            <div class="w-full md:w-1/2 lg:w-1/3 px-[6px] lg:px-3 mb-3 lg:mb-6">
                <a class="gap-x-3 lg:flex-wrap bg-background-tertiary px-3 lg:px-4 py-2 lg:pt-4 lg:pb-2 rounded-md relative" href="<?php echo $alamat_website; ?>details-promo?id=<?php echo $id_promosi; ?>">
                    <figure class="w-2/5 lg:w-full ">
                        <img alt="Promo <?php echo strtoupper($judul_promosi); ?>" loading="lazy" width="0" height="0" decoding="async" data-nimg="<?php echo $id_promosi; ?>" class="rounded-md w-full min-h-[64px] lg:min-h-[128px] max-h-16 lg:max-h-32 object-cover object-center" src="<?php echo $alamat_website . 'assets/img/' . $gambar_promosi; ?>" style="color: transparent;">
                    </figure>
                    <article class="w-3/5 lg:w-full lg:my-2 h-16 lg:h-auto pt-4 lg:pt-0 opacity-100 relative">
                        <span class="font-semibold text-primary px-2 absolute text-[8px] lg:text-[10px] lg:relative top-0">
                            <span class="bg-primary opacity-20 absolute top-0 bottom-0 left-0 right-0 rounded-full"></span>Ongoing Promo
                        </span>
                        <p title="<?php echo strtoupper($judul_promosi); ?>" class="h-8 lg:h-10 flex flex-col items-start justify-center">
                            <span class="text-xs lg:text-sm max-h-8 lg:max-h-10 overflow-hidden whitespace-normal"><?php echo strtoupper($judul_promosi); ?></span>
                        </p>
                        <div class="flex justify-between items-center lg:mt-1 absolute lg:relative left-0 right-0 bottom-0">
                            <p class="text-[9px] lg:text-xs opacity-60"> <?php echo $kategori_promosi; ?> </p>
                        </div>
                    </article>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
</section>

<?php include_once 'footer.php'; ?>