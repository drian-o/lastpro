<?php
	include_once '../koneksi.php';
	include_once '../classes/class.exa.php';
	
	session_start();
	
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
	
	if (isset($_GET['id_anggota'])) {
		$id_anggota = $_GET['id_anggota'];
		$anggota_query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota'");
		if (mysqli_num_rows($anggota_query) == 0) {
			echo '
			<script>
			alert("Anggota tidak ditemukan!");
			window.location.replace("' . $alamat_admin . 'saldo");
			</script>
			';
			exit();
		}
		
		$data_anggota = mysqli_fetch_array($anggota_query);
		
		$saldo_anggota = (float)$data_anggota['saldo_anggota'];
		$nama_pengguna_anggota = $data_anggota['nama_pengguna_anggota'];
		$ext_pengguna_anggota = $data_anggota['ext_pengguna_anggota'];
		} else {
		echo '
        <script>
		alert("Pilih anggota yang ingin diubah!");
		window.location.replace("' . $alamat_admin . 'saldo");
        </script>
		';
		exit();
	}
	
	if (isset($_POST['ubah_data'])) {
		$jumlah_transaksi = (float)$_POST['saldo_anggota'];
		$status_transaksi = $_POST['status_transaksi'];
		$player_id_api = $ext_pengguna_anggota;
		$reference_id = 'TRX-MAN-' . time();
		
		if ($player_id_api == 0) {
			echo '
			<script>
			alert("Kesalahan: ext_pengguna_anggota kosong atau nol. Tidak dapat memproses transaksi API.");
			window.location.replace("' . $alamat_admin . 'ubah_saldo/' . $id_anggota . '");
			</script>
			';
			exit();
		}
		
		if ($status_transaksi == 'deposit') {
			
			$proses_api = $GXA->depositToPlayer($player_id_api, $jumlah_transaksi, $reference_id);
			
			if ($proses_api['success'] === true) {
				$saldo_baru = $saldo_anggota + $jumlah_transaksi;
				$ubah_saldo = mysqli_query($koneksi, "UPDATE anggota SET saldo_anggota = '$saldo_baru' WHERE id_anggota = '$id_anggota'");
				
				if ($ubah_saldo) {
					echo '
                    <script>
					alert("Berhasil menambah saldo.");
					window.location.replace("' . $alamat_admin . 'saldo");
                    </script>
					';
					} else {
					echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
				}
				} else {
				$message = isset($proses_api['data']['message']) ? $proses_api['data']['message'] : 'Kesalahan deposit API tidak diketahui.';
				echo '
                <script>
				alert("Proses deposit gagal: ' . htmlspecialchars($message) . '");
				window.location.replace("' . $alamat_admin . 'ubah_saldo/' . $id_anggota . '");
                </script>
				';
			}
			} elseif ($status_transaksi == 'withdraw') {
			
			if ($jumlah_transaksi > $saldo_anggota) {
				echo '
				<script>
				alert("Proses withdraw gagal: Jumlah penarikan melebihi saldo anggota.");
				window.location.replace("' . $alamat_admin . 'ubah_saldo/' . $id_anggota . '");
				</script>
				';
				exit();
			}
			
			$proses_api = $GXA->withdrawFromPlayer($player_id_api, $jumlah_transaksi, $reference_id);
			
			if ($proses_api['success'] === true) {
				$saldo_baru = $saldo_anggota - $jumlah_transaksi;
				$ubah_saldo = mysqli_query($koneksi, "UPDATE anggota SET saldo_anggota = '$saldo_baru' WHERE id_anggota = '$id_anggota'");
				
				if ($ubah_saldo) {
					echo '
                    <script>
					alert("Berhasil mengurangi saldo.");
					window.location.replace("' . $alamat_admin . 'saldo");
                    </script>
					';
					} else {
					echo "Proses Gagal<br>Error : " . mysqli_error($koneksi);
				}
				} else {
				$message = isset($proses_api['data']['message']) ? $proses_api['data']['message'] : 'Kesalahan withdraw API tidak diketahui.';
				echo '
                <script>
				alert("Proses withdraw gagal: ' . htmlspecialchars($message) . '");
				window.location.replace("' . $alamat_admin . 'ubah_saldo/' . $id_anggota . '");
                </script>
				';
			}
			} else {
			echo '
            <script>
			alert("Status transaksi tidak valid!");
			window.location.replace("' . $alamat_admin . 'ubah_saldo/' . $id_anggota . '");
            </script>
			';
		}
	}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row gy-4 mb-4">
        <div class="col-md-6">
            <div class="fw-bold fs-4 text-center text-md-start">Anggota</div>
		</div>
        <div class="col-md-6">
            <div class="text-center text-md-end">
                <a href="<?php echo $alamat_admin . 'anggota'; ?>" class="btn btn-sm btn-primary waves-effect waves-light">
                    <span class="tf-icons mdi mdi-chevron-double-left me-1"></span>
                    Kembali
				</a>
			</div>
			</div>
			</div>
			
			<div class="card mb-4">
			<h5 class="card-header">Ubah Saldo Anggota</h5>
			<form method="post" class="card-body">
            <hr class="my-4 mx-n4">
            <h6>Transaksi Manual Saldo API</h6>
            <div class="row g-3">
			<div class="col-md-4">
			<div class="form-floating form-floating-outline">
			<input type="number" name="saldo_anggota" class="form-control" value="0" min="1" required>
			<label>Jumlah Transaksi</label>
			</div>
			</div>
			<div class="col-md-4">
			<div class="form-floating form-floating-outline mb-4">
			<select name="status_transaksi" class="form-select select2" required>
			<option value="deposit">Tambah Saldo (Deposit)</option>
			<option value="withdraw">Kurangi Saldo (Withdraw)</option>
			</select>
			<label>Status Transaksi</label>
			</div>
			</div>
			<div class="col-md-4">
			<div class="form-floating form-floating-outline">
			<input type="text" class="form-control" value="<?php echo 'Saat ini: Rp. ' . number_format($saldo_anggota, 0, ',', '.'); ?>" readonly disabled>
			<label>Saldo Anggota</label>
			</div>
			</div>
            </div>
            <div class="pt-4 text-end">
			<button type="submit" name="ubah_data" class="btn btn-primary waves-effect waves-light">
			<span class="tf-icons mdi mdi-content-save me-1"></span>
			Simpan
			</button>
            </div>
			</form>
			</div>
			</div>