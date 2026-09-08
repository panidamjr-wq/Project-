<?php
// add_to_cart.php?id=xx&qty=yy
if (session_status() == PHP_SESSION_NONE) session_start();
require 'connectdb.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$qty = isset($_GET['qty']) ? max(1,intval($_GET['qty'])) : 1;

// ตรวจสอบว่ามีสินค้าใน DB
$stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id = :id");
$stmt->execute([':id'=>$id]);
$p = $stmt->fetch();
if (!$p) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
// ถ้ามีแล้วให้เพิ่มจำนวน
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty'] += $qty;
} else {
    $_SESSION['cart'][$id] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'price' => $p['price'],
        'image' => $p['image'],
        'qty' => $qty
    ];
}

// กลับไปหน้าก่อน หรือหน้า cart
header("Location: cart.php");
exit;
