<?php
require 'connectdb.php';
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { header("Location: cart.php"); exit; }

$userId = $_SESSION['user']['id'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$address = trim($_POST['address']);
$pm = trim($_POST['payment_method']);
$total = 0;
foreach($cart as $it) $total += $it['price'] * $it['qty'];

$pdo->beginTransaction();
try {
  // สร้าง order
  $stmt = $pdo->prepare("INSERT INTO orders (user_id,name,email,address,payment_method,total,status,created_at) VALUES (:uid,:n,:e,:a,:pm,:total,'pending',NOW())");
  $stmt->execute([':uid'=>$userId,':n'=>$name,':e'=>$email,':a'=>$address,':pm'=>$pm,':total'=>$total]);
  $orderId = $pdo->lastInsertId();

  // บันทึกรายการสินค้า
  $stmtDetail = $pdo->prepare("INSERT INTO order_items (order_id,product_id,product_name,price,qty) VALUES (:oid,:pid,:pname,:price,:qty)");
  foreach($cart as $it){
    $stmtDetail->execute([
      ':oid'=>$orderId,
      ':pid'=>$it['id'],
      ':pname'=>$it['name'],
      ':price'=>$it['price'],
      ':qty'=>$it['qty']
    ]);
  }
  $pdo->commit();
  // เคลียร์ตะกร้า
  unset($_SESSION['cart']);
  header("Location: order_history.php?msg=order_success");
  exit;
} catch(Exception $e) {
  $pdo->rollBack();
  die("เกิดข้อผิดพลาด: " . $e->getMessage());
}
