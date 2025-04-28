<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kafe Express - Sistem Pemesanan</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    // Inisialisasi session
    session_start();

    // Koneksi database (ini hanya contoh, ganti dengan konfigurasi database Anda)
    $host = "localhost";
    $user = "username";
    $password = "password";
    $database = "cafe_db";

    // Fungsi untuk koneksi database (dalam implementasi nyata)
    function connectDB()
    {
        global $host, $user, $password, $database;
        // Uncomment line di bawah untuk koneksi database yang sebenarnya
        // $conn = new mysqli($host, $user, $password, $database);
        // return $conn;
    
        // Untuk demo, kita tidak benar-benar terhubung ke database
        return true;
    }

    // Data menu (dalam implementasi nyata, ini akan diambil dari database)
    $menu = [
        'makanan' => [
            [
                'id' => 1,
                'nama' => 'Nasi Goreng Spesial',
                'harga' => 35000,
                'deskripsi' => 'Nasi goreng dengan telur, ayam, udang, dan sayuran segar'
            ],
            [
                'id' => 2,
                'nama' => 'Mie Goreng',
                'harga' => 30000,
                'deskripsi' => 'Mie goreng dengan telur, ayam, dan sayuran segar'
            ],
            [
                'id' => 3,
                'nama' => 'Ayam Bakar',
                'harga' => 45000,
                'deskripsi' => 'Ayam bakar dengan bumbu khas dan sambal'
            ],
            [
                'id' => 4,
                'nama' => 'img/sate.jpeg',
                'harga' => 25000,
                'deskripsi' => '10 tusuk sate ayam dengan bumbu kacang'
            ]
        ],
        'minuman' => [
            [
                'id' => 5,
                'nama' => 'Es Teh Manis',
                'harga' => 8000,
                'deskripsi' => 'Teh manis dingin dengan es batu'
            ],
            [
                'id' => 6,
                'nama' => 'Kopi Hitam',
                'harga' => 12000,
                'deskripsi' => 'Kopi hitam khas dengan biji pilihan'
            ],
            [
                'id' => 7,
                'nama' => 'Jus Alpukat',
                'harga' => 15000,
                'deskripsi' => 'Jus alpukat segar dengan susu'
            ],
            [
                'id' => 8,
                'nama' => 'Lemon Tea',
                'harga' => 10000,
                'deskripsi' => 'Teh lemon dingin dengan es batu'
            ]
        ],
        'dessert' => [
            [
                'id' => 9,
                'nama' => 'Pudding Coklat',
                'harga' => 18000,
                'deskripsi' => 'Pudding coklat lembut dengan saus vanilla'
            ],
            [
                'id' => 10,
                'nama' => 'Es Krim',
                'harga' => 20000,
                'deskripsi' => 'Es krim dengan 3 pilihan rasa'
            ],
            [
                'id' => 11,
                'nama' => 'Pancake',
                'harga' => 25000,
                'deskripsi' => 'Pancake dengan maple syrup dan butter'
            ],
            [
                'id' => 12,
                'nama' => 'Fruit Salad',
                'harga' => 22000,
                'deskripsi' => 'Salad buah segar dengan yogurt'
            ]
        ]
    ];

    // Inisialisasi keranjang belanja jika belum ada
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Handle aksi tambah ke keranjang
    if (isset($_POST['add_to_cart'])) {
        $item_id = $_POST['item_id'];
        $item_name = $_POST['item_name'];
        $item_price = $_POST['item_price'];

        // Cek apakah item sudah ada di keranjang
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $item_id) {
                $_SESSION['cart'][$key]['quantity']++;
                $found = true;
                break;
            }
        }

        // Jika item belum ada di keranjang, tambahkan
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $item_id,
                'name' => $item_name,
                'price' => $item_price,
                'quantity' => 1
            ];
        }

        // Redirect untuk menghindari form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Handle aksi checkout
    if (isset($_POST['checkout'])) {
        // Di sini biasanya akan menyimpan data pesanan ke database
        // Tapi untuk contoh, kita hanya akan reset keranjang
        $_SESSION['order_success'] = true;
        $_SESSION['cart'] = [];

        // Redirect untuk menghindari form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
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

    // Format harga ke format rupiah
    function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
    ?>

    <header>
        <div class="container">
            <div class="logo">Kafe Ngelak</div>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_SESSION['order_success']) && $_SESSION['order_success']): ?>
            <div class="success-message" style="display: block;">
                <h3>Terima kasih! Pesanan Anda telah diterima.</h3>
                <p>Pesanan Anda sedang diproses dan akan segera disiapkan.</p>
            </div>
            <?php unset($_SESSION['order_success']); ?>
        <?php endif; ?>

        <!-- Menu Makanan -->
        <section class="menu-section">
            <h2>Menu Makanan</h2>
            <div class="menu-items">
                <?php foreach ($menu['makanan'] as $item): ?>
                    <div class="menu-item">
                        <div class="item-image">
                            <img src="<?php echo $item['nama']; ?>" alt="">
                        </div>
                        <div class="item-info">
                            <div class="item-name"><?php echo $item['nama']; ?></div>
                            <div class="item-price"><?php echo formatRupiah($item['harga']); ?></div>
                            <div class="item-description"><?php echo $item['deskripsi']; ?></div>
                            <form method="post">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="item_name" value="<?php echo $item['nama']; ?>">
                                <input type="hidden" name="item_price" value="<?php echo $item['harga']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Tambah ke Pesanan</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Menu Minuman -->
        <section class="menu-section">
            <h2>Menu Minuman</h2>
            <div class="menu-items">
                <?php foreach ($menu['minuman'] as $item): ?>
                    <div class="menu-item">
                        <div class="item-image">Gambar <?php echo $item['nama']; ?></div>
                        <div class="item-info">
                            <div class="item-name"><?php echo $item['nama']; ?></div>
                            <div class="item-price"><?php echo formatRupiah($item['harga']); ?></div>
                            <div class="item-description"><?php echo $item['deskripsi']; ?></div>
                            <form method="post">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="item_name" value="<?php echo $item['nama']; ?>">
                                <input type="hidden" name="item_price" value="<?php echo $item['harga']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Tambah ke Pesanan</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Menu Dessert -->
        <section class="menu-section">
            <h2>Menu Dessert</h2>
            <div class="menu-items">
                <?php foreach ($menu['dessert'] as $item): ?>
                    <div class="menu-item">
                        <div class="item-image">Gambar <?php echo $item['nama']; ?></div>
                        <div class="item-info">
                            <div class="item-name"><?php echo $item['nama']; ?></div>
                            <div class="item-price"><?php echo formatRupiah($item['harga']); ?></div>
                            <div class="item-description"><?php echo $item['deskripsi']; ?></div>
                            <form method="post" style="width: 100%;">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="item_name" value="<?php echo $item['nama']; ?>">
                                <input type="hidden" name="item_price" value="<?php echo $item['harga']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">Tambah ke Pesanan</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Keranjang Belanja -->
        <!-- Ganti bagian tombol Checkout di file index.php -->
        <div class="cart-section">
            <h2>Pesanan Anda</h2>

            <?php if (empty($_SESSION['cart'])): ?>
                <p>Keranjang pesanan Anda kosong.</p>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-name"><?php echo $item['name']; ?></div>
                            <div class="cart-item-quantity">
                                x<?php echo $item['quantity']; ?>
                            </div>
                            <div class="cart-item-price">
                                <?php echo formatRupiah($item['price'] * $item['quantity']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-total">
                    <div>Total</div>
                    <div><?php echo formatRupiah(calculateTotal()); ?></div>
                </div>

                <!-- Perubahan di sini, mengarahkan ke halaman detail pemesanan -->
                <a href="order_details.php" class="checkout-btn">Lanjutkan ke Pembayaran</a>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Kafe Express. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script>
        // Toggle formulir checkout
        document.getElementById('checkoutBtn').addEventListener('click', function () {
            document.getElementById('orderForm').style.display = 'block';
            this.style.display = 'none';
        });

        // Sembunyikan pesan sukses setelah beberapa detik
        setTimeout(function () {
            var successMessage = document.querySelector('.success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);
    </script>
</body>

</html>