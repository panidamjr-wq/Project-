<?php
session_start();
require_once 'connectdb.php'; 

 
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ในการลบประเภทสินค้า";
    header("Location: category_management.php");
    exit();
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ไม่พบ ID ประเภทสินค้าที่ต้องการลบ";
    header("Location: category_management.php");
    exit();
}


if (!isset($conn) || !$conn) {
    $_SESSION['error_message'] = "การเชื่อมต่อฐานข้อมูลล้มเหลว";
    header("Location: category_management.php");
    exit();
}

$category_id_to_delete = intval($_GET['id']);


$sql = "DELETE FROM categories WHERE categories_id = {$category_id_to_delete}";

if (mysqli_query($conn, $sql)) {
    
    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['success_message'] = "ลบประเภทสินค้า ID: {$category_id_to_delete} สำเร็จแล้ว";
    } else {
        $_SESSION['error_message'] = "ไม่พบประเภทสินค้า ID: {$category_id_to_delete} ในระบบ";
    }
} else {
    
    $error_detail = mysqli_error($conn);
    if (strpos($error_detail, 'Cannot delete or update a parent row') !== false) {
        $_SESSION['error_message'] = "ไม่สามารถลบประเภทสินค้านี้ได้ เนื่องจากยังมีสินค้าที่ถูกผูกกับประเภทนี้อยู่ กรุณาลบสินค้าเหล่านั้นก่อน";
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $error_detail;
    }
}

mysqli_close($conn);

header("Location: category_management.php");
exit();
?>