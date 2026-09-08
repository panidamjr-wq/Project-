<?php
session_start();
require_once 'connectdb.php';

// TODO: ตรวจสอบสิทธิ์ผู้ใช้เป็น admin ก่อน (ตัวอย่างไม่ได้ตรวจสอบจริง)
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("ต้องระบุ order_id");
}
$order_id = intval($_GET['order_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tracking = trim($_POST['tracking_number'] ?? '');
    $status = trim($_POST['status'] ?? 'processing');

    $sql = "UPDATE orders SET tracking_number = ?, status = ? WHERE orders_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $tracking, $status, $order_id);
    if ($stmt->execute()) {
        $msg = "อัปเดตสำเร็จ";
    } else {
        $msg = "เกิดข้อผิดพลาด: " . $stmt->error;
    }
    $stmt->close();
}

// ดึงข้อมูล order ปัจจุบัน
$sql2 = "SELECT orders_id, status, tracking_number FROM orders WHERE orders_id = ?";
$s2 = $conn->prepare($sql2);
$s2->bind_param("i", $order_id);
$s2->execute();
$order = $s2->get_result()->fetch_assoc();
$s2->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
<a href="admin_orders.php" class="text-blue-500 hover:text-red-700 font-semibold">ย้อนกลับ</a>
<meta charset="utf-8">
<title>Admin - อัปเดตเลขพัสดุ</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 font-Kanit bg-gray-50">
<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-xl font-bold mb-4">อัปเดตเลขพัสดุ — Order #<?php echo htmlspecialchars($order_id); ?></h1>
    <?php if (!empty($msg)): ?>
        <p class="mb-3 text-green-600"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="block font-medium">สถานะ</label>
            <select name="status" class="w-full border p-2 rounded">
                <?php $st = $order['status'] ?? 'pending'; ?>
                <option value="pending" <?php if($st==='pending') echo 'selected'; ?>>pending</option>
                <option value="processing" <?php if($st==='processing') echo 'selected'; ?>>processing</option>
                <option value="shipped" <?php if($st==='shipped') echo 'selected'; ?>>shipped</option>
                <option value="completed" <?php if($st==='completed') echo 'selected'; ?>>completed</option>
                <option value="cancelled" <?php if($st==='cancelled') echo 'selected'; ?>>cancelled</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="block font-medium">เลขพัสดุ (tracking number)</label>
            <input type="text" name="tracking_number" class="w-full border p-2 rounded" value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>">
        </div>
        <div class="text-right">
            <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded">บันทึก</button>
        </div>
    </form>
</div>
</body>
</html>
