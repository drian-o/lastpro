<?php
$sections = [
    [
        'icon' => '⭐',
        'title' => 'Visi & Misi Inti',
        'content' => 'Menjadi platform hiburan digital terdepan dan terpercaya di Asia. Kami berkomitmen untuk menyediakan pengalaman pengguna yang aman, adil, bertanggung jawab, dan terus berupaya meningkatkan standar kualitas layanan kami.',
    ],
    [
        'icon' => '🔒',
        'title' => 'Keamanan Kelas Atas',
        'content' => 'Keamanan data adalah pilar fundamental. Kami menerapkan enkripsi canggih dan protokol keamanan tingkat tinggi untuk menjamin kerahasiaan informasi pribadi Anda 100% terjaga. Kami tidak akan pernah membagikan atau menjual data Anda kepada pihak ketiga.',
    ],
    [
        'icon' => '🤝',
        'title' => 'Integritas & Keadilan',
        'content' => 'Integritas produk kami adalah janji. Audit berkala independen dilakukan untuk memastikan keadilan dan transparansi absolut pada seluruh proses permainan. Kami beroperasi berdasarkan prinsip kejujuran total.',
    ],
    [
        'icon' => '📞',
        'title' => 'Dukungan Pelanggan 24/7',
        'content' => 'Tim layanan pelanggan kami yang profesional siap melayani Anda tanpa henti, 24 jam sehari, 7 hari seminggu. Kami memprioritaskan penyelesaian masalah secara cepat, efisien, dan ramah melalui Live Chat dan WhatsApp.',
    ],
    [
        'icon' => '📜',
        'title' => 'Kepatuhan Regulasi (KYC/AML)',
        'content' => 'Kami beroperasi dengan standar kepatuhan tertinggi, secara ketat mengikuti kebijakan Kenali Pelanggan Anda (KYC) dan Anti-Pencucian Uang (AML). Kami bekerja sama dengan badan pengatur untuk memastikan ketaatan penuh terhadap hukum.',
    ],
];

include_once 'header.php';
?>

<section class="container mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-gray-900 text-gray-100 min-h-screen">
    
    <div class="text-center mb-12">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-primary uppercase tracking-wider mb-3">
            PROFIL PERUSAHAAN
        </h1>
        <p class="mt-4 text-xl text-gray-400 max-w-3xl mx-auto">
            Komitmen kami terhadap Keamanan, Keadilan, dan Pengalaman Pengguna Premium di Asia.
        </p>
    </div>

    <div class="lg:w-3/4 mx-auto mb-16 p-8 bg-gray-800 shadow-xl rounded-lg border-t-4 border-primary">
        <h2 class="text-2xl font-bold mb-4 text-white">Selamat Datang</h2>
        <p class="text-gray-300 leading-relaxed text-justify">
            Kami adalah platform hiburan digital terkemuka yang melayani pasar Asia. Kami berdedikasi untuk menyediakan lingkungan permainan online yang komprehensif dan premium, menampilkan beragam pilihan produk mulai dari taruhan olahraga hingga kasino digital. Kami berkomitmen penuh untuk menghadirkan pengalaman pengguna yang tidak hanya menarik, tetapi juga **aman, adil, dan bertanggung jawab**.
        </p>
    </div>

    <div class="lg:w-11/12 mx-auto grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        
        <?php foreach ($sections as $section): ?>
            <div class="bg-gray-800 p-6 rounded-lg shadow-2xl transition duration-300 hover:shadow-primary/50 hover:bg-gray-700/70 h-full flex flex-col">
                
                <div class="text-4xl mb-3 text-primary">
                    <?php echo $section['icon']; ?>
                </div>

                <h3 class="text-xl font-bold text-white mb-3 border-b border-gray-600 pb-2">
                    <?php echo $section['title']; ?>
                </h3>
                
                <p class="text-gray-400 leading-relaxed flex-grow">
                    <?php echo $section['content']; ?>
                </p>
            </div>
        <?php endforeach; ?>
        
    </div>

</section>

<?php include_once 'footer.php'; ?>