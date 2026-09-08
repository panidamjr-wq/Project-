<?php
session_start();
require_once 'connectdb.php';

// TODO: ตรวจสอบสิทธิ์ admin ก่อนใช้งาน

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("ไม่พบหมายเลขคำสั่งซื้อ");
}
$order_id = intval($_GET['order_id']);

// ดึงข้อมูล order พร้อมข้อมูลลูกค้า
$sql = "SELECT o.*, u.username, u.full_name, u.email, u.phone_number, u.shipping_address AS user_address
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.orders_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) die("ไม่พบคำสั่งซื้อ");

// ดึงสินค้า
$sql_items = "SELECT oi.*, p.product_name 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$stmt2 = $conn->prepare($sql_items);
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$items = $stmt2->get_result();
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>รายละเอียดคำสั่งซื้อ</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-Kanit p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-pink-600 mb-4">รายละเอียดคำสั่งซื้อ #<?php echo $order_id; ?></h1>

    <div class="mb-4 p-4 bg-gray-50 border rounded">
        <p><b>ลูกค้า:</b> <?php echo htmlspecialchars($order['full_name'] ?: $order['username']); ?></p>
        <p><b>อีเมล:</b> <?php echo htmlspecialchars($order['email']); ?></p>
        <p><b>โทร:</b> <?php echo htmlspecialchars($order['phone_number']); ?></p>
        <p><b>ที่อยู่จาก users:</b> <?php echo nl2br(htmlspecialchars($order['user_address'])); ?></p>
    </div>

    <div class="mb-4 p-4 bg-gray-50 border rounded">
        <h2 class="font-semibold">ข้อมูลการจัดส่ง (ตอนสั่งซื้อ)</h2>
        <p><b>ชื่อ:</b> <?php echo htmlspecialchars($order['shipping_name']); ?></p>
        <p><b>โทร:</b> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
        <p><b>ที่อยู่:</b> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
    </div>

    <div class="mb-4 p-4 bg-gray-50 border rounded">
        <p><b>วันที่สั่งซื้อ:</b> <?php echo date("d/m/Y H:i", $order['created']); ?></p>
        <p><b>ยอดรวม:</b> <?php echo number_format($order['total_amount'], 2); ?> ฿</p>
        <p><b>สถานะ:</b> <?php echo htmlspecialchars($order['status']); ?></p>
        <p><b>เลขพัสดุ:</b> <?php echo htmlspecialchars($order['tracking_number'] ?? '-'); ?></p>
    </div>

    <h2 class="font-semibold mb-2">🛍 รายการสินค้า</h2>
    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">สินค้า</th>
                <th class="p-2 border">จำนวน</th>
                <th class="p-2 border">ราคา</th>
            </tr>
        </thead>
        <tbody>
            <?php while($it = $items->fetch_assoc()): ?>
            <tr>
                <td class="p-2 border"><?php echo htmlspecialchars($it['product_name']); ?></td>
                <td class="p-2 border text-center"><?php echo $it['quantity']; ?></td>
                <td class="p-2 border text-right"><?php echo number_format($it['price'],2); ?> ฿</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="mt-6">
        <a href="admin_orders.php" class="px-4 py-2 bg-gray-500 text-white rounded">⬅ กลับ</a>
        <a href="admin_update_tracking.php?order_id=<?php echo $order_id; ?>" class="px-4 py-2 bg-pink-500 text-white rounded">แก้ไขสถานะ / เลขพัสดุ</a>
    </div>
</div>
</body>
</html>
