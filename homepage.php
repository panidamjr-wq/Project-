<?php
session_start();
require_once 'connectdb.php';

$products = [];
$sql = "SELECT product_id, product_name, price, image_url FROM `products` ORDER BY created_at DESC LIMIT 4";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, , #e0f7fa);
            color: #4b5563;
            overflow-x: hidden;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }
        .product-card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 0 10px rgba(255, 105, 180, 0.2);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            padding: 1rem;
            text-align: center;
        }
        .product-card:hover {
            transform: translateY(-8px) rotate(1deg);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15), 0 0 20px rgba(255, 105, 180, 0.5);
        }
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 1rem;
            margin-bottom: 1rem;
        }
        .btn-primary {
            display: inline-block;
            background: linear-gradient(45deg, #a78bfa, #f687b3);
            color: #ffffff;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(167, 139, 250, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(167, 139, 250, 0.6);
        }
        .btn-secondary {
            display: inline-block;
            background-color: #f687b3;
            color: #ffffff;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background-color: #e85d95;
        }
        .balloon-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }
        .balloon {
            position: absolute;
            bottom: -150px;
            width: 80px;
            height: 95px;
            border-radius: 80px 80px 70px 70px / 80px 80px 80px 80px;
            animation: floatUp ease-in-out infinite;
            box-shadow: inset -10px -10px 0 rgba(0,0,0,0.1);
        }
        .balloon::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -15px;
            width: 15px;
            height: 15px;
            transform: translateX(-50%) rotate(45deg);
            background: inherit;
        }
        @keyframes floatUp {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                transform: translateY(-1200px) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="balloon-container"></div>

    <!-- ✅ Header + ช่องค้นหา -->
    <header class="bg-white shadow-lg py-4">
        <div class="container flex flex-col md:flex-row items-center justify-between">
            <a href="homepage.php" class="text-3xl font-bold text-pink-500 hover:text-pink-600 transition-colors">FlauntFit</a>

            <!-- 🔍 Search Form -->
            <form action="search.php" method="GET" class="mt-4 md:mt-0 flex w-full md:w-auto md:ml-6">
                <input type="text" name="query" placeholder="ค้นหาสินค้า..." required
                    class="w-full md:w-64 px-4 py-2 rounded-l-full border border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-500">
                <button type="submit"
                    class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-r-full font-semibold transition">
                    ค้นหา
                </button>
            </form>

            <nav class="mt-4 md:mt-0 flex items-center">
                <ul class="flex space-x-4 md:space-x-8">
                    <li><a href="#" class="text-pink-500 font-bold transition-colors">หน้าหลัก</a></li>
                    <li><a href="ii1dex.php" class="text-gray-600 hover:text-pink-500 font-semibold transition-colors">สินค้าทั้งหมด</a></li>
                    <li><a href="edit_my_profile.php" class="text-gray-600 hover:text-pink-500 font-semibold transition-colors">แก้ไขข้อมูล</a></li>
                    <li><a href="cart.php" class="text-gray-600 hover:text-pink-500 font-semibold transition-colors">ตะกร้าสินค้า</a></li>
                    <li><a href="order_history.php" class="text-gray-600 hover:text-pink-500 font-semibold transition-colors">ประวัติการสั่งซื้อ</a></li>
                    <li><a href="index.php" class="text-gray-600 hover:text-pink-500 font-semibold transition-colors">ออกจากระบบ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="py-10">
            <div class="container">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-pink-600 mb-8">ประเภทสินค้า</h2>
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="products.php?category=Hoodie" class="bg-gradient-to-br from-pink-400 to-purple-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl">Hoodie</a>
                    <a href="products.php?category=t-shirt" class="bg-gradient-to-br from-purple-400 to-blue-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl">t-shirt</a>
                    <a href="products.php?category=shorts" class="bg-gradient-to-br from-blue-400 to-green-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl">shorts</a>
                    <a href="products.php?category=trousers" class="bg-gradient-to-br from-blue-400 to-green-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl">trousers</a>
                    <a href="products.php?category=Dress" class="bg-gradient-to-br from-green-400 to-yellow-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl">Dress</a>
                    <a href="products.php?category=pajamas" class="bg-gradient-to-br from-green-400 to-yellow-500 text-white font-semibold py-3 px-8 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110 hover:shadow-2xl">pajamas</a>
                </div>
            </div>
        </section>

        <section class="bg-pink-300 text-white py-16 md:py-24">
            <div class="container flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 text-center md:text-left mb-8 md:mb-0">
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
                        FlauntFit <br> แต่งตัวมั่นใจ <br> ทุกการเคลื่อนไหว

                    </h1>
                    <p class="mt-4 text-lg md:text-xl font-light">
                        เสื้อผ้าแฟชั่นที่ออกแบบมาเพื่อคุณโดยเฉพาะ
                    </p>
                    <a href="ii1dex.php" class="btn-primary mt-8 inline-block">ดูสินค้าทั้งหมด</a>
                </div>
                <div class="md:w-1/2"><img src="0.png"></div>
            </div>
        </section>

        <section class="py-16">
            <div class="container">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-pink-600 mb-10">สินค้ามาใหม่</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                                <h3 class="text-xl font-semibold mt-2"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="text-2xl font-bold text-pink-600 mt-2">฿<?php echo number_format($product['price'], 2); ?></p>
                                <a href="product_detail.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="btn-secondary mt-4 w-full">ดูรายละเอียดสินค้า</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-span-4 text-center text-gray-500">ไม่พบสินค้า</p>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-12">
                    <a href="ii1dex.php" class="btn-primary">ดูสินค้าทั้งหมด</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white shadow-lg mt-8 py-6 text-center text-gray-600">
        &copy; 2025 FlauntFit. All rights reserved.
    </footer>

    <script>
        const colors = ['#f687b3', '#a78bfa', '#60a5fa', '#34d399'];
        const balloonContainer = document.querySelector('.balloon-container');

        function createBalloon() {
            const balloon = document.createElement('div');
            balloon.classList.add('balloon');

            const randomColor = colors[Math.floor(Math.random() * colors.length)];
            const randomSize = Math.random() * (1.2 - 0.8) + 0.8;
            const randomLeft = Math.random() * 100;
            const randomDuration = Math.random() * (25 - 15) + 15;
            const randomDelay = Math.random() * 10;

            balloon.style.backgroundColor = randomColor;
            balloon.style.transform = `scale(${randomSize})`;
            balloon.style.left = `${randomLeft}vw`;
            balloon.style.animationDuration = `${randomDuration}s`;
            balloon.style.animationDelay = `-${randomDelay}s`;

            balloonContainer.appendChild(balloon);
            balloon.addEventListener('animationend', () => {
                balloon.remove();
            });
        }

        setInterval(createBalloon, 2000);
        for(let i = 0; i < 5; i++) {
            createBalloon();
        }
    </script>
</body>
</html>