<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'koneksi.php';
include_once 'header.php';
// Sertakan class PGA
include_once 'classes/class.pga.php';

// Cek jika pengguna sudah login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $id_anggota = $_SESSION['id_anggota'];
    $username = $_SESSION['ext_pengguna_anggota'];
} else {
    echo '
        <script>
          window.location.replace("' . $alamat_website . 'home");
        </script>
      ';
    exit();
}

// Inisialisasi variabel untuk tampilan
$show_qris_invoice = false;
$error_message = null;
$status_message = null;

// Proses Form Kirim Deposit, Check Status, atau Batalkan Transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pga = new pga();

    // Aksi untuk Kirim Deposit
    if (isset($_POST['submitdeposit'])) {
        $jumlah_deposit = (int)$_POST['jumlah_deposit'];
        $tujuan_deposit = $_POST['tujuan_deposit'];
        $kode_deposit = uniqid(); // Generate kode unik untuk deposit

        try {
            // Panggil API untuk generate QRIS
            $response = $pga->generateQris($username, $jumlah_deposit, 1200, $kode_deposit);

            if ($response['success'] && isset($response['data']['data'])) {
                // Simpan data ke database
                $trx_id = $response['data']['trx_id'];
                $qris_data = $response['data']['data'];
                $status_deposit = 'diproses';
                
                // Cek apakah trx_id sudah ada di database
                $query_check = "SELECT COUNT(*) FROM deposit WHERE trx_id = ?";
                $stmt_check = mysqli_prepare($koneksi, $query_check);
                mysqli_stmt_bind_param($stmt_check, "s", $trx_id);
                mysqli_stmt_execute($stmt_check);
                mysqli_stmt_bind_result($stmt_check, $count);
                mysqli_stmt_fetch($stmt_check);
                mysqli_stmt_close($stmt_check);

                if ($count == 0) {
                    $query_insert = "INSERT INTO deposit (id_anggota_deposit, kode_deposit, nama_pengguna_anggota_deposit, asal_deposit, tujuan_deposit, jumlah_deposit, tanggal_deposit, status_deposit, trx_id) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'diproses', ?)";
                    $stmt = mysqli_prepare($koneksi, $query_insert);
                    mysqli_stmt_bind_param($stmt, "issssis", $id_anggota, $kode_deposit, $username, $username, $tujuan_deposit, $jumlah_deposit, $trx_id);
                    mysqli_stmt_execute($stmt);
                }

                // Simpan data QRIS ke sesi untuk ditampilkan
                $_SESSION['qris_invoice'] = [
                    'amount' => $jumlah_deposit,
                    'trx_id' => $trx_id,
                    'qris_data' => $qris_data
                ];
                
                // Atur flag untuk menampilkan invoice
                $show_qris_invoice = true;
                $invoice_data = $_SESSION['qris_invoice'];

            } else {
                $error_message = "Gagal menghasilkan QRIS: " . ($response['message'] ?? 'Unknown error');
            }
        } catch (Exception $e) {
            $error_message = "Terjadi kesalahan API: " . $e->getMessage();
        }
    }

    // Aksi untuk Check Status
    if (isset($_POST['check_status'])) {
        $trx_id = $_POST['trx_id'];
        
        // Cek status di database
        $query_db_status = "SELECT status_deposit FROM deposit WHERE trx_id = ?";
        $stmt_db = mysqli_prepare($koneksi, $query_db_status);
        if ($stmt_db) {
            mysqli_stmt_bind_param($stmt_db, "s", $trx_id);
            mysqli_stmt_execute($stmt_db);
            mysqli_stmt_bind_result($stmt_db, $db_status);
            mysqli_stmt_fetch($stmt_db);
            mysqli_stmt_close($stmt_db);

            if ($db_status === 'disetujui') {
                // Jika status di database sudah disetujui, langsung redirect
                unset($_SESSION['qris_invoice']);
                echo '<script>window.location.href = "' . $alamat_website . 'home";</script>';
            exit();
            } else if ($db_status === 'diproses') {
                $status_message = "Status transaksi masih 'diproses'. Silakan cek kembali dalam beberapa saat.";
            } else if ($db_status === 'dibatalkan') {
                $status_message = "Transaksi ini telah dibatalkan atau gagal.";
                unset($_SESSION['qris_invoice']); // Hapus sesi jika sudah dibatalkan
            } else {
                $status_message = "Status transaksi tidak ditemukan.";
            }
        } else {
            $error_message = "Terjadi kesalahan saat menyiapkan query database.";
        }
    }

    // Aksi untuk Batalkan Transaksi
    if (isset($_POST['action']) && $_POST['action'] === 'cancel_deposit' && isset($_POST['trx_id_to_cancel'])) {
        $trx_id_to_cancel = $_POST['trx_id_to_cancel'];

        // 1. Update status di database menjadi 'dibatalkan'
        // Memastikan hanya transaksi milik anggota yang sedang login dan berstatus 'diproses' yang dibatalkan
        $query_update = "UPDATE deposit SET status_deposit = 'dibatalkan' WHERE trx_id = ? AND id_anggota_deposit = ? AND status_deposit = 'diproses'";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "si", $trx_id_to_cancel, $id_anggota);
            mysqli_stmt_execute($stmt_update);
            $rows_affected = mysqli_stmt_affected_rows($stmt_update);
            mysqli_stmt_close($stmt_update);
            
            if ($rows_affected > 0) {
                // Hapus sesi setelah berhasil membatalkan di database
                unset($_SESSION['qris_invoice']);
                // Tidak perlu $status_message karena akan langsung di-redirect
            } else {
                // Opsi: Tetapkan error message jika diperlukan, tapi redirect tetap dilakukan
                // $error_message = "Gagal membatalkan transaksi atau status sudah tidak 'diproses'.";
            }
        } else {
            // $error_message = "Terjadi kesalahan saat menyiapkan query pembatalan database.";
        }
        
        // Redirect ke halaman deposit setelah pembatalan
        echo '<script>window.location.href = "' . $alamat_website . 'deposit";</script>';
        exit();
    }
}

