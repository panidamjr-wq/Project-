<?php
session_start();
require_once 'connectdb.php';

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    die("ไม่พบหมายเลขคำสั่งซื้อ หรือคุณไม่ได้เข้าสู่ระบบ");
}
$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];
$message = '';

$sql = "SELECT user_id, status, shipping_address, shipping_phone, shipping_name FROM orders WHERE orders_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("ไม่พบคำสั่งซื้อ หรือคุณไม่มีสิทธิ์");
}

if ($order['status'] !== 'pending') {
    die("<div style='max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #f99; background: #fee; text-align: center;'>ไม่สามารถแก้ไขได้: คำสั่งซื้ออยู่ในสถานะ <strong>" . $order['status'] . "</strong><br><a href='order_confirmation.php?order_id=$order_id'>กลับไปหน้าคำสั่งซื้อ</a></div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['shipping_name'] ?? '');
    $new_address = trim($_POST['shipping_address'] ?? '');
    $new_phone = trim($_POST['shipping_phone'] ?? '');

    if (empty($new_address) || empty($new_phone) || empty($new_name)) {
        $message = "❌ กรุณากรอกชื่อผู้รับ, ที่อยู่, และเบอร์โทรศัพท์ให้ครบถ้วน";
    } else {
        
        $sql_update = "UPDATE orders SET shipping_name = ?, shipping_address = ?, shipping_phone = ? WHERE orders_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $new_name, $new_address, $new_phone, $order_id);
        
        if ($stmt_update->execute()) {
            $message = "✅ แก้ไขที่อยู่จัดส่งเรียบร้อยแล้ว";
            
            $order['shipping_name'] = $new_name;
            $order['shipping_address'] = $new_address;
            $order['shipping_phone'] = $new_phone;
        } else {
            $message = "❌ เกิดข้อผิดพลาดในการแก้ไข: " . $stmt_update->error;
        }
        $stmt_update->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>แก้ไขที่อยู่จัดส่ง #<?php echo $order_id; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-Kanit p-6">
<div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold text-blue-600 mb-6">✏️ แก้ไขที่อยู่จัดส่ง คำสั่งซื้อ #<?php echo $order_id; ?></h1>

    <?php if (!empty($message)): ?>
    <p class="p-3 mb-4 text-center font-bold border rounded-lg <?php echo (strpos($message, '✅') !== false ? 'bg-green-100 text-green-700 border-green-300' : 'bg-red-100 text-red-700 border-red-300'); ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label for="shipping_name" class="block text-gray-700 font-semibold mb-2">ชื่อผู้รับ</label>
            <input type="text" id="shipping_name" name="shipping_name" class="w-full border p-3 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($order['shipping_name'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-4">
            <label for="shipping_address" class="block text-gray-700 font-semibold mb-2">ที่อยู่จัดส่งใหม่</label>
            <textarea id="shipping_address" name="shipping_address" rows="4" class="w-full border p-3 rounded-lg focus:ring-blue-500 focus:border-blue-500" required><?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?></textarea>
        </div>

        <div class="mb-6">
            <label for="shipping_phone" class="block text-gray-700 font-semibold mb-2">เบอร์โทรศัพท์ใหม่</label>
            <input type="text" id="shipping_phone" name="shipping_phone" class="w-full border p-3 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="<?php echo htmlspecialchars($order['shipping_phone'] ?? ''); ?>" required>
        </div>

        <div class="flex justify-between items-center">
            <a href="order_confirmation.php?order_id=<?php echo $order_id; ?>" class="text-gray-500 hover:text-gray-700 transition px-4 py-2 border rounded-full">← กลับสู่หน้ายืนยัน</a>
            <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-full font-bold hover:bg-blue-600 transition shadow-md">
                💾 บันทึกการแก้ไข
            </button>
        </div>
    </form>
</div>
</body>
</html>