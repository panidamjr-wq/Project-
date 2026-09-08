<?php
require_once 'connectdb.php';

if (!isset($_GET['order_id'])) {
    die("ไม่พบคำสั่งซื้อ");
}
$order_id = intval($_GET['order_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $tracking = $_POST['tracking_number'];
    $shipping_name = $_POST['shipping_name'];
    $shipping_phone = $_POST['shipping_phone'];
    $shipping_address = $_POST['shipping_address'];

    $sql = "UPDATE orders 
            SET status=?, tracking_number=?, shipping_name=?, shipping_phone=?, shipping_address=? 
            WHERE orders_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $status, $tracking, $shipping_name, $shipping_phone, $shipping_address, $order_id);
    $stmt->execute();

    header("Location: order_management.php");
    exit;
}

$sql = "SELECT * FROM orders WHERE orders_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>แก้ไขออเดอร์</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-Kanit">
<div class="max-w-lg mx-auto bg-white p-6 shadow rounded">
    <h1 class="text-xl font-bold mb-4">แก้ไขคำสั่งซื้อ #<?php echo $order_id; ?></h1>
    <form method="post">
        <label class="block mb-2">สถานะ</label>
        <select name="status" class="w-full border p-2 mb-4">
            <option value="pending" <?php if($order['status']=="pending") echo "selected"; ?>>Pending</option>
            <option value="processing" <?php if($order['status']=="processing") echo "selected"; ?>>Processing</option>
            <option value="shipped" <?php if($order['status']=="shipped") echo "selected"; ?>>Shipped</option>
            <option value="completed" <?php if($order['status']=="completed") echo "selected"; ?>>Completed</option>
        </select>

        <label class="block mb-2">เลขพัสดุ</label>
        <input type="text" name="tracking_number" value="<?php echo htmlspecialchars($order['tracking_number']); ?>" class="w-full border p-2 mb-4">

        <label class="block mb-2">ชื่อผู้รับ</label>
        <input type="text" name="shipping_name" value="<?php echo htmlspecialchars($order['shipping_name']); ?>" class="w-full border p-2 mb-4">

        <label class="block mb-2">เบอร์โทร</label>
        <input type="text" name="shipping_phone" value="<?php echo htmlspecialchars($order['shipping_phone']); ?>" class="w-full border p-2 mb-4">

        <label class="block mb-2">ที่อยู่</label>
        <textarea name="shipping_address" class="w-full border p-2 mb-4"><?php echo htmlspecialchars($order['shipping_address']); ?></textarea>

        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded">บันทึก</button>
        <a href="order_management.php" class="ml-2 text-gray-600">ยกเลิก</a>
    </form>
</div>
</body>
</html>
