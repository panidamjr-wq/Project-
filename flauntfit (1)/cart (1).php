<?php
require 'connectdb.php';
require 'header.php';
if (session_status() == PHP_SESSION_NONE) session_start();

$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // อัปเดตจำนวน (update_cart.php หรือทำที่นี่ก็ได้)
  foreach($_POST['qty'] as $id => $qty) {
    $id = intval($id);
    $q = max(0,intval($qty));
    if ($q === 0) {
      unset($_SESSION['cart'][$id]);
    } else {
      $_SESSION['cart'][$id]['qty'] = $q;
    }
  }
  header("Location: cart.php");
  exit;
}

$total = 0;
?>
<h2>ตะกร้าสินค้า</h2>
<?php if(empty($cart)): ?>
  <p>ไม่มีสินค้าในตะกร้า</p>
<?php else: ?>
  <form method="post" action="cart.php">
    <table class="cart-table">
      <thead><tr><th>สินค้า</th><th>ราคา</th><th>จำนวน</th><th>รวม</th></tr></thead>
      <tbody>
      <?php foreach($cart as $item): $sub = $item['price'] * $item['qty']; $total += $sub; ?>
        <tr>
          <td>
            <img src="/flauntfit/images/<?php echo htmlspecialchars($item['image']); ?>" style="width:60px;height:60px;object-fit:cover">
            <?php echo htmlspecialchars($item['name']); ?>
          </td>
          <td><?php echo number_format($item['price'],2); ?> ฿</td>
          <td><input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['qty']; ?>" min="0"></td>
          <td><?php echo number_format($sub,2); ?> ฿</td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p>ยอดรวม: <strong><?php echo number_format($total,2); ?> ฿</strong></p>
    <button class="btn" type="submit">อัปเดตตะกร้า</button>
    <a class="btn" href="checkout.php">ไปชำระเงิน</a>
  </form>
<?php endif; ?>

<?php require 'footer.php'; ?>
