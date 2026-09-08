<?php
session_start();
require_once 'connectdb.php';

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("ไม่พบหมายเลขคำสั่งซื้อ");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    
    if (isset($_SESSION['user_id']) && $order['user_id'] == $_SESSION['user_id'] && $order['status'] == 'pending') {
        
        $sql_cancel = "UPDATE orders SET status = 'cancelled' WHERE orders_id = ? AND status = 'pending'";
        $stmt_cancel = $conn->prepare($sql_cancel);
        $stmt_cancel->bind_param("i", $order_id);
        
        if ($stmt_cancel->execute()) {
            $_SESSION['message'] = "✅ คำสั่งซื้อ #$order_id ถูกยกเลิกเรียบร้อยแล้ว";
        } else {
            $_SESSION['message'] = "❌ เกิดข้อผิดพลาดในการยกเลิก: " . $stmt_cancel->error;
        }
        $stmt_cancel->close();
        
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    } else {
        $_SESSION['message'] = "⚠️ ไม่สามารถยกเลิกคำสั่งซื้อนี้ได้ (สถานะไม่อนุญาต หรือคุณไม่มีสิทธิ์)";
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    }
}
$order_id = intval($_GET['order_id']);

$sql = "SELECT * FROM orders WHERE orders_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("ไม่พบคำสั่งซื้อที่ระบุ");
}

$sql_items = "SELECT oi.*, p.product_name FROM order_items oi
              LEFT JOIN products p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$stmt2 = $conn->prepare($sql_items);
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$orderItems = $stmt2->get_result();
$stmt2->close();

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
<meta charset="utf-8">
<title>ยืนยันคำสั่งซื้อ #<?php echo htmlspecialchars($order_id); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-Kanit p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-pink-600 mb-3">✅ คำสั่งซื้อสำเร็จ</h1>
    <p class="text-gray-700 mb-4">รหัสคำสั่งซื้อ: <strong>#<?php echo htmlspecialchars($order['orders_id']); ?></strong></p>

    <div class="mb-4 p-4 bg-gray-100 rounded">
        <h2 class="font-semibold">ข้อมูลการจัดส่ง</h2>
        <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($order['shipping_name'] ?? '-'); ?></p>
        <p><strong>โทร:</strong> <?php echo htmlspecialchars($order['shipping_phone'] ?? '-'); ?></p>
        <p><strong>ที่อยู่:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? '-')); ?></p>
    </div>

    <div class="mb-4 p-4 bg-gray-50 border rounded">
        <p><strong>สถานะ:</strong> <?php echo getStatusThai($order['status'] ?? 'pending'); ?></p>
        <p><strong>เลขพัสดุ:</strong>
            <?php
                if (!empty($order['tracking_number'])) {
                    echo htmlspecialchars($order['tracking_number']);
                } else {
                    echo "<span class='text-gray-500'>ยังไม่มีเลขพัสดุ</span>";
                }
            ?>
        </p>
    </div>

    <h3 class="font-semibold mb-2">รายการสินค้า</h3>
    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">สินค้า</th>
                <th class="p-2 border">จำนวน</th>
                <th class="p-2 border">ราคา</th>
            </tr>
        </thead>
        <tbody>
            <?php while($it = $orderItems->fetch_assoc()): ?>
            <tr>
                <td class="p-2 border"><?php echo htmlspecialchars($it['product_name'] ?? 'ไม่ทราบชื่อ'); ?></td>
                <td class="p-2 border text-center"><?php echo intval($it['quantity']); ?></td>
                <td class="p-2 border text-right"><?php echo number_format($it['price'],2); ?> ฿</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="text-right mt-4 font-bold text-lg text-pink-600">
        ยอดรวม: <?php echo number_format($order['total_amount'],2); ?> ฿
    </div>
    
    <?php
    
    if (isset($_SESSION['user_id']) && $order['user_id'] == $_SESSION['user_id'] && $order['status'] == 'pending'):
    ?>
    <div class="mt-8 border-t pt-6 text-center bg-yellow-50 p-4 rounded-lg">
        <h3 class="text-xl font-bold text-red-600 mb-4">⚙️ จัดการคำสั่งซื้อ</h3>
        
        <a href="edit_shipping.php?order_id=<?php echo $order_id; ?>" class="inline-block px-6 py-3 bg-blue-500 text-white rounded-full font-semibold hover:bg-blue-600 transition shadow-md">
            ✏️ แก้ไขที่อยู่จัดส่ง
        </a>
        
        <form method="POST" action="order_confirmation.php?order_id=<?php echo $order_id; ?>" class="inline-block ml-4" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะยกเลิกคำสั่งซื้อ #<?php echo $order_id; ?>? การกระทำนี้ไม่สามารถย้อนกลับได้');">
            <input type="hidden" name="action" value="cancel_order">
            <button type="submit" class="px-6 py-3 bg-red-500 text-white rounded-full font-semibold hover:bg-red-600 transition shadow-md">
                ❌ ยกเลิกคำสั่งซื้อ
            </button>
        </form>
    </div>
    <?php endif; ?>

    <div class="mt-6 text-center">
        <a href="homepage.php" class="px-4 py-2 bg-pink-500 text-white rounded-full">กลับสู่หน้าหลัก</a>
    </div>
</div>
</body>
</html>
