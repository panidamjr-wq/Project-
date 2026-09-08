<?php
session_start();
// สมมติว่าไฟล์นี้มีข้อมูลการเชื่อมต่อฐานข้อมูล: $conn
require_once 'connectdb.php'; 

// ตรวจสอบสิทธิ์ Admin (จำเป็นสำหรับหน้าหลังบ้าน)
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header("Location: login.php"); // ส่งไปหน้าล็อกอินหากไม่มีสิทธิ์
    exit();
}

// ดึงข้อมูลประเภทสินค้าทั้งหมด
$categories = [];
$sql = "SELECT categories_id, categories_name FROM categories ORDER BY categories_id DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    mysqli_free_result($result);
} else {
    $error = "เกิดข้อผิดพลาดในการดึงข้อมูลประเภทสินค้า: " . mysqli_error($conn);
}

// ตรวจสอบและแสดงข้อความสถานะ/ข้อผิดพลาด
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการประเภทสินค้า</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; }
        .table-container { 
            max-width: 900px; 
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        th, td { padding: 12px 15px; }
        .action-btn { transition: all 0.2s; }
        .action-btn:hover { transform: translateY(-1px); }
    </style>
</head>
<body class="bg-gray-100">

<div class="table-container">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">จัดการประเภทสินค้า</h1>

    <!-- Display Messages -->
    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($success_message); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($error_message || isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error_message ?? $error); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-6">
        <a href="category_edit.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl action-btn flex items-center">
            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            เพิ่มประเภทสินค้าใหม่
        </a>
        <a href="backend_dashboard.php" class="text-blue-500 hover:text-blue-700 font-semibold">กลับสู่แดชบอร์ด</a>
    </div>

    <!-- Category Table -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ชื่อประเภทสินค้า
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        การดำเนินการ
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($category['categories_id']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php echo htmlspecialchars($category['categories_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="category_edit.php?id=<?php echo htmlspecialchars($category['categories_id']); ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 action-btn mr-4">
                                    แก้ไข
                                </a>
                                <a href="category_delete.php?id=<?php echo htmlspecialchars($category['categories_id']); ?>" 
                                   onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบประเภทสินค้า: <?php echo htmlspecialchars(addslashes($category['categories_name'])); ?>?');" 
                                   class="text-red-600 hover:text-red-900 action-btn">
                                    ลบ
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            ยังไม่มีประเภทสินค้าในระบบ
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php mysqli_close($conn); ?>
</body>
</html>