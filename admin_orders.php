<?php
session_start();
require_once 'connectdb.php';

// TODO: ตรวจสอบสิทธิ์ admin ก่อนใช้งาน

$sql = "SELECT o.*, u.username, u.full_name, u.email 
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.created DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<a href="backend_dashboard.php" class="text-blue-500 hover:text-red-700 font-semibold">ย้อนกลับ</a>
<meta charset="utf-8">
<title>จัดการออเดอร์</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-Kanit p-6">
<div class="max-w-6xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold mb-4 text-pink-600">📦 รายการออเดอร์</h1>
    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">รหัส</th>
                <th class="p-2 border">ลูกค้า</th>
                <th class="p-2 border">อีเมล</th>
                <th class="p-2 border">ยอดรวม</th>
                <th class="p-2 border">วันที่สั่งซื้อ</th>
                <th class="p-2 border">สถานะ</th>
                <th class="p-2 border">เลขพัสดุ</th>
                <th class="p-2 border">การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td class="p-2 border">#<?php echo $row['orders_id']; ?></td>
                <td class="p-2 border"><?php echo htmlspecialchars($row['full_name'] ?: $row['username']); ?></td>
                <td class="p-2 border"><?php echo htmlspecialchars($row['email']); ?></td>
                <td class="p-2 border text-right"><?php echo number_format($row['total_amount'], 2); ?> ฿</td>
                <td class="p-2 border"><?php echo date("d/m/Y H:i", $row['created']); ?></td>
                <td class="p-2 border"><?php echo htmlspecialchars($row['status']); ?></td>
                <td class="p-2 border"><?php echo htmlspecialchars($row['tracking_number'] ?? '-'); ?></td>
                <td class="p-2 border text-center">
                    <a href="admin_order_detail.php?order_id=<?php echo $row['orders_id']; ?>" class="px-3 py-1 bg-blue-500 text-white rounded">ดูรายละเอียด</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
