<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f7f9fc;
            color: #4b5563;
        }
        .container {
            max-width: 960px;
            margin: 2rem auto;
            padding: 1.5rem;
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .product-image {
            border-radius: 0.75rem;
            margin-bottom: 1rem;
        }
        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-delete {
            background-color: #f43f5e;
            color: #ffffff;
        }
        .btn-delete:hover {
            background-color: #e11d48;
        }
        .btn-edit {
            background-color: #3b82f6;
            color: #ffffff;
        }
        .btn-edit:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-extrabold text-[#e91e63]">รายการสินค้า</h1>
        <a href="backend_dashboard.php" class="text-blue-500 hover:text-blue-700 font-semibold">กลับสู่แดชบอร์ด</a>
    </div>

    <?php
    include("connectdb.php");

    // Change table name to products
    $sql = "SELECT * FROM `products` ORDER BY product_id DESC";
    $rs = mysqli_query($conn, $sql);

    while($data = mysqli_fetch_array($rs)){
        echo "<div class='product-card'>";
        echo "<h2 class='text-2xl font-bold text-gray-800 mb-2'>" . $data['product_name'] . "</h2>";
        echo "<p class='text-gray-600 mb-2'>รหัสสินค้า: " . $data['product_id'] . "</p>";
        echo "<p class='text-gray-600 mb-2'>รายละเอียด: " . $data['description'] . "</p>";
        echo "<p class='text-xl font-bold text-red-500 mb-2'>ราคา: " . number_format($data['price'], 2) . " บาท</p>";
        
        // Display image from the saved URL
        if (!empty($data['image_url'])) {
            echo "<img src='" . htmlspecialchars($data['image_url']) . "' alt='" . htmlspecialchars($data['product_name']) . "' class='product-image' width='200'>";
        } else {
            echo "<img src='https://placehold.co/200x200?text=No+Image' alt='No Image' class='product-image'>";
        }

        echo "<p class='text-sm text-gray-500 mb-4'>สถานะ: " . $data['status'] . " | วันที่สร้าง: " . $data['created_at'] . "</p>";
        
        echo "<div class='flex gap-4'>";
        // Link to delete product
        echo "<a href='delete_product.php?id=" . $data['product_id'] . "' class='btn btn-delete' onclick='return confirm(\"ยืนยันการลบสินค้า " . htmlspecialchars($data['product_name']) . " หรือไม่?\");'>ลบ</a>";
        
        // Link to edit product (passing product ID to the edit page)
        echo "<a href='product_edit.php?id=" . $data['product_id'] . "' class='btn btn-edit'>แก้ไข</a>";
        echo "</div>";
        echo "</div>";
    }

    mysqli_close($conn);
    ?>
</div>

</body>
</html>
