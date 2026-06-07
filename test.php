<?php

// Sertakan file class pga
require_once __DIR__ . '/classes/class.pga.php';

// Inisialisasi variabel untuk menyimpan respons dan URL gambar QRIS
$response = null;
$qrImage = null;
$error = null;

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pga = new pga();
        
        // Cek aksi yang diminta dari form
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'generate_qris':
                $username = $_POST['qris_username'];
                $amount = $_POST['qris_amount'];
                $expire = $_POST['qris_expire'] ?? 1200;
                $customRef = $_POST['qris_custom_ref'] ?? '';
                $response = $pga->generateQris($username, $amount, $expire, $customRef);

                // **TAMBAHAN BARU UNTUK MEMBUAT URL GAMBAR QRIS**
                if (isset($response['data']['data'])) {
                    $qrCodeData = $response['data']['data'];
                    $qrImage = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrCodeData);
                }
                break;

            case 'check_status':
                $transactionId = $_POST['status_transaction_id'];
                $response = $pga->checkPaymentStatus($transactionId);
                break;
            
            case 'inquiry_transfer':
                $amount = $_POST['inquiry_amount'];
                $bankCode = $_POST['inquiry_bank_code'];
                $accountNumber = $_POST['inquiry_account_number'];
                $type = $_POST['inquiry_type'] ?? 1;
                $response = $pga->inquiryTransfer($amount, $bankCode, $accountNumber, $type);
                break;
            
            case 'execute_transfer':
                $amount = $_POST['execute_amount'];
                $bankCode = $_POST['execute_bank_code'];
                $accountNumber = $_POST['execute_account_number'];
                $inquiryId = $_POST['execute_inquiry_id'];
                $type = $_POST['execute_type'] ?? 1;
                $response = $pga->executeTransfer($amount, $bankCode, $accountNumber, $inquiryId, $type);
                break;
            
            case 'get_bank_codes':
                $response = $pga->getBankCodes();
                break;
                
            default:
                $error = 'Aksi tidak valid.';
                break;
        }

    } catch (Exception $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PGA API Tester</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .tab-container { display: flex; border-bottom: 2px solid #ccc; margin-bottom: 20px; }
        .tab-button { background-color: #ddd; border: none; outline: none; cursor: pointer; padding: 10px 20px; transition: 0.3s; font-size: 16px; }
        .tab-button:hover { background-color: #ccc; }
        .tab-button.active { background-color: #fff; border-top: 2px solid #007bff; border-left: 2px solid #007bff; border-right: 2px solid #007bff; border-bottom: 2px solid #fff; }
        .tab-content { display: none; padding-top: 20px; }
        .tab-content.active { display: block; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        .response-box { background: #333; color: #fff; padding: 15px; border-radius: 8px; margin-top: 20px; white-space: pre-wrap; word-wrap: break-word; }
        .qr-box { background: #fff; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: center; border: 1px solid #ccc; }
        .error-box { background: #dc3545; color: white; padding: 15px; border-radius: 8px; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <h1>PGA API Tester</h1>

        <div class="tab-container">
            <button class="tab-button active" onclick="openTab(event, 'qris')">Generate QRIS</button>
            <button class="tab-button" onclick="openTab(event, 'status')">Check Status</button>
            <button class="tab-button" onclick="openTab(event, 'inquiry')">Transfer Inquiry</button>
            <button class="tab-button" onclick="openTab(event, 'execute')">Execute Transfer</button>
            <button class="tab-button" onclick="openTab(event, 'banks')">Get Bank Codes</button>
        </div>

        <div id="qris" class="tab-content active">
            <h2>Generate QRIS Payment</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="generate_qris">
                <div class="form-group">
                    <label for="qris_username">Username:</label>
                    <input type="text" id="qris_username" name="qris_username" required>
                </div>
                <div class="form-group">
                    <label for="qris_amount">Amount:</label>
                    <input type="number" id="qris_amount" name="qris_amount" required>
                </div>
                <div class="form-group">
                    <label for="qris_expire">Expire (seconds, default: 1200):</label>
                    <input type="number" id="qris_expire" name="qris_expire" value="1200">
                </div>
                <div class="form-group">
                    <label for="qris_custom_ref">Custom Reference:</label>
                    <input type="text" id="qris_custom_ref" name="qris_custom_ref">
                </div>
                <button type="submit" class="btn">Generate QRIS</button>
            </form>
        </div>

        <div id="status" class="tab-content">
            <h2>Check Payment Status</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="check_status">
                <div class="form-group">
                    <label for="status_transaction_id">Transaction ID:</label>
                    <input type="text" id="status_transaction_id" name="status_transaction_id" required>
                </div>
                <button type="submit" class="btn">Check Status</button>
            </form>
        </div>

        <div id="inquiry" class="tab-content">
            <h2>Transfer Inquiry</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="inquiry_transfer">
                <div class="form-group">
                    <label for="inquiry_amount">Amount:</label>
                    <input type="number" id="inquiry_amount" name="inquiry_amount" required>
                </div>
                <div class="form-group">
                    <label for="inquiry_bank_code">Bank Code:</label>
                    <input type="text" id="inquiry_bank_code" name="inquiry_bank_code" required>
                </div>
                <div class="form-group">
                    <label for="inquiry_account_number">Account Number:</label>
                    <input type="text" id="inquiry_account_number" name="inquiry_account_number" required>
                </div>
                <div class="form-group">
                    <label for="inquiry_type">Type (default: 1):</label>
                    <input type="number" id="inquiry_type" name="inquiry_type" value="1">
                </div>
                <button type="submit" class="btn">Inquiry Transfer</button>
            </form>
        </div>

        <div id="execute" class="tab-content">
            <h2>Execute Transfer</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="execute_transfer">
                <div class="form-group">
                    <label for="execute_amount">Amount:</label>
                    <input type="number" id="execute_amount" name="execute_amount" required>
                </div>
                <div class="form-group">
                    <label for="execute_bank_code">Bank Code:</label>
                    <input type="text" id="execute_bank_code" name="execute_bank_code" required>
                </div>
                <div class="form-group">
                    <label for="execute_account_number">Account Number:</label>
                    <input type="text" id="execute_account_number" name="execute_account_number" required>
                </div>
                <div class="form-group">
                    <label for="execute_inquiry_id">Inquiry ID (from Inquiry Transfer):</label>
                    <input type="number" id="execute_inquiry_id" name="execute_inquiry_id" required>
                </div>
                <div class="form-group">
                    <label for="execute_type">Type (default: 1):</label>
                    <input type="number" id="execute_type" name="execute_type" value="1">
                </div>
                <button type="submit" class="btn">Execute Transfer</button>
            </form>
        </div>

        <div id="banks" class="tab-content">
            <h2>Get Bank Codes</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="get_bank_codes">
                <p>Klik tombol di bawah untuk mendapatkan daftar kode bank.</p>
                <button type="submit" class="btn">Get Bank Codes</button>
            </form>
        </div>
        
        <?php if ($error): ?>
            <div class="error-box">
                <h3>Error</h3>
                <pre><?php echo htmlspecialchars($error); ?></pre>
            </div>
        <?php endif; ?>

        <?php if ($response): ?>
            <?php if ($qrImage): ?>
                <div class="qr-box">
                    <h3>QRIS Code</h3>
                    <img src="<?php echo htmlspecialchars($qrImage); ?>" alt="QRIS Code">
                    <p>Silahkan scan kode QR di atas untuk melakukan pembayaran.</p>
                </div>
            <?php endif; ?>
            <div class="response-box">
                <h3>API Response</h3>
                <pre><?php echo htmlspecialchars(json_encode($response, JSON_PRETTY_PRINT)); ?></pre>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabContent, tabButtons;
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }
            tabButtons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabButtons.length; i++) {
                tabButtons[i].className = tabButtons[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        // Tampilkan tab yang terakhir diakses jika ada
        window.onload = function() {
            const currentAction = "<?php echo isset($_POST['action']) ? htmlspecialchars($_POST['action']) : ''; ?>";
            let activeTabId = 'qris';
            
            switch(currentAction) {
                case 'check_status':
                    activeTabId = 'status';
                    break;
                case 'inquiry_transfer':
                    activeTabId = 'inquiry';
                    break;
                case 'execute_transfer':
                    activeTabId = 'execute';
                    break;
                case 'get_bank_codes':
                    activeTabId = 'banks';
                    break;
            }
            
            // Set tab yang sesuai menjadi aktif
            const tabButtons = document.getElementsByClassName("tab-button");
            for (let i = 0; i < tabButtons.length; i++) {
                if (tabButtons[i].getAttribute('onclick').includes(activeTabId)) {
                    tabButtons[i].click();
                    break;
                }
            }
        };
    </script>

</body>
</html>