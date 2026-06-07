<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'koneksi.php';
require_once 'classes/class.exa.php';
$GXG_API = new GameXaAPI(); 

$raw_data = file_get_contents("php://input");
$log_filename = 'callback.log';
$log_entry = "[" . date('Y-m-d H:i:s') . "] RAW DATA RECEIVED:\n" . $raw_data . "\n---\n";
file_put_contents($log_filename, $log_entry, FILE_APPEND);

$data = json_decode($raw_data, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit();
}

if (isset($data['trx_id']) && isset($data['status'])) {
    $trx_id = mysqli_real_escape_string($koneksi, $data['trx_id']);
    $payment_status = $data['status'];
    
    if ($payment_status === 'success') {
        $query_deposit = mysqli_query($koneksi, "SELECT * FROM deposit WHERE trx_id = '$trx_id'");
        $data_deposit = mysqli_fetch_array($query_deposit);
        
        if ($data_deposit) {
            if ($data_deposit['status_deposit'] === 'disetujui') {
                $log_entry = "[" . date('Y-m-d H:i:s') . "] TRX ID: {$trx_id} already approved. Skipping.\n---\n";
                file_put_contents($log_filename, $log_entry, FILE_APPEND);
                
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Callback received and processed']);
                exit();
            }
            $id_user_deposit = $data_deposit['nama_pengguna_anggota_deposit'];
           
            $query_anggota = mysqli_query($koneksi, "SELECT * FROM anggota WHERE ext_pengguna_anggota = '$id_user_deposit'");
            $data_anggota = mysqli_fetch_array($query_anggota);
            
            if ($data_anggota) {
                $total_deposit = $data_deposit['jumlah_deposit'];                
                $gxg_player_id = $data_anggota['ext_pengguna_anggota']; 
                $reference_id = $trx_id;                 
                $response_deposit = $GXG_API->depositToPlayer(
                    $gxg_player_id, 
                    (float)$total_deposit, 
                    $reference_id
                );
                
                $log_entry = "[" . date('Y-m-d H:i:s') . "] GxG API Response for TRX ID {$trx_id}: " . json_encode($response_deposit) . "\n";
                file_put_contents($log_filename, $log_entry, FILE_APPEND);
                
                if (isset($response_deposit['success']) && $response_deposit['success'] === true) {
                    $update_deposit_query = "UPDATE deposit SET status_deposit = 'disetujui' WHERE trx_id = '$trx_id'";
                    mysqli_query($koneksi, $update_deposit_query);
                    
                    $saldo_anggota_awal = $data_anggota['saldo_anggota'];
                    $saldo_baru = (float)$saldo_anggota_awal + (float)$total_deposit;
                    $update_saldo_anggota = mysqli_query($koneksi, "UPDATE anggota SET saldo_anggota = '$saldo_baru' WHERE ext_pengguna_anggota = '$id_user_deposit'");
                    
                    $log_entry = "[" . date('Y-m-d H:i:s') . "] TRX ID: {$trx_id} Deposit Approved and DB Updated (User: {$id_user_deposit}).\n---\n";
                } else {
                    $update_deposit_query = "UPDATE deposit SET status_deposit = 'dibatalkan' WHERE trx_id = '$trx_id'";
                    mysqli_query($koneksi, $update_deposit_query);

                    $log_entry = "[" . date('Y-m-d H:i:s') . "] TRX ID: {$trx_id} GxG API Failed. Status set to 'dibatalkan'.\n---\n";
                }
                file_put_contents($log_filename, $log_entry, FILE_APPEND);
            } else {
                $log_entry = "[" . date('Y-m-d H:i:s') . "] TRX ID: {$trx_id} User '{$id_user_deposit}' NOT found in 'anggota' table using ext_pengguna_anggota.\n---\n";
                file_put_contents($log_filename, $log_entry, FILE_APPEND);
            }
        } else {
            $log_entry = "[" . date('Y-m-d H:i:s') . "] TRX ID: {$trx_id} not found in 'deposit' table.\n---\n";
            file_put_contents($log_filename, $log_entry, FILE_APPEND);
        }
    } else if ($payment_status === 'failed' || $payment_status === 'expired') {
        $update_deposit_query = "UPDATE deposit SET status_deposit = 'dibatalkan' WHERE trx_id = '$trx_id' AND status_deposit = 'diproses'";
        mysqli_query($koneksi, $update_deposit_query);
        
        $log_entry = "[" . date('Y-m-d H:i:s') . "] TRX ID: {$trx_id} Status received: '{$payment_status}'. Status set to 'dibatalkan'.\n---\n";
        file_put_contents($log_filename, $log_entry, FILE_APPEND);
    }
    
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Callback received and processed']);
    
} else {
    $log_entry = "[" . date('Y-m-d H:i:s') . "] ERROR: Missing required data (trx_id or status).\n---\n";
    file_put_contents($log_filename, $log_entry, FILE_APPEND);
    
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
}
?>