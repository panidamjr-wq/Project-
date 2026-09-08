<?php
require 'connectdb.php';
require 'header.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) { echo "<p>ตะกร้าว่าง</p>"; require 'footer.php'; exit; }

$userId = $_SESSION['user']['id'];
// ดึงข้อมูลผู้ใช้เป็นค่าเริ่มต้น
$stmt = $pdo->prepare("SELECT name,email,address FROM users WHERE id = :id");
$stmt->execute([':id'=>$userId]);
$user = $stmt->fetch();

$total = 0; foreach($cart as $it) $total += $it['price'] * $it['qty'];
?>
<h2>สรุปคำสั่งซื้อ</h2>
<form method="post" action="process_order.php">
  <div class="form-row"><input name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required></div>
  <div class="form-row"><input name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></div>
  <div class="form-row"><textarea name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea></div>
  <div class="form-row">
    <label>วิธีชำระเงิน</label>
    <select name="payment_method">
      <option value="bank">โอนผ่านธนาคาร</option>
      <option value="cod">เก็บเงินปลายทาง</option>
    </select>
  </div>
  <p>ยอดชำระรวม: <?php echo number_format($total,2); ?> ฿</p>
  <button class="btn" type="submit">ยืนยันคำสั่งซื้อ</button>
</form>
<?php require 'footer.php'; ?>