// Logika untuk menampilkan invoice yang sudah ada di sesi atau database
if (isset($_SESSION['qris_invoice'])) {
    $show_qris_invoice = true;
    $invoice_data = $_SESSION['qris_invoice'];
    $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($invoice_data['qris_data']);
} else {
    // Cek deposit terakhir yang masih berstatus 'diproses'
    $query_deposit_diproses = mysqli_query($koneksi, "SELECT jumlah_deposit, trx_id FROM deposit WHERE id_anggota_deposit = '$id_anggota' AND status_deposit = 'diproses' ORDER BY tanggal_deposit DESC LIMIT 1");
    $data_deposit_diproses = mysqli_fetch_assoc($query_deposit_diproses);

    if ($data_deposit_diproses) {
        $pga = new pga();
        try {
            // Panggil kembali API untuk mendapatkan data QRIS lama
            $response = $pga->checkPaymentStatus($data_deposit_diproses['trx_id']);

            if ($response['success'] && isset($response['data']['data'])) {
                // Jika data respons valid, buat invoice
                $_SESSION['qris_invoice'] = [
                    'amount' => $data_deposit_diproses['jumlah_deposit'],
                    'trx_id' => $data_deposit_diproses['trx_id'],
                    'qris_data' => $response['data']['data']
                ];
                $show_qris_invoice = true;
                $invoice_data = $_SESSION['qris_invoice'];
                $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($invoice_data['qris_data']);
            } else {
                // Hapus sesi jika transaksi sudah expired atau gagal
                unset($_SESSION['qris_invoice']);
            }
        } catch (Exception $e) {
            // Abaikan error, biarkan user input nominal lagi
            unset($_SESSION['qris_invoice']);
        }
    }
}
?>
    <style>
        .qris-invoice { 
            text-align: center;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
            background-color: #1b1b1b;
        }
        .qris-invoice img { 
            margin: 20px auto; 
            border: 1px solid #ccc;
            padding: 5px;
            background: white;
        }
    </style>
