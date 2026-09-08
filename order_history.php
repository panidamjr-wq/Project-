<?php
session_start();
require_once 'connectdb.php';

if (!isset($_SESSION['user_id'])) {
    
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT orders_id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
$conn->close();

function getStatusThai($s) {
    
    switch ($s) {
        case 'pending': return 'รอการชำระเงิน/รอการยืนยัน';
        case 'processing': return 'กำลังดำเนินการ';
        case 'shipped': return 'จัดส่งแล้ว';
        case 'completed': return 'เสร็จสมบูรณ์';
        case 'cancelled': return 'ยกเลิกแล้ว';
        default: return $s;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<a href="homepage.php" class="text-blue-500 hover:text-red-700 font-semibold">ย้อนกลับ</a>
<meta charset="utf-8">
<title>ประวัติคำสั่งซื้อ</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-Kanit p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-pink-600 mb-6">🗓️ ประวัติคำสั่งซื้อ</h1>

    <?php if ($orders->num_rows > 0): ?>
    <table class="w-full border-collapse">
        <thead class="bg-pink-100">
            <tr>
                <th class="p-3 border text-left">รหัส</th>
                <th class="p-3 border text-left">วันที่สั่งซื้อ</th>
                <th class="p-3 border text-right">ยอดรวม</th>
                <th class="p-3 border text-center">สถานะ</th>
                <th class="p-3 border text-center">รายละเอียด</th>
            </tr>
        </thead>
        <tbody>
            <?php while($order = $orders->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
                <td class="p-3 border">#<?php echo htmlspecialchars($order['orders_id']); ?></td>
                <td class="p-3 border"><?php echo date("d/m/Y", strtotime($order['created_at'])); ?></td>
                <td class="p-3 border text-right"><?php echo number_format($order['total_amount'], 2); ?> ฿</td>
                <td class="p-3 border text-center">
                    <span class="font-semibold text-sm text-<?php echo ($order['status'] == 'shipped' || $order['status'] == 'completed') ? 'green' : (($order['status'] == 'pending') ? 'orange' : 'blue'); ?>-600">
                        <?php echo getStatusThai($order['status']); ?>
                    </span>
                </td>
                <td class="p-3 border text-center">
                    <a href="order_confirmation.php?order_id=<?php echo $order['orders_id']; ?>" class="text-pink-500 hover:text-pink-700 underline text-sm">ดูรายละเอียด</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="text-center text-gray-500 p-8 border rounded-lg">คุณยังไม่มีประวัติการสั่งซื้อในระบบ</p>
    <?php endif; ?>

    <div class="mt-6 text-center">
        <a href="homepage.php" class="px-4 py-2 bg-pink-500 text-white rounded-full hover:bg-pink-600 transition">กลับสู่หน้าหลัก</a>
    </div>
</div>
</body>
</html>