<?php
include_once '../koneksi.php';
include_once '../classes/class.exa.php';

$GXA = new GameXaAPI();

if (!isset($_SESSION['kode_admin'])) {
    echo '
    <script>
    alert("Terjadi kesalahan, harap masuk kembali!");
    window.location.replace("' . $alamat_admin . 'keluar.php");
    </script>
    ';
    exit();
}

if (isset($_GET['id_withdraw'])) {
    $id_withdraw = mysqli_real_escape_string($koneksi, $_GET['id_withdraw']);
    $withdraw = mysqli_query($koneksi, "SELECT * FROM withdraw WHERE id_withdraw = '$id_withdraw'");
    if (!$withdraw || mysqli_num_rows($withdraw) == 0) {
        echo '
        <script>
        alert("Withdraw tidak ditemukan!");
        window.location.replace("' . $alamat_admin . 'withdraw");
        </script>
        ';
        exit();
    }

    $data_withdraw = mysqli_fetch_array($withdraw);
    $id_anggota_withdraw = $data_withdraw['id_anggota_withdraw'];
    $kode_withdraw = $data_withdraw['kode_withdraw'];
    $tujuan_withdraw = $data_withdraw['tujuan_withdraw'];
    $jumlah_withdraw = (float)$data_withdraw['jumlah_withdraw'];
    $tanggal_withdraw = $data_withdraw['tanggal_withdraw'];
    $status_withdraw = $data_withdraw['status_withdraw'];

    $anggota = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota_withdraw'");
    if (!$anggota || mysqli_num_rows($anggota) == 0) {
        echo '
        <script>
        alert("Anggota tidak ditemukan!");
        window.location.replace("' . $alamat_admin . 'withdraw");
        </script>
        ';
        exit();
    }

    $data_anggota = mysqli_fetch_array($anggota);
    $saldo_anggota = (float)$data_anggota['saldo_anggota'];
    $nama_pengguna = $data_anggota['nama_pengguna_anggota'];
    $ext_pengguna_anggota = $data_anggota['ext_pengguna_anggota'];
} else {
    echo '
    <script>
    alert("Pilih withdraw yang ingin diubah!");
    window.location.replace("' . $alamat_admin . 'withdraw");
    </script>
    ';
    exit();
}

if (isset($_POST['ubah_data'])) {
    $status_withdraw_2 = mysqli_real_escape_string($koneksi, $_POST['status_withdraw']);

    if ($status_withdraw_2 == "disetujui") {
        
        if (empty($ext_pengguna_anggota) || $ext_pengguna_anggota == 0) {
            echo '
            <script>
            alert("Kesalahan: Kolom ext_pengguna_anggota kosong atau nol. Gagal tarik dana dari API.");
            window.location.replace("' . $alamat_admin . 'withdraw");
            </script>
            ';
            exit();
        }

        if ($jumlah_withdraw > $saldo_anggota) {
            echo '
            <script>
            alert("Proses withdraw gagal: Jumlah penarikan melebihi saldo lokal anggota.");
            window.location.replace("' . $alamat_admin . 'withdraw");
            </script>
            ';
            exit();
        }

        $player_id_api = $ext_pengguna_anggota;
        $reference_id = $kode_withdraw;

        $proses_api = $GXA->withdrawFromPlayer($player_id_api, $jumlah_withdraw, $reference_id);

        if (!$proses_api || $proses_api['success'] !== true) {
            $message = isset($proses_api['data']['message']) ? $proses_api['data']['message'] : 'Kesalahan API tidak diketahui.';
            echo '
            <script>
            alert("Gagal melakukan withdraw dari Player ke Agent! Pesan: ' . htmlspecialchars($message) . '");
            window.location.replace("' . $alamat_admin . 'withdraw");
            </script>
            ';
            exit();
        }

        $saldo_anggota_fix = $saldo_anggota - $jumlah_withdraw;

        $perbarui_anggota = mysqli_query($koneksi, "UPDATE anggota SET saldo_anggota = '$saldo_anggota_fix' WHERE id_anggota = '$id_anggota_withdraw'");
        if ($perbarui_anggota) {
            $ubah_data = mysqli_query($koneksi, "UPDATE withdraw SET status_withdraw = '$status_withdraw_2' WHERE id_withdraw = '$id_withdraw'");
            if ($ubah_data) {
                echo '
                <script>
                alert("Berhasil ubah data dan saldo telah dikurangi.");
                window.location.replace("' . $alamat_admin . 'withdraw");
                </script>
                ';
            } else {
                echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
            }
        } else {
            echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
        }
    } else if ($status_withdraw_2 == "dibatalkan") {
        $ubah_data = mysqli_query($koneksi, "UPDATE withdraw SET status_withdraw = '$status_withdraw_2' WHERE id_withdraw = '$id_withdraw'");
        if ($ubah_data) {
            echo '
            <script>
            alert("Berhasil ubah data.");
            window.location.replace("' . $alamat_admin . 'withdraw");
            </script>
            ';
        } else {
            echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
        }
    } else {
        $ubah_data = mysqli_query($koneksi, "UPDATE withdraw SET status_withdraw = '$status_withdraw_2' WHERE id_withdraw = '$id_withdraw'");
        if ($ubah_data) {
            echo '
            <script>
            alert("Berhasil ubah data.");
            window.location.replace("' . $alamat_admin . 'withdraw");
            </script>
            ';
        } else {
            echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
        }
    }
} elseif (isset($_POST['hapus_data'])) {
    $hapus_data = mysqli_query($koneksi, "DELETE FROM withdraw WHERE id_withdraw = '$id_withdraw'");
    if ($hapus_data) {
        echo '
        <script>
        alert("Berhasil hapus data.");
        window.location.replace("' . $alamat_admin . 'withdraw");
        </script>
        ';
    } else {
        echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
    }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4 mb-4">
        <div class="col-md-6">
            <div class="fw-bold fs-4 text-center text-md-start">Withdraw</div>
        </div>
        <div class="col-md-6">
            <div class="text-center text-md-end">
                <a href="<?php echo $alamat_admin . 'withdraw'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
                    <span class="tf-icons mdi mdi-chevron-double-left me-1"></span>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Ubah Data Withdraw</h5>
        <form method="post" class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" value="<?php echo $jumlah_withdraw; ?>" readonly disabled>
                        <label>Jumlah</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating form-floating-outline mb-4">
                        <select name="status_withdraw" class="form-select select2" required <?php echo ($status_withdraw != "diproses") ? 'disabled' : ''; ?>>
                            <option value="diproses" <?php echo ($status_withdraw == "diproses") ? 'selected' : ''; ?>>Diproses</option>
                            <option value="dibatalkan" <?php echo ($status_withdraw == "dibatalkan") ? 'selected' : ''; ?>>Dibatalkan</option>
                            <option value="disetujui" <?php echo ($status_withdraw == "disetujui") ? 'selected' : ''; ?>>Disetujui</option>
                        </select>
                        <label>Status</label>
                    </div>
                </div>
            </div>
            <div class="pt-4 text-end">
                <button type="button" class="btn btn-danger waves-effect waves-light me-sm-3 me-1" data-bs-toggle="modal" data-bs-target="#hapus_data">
                    <span class="tf-icons mdi mdi-delete me-1"></span>
                    Hapus
                </button>
                <button type="submit" name="ubah_data" class="btn btn-primary waves-effect waves-light">
                    <span class="tf-icons mdi mdi-content-save me-1"></span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="hapus_data" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Hapus Data</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    Yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="hapus_data" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>