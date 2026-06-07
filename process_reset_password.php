<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} // Pastikan session sudah dimulai
include_once 'koneksi.php';


// Cek jika pengguna sudah login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $id_anggota = $_SESSION['id_anggota'];
    $username = $_SESSION['nama_pengguna_anggota']; // Pastikan username disimpan dalam session
} else {
    // Jika tidak login, alihkan atau tampilkan pesan error
    echo '
        <script>
          alert("Anda harus login terlebih dahulu.");
          window.location.replace("'.$alamat_website.'home");
        </script>
    ';
    exit();
}

if (isset($_POST['chg_pass'])) {
    // Ambil data dari formulir
    $old_password = $_POST['old-password'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['cnew-password']; // Variabel konfirmasi kata sandi harus ada

    // Validasi kata sandi baru dan konfirmasi kata sandi
    if ($new_password !== $confirm_password) {
        echo '
            <script>
                alert("Kata sandi baru dan konfirmasi kata sandi tidak cocok.");
                window.location.replace("'.$alamat_website.'home");
            </script>
        ';
        exit();
    }

    // Periksa kata sandi lama
    if ($stmt = $koneksi->prepare("SELECT kata_sandi_anggota FROM anggota WHERE nama_pengguna_anggota = ?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data && password_verify($old_password, $data['kata_sandi_anggota'])) {
            // Enkripsi kata sandi baru
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            // Update kata sandi di database
            if ($stmt = $koneksi->prepare("UPDATE anggota SET kata_sandi_anggota = ? WHERE nama_pengguna_anggota = ?")) {
                $stmt->bind_param("ss", $new_password_hash, $username);

                if ($stmt->execute()) {
                    echo '
                        <script>
                            alert("Berhasil merubah data kata sandi.");
                            window.location.replace("'.$alamat_website.'home");
                        </script>
                    ';
                } else {
                    echo '
                        <script>
                            alert("Terjadi kesalahan saat memperbarui kata sandi.");
                            window.location.replace("'.$alamat_website.'home");
                        </script>
                    ';
                }
                $stmt->close();
            } else {
                echo '
                    <script>
                        alert("Terjadi kesalahan dalam query update.");
                        window.location.replace("'.$alamat_website.'home");
                    </script>
                ';
            }
        } else {
            echo '
                <script>
                    alert("Kata sandi lama salah, ulangi lagi!");
                    window.location.replace("'.$alamat_website.'home");
                </script>
            ';
        }
        $stmt->close();
    } else {
        echo '
            <script>
                alert("Terjadi kesalahan dalam query pengecekan.");
                window.location.replace("'.$alamat_website.'home");
            </script>
        ';
    }

    // Tutup koneksi database
    mysqli_close($koneksi);
}
?>
