<?php
session_start();
require_once 'connectdb.php'; 

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์ในการลบผู้ใช้งาน";
    header("Location: user_list.php");
    exit();
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ไม่พบ ID ผู้ใช้งานที่ต้องการลบ";
    header("Location: user_list.php");
    exit();
}


if (!isset($conn) || !$conn) {
    $_SESSION['error_message'] = "การเชื่อมต่อฐานข้อมูลล้มเหลว กรุณาตรวจสอบไฟล์ connectdb.php";
    header("Location: user_list.php");
    exit();
}

$user_id_to_delete = intval($_GET['id']);

$sql = "DELETE FROM users WHERE id = {$user_id_to_delete}";


if (mysqli_query($conn, $sql)) {
    
    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['success_message'] = "ลบผู้ใช้งาน ID: {$user_id_to_delete} สำเร็จแล้ว";
    } else {
        $_SESSION['error_message'] = "ไม่พบผู้ใช้งาน ID: {$user_id_to_delete} ในระบบ หรือไม่มีการเปลี่ยนแปลง";
    }
} else {
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
}

mysqli_close($conn);

header("Location: user_list.php");
exit();
?>