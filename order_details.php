<?php
// File: order_details.php
// Halaman untuk detail pemesanan dan checkout

session_start();
// Cek apakah ada pesanan di keranjang
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Koneksi database (ini hanya contoh, ganti dengan konfigurasi database Anda)
$host = "localhost";
$user = "username";
$password = "password";
$database = "cafe_db";

// Fungsi format rupiah
function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Hitung total harga di keranjang
function calculateTotal()
{
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Hitung pajak (misalnya 10%)
function calculateTax()
{
    return calculateTotal() * 0.1;
}

// Hitung grand total (total + pajak)
function calculateGrandTotal()
{
    return calculateTotal() + calculateTax();
}

// Proses checkout
if (isset($_POST['process_order'])) {
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $table_number = htmlspecialchars($_POST['table_number']);
    $notes = htmlspecialchars($_POST['notes']);
    $payment_method = htmlspecialchars($_POST['payment_method']);

    // Dalam implementasi nyata, kode berikut akan menyimpan pesanan ke database
    // $conn = new mysqli($host, $user, $password, $database);

    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    // // Simpan data order ke tabel orders
    // $sql = "INSERT INTO orders (customer_name, table_number, notes, payment_method, total_amount, status, order_date) 
    //         VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
    // $stmt = $conn->prepare($sql);
    // $grand_total = calculateGrandTotal();
    // $stmt->bind_param("sissd", $customer_name, $table_number, $notes, $payment_method, $grand_total);
    // $stmt->execute();
    // $order_id = $conn->insert_id;

    // // Simpan detail order ke tabel order_items
    // foreach ($_SESSION['cart'] as $item) {
    //     $sql = "INSERT INTO order_items (order_id, item_name, price, quantity) VALUES (?, ?, ?, ?)";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['quantity']);
    //     $stmt->execute();
    // }

    // $conn->close();

    // Untuk demo, simpan order ID dalam session
    $_SESSION['order_id'] = rand(1000, 9999);
    $_SESSION['order_details'] = [
        'customer_name' => $customer_name,
        'table_number' => $table_number,
        'notes' => $notes,
        'payment_method' => $payment_method,
        'items' => $_SESSION['cart'],
        'total' => calculateTotal(),
        'tax' => calculateTax(),
        'grand_total' => calculateGrandTotal(),
        'order_date' => date('Y-m-d H:i:s')
    ];

    // Reset keranjang
    $_SESSION['cart'] = [];

    // Redirect ke halaman konfirmasi
    header("Location: order_confirmation.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan - Kafe Express</title>
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
            max-width: 1200px;
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

        .back-link {
            color: white;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Main Content */
        main {
            margin: 40px 0;
        }

        .order-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        h2 {
            color: #5c3d2e;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e6e6e6;
        }

        .order-items {
            margin-bottom: 20px;
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

        .item-quantity {
            width: 100px;
            text-align: center;
        }

        .item-price {
            width: 150px;
            text-align: right;
        }

        .order-summary {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
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

        .checkout-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .payment-method {
            flex: 1;
            min-width: 120px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #b85c38;
        }

        .payment-method.selected {
            border-color: #b85c38;
            background-color: #f8f0ed;
        }

        .payment-method img {
            height: 40px;
            margin-bottom: 10px;
        }

        .submit-order {
            background-color: #5c3d2e;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .submit-order:hover {
            background-color: #b85c38;
        }

        /* Footer */
        footer {
            background-color: #5c3d2e;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-item {
                flex-direction: column;
                padding: 15px 0;
            }

            .item-quantity,
            .item-price {
                width: 100%;
                margin-top: 5px;
                text-align: left;
            }

            .payment-methods {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">Kafe Express</div>
            <a href="index.php" class="back-link">← Kembali ke Menu</a>
        </div>
    </header>

    <main class="container">
        <section class="order-section">
            <h2>Rincian Pesanan</h2>

            <div class="order-items">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="order-item">
                        <div class="item-name"><?php echo $item['name']; ?></div>
                        <div class="item-quantity"><?php echo $item['quantity']; ?> x</div>
                        <div class="item-price"><?php echo formatRupiah($item['price'] * $item['quantity']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="order-summary">
                <div class="summary-row">
                    <div>Subtotal</div>
                    <div><?php echo formatRupiah(calculateTotal()); ?></div>
                </div>
                <div class="summary-row">
                    <div>Pajak (10%)</div>
                    <div><?php echo formatRupiah(calculateTax()); ?></div>
                </div>
                <div class="summary-row total">
                    <div>Total</div>
                    <div><?php echo formatRupiah(calculateGrandTotal()); ?></div>
                </div>
            </div>
        </section>

        <section class="order-section">
            <h2>Informasi Pemesanan</h2>

            <form method="post" action="" class="checkout-form">
                <div class="form-group">
                    <label for="customer_name">Nama</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                </div>

                <div class="form-group">
                    <label for="table_number">Nomor Meja</label>
                    <select id="table_number" name="table_number" required>
                        <option value="">Pilih Nomor Meja</option>
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                            <option value="<?php echo $i; ?>">Meja <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan Tambahan</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <div class="payment-methods">
                        <div class="payment-method" data-method="cash">
                            <div class="payment-icon">💵</div>
                            <div>Tunai</div>
                        </div>
                        <div class="payment-method" data-method="debit">
                            <div class="payment-icon">💳</div>
                            <div>Kartu Debit</div>
                        </div>
                        <div class="payment-method" data-method="qris">
                            <div class="payment-icon">📱</div>
                            <div>QRIS</div>
                        </div>
                    </div>
                    <input type="hidden" id="payment_method" name="payment_method" value="" required>
                </div>

                <button type="submit" name="process_order" class="submit-order">Proses Pesanan</button>
            </form>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Kafe Express. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script>
        // Handler untuk metode pembayaran
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function () {
                // Hapus kelas selected dari semua metode
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('selected');
                });

                // Tambahkan kelas selected ke metode yang dipilih
                this.classList.add('selected');

                // Set nilai input hidden
                document.getElementById('payment_method').value = this.dataset.method;
            });
        });
    </script>
</body>

</html>