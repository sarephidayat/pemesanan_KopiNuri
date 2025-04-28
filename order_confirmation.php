<?php
// File: order_confirmation.php
// Halaman konfirmasi pesanan setelah checkout - Dengan perbaikan tampilan mobile

session_start();
// Cek apakah ada data pesanan
if (!isset($_SESSION['order_id']) || !isset($_SESSION['order_details'])) {
    header("Location: index.php");
    exit();
}

// Fungsi format rupiah
function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Ambil detail pesanan dari session
$order = $_SESSION['order_details'];
$order_id = $_SESSION['order_id'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan - Kafe Express</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        header {
            background-color: #5c3d2e;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        /* Main Content */
        main {
            margin: 40px 0;
        }

        .confirmation-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-icon {
            font-size: 60px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        h1 {
            color: #5c3d2e;
            margin-bottom: 20px;
        }

        .order-id {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #b85c38;
        }

        .confirmation-msg {
            margin-bottom: 30px;
            font-size: 18px;
            line-height: 1.6;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e6e6e6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        h3 {
            color: #5c3d2e;
            margin: 20px 0 10px;
            text-align: left;
        }

        .order-items {
            margin-top: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e6e6e6;
        }

        .item-name {
            flex: 1;
        }

        .item-details {
            text-align: right;
        }

        .order-summary {
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #e6e6e6;
            padding-top: 10px;
            margin-top: 10px;
        }

        .back-to-menu {
            display: inline-block;
            background-color: #5c3d2e;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .back-to-menu:hover {
            background-color: #b85c38;
        }

        /* Footer */
        footer {
            background-color: #5c3d2e;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .print-btn {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            color: #333;
            padding: 10px 20px;
            margin-right: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .print-btn:hover {
            background-color: #e6e6e6;
        }

        /* Responsive */
        @media print {

            header,
            .action-buttons,
            footer {
                display: none;
            }

            body {
                background-color: white;
            }

            .container {
                padding: 0;
            }

            .confirmation-section {
                box-shadow: none;
                padding: 0;
            }
        }

        @media (max-width: 768px) {

            /* Perbaikan untuk tampilan mobile */
            .order-item {
                display: flex;
                flex-direction: row;
                /* Tetap dalam satu baris */
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #e6e6e6;
            }

            .item-name {
                flex: 1;
                padding-right: 10px;
                /* Ruang antara nama dan detail */
            }

            .item-details {
                text-align: right;
                min-width: 140px;
                /* Menetapkan lebar minimum untuk kolom harga */
                white-space: nowrap;
                /* Mencegah pemecahan teks */
            }

            .confirmation-section {
                padding: 20px 15px;
                /* Mengurangi padding di mobile */
            }

            .order-details {
                padding: 15px;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .print-btn,
            .back-to-menu {
                width: 100%;
                margin-right: 0;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">Kafe Express</div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="confirmation-section">
                <div class="success-icon">✓</div>
                <h1>Pesanan Berhasil!</h1>
                <div class="order-id">Nomor Pesanan: #<?php echo $order_id; ?></div>

                <div class="confirmation-msg">
                    Terima kasih, <?php echo $order['customer_name']; ?>! Pesanan Anda telah kami terima dan sedang
                    diproses.
                    <br>Mohon tunggu sebentar, pesanan Anda akan segera diantar ke meja
                    <?php echo $order['table_number']; ?>.
                </div>

                <div class="order-details">
                    <h3>Detail Pesanan</h3>
                    <div class="detail-row">
                        <div>Tanggal & Waktu:</div>
                        <div><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></div>
                    </div>
                    <div class="detail-row">
                        <div>Nomor Meja:</div>
                        <div><?php echo $order['table_number']; ?></div>
                    </div>
                    <div class="detail-row">
                        <div>Metode Pembayaran:</div>
                        <div>
                            <?php
                            switch ($order['payment_method']) {
                                case 'cash':
                                    echo 'Tunai';
                                    break;
                                case 'debit':
                                    echo 'Kartu Debit';
                                    break;
                                case 'qris':
                                    echo 'QRIS';
                                    break;
                                default:
                                    echo $order['payment_method'];
                            }
                            ?>
                        </div>
                    </div>
                    <?php if (!empty($order['notes'])): ?>
                        <div class="detail-row">
                            <div>Catatan:</div>
                            <div><?php echo $order['notes']; ?></div>
                        </div>
                    <?php endif; ?>

                    <h3>Item Pesanan</h3>
                    <div class="order-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <div class="item-name"><?php echo $item['name']; ?> (<?php echo $item['quantity']; ?>x)
                                </div>
                                <div class="item-details">
                                    <?php echo formatRupiah($item['price'] * $item['quantity']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-summary">
                        <div class="summary-row">
                            <div>Subtotal</div>
                            <div><?php echo formatRupiah($order['total']); ?></div>
                        </div>
                        <div class="summary-row">
                            <div>Pajak (10%)</div>
                            <div><?php echo formatRupiah($order['tax']); ?></div>
                        </div>
                        <div class="summary-row total">
                            <div>Total</div>
                            <div><?php echo formatRupiah($order['grand_total']); ?></div>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="print-btn" onclick="window.print()">Cetak Struk</button>
                    <a href="index.php" class="back-to-menu">Kembali ke Menu</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Kafe Express. Semua hak dilindungi.</p>
        </div>
    </footer>
</body>

</html>