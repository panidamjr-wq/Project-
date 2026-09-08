<?php
require 'connectdb.php';
require 'header.php';
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$uid = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid'=>$uid]);
$orders = $stmt->fetchAll();
?>
<h2>ประวัติคำสั่งซื้อ</h2>
<?php foreach($orders as $o): ?>
  <div style="border:1px solid #eee;padding:12px;margin-bottom:8px;">
    <strong>คำสั่งซื้อ #<?php echo $o['id']; ?></strong>
    <div>วันที่: <?php echo $o['created_at']; ?></div>
    <div>ยอด: <?php echo number_format($o['total'],2); ?> ฿</div>
    <div>สถานะ: <?php echo htmlspecialchars($o['status']); ?></div>
    <a href="order_detail.php?id=<?php echo $o['id']; ?>">รายละเอียด</a>
  </div>
<?php endforeach; ?>

<?php require 'footer.php'; ?>
