<?php
session_start();
require_once 'connectdb.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$products = [];

if ($query !== '') {
    $stmt = $conn->prepare("SELECT product_id, product_name, price, image_url FROM products WHERE product_name LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<a href="homepage.php" class="text-blue-500 hover:text-blue-700 font-semibold">ย้อนกลับ</a>
    <meta charset="UTF-8">
    <title>ผลการค้นหา - <?php echo htmlspecialchars($query); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-white shadow-lg py-4">
        <div class="container mx-auto px-4 flex items-center justify-between">
            <a href="homepage.php" class="text-3xl font-bold text-pink-500">FlauntFit</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-pink-600 mb-6">ผลการค้นหา: "<?php echo htmlspecialchars($query); ?>"</h1>

        <?php if (!empty($products)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="w-full h-48 object-cover">
                        <div class="p-4 text-center">
                            <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($product['product_name']); ?></h2>
                            <p class="text-pink-500 text-xl font-bold mt-2">฿<?php echo number_format($product['price'], 2); ?></p>
                            <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="mt-4 inline-block bg-pink-500 text-white py-2 px-4 rounded-full hover:bg-pink-600">ดูรายละเอียด</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 mt-6">ไม่พบสินค้าที่ตรงกับคำค้น</p>
        <?php endif; ?>
    </main>
</body>
</html>