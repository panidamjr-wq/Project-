<?php

session_start();

require_once 'connectdb.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';

$page_title = 'สินค้าทั้งหมด';
if (!empty($category)) {
    $page_title = 'สินค้าประเภท: ' . htmlspecialchars($category);
}

$sql = "SELECT product_id, product_name, price, image_url FROM products";
if (!empty($category)) {
    $sql .= " WHERE category = ?";
}

$stmt = mysqli_prepare($conn, $sql);

if (!empty($category)) {
    mysqli_stmt_bind_param($stmt, "s", $category);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .product-card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .product-card-link {
            text-decoration: none;
            color: inherit;
        }
        .product-card-link:hover .product-card {
             transform: translateY(-5px);
             box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    
    <header class="bg-white shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="homepage.php" class="text-2xl font-bold text-gray-800">FlauntFit</a>
            <nav>
                <a href="homepage.php" class="text-gray-600 hover:text-pink-500 mr-4">หน้าหลัก</a>
                <a href="products.php" class="text-gray-600 hover:text-pink-500 mr-4">สินค้าทั้งหมด</a>
                <a href="cart.php" class="text-gray-600 hover:text-pink-500 mr-4">ตะกร้าสินค้า</a>
                <a href="logout.php" class="text-gray-600 hover:text-pink-500">ออกจากระบบ</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        <div class="container">
            <h1 class="text-4xl font-bold text-gray-800 text-center my-8"><?php echo $page_title; ?></h1>

            <?php if (count($products) > 0): ?>
            <div class="flex flex-wrap justify-center gap-8">
                <?php foreach ($products as $product): ?>
                <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="product-card-link">
                    <div class="product-card w-64">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full h-48 object-cover rounded-t-xl">
                        <div class="p-5 text-center">
                            <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($product['product_name']); ?></h2>
                            <p class="text-2xl font-bold text-pink-500 mt-2">฿<?php echo number_format($product['price'], 2); ?></p>
                            <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="mt-4 inline-block bg-pink-500 text-white font-bold py-2 px-6 rounded-full hover:bg-pink-600 transition duration-300">
                                ดูรายละเอียด
                            </a>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <p class="text-2xl text-gray-600">ไม่พบสินค้าในหมวดหมู่นี้</p>
            </div>
            <?php endif; ?>
        </div>
    </main>

   
    <footer class="bg-gray-800 text-white text-center py-4 mt-8">
        © 2024 FlauntFit. All rights reserved.
    </footer>

</body>
</html>