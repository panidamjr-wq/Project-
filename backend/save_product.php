<?php
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบว่าไฟล์เชื่อมต่อฐานข้อมูลมีอยู่จริงและสามารถเรียกใช้ได้
if (!@include("connectdb.php")) {
    echo json_encode(["success" => false, "message" => "ไม่พบไฟล์ connectdb.php หรือมีข้อผิดพลาดในการรวมไฟล์"]);
    exit();
}

// ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if (!$conn) {
    echo json_encode(["success" => false, "message" => "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error()]);
    exit();
}

// ตรวจสอบว่าข้อมูลจากฟอร์มครบถ้วนหรือไม่
if (!isset($_POST['productName'], $_POST['productPrice'], $_POST['productDescription']) || !isset($_FILES['imageFile'])) {
    echo json_encode(["success" => false, "message" => "ข้อมูลฟอร์มไม่ครบถ้วน"]);
    mysqli_close($conn);
    exit();
}

$productName = $_POST['productName'];
$productPrice = $_POST['productPrice'];
$productDescription = $_POST['productDescription'];
$imageFile = $_FILES['imageFile'];

// ตั้งค่าสถานะและเวลาปัจจุบัน
$status = 'active'; // กำหนดสถานะสินค้าเป็น 'active'
$created_at = date('Y-m-d H:i:s');

// จัดการการอัปโหลดรูปภาพ
$target_dir = "uploads/";
$image_name = uniqid() . '-' . basename($imageFile["name"]);
$target_file = $target_dir . $image_name;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// ตรวจสอบประเภทไฟล์
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($imageFileType, $allowed_types)) {
    echo json_encode(["success" => false, "message" => "ไม่อนุญาตให้ใช้ไฟล์ประเภทนี้"]);
    mysqli_close($conn);
    exit();
}

// ตรวจสอบขนาดไฟล์ (ไม่เกิน 500KB)
if ($imageFile["size"] > 500000) {
    echo json_encode(["success" => false, "message" => "ขนาดไฟล์ใหญ่เกินไป"]);
    mysqli_close($conn);
    exit();
}

// เริ่มต้น Transaction เพื่อความปลอดภัยของข้อมูล
mysqli_begin_transaction($conn);
$is_upload_success = false;

try {
    // ตรวจสอบและสร้างไดเรกทอรี "uploads" ถ้ายังไม่มี
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // ย้ายไฟล์รูปภาพ
    if (move_uploaded_file($imageFile["tmp_name"], $target_file)) {
        $is_upload_success = true;
    } else {
        throw new Exception("ไม่สามารถอัปโหลดไฟล์ได้");
    }

    // สร้าง URL สำหรับรูปภาพ
    $image_url = 'uploads/' . $image_name;

    // เตรียมคำสั่ง SQL เพื่อป้องกัน SQL Injection
    $sql = "INSERT INTO `products` (`product_name`, `description`, `price`, `image_url`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    // ผูกตัวแปรเข้ากับคำสั่ง SQL
    mysqli_stmt_bind_param($stmt, "ssisss", $productName, $productDescription, $productPrice, $image_url, $status, $created_at);

    // ทำการ execute คำสั่ง
    if (mysqli_stmt_execute($stmt)) {
        // หากสำเร็จ ให้ยืนยันการบันทึกข้อมูล (commit)
        mysqli_commit($conn);
        echo json_encode(["success" => true, "message" => "บันทึกข้อมูลสินค้าเรียบร้อยแล้ว"]);
    } else {
        throw new Exception("บันทึกข้อมูลสินค้าล้มเหลว: " . mysqli_error($conn));
    }

} catch (Exception $e) {
    // หากมีข้อผิดพลาด ให้ย้อนกลับ (rollback)
    mysqli_rollback($conn);

    // ลบไฟล์ที่อัปโหลดไปแล้วหากเกิดข้อผิดพลาด
    if ($is_upload_success) {
        unlink($target_file);
    }
    
    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาด: " . $e->getMessage()]);

} finally {
    // ปิดการเชื่อมต่อ
    mysqli_close($conn);
}
?>