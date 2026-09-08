<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านค้าเสื้อผ้า - หน้าหลัก</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .flauntfit-pink { background-color: #FFC0CB; }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="flauntfit-pink text-black p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="index.php" class="text-2xl font-bold">FlauntFit</a>
            <form action="search.php" method="GET" class="flex-grow flex justify-center">
                <input type="text" name="q" placeholder="ค้นหาสินค้า" class="w-96 p-2 rounded-lg text-black">
            </form>
            <nav id="auth-links">
                <div id="logged-out-view">
                    <a href="login.php" class="text-white hover:underline mr-4">เข้าสู่ระบบ</a>
                    <a href="register.php" class="text-white hover:underline">สมัครสมาชิก</a>
                </div>
                <div id="logged-in-view" class="hidden">
                    <a href="#" class="text-white hover:underline mr-4">ชื่อผู้ใช้</a>
                    <a href="#" id="logout-link" class="text-white hover:underline">ออกจากระบบ</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-4 mt-8">
        <h2 class="text-2xl font-semibold mb-4">สินค้าทั้งหมด</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            
            <!-- สินค้าที่ 1 -->
            <a href="product.php?id=1" class="bg-white rounded-lg shadow-md p-2 hover:shadow-xl transition-shadow">
                <img src="https://placehold.co/300x300/f5f5f5/333333?text=Hoodie" alt="MNO.9 Hoodie" class="w-full h-auto rounded-lg">
                <div class="p-2">
                    <h3 class="text-sm font-semibold truncate">MNO.9 Hoodie M248</h3>
                    <p class="text-orange-500 text-lg font-bold mt-1">฿450.00</p>
                </div>
            </a>

            <!-- สินค้าที่ 2 -->
            <a href="product.php?id=2" class="bg-white rounded-lg shadow-md p-2 hover:shadow-xl transition-shadow">
                <img src="https://placehold.co/300x300/f5f5f5/333333?text=T-shirt" alt="เสื้อยืด" class="w-full h-auto rounded-lg">
                <div class="p-2">
                    <h3 class="text-sm font-semibold truncate">เสื้อยืดสกรีนอก</h3>
                    <p class="text-orange-500 text-lg font-bold mt-1">฿250.00</p>
                </div>
            </a>

            <!-- สินค้าที่ 3 -->
            <a href="product.php?id=3" class="bg-white rounded-lg shadow-md p-2 hover:shadow-xl transition-shadow">
                <img src="https://placehold.co/300x300/f5f5f5/333333?text=Sweatshirt" alt="เสื้อสเวตเตอร์" class="w-full h-auto rounded-lg">
                <div class="p-2">
                    <h3 class="text-sm font-semibold truncate">เสื้อสเวตเตอร์</h3>
                    <p class="text-orange-500 text-lg font-bold mt-1">฿380.00</p>
                </div>
            </a>

            <!-- สินค้าที่ 4 -->
            <a href="product.php?id=4" class="bg-white rounded-lg shadow-md p-2 hover:shadow-xl transition-shadow">
                <img src="https://placehold.co/300x300/f5f5f5/333333?text=Jacket" alt="เสื้อแจ็คเก็ต" class="w-full h-auto rounded-lg">
                <div class="p-2">
                    <h3 class="text-sm font-semibold truncate">เสื้อแจ็คเก็ตกันหนาว</h3>
                    <p class="text-orange-500 text-lg font-bold mt-1">฿590.00</p>
                </div>
            </a>

            <!-- สินค้าที่ 5 -->
            <a href="product.php?id=5" class="bg-white rounded-lg shadow-md p-2 hover:shadow-xl transition-shadow">
                <img src="https://placehold.co/300x300/f5f5f5/333333?text=Jeans" alt="กางเกงยีนส์" class="w-full h-auto rounded-lg">
                <div class="p-2">
                    <h3 class="text-sm font-semibold truncate">กางเกงยีนส์ชาย</h3>
                    <p class="text-orange-500 text-lg font-bold mt-1">฿790.00</p>
                </div>
            </a>

        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-8 mt-12 text-center">
        <p>&copy; 2024 ร้านค้าเสื้อผ้า. สงวนลิขสิทธิ์.</p>
    </footer>
</body>
</html>