<section class="container flex mx-auto lg:pt-3 lg:pb-5 lg:mt-5">
    <div class="w-full lg:w-1/3 px-3 hidden lg:block">
        <a class="px-3 py-4 bg-background-default lg:bg-background-secondary rounded-xl mt-4 drop-shadow-[0_0_5px_rgba(0,0,0,0.6)] lg:drop-shadow-none group transition-all duration-300 ease-in-out lg:hover:bg-black/30" href="#">
            <figure class="flex flex-none items-center justify-center w-12 md:w-16 h-12 md:h-16">
                <img alt="VIP Level Badge" width="0" height="0" decoding="async" data-nimg="1" class="w-full" style="color: transparent;" loading="lazy" src="assets/img/pemainbaru1.png">
            </figure>
            <article class="w-full pl-4">
                <p class="text-sm md:text-base group-hover:text-white">Pemain Baru</p>
                <progress class="w-full h-[5px] primary-progress" value="0" max="100"></progress>
                <span class="text-xs md:text-sm group-hover:text-white">Increase your level and get rewards</span>
            </article>
            <figure class="pl-2"><svg width="26" height="26" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="26">
                    <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                </svg>
            </figure>
        </a>
        <section class="bg-background-secondary rounded-xl mt-4">
            <div class="w-full lg:px-4 pt-3 flex flex-wrap px-4">
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
                    <div class="w-full flex items-center">
                        <section class="w-full flex items-center h-7">
                            <span class="text-sm lg:text-base font-semibold">IDR&nbsp;<?php echo number_format($_SESSION['saldo_anggota'], 0, ',', '.'); ?></span>
                            <button class="rounded-full bg-background-default cursor-pointer rotate-270 w-7 h-7 ml-2 items-center justify-center flex">
                                <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="m10 19-.707-.707-.707.707.707.707L10 19Zm3.293-4.707-4 4 1.414 1.414 4-4-1.414-1.414Zm-4 5.414 4 4 1.414-1.414-4-4-1.414 1.414Z" fill="var(--caption)"></path>
                                    <path d="M5.938 15.5A7 7 0 1 1 12 19" stroke="var(--caption)" stroke-width="2" stroke-linecap="round"></path>
                                </svg>
                            </button>
                        </section>
                    </div>
                </div>
            </div>
            <div class="flex gap-x-4 px-4 pb-6 mt-5">
                <a class="w-full justify-center py-2 rounded-lg text-primary border border-primary transition-all duration-200 ease-in-out hover:lg:bg-background-tertiary" href="<?php echo $alamat_website . 'withdraw'; ?>">Withdraw</a>
                <a class="w-full justify-center py-2 rounded-lg bg-primary text-white transition-all duration-200 ease-in-out hover:lg:brightness-90" href="<?php echo $alamat_website . 'deposit'; ?>">Deposit</a>
            </div>
        </section>
        <section class="bg-background-secondary rounded-xl mt-4 overflow-hidden"><a class="justify-between px-4 py-3 hover:lg:bg-background-tertiary transition-all duration-300 ease-in-out" href="<?php echo $alamat_website . 'my-account'; ?>">
                <div class="flex items-center w-[calc(100%-24px)] pr-1">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.002 10h19.996c-.012-2.175-.108-3.353-.877-4.121C20.243 5 18.828 5 16 5H8c-2.828 0-4.243 0-5.121.879-.769.768-.865 1.946-.877 4.121ZM22 12H2v2c0 2.828 0 4.243.879 5.121C3.757 20 5.172 20 8 20h8c2.828 0 4.243 0 5.121-.879C22 18.243 22 16.828 22 14v-2ZM7 15a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H7Z" fill="var(--primary)"></path>
                    </svg>
                    <span class="text-sm pl-2 undefined text-primary">My Account</span>
                </div>
                <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="22">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--primary)"></path>
                    </svg>
                </figure>
                <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="22">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                    </svg>
                </figure>
            </a>
        </section>
        <section class="bg-background-secondary rounded-xl mt-4 overflow-hidden"><a class="justify-between px-4 py-3 hover:lg:bg-background-tertiary transition-all duration-300 ease-in-out" href="<?php echo $alamat_website . 'change-pasword'; ?>">
                <div class="flex items-center w-[calc(100%-24px)] pr-1">
                    <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M13.984 2.542c.087.169.109.386.152.82.082.82.123 1.23.295 1.456a1 1 0 0 0 .929.384c.28-.037.6-.298 1.238-.82.337-.277.506-.415.687-.473a1 1 0 0 1 .702.035c.175.076.33.23.637.538l.894.894c.308.308.462.462.538.637a1 1 0 0 1 .035.702c-.058.181-.196.35-.472.687-.523.639-.784.958-.822 1.239a1 1 0 0 0 .385.928c.225.172.636.213 1.457.295.433.043.65.065.82.152a1 1 0 0 1 .47.521c.071.177.071.395.071.831v1.264c0 .436 0 .654-.07.83a1 1 0 0 1-.472.522c-.169.087-.386.109-.82.152-.82.082-1.23.123-1.456.295a1 1 0 0 0-.384.929c.038.28.299.6.821 1.238.276.337.414.505.472.687a1 1 0 0 1-.035.702c-.076.175-.23.329-.538.637l-.894.893c-.308.309-.462.463-.637.538a1 1 0 0 1-.702.035c-.181-.058-.35-.196-.687-.472-.639-.522-.958-.783-1.238-.82a1 1 0 0 0-.929.384c-.172.225-.213.635-.295 1.456-.043.434-.065.651-.152.82a1 1 0 0 1-.521.472c-.177.07-.395.07-.831.07h-1.264c-.436 0-.654 0-.83-.07a1 1 0 0 1-.522-.472c-.087-.169-.109-.386-.152-.82-.082-.82-.123-.1.295-1.456a1 1 0 0 0-.928-.384c-.281.037-.6.298-1.239.82-.337.277-.506.415-.687.473a1 1 0 0 1-.702-.035c-.175-.076-.33-.23-.637-.538l-.894-.894c-.308-.308-.462-.462-.538-.637a1 1 0 0 1-.035-.702c.058-.181.196-.35.472-.687.523-.639.784-.958.821-1.239a1 1 0 0 0-.384-.928c-.225-.172-.636-.213-1.457-.295-.433-.043-.65-.065-.82-.152a1 1 0 0 1-.47-.521C2 13.286 2 13.068 2 12.632v-1.264c0-.436 0-.654.07-.83a1 1 0 0 1 .472-.522c.169-.087.386-.109.82-.152.82-.082 1.231-.123 1.456-.295a1 1 0 0 0 .385-.928c-.038-.281-.3-.6-.822-1.24-.276-.337-.414-.505-.472-.687a1 1 0 0 1 .035-.702c.076-.174.23-.329.538-.637l-.894-.893c.308-.308.462-.463.637-.538a1 1 0 0 1 .702-.035c.181.058.35.196.687.472.639.522.958.783 1.238.821a1 1 0 0 0 .93-.385c.17-.225.212-.635.294-1.456.043-.433.065-.65.152-.82a1 1 0 0 1 .521-.471c.177-.07.395-.07.831-.07h1.264c.436 0 .654 0 .83.07a1 1 0 0 1 .522.472ZM12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" fill="var(--primary)"></path>
                    </svg>
                    <span class="text-sm pl-2 undefined false">Account Settings</span>
                </div>
                <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="22">
                        <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                    </svg>
                </figure>
            </a>
        </section>
        <a href="<?php echo $alamat_website . 'logout'; ?>" class="block">
            <div class="bg-background-secondary rounded-xl mt-4 overflow-hidden">
                <section class="justify-between px-4 py-3 flex cursor-pointer hover:lg:bg-background-tertiary transition-all duration-300 ease-in-out " href="<?php echo $alamat_website . 'logout'; ?>">
                    <div class="flex items-center w-[calc(100%-24px)] pr-1">
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="var(--primary)" xmlns="http://www.w3.org/2000/svg" size="24">
                            <path d="m2 12-.78-.625-.5.625.5.625L2 12Zm9 1a1 1 0 1 0 0-2v2ZM5.22 6.375l-4 5 1.56 1.25 4-5-1.56-1.25Zm-4 6.25 4 5 1.56-1.25-4-5-1.56 1.25ZM2 13h9v-2H2v2ZM13.342 20.557l.165-.986-.165.986Zm7.597.19.646.764-.646-.763ZM15.014 3.165l-.165-.986.165.986Zm5.925.088.646-.763-.646.763ZM13.507 4.43l1.671-.278-.329-1.973-1.671.279.329 1.972ZM21 9.083v5.834h2V9.083h-2Zm-5.822 10.766-1.671-.278-.329 1.973 1.671.278.329-1.973ZM11 8.132v-.743H9v.743h2Zm0 8.48v-.546H9v.546h2Zm2.507 2.959c-.824-.138-1.35-.227-1.734-.342-.358-.106-.472-.201-.536-.277l-1.526 1.293c.41.484.932.735 1.491.901.532.159 1.203.269 1.976.398l.329-1.973ZM9 16.61c0 .784-.002 1.464.067 2.015.072.578.234 1.135.644 1.619l1.526-1.293c-.064-.075-.14-.203-.185-.574-.05-.398-.052-.932-.052-1.767H9Zm12-1.694c0 1.675-.002 2.823-.123 3.67-.116.82-.32 1.174-.584 1.398l1.293 1.526c.797-.675 1.123-1.593 1.272-2.642.144-1.021.142-2.338.142-3.952h-2Zm-6.15 6.905c1.59.265 2.89.484 3.92.51 1.06.025 2.018-.146 2.816-.821l-1.293-1.526c-.264.223-.646.367-1.474.347-.856-.021-1.99-.207-3.641-.483l-.329 1.973Zm.328-17.671c1.652-.275 2.785-.462 3.64-.483.829-.02 1.21.124 1.475.347l1.293-1.526c-.797-.675-1.757-.846-2.816-.82-1.03.025-2.33.244-3.92.509l.328 1.973ZM23 9.083c0-1.614.002-2.93-.142-3.952-.15-1.049-.476-1.967-1.273-2.642l-1.292 1.526c.264.224.468.577.584 1.397.12.848.123 1.996.123 3.67h2Zm-9.822-6.626c-.773.128-1.444.238-1.976.397-.559.166-1.081.417-1.491.901l1.526 1.293c.064-.076.178-.17.536-.277.384-.115.91-.204 1.734-.342l-.329-1.972ZM11 7.389c0-.835.002-1.369.052-1.767.046-.37.121-.499.185-.574L9.711 3.755c-.41.484-.572 1.04-.644 1.62C8.998 5.924 9 6.604 9 7.388h2Z" fill="var(--primary)"></path>
                        </svg>
                        <span class="text-sm pl-2 undefined undefined">Logout</span>
                    </div>
                    <figure class="w-6"><svg width="22" height="22" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg" size="22">
                            <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                        </svg>
                    </figure>
                </section>
            </div>
        </a>
    </div>

    <div class="w-full lg:w-2/3 lg:px-3 pb-24 lg:pb-0">
        <div class="grid grid-cols-2 px-3 lg:gap-x-5 lg:px-4 lg:mb-6 mt-4 lg:mt-0">
            <a aria-label="Deposit-tab-button" aria-labelledby="Deposit-tab-button" class="text-sm border-b uppercase flex-none px-1 pb-2 justify-center cursor-pointer transition-all duration-300 ease-in-out hover:lg:border-primary hover:lg:text-inverse font-semibold border-primary " href="<?php echo $alamat_website . 'deposit'; ?>">Deposit</a>
            <a aria-label="Withdraw-tab-button" aria-labelledby="Withdraw-tab-button" class="text-sm border-b uppercase flex-none px-1 pb-2 justify-center cursor-pointer transition-all duration-300 ease-in-out hover:lg:border-primary hover:lg:text-inverse font-semibold border-transparent text-caption " href="<?php echo $alamat_website . 'withdraw'; ?>">Withdraw</a>
        </div>

        <?php if ($error_message): ?>
            <div class="px-4 py-3 bg-red-100 text-red-700 rounded-lg mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($show_qris_invoice) : ?>
            <section class="mt-3 px-3 lg:px-4 pb-6">
                <div class="lg:bg-background-secondary lg:px-5 lg:pt-6 lg:pb-16 lg:mb-4 rounded-xl">
                    <div class="qris-invoice">
                        <p class="text-sm opacity-70">QRIS Payment</p>
                        <p class="text-lg font-semibold mt-1">Invoice Details</p>

                        <div class="mt-5 border-b border-disable pb-3">
                            <p class="text-sm opacity-70">Amount</p>
                            <p class="text-xl font-bold mt-1">IDR&nbsp;<?php echo number_format($invoice_data['amount'], 0, ',', '.'); ?></p>
                        </div>
                        
                        <img src="<?php echo htmlspecialchars($qr_code_url); ?>" alt="QRIS Code">
                        
                        <div class="mt-5 border-b border-disable pb-3">
                            <p class="text-xs opacity-70">Transaction ID</p>
                            <p class="text-sm font-medium mt-1"><?php echo htmlspecialchars($invoice_data['trx_id']); ?></p>
                        </div>

                        <p class="text-xs mt-3 text-center text-gray-500">Scan QR Code di atas dengan aplikasi e-wallet atau mobile banking Anda.</p>

                        <?php if (isset($status_message)): ?>
                            <p class="text-sm text-info mt-3"><?php echo htmlspecialchars($status_message); ?></p>
                        <?php endif; ?>
                    </div>

                    <form method="post" action="" class="mt-4">
                        <input type="hidden" name="trx_id" value="<?php echo htmlspecialchars($invoice_data['trx_id']); ?>">
                        <button type="submit" name="check_status" class="bg-primary justify-center rounded-xl py-3 w-full text-white font-medium">Cek Status Pembayaran</button>
                    </form>
                    
                    <form method="post" action="">
                        <input type="hidden" name="action" value="cancel_deposit">
                        <input type="hidden" name="trx_id_to_cancel" value="<?php echo htmlspecialchars($invoice_data['trx_id']); ?>">
                        <button type="submit" class="block text-center border border-primary text-primary font-medium mt-4 py-3 rounded-xl w-full" onclick="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini?...);">Batalkan Transaksi</button>
                    </form>
                </div>
            </section>

        <?php else : ?>
            <div class="flex justify-between items-center px-4 my-4">
                <p class="text-sm">Pilih Metode Deposit</p>
            </div>
            <div class="px-4">
                <div class="flex">
                    <button class="w-full items-center rounded-lg px-3 py-4 lg:px-5 relative overflow-hidden transition-all duration-200 ease-in-out bg-gradient-to-tl from-primary to-[#3431C2]">
                        <figure class="w-10 h-auto ">
                            <svg width="100%" height="100%" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg">
                                <g fill="var(--base)">
                                    <path d="M16.733 23.798c-.433-.102-.88-.166-1.297-.312-1.768-.616-2.963-1.82-3.595-3.582-.022-.062-.047-.122-.077-.202-.069.04-.125.071-.178.107-.466.31-.929.627-1.398.933-.366.239-.746.252-1.116.013-1.999-1.291-3.919-2.686-5.635-4.343-.754-.728-1.448-1.508-1.954-2.439a3.845 3.845 0 0 1-.482-1.885c.009-2.735.003-5.47.005-8.205 0-.134.007-.27.034-.4.082-.388.426-.703.819-.717 1.827-.059 3.562-.552 5.283-1.109A41.054 41.054 0 0 0 9.146.948c.328-.125.629-.122.954.003a35.002 35.002 0 0 0 4.347 1.382c.915.222 1.838.396 2.784.423.624.019.99.395.991 1.026.003 1.65.002 3.302.002 4.953v3.594c.571.214 1.134.361 1.637.623 1.819.948 2.88 2.457 3.105 4.5a5.705 5.705 0 0 1-5.053 6.307c-.05.006-.1.025-.15.038h-1.03Zm-.434-11.471V4.626c-.46-.077-.904-.139-1.343-.226-1.762-.346-3.464-.895-5.15-1.5a.599.599 0 0 0-.378 0c-1.482.534-2.976 1.028-4.516 1.366-.654.145-1.315.251-1.99.378V4.9c0 2.406-.008 4.812.008 7.219.002.294.065.626.214.873.306.508.647 1.009 1.048 1.444 1.565 1.698 3.424 3.043 5.33 4.324.05.034.17.015.228-.024a80.76 80.76 0 0 0 1.688-1.183.386.386 0 0 0 .134-.232c.304-1.936 1.296-3.4 3.026-4.325.528-.28 1.117-.443 1.701-.67Zm-2.85 5.73c-.007 2.108 1.691 3.81 3.815 3.823a3.817 3.817 0 0 0 3.827-3.787c.014-2.12-1.683-3.85-3.781-3.856-2.15-.007-3.853 1.679-3.86 3.82Z"></path>
                                    <path d="M10.578 15.18H8.666v-.94H6.761v-1.921h.244c1.009 0 2.018.001 3.026-.002.381 0 .612-.273.52-.603-.06-.22-.226-.34-.487-.347-.396-.01-.794.01-1.186-.03-1.168-.115-2.027-1.028-2.128-2.23-.09-1.073.658-2.127 1.729-2.435l.183-.054v-.977h1.91v.932h1.894v1.923h-.235c-.986 0-1.973 0-2.958.002-.097 0-.198 0-.289.025a.473.473 0 0 0 .14.922c.306.01.612.003.92.004 1.246.007 2.257.864 2.422 2.053.165 1.194-.546 2.284-1.732 2.639-.131.04-.16.097-.158.217.006.268.002.535.002.822ZM14.93 18.913l.855-1.708 1.158.575 1.445-2.165 1.59 1.06-.292.442c-.518.776-1.035 1.553-1.554 2.328-.35.523-.833.652-1.396.372l-1.806-.904Z"></path>
                                </g>
                            </svg>
                        </figure>
                        <div class="flex flex-wrap items-center w-full pl-3">
                            <p class="w-full text-sm text-left uppercase font-medium">Deposit via VA or QRIS</p>
                            <p class="text-xs text-left w-full">Faster and automatic deposit</p>
                        </div>
                        <figure class="w-7">
                            <svg width="100%" height="100%" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg">
                                <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                            </svg>
                        </figure>
                    </button>
                </div>
            </div>

            <section class="lg:px-4 lg:pb-0" style="padding-bottom: 80px;">
                <div class="lg:bg-background-secondary lg:py-1 lg:pb-3 lg:rounded-t-xl lg:mt-4">
                    <div class="px-4 lg:px-5 mt-4">
                        <section class="flex gap-x-3 mt-2">
                            <a href="deposit.php" class="flex flex-col items-center rounded-lg w-full p-3 relative border transition-all duration-200 ease-in-out hover:bg-background-default cursor-pointer border-separator">
                                <figure class="flex flex-none justify-center w-6 lg:w-8 h-6 lg:h-8 mx-auto">
                                    <img alt="Bank Assets" loading="lazy" width="0" height="0" decoding="async" data-nimg="1" class="w-full h-full" src="assets/img/BANK-LOGO.webp" style="color: transparent;">
                                </figure>
                                <p class="text-xs text-center mt-2 truncate text-inverse">Bank</p>
                            </a>
                            <div class="rounded-lg w-full p-3 relative border transition-all duration-200 ease-in-out lg:flex lg:items-center hover:lg:bg-background-default cursor-pointer border-separator false">
                                <figure class="flex flex-none justify-center w-6 lg:w-8 h-6 lg:h-8 mx-auto lg:mr-3 lg:ml-0">
                                    <img alt="E-Wallet Assets" loading="lazy" width="0" height="0" decoding="async" data-nimg="1" class="w-full h-full lg:mx-auto" src="assets/img/E_WALLET.webp" style="color: transparent;">
                                </figure>
                                <p class="text-xs text-center mt-2 truncate text-caption flex-1 lg:flex-none lg:mt-0 lg:text-base">QRIS</p>
                                <section class="absolute bottom-0 right-0 bg-primary w-5 h-5 flex items-center justify-center rounded-br-md rounded-tl-md">
                                    <img alt="E-Wallet" loading="lazy" width="0" height="0" decoding="async" data-nimg="1" class="h-3 w-auto p-[2px]" src="assets/img/done.webp" style="color: transparent;">
                                </section>
                            </div>
                        </section>
                    </div>

                    <form class="relative" method="post">
                        <input type="hidden" name="tujuan_deposit" value="QRIS">
                        <div id="deposit-nominal-section" class="px-4 lg:px-5 mt-10 border-b-8 border-background-secondary lg:border-background-default pb-5">
                            <p class="text-sm">Type Deposit Amount</p>
                            <div class="relative">
                                <section class="mt-3 relative">
                                    <div class="relative mt-4 lg:mt-5 rounded-xl group border lg:bg-background-default border-caption focus-within:border-primary focus-within:ring-1">
                                        <div class="relative flex items-center top-0 pt-3 px-3 ">
                                            <label class="text-xs opacity-70 bg-background-default rounded-full"> type the amount here</label>
                                        </div>
                                        <div class="relative">
                                            <input id="amount-input" inputmode="numeric" name="jumlah_deposit" maxlength="16" class="px-3 pt-2 pb-3 focus:border-transparent focus:ring-0 text-sm lg:text-base w-full rounded-lg border bg-transparent border-transparent focus:outline-none" placeholder="Minimum deposit IDR 10.000" type="text" value="" required>
                                        </div>
                                    </div>
                                    <div class="flex justify-between mt-2">
                                        <p class="text-error text-sm" id="amount-error"></p>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <div id="deposit-summary" class="fixed rounded-t-3xl px-4 lg:px-5 pt-6 lg:pt-5 pb-8 lg:pb-5 lg:mx-4 lg:mt-2 lg:mb-20 lg:rounded-none lg:rounded-b-lg shadow-[0px_4px_10px_6px_black] lg:shadow-none lg:relative left-0 right-0 bottom-0 z-[999] bg-background-tertiary lg:bg-background-secondary">
                            <div class="flex">
                                <p class="text-sm font-medium mr-2">Transfer Amount</p>
                                <button>
                                    <figure class="w-5 h-5 rotate-270 transition-all duration-300 ease-in-out"><svg width="100%" height="100%" viewbox="0 0 24 24" fill="var(--base)" xmlns="http://www.w3.org/2000/svg">
                                            <path d="m15 12 .354-.354.353.354-.353.354L15 12ZM9.354 5.646l6 6-.708.708-6-6 .708-.708Zm6 6.708-6 6-.708-.708 6-6 .708.708Z" fill="var(--base)"></path>
                                        </svg>
                                    </figure>
                                </button>
                            </div>
                            <div class="max-h-[0px] transition-all duration-300 ease-in-out overflow-hidden overflow-y-scroll">
                                <div class="flex justify-between mt-3">
                                    <p class="text-sm opacity-50 font-light">Fee deduction</p>
                                    <p id="fee-deduction" class="text-sm font-medium">IDR&nbsp;</p>
                                </div>
                                <div class="flex justify-between mt-3">
                                    <p class="text-sm opacity-50 font-light">Estimated balance received</p>
                                    <p id="estimated-balance" class="text-sm font-medium">IDR&nbsp;</p>
                                </div>
                            </div>
                            <div class="flex mt-5 lg:mt-3 items-center">
                                <p id="total-amount" class="w-7/12 text-xl font-semibold">IDR&nbsp;</p>
                                <button type="submit" name="submitdeposit" class="w-5/12 justify-center rounded-xl py-3 lg:mx-auto bg-primary text-white transition-all duration-200 ease-in-out hover:lg:brightness-[0.9]">Kirim Deposit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        <?php endif; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount-input');
        const feeDeduction = document.getElementById('fee-deduction');
        const estimatedBalance = document.getElementById('estimated-balance');
        const totalAmount = document.getElementById('total-amount');
        const amountError = document.getElementById('amount-error');
        const submitButton = document.querySelector('button[name="submitdeposit"]');

        function formatRupiah(angka) {
            let numberString = angka.toString();
            if (numberString === '') return '';
            let split = numberString.split('.');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return 'IDR ' + rupiah;
        }

        function updateAmounts() {
            let amountValue = amountInput.value.replace(/\D/g, ''); // Hapus semua non-digit
            amountInput.value = amountValue;

            if (amountValue === '') {
                feeDeduction.textContent = 'IDR 0';
                estimatedBalance.textContent = 'IDR 0';
                totalAmount.textContent = 'IDR 0';
                amountError.textContent = 'Minimum deposit IDR 10.000';
                submitButton.disabled = true;
                return;
            }

            let amount = parseInt(amountValue, 10);
            if (amount < 1000) { // Diubah dari 1000 menjadi 10000 sesuai placeholder
                amountError.textContent = 'Minimum deposit IDR 10.000';
                submitButton.disabled = true;
            } else {
                amountError.textContent = '';
                submitButton.disabled = false;
            }

            const formattedAmount = formatRupiah(amount);
            feeDeduction.textContent = formattedAmount;
            estimatedBalance.textContent = formattedAmount;
            totalAmount.textContent = formattedAmount;
        }

        amountInput.addEventListener('input', updateAmounts);

        updateAmounts(); // Panggil saat halaman dimuat
    });
</script>