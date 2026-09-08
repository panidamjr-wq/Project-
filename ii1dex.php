<?php
session_start();
require_once 'connectdb.php';

$products = [];
$sql = "SELECT product_id, product_name, price, image_url, category FROM `products`";
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
    <title>สินค้าทั้งหมด - FlauntFit</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <style>
        
        body {
            font-family: 'Kanit', 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #FFE5EC, #F5DCE0); 
            color: #4a4a4a;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        h1, h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            color: #E18AAA; 
            text-shadow: 1px 1px 3px rgba(0,0,0,0.05);
        }

        
        header {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            backdrop-filter: blur(5px);
        }

        header a.logo-text { 
            color: #FB6F92; 
            transition: color 0.3s ease-in-out;
        }
        header a.logo-text:hover {
            color: #E18AAA; 
        }
        
        
        nav ul {
            display: flex;
            gap: 1.5rem;
        }
        nav a {
            color: #6b7280;
            font-weight: 500;
            transition: all 0.3s ease-in-out;
            position: relative;
            text-decoration: none;
            padding-bottom: 0.25rem;
        }
        nav a:hover {
            color: #FF8FAB; 
        }
        nav a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #FFB3C6, #FB6F92); 
            transition: width 0.3s ease-in-out;
        }
        nav a:hover::after, nav a.font-bold::after {
            width: 100%;
        }
        nav a.font-bold {
            color: #FB6F92; 
            font-weight: 700;
        }

        
        .product-card {
            background-color: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 8px 20px rgba(236, 189, 196, 0.2); 
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            overflow: hidden;
            border: 1px solid #FFE5EC; 
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(225, 138, 170, 0.25); 
        }

        .product-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-bottom: 1px solid #F5DCE0; 
        }

        .product-card h2 {
            font-size: 1.15rem;
            font-weight: 600;
            color: #333333;
            margin-bottom: 0.5rem;
            font-family: 'Montserrat', sans-serif;
        }

        .product-card p.price-text { 
            color: #E18AAA; 
            font-weight: 700;
            font-size: 1.35rem;
            margin-bottom: 1rem;
        }

        
        .btn-bs-primary.btn-outline-primary { 
            color: #E18AAA; 
            border-color: #EFCFD4; 
            background-color: transparent;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            border-radius: 0.75rem;
            padding: 0.75rem 1.75rem;
            transition: all 0.3s ease-in-out;
        }
        .btn-bs-primary.btn-outline-primary:hover {
            background-color: #E18AAA; 
            color: #ffffff;
            border-color: #E18AAA;
            box-shadow: 0 4px 15px rgba(225, 138, 170, 0.4); 
        }

        footer {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
            margin-top: 3rem;
            padding: 1.5rem 0;
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <header class="bg-white shadow-lg py-4">
        <div class="container flex flex-col md:flex-row items-center justify-between">
            <a href="homepage.php" class="text-3xl font-bold logo-text transition-colors">FlauntFit</a>
            
            <nav class="mt-4 md:mt-0 flex items-center">
                <ul class="flex space-x-4 md:space-x-8">
                    <li><a href="homepage.php" class="font-semibold">หน้าหลัก</a></li>
                    <li><a href="index.php" class="font-bold">สินค้าทั้งหมด</a></li>
                    <li><a href="edit_my_profile.php" class="font-semibold">แก้ไขข้อมูล</a></li>
                    <li><a href="cart.php" class="font-semibold">ตะกร้าสินค้า</a></li>
                    <li><a href="order_history.php" class="font-semibold">ประวัติการสั่งซื้อ</a></li>
                    <li><a href="index.php" class="font-semibold">ออกจากระบบ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="py-12">
        <div class="container">
            <h1 class="text-4xl font-bold mb-10 text-center">สินค้าทั้งหมด</h1>
            
            <div id="product-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-name="<?php echo htmlspecialchars($product['product_name']); ?>" data-category="<?php echo htmlspecialchars($product['category']); ?>">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                            <div class="p-6 text-center">
                                <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                                <p class="price-text font-bold mb-4">฿<?php echo number_format($product['price'], 2); ?></p>
                                <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-outline-primary btn-bs-primary">ดูรายละเอียด</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="col-span-full text-center text-gray-500">ไม่พบสินค้า</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-white shadow-lg mt-8 py-6 text-center text-gray-600">
        &copy; 2025 FlauntFit. All rights reserved.
    </footer>
    
    <script>
        
    </script>
</body>
</html>