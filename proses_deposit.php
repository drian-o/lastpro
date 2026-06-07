<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} // Pastikan session sudah dimulai sebelum menggunakan $_SESSION
include('koneksi.php');

$id_anggota = $_SESSION['id_anggota'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form deposit
    $asal_deposit = isset($_POST['asal_deposit']) ? mysqli_real_escape_string($koneksi, $_POST['asal_deposit']) : '';
    $tujuan_deposit = isset($_POST['tujuan_deposit']) ? mysqli_real_escape_string($koneksi, $_POST['tujuan_deposit']) : '';
    $kode_deposit = isset($_POST['kode_deposit']) ? mysqli_real_escape_string($koneksi, $_POST['kode_deposit']) : '';
    $bonus_deposit = isset($_POST['bonus_deposit']) ? mysqli_real_escape_string($koneksi, $_POST['bonus_deposit']) : '';
    $jumlah_deposit = isset($_POST['jumlah_deposit']) ? mysqli_real_escape_string($koneksi, $_POST['jumlah_deposit']) : '';
    $tanggal_deposit = date('Y-m-d H:i:s'); // Tanggal dan waktu saat ini

    // Bersihkan format angka
    $jumlah_deposit = str_replace(['.', ','], '', $jumlah_deposit);
    $jumlah_deposit = preg_replace('/[^0-9]/', '', $jumlah_deposit);

    // Validasi input
    $errors = [];
    if (empty($tujuan_deposit)) {
        $errors[] = "Pilih Tujuan Deposit.";
    }
    if (empty($jumlah_deposit)) {
        $errors[] = "Isi jumlah Deposit.";
    }
    if (!empty($errors)) {
        // Jika ada lebih dari satu kesalahan, tampilkan pesan umum
        $error_message = count($errors) > 1 ? "Harap isi form deposit dengan lengkap!" : implode("\\n", $errors);
        echo "<script>alert('$error_message'); window.location.href = 'deposit';</script>";
        exit();
    }

    if (isset($id_anggota)) {
        // Query nama pengguna
        $query_nama_pengguna = mysqli_prepare($koneksi, "SELECT nama_pengguna_anggota FROM anggota WHERE id_anggota = ?");
        mysqli_stmt_bind_param($query_nama_pengguna, 'i', $id_anggota);
        mysqli_stmt_execute($query_nama_pengguna);
        $result_nama_pengguna = mysqli_stmt_get_result($query_nama_pengguna);

        if ($result_nama_pengguna) {
            $data_nama_pengguna = mysqli_fetch_assoc($result_nama_pengguna);
            $nama_pengguna_anggota_deposit = $data_nama_pengguna['nama_pengguna_anggota'];

            // Query deposit terakhir
            $query_deposit_terakhir = mysqli_prepare($koneksi, "SELECT status_deposit FROM deposit WHERE id_anggota_deposit = ? ORDER BY tanggal_deposit DESC LIMIT 1");
            mysqli_stmt_bind_param($query_deposit_terakhir, 'i', $id_anggota);
            mysqli_stmt_execute($query_deposit_terakhir);
            $result_deposit_terakhir = mysqli_stmt_get_result($query_deposit_terakhir);

            if ($result_deposit_terakhir) {
                $data_deposit_terakhir = mysqli_fetch_assoc($result_deposit_terakhir);

                if ($data_deposit_terakhir && $data_deposit_terakhir['status_deposit'] === 'diproses') {
                    echo '<script>
                        alert("Deposit terakhir Anda masih diproses. Silakan tunggu hingga proses tersebut selesai sebelum melakukan deposit baru.");
                        window.location.href = "home";
                    </script>';
                    exit();
                }

                // Insert deposit baru
                $sql = "INSERT INTO deposit (id_anggota_deposit, kode_deposit, nama_pengguna_anggota_deposit, asal_deposit, tujuan_deposit, bonus_deposit, jumlah_deposit, tanggal_deposit, status_deposit)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'diproses')";

                $stmt = mysqli_prepare($koneksi, $sql);
                mysqli_stmt_bind_param($stmt, 'isssssss', $id_anggota, $kode_deposit, $nama_pengguna_anggota_deposit, $asal_deposit, $tujuan_deposit, $bonus_deposit, $jumlah_deposit, $tanggal_deposit);

                if (mysqli_stmt_execute($stmt)) {
                    // Buat token dan simpan di sesi
                    $_SESSION['valid_navigation'] = true;

                    // Arahkan berdasarkan tujuan deposit
                    if ($tujuan_deposit === 'QRIS') {
                        echo '<script>window.location.href = "qris-progress";</script>';
                    } else {
                        echo '<script>window.location.href = "deposit-progress";</script>';
                    }
                } else {
                    echo '<script>alert("Deposit Gagal. Silahkan Coba lagi."); window.location.href = "home";</script>';
                }
                mysqli_stmt_close($stmt);
            } else {
                echo '<script>alert("Terjadi kesalahan pada query deposit terakhir."); window.location.href = "home";</script>';
            }
        } else {
            echo '<script>alert("Terjadi kesalahan pada query nama pengguna."); window.location.href = "home";</script>';
        }
    } else {
        echo '<script>alert("Deposit Gagal. Silahkan Coba lagi."); window.location.href = "home";</script>';
    }

    mysqli_close($koneksi);
}
?>
