<?php
session_start();
require_once 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout_items']) && !isset($_POST['confirm_payment'])) {
    
    $_SESSION['checkout_items'] = array_values($_POST['checkout_items']); 

    ?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="utf-8">
        <title>ข้อมูลการจัดส่ง - ยืนยันการสั่งซื้อ</title>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 font-Kanit">
    <div class="max-w-lg mx-auto mt-12 bg-white p-6 rounded-xl shadow">
        <h1 class="text-xl font-bold text-pink-600 mb-4">ข้อมูลการจัดส่ง</h1>
        <form action="process_payment.php" method="POST">
            <div class="mb-3">
                <label class="block font-medium">ชื่อ-นามสกุล</label>
                <input type="text" name="shipping_name" required class="w-full border p-2 rounded">
            </div>
            <div class="mb-3">
                <label class="block font-medium">เบอร์โทรศัพท์</label>
                <input type="text" name="shipping_phone" required class="w-full border p-2 rounded">
            </div>
            <div class="mb-3">
                <label class="block font-medium">ที่อยู่จัดส่ง</label>
                <textarea name="shipping_address" required class="w-full border p-2 rounded"></textarea>
            </div>
            <div class="text-center">
                <button type="submit" name="confirm_payment" class="bg-pink-500 text-white px-6 py-2 rounded-full">
                    ✅ ยืนยันชำระเงิน
                </button>
            </div>
        </form>
    </div>
    </body>
    </html>
    <?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {

    if (!isset($_SESSION['checkout_items']) || empty($_SESSION['checkout_items'])) {
        die("ไม่มีสินค้าที่เลือกสำหรับสั่งซื้อ");
    }

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        die("ตะกร้าว่าง");
    }

    $shipping_name = trim($_POST['shipping_name'] ?? '');
    $shipping_phone = trim($_POST['shipping_phone'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');

    if ($shipping_name === '' || $shipping_phone === '' || $shipping_address === '') {
        die("กรุณากรอกข้อมูลการจัดส่งให้ครบถ้วน");
    }

    $checkout_keys = $_SESSION['checkout_items']; 
    $items_to_insert = []; 
    $total_amount = 0.0;

    foreach ($checkout_keys as $key) {
        if (!isset($_SESSION['cart'][$key])) continue; 
        $row = $_SESSION['cart'][$key];
        
        $items_to_insert[] = [
            'product_id' => intval($row['product_id']),
            'quantity' => intval($row['quantity']),
            'price' => floatval($row['price']),
            'cart_key' => $key
        ];
        $total_amount += floatval($row['price']) * intval($row['quantity']);
    }

    if (empty($items_to_insert)) {
        die("ไม่มีสินค้าที่จะสั่ง (ข้อมูลไม่ถูกต้อง)");
    }

    $user_id = $_SESSION['user_id'] ?? 1;

    mysqli_begin_transaction($conn);

    try {

        $status = 'pending';
        $tracking_number = null; 
        $created = time();

        $order_data_json = json_encode($items_to_insert, JSON_UNESCAPED_UNICODE);

        $sql_order = "INSERT INTO orders (user_id, order_data, total_amount, created, shipping_name, shipping_phone, shipping_address, status, tracking_number)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_order);
        if (!$stmt) throw new Exception("Prepare order failed: " . $conn->error);

        $tn_for_bind = $tracking_number; 
        $stmt->bind_param("isdisssss", $user_id, $order_data_json, $total_amount, $created, $shipping_name, $shipping_phone, $shipping_address, $status, $tn_for_bind);
        if (!$stmt->execute()) throw new Exception("Execute order failed: " . $stmt->error);
        $order_id = $stmt->insert_id;
        $stmt->close();

        $sql_item = "INSERT INTO order_items (`order_id`, `product_id`, `quantity`, `price`) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);
        if (!$stmt_item) throw new Exception("Prepare item failed: " . $conn->error);

        foreach ($items_to_insert as $it) {
            $pid = $it['product_id'];
            $qty = $it['quantity'];
            $price = $it['price'];
            $stmt_item->bind_param("iiid", $order_id, $pid, $qty, $price);
            if (!$stmt_item->execute()) throw new Exception("Execute item failed: " . $stmt_item->error);
        }
        $stmt_item->close();

        mysqli_commit($conn);

        foreach ($items_to_insert as $it) {
            $k = $it['cart_key'];
            unset($_SESSION['cart'][$k]);
        }
        
        unset($_SESSION['checkout_items']);

        
        header("Location: order_confirmation.php?order_id=" . intval($order_id));
        exit;

    } catch (Exception $e) {
        
        mysqli_rollBack($conn);
        die("ไม่สามารถบันทึกคำสั่งซื้อได้: " . $e->getMessage());
    }
}

header("Location: cart.php");
exit;
