<?php
session_start(); 

if (!@include("connectdb.php")) {
    
    $db_error = "ไม่พบไฟล์ connectdb.php หรือมีข้อผิดพลาดในการรวมไฟล์ โปรดตรวจสอบการเชื่อมต่อฐานข้อมูล";
    $products = [];
} else {
    
    if (!$conn) {
        $db_error = "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error();
        $products = [];
    } else {
        
        $products = [];
        $sql = "SELECT product_id, product_name, price, image_url, status, created_at FROM `products` ORDER BY product_id DESC";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
        } else {
            $db_error = "Error fetching products: " . mysqli_error($conn);
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlauntFit - จัดการสินค้า</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f7f9fc;
        }
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem; 
        }
        .card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 1.5rem; 
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
    </style>
</head>
<body class="p-4">
    <div class="main-container">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">จัดการสินค้า</h1>

        
        <?php 
        $message_text = '';
        $message_class = '';
        $should_display = false;
        
        if (isset($_SESSION['success_message'])) {
            $message_text = $_SESSION['success_message'];
            $message_class = 'bg-green-100 text-green-800 border-green-400';
            $should_display = true;
            unset($_SESSION['success_message']); 
        } elseif (isset($_SESSION['error_message'])) {
            $message_text = $_SESSION['error_message'];
            $message_class = 'bg-red-100 text-red-800 border-red-400';
            $should_display = true;
            unset($_SESSION['error_message']); 
        }
        
        if ($should_display) {
            echo "<div id=\"message-box\" class=\"p-4 mb-6 rounded-lg font-medium border-l-4 {$message_class} animate-fadeIn\">{$message_text}</div>";
        }

        if (isset($db_error)) {
             echo "<div class=\"p-4 mb-6 rounded-lg font-medium border-l-4 bg-red-100 text-red-800 border-red-400\">{$db_error}</div>";
        }
        ?>

        <div class="flex justify-between items-center mb-6 flex-wrap space-y-4 sm:space-y-0">
            <a href="product_add.php" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 shadow-lg flex items-center w-full sm:w-auto justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                เพิ่มสินค้าใหม่
            </a>
            <a href="backend_dashboard.php" class="text-gray-600 hover:text-pink-600 transition duration-150 w-full sm:w-auto text-center sm:text-right">
                &larr; กลับไปหน้า Dashboard
            </a>
        </div>

        <div class="card">
            <div class="table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปภาพ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อสินค้า</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ราคา</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    ยังไม่มีสินค้าในระบบ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($product['product_id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="รูปสินค้า" class="w-12 h-12 object-cover rounded-md">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold"><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">฿<?php echo number_format($product['price'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $product['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo htmlspecialchars($product['status'] === 'active' ? 'พร้อมขาย' : 'ระงับ'); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="product_edit.php?id=<?php echo $product['product_id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">แก้ไข</a>
                                        <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>')" class="text-red-600 hover:text-red-900">ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <script>
        function confirmDelete(id, name) {
            
            const modal = document.createElement('div');
            modal.innerHTML = `
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-xl p-8 max-w-sm mx-auto shadow-2xl">
                        <h3 class="text-xl font-bold text-red-600 mb-4">ยืนยันการลบสินค้า</h3>
                        <p class="text-gray-700 mb-6">คุณแน่ใจหรือไม่ว่าต้องการลบสินค้า <strong>${name}</strong> (ID: ${id})?</p>
                        <div class="flex justify-end space-x-3">
                            <button id="cancel-btn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-150">ยกเลิก</button>
                            <!-- ส่ง Request ไปยังไฟล์ delete_product.php -->
                            <a href="delete_product.php?id=${id}" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150">ยืนยันการลบ</a>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            
            document.getElementById('cancel-btn').addEventListener('click', () => {
                document.body.removeChild(modal);
            });
        }
    </script>
</body>
</html>