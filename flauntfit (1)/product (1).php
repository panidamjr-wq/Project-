<?php
require 'connectdb.php';
require 'header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id'=>$id]);
$p = $stmt->fetch();
if (!$p) {
    echo "<p>ไม่พบสินค้า</p>";
    require 'footer.php';
    exit;
}

// สมมติว่า field images เก็บเป็น comma-separated หรือเก็บในตาราง images ที่แยกต่างหาก
$images = explode(',', $p['images'] ?: $p['image']);
?>
<h2><?php echo htmlspecialchars($p['name']); ?></h2>
<div style="display:flex; gap:16px;">
  <div style="flex:1;">
    <img src="/flauntfit/images/<?php echo htmlspecialchars($images[0]); ?>" style="width:100%; max-height:400px; object-fit:cover;">
    <?php if(count($images)>1): ?>
      <div style="display:flex; gap:8px; margin-top:8px;">
        <?php foreach($images as $img): ?>
          <img src="/flauntfit/images/<?php echo htmlspecialchars($img); ?>" style="width:80px; height:80px; object-fit:cover; border:1px solid #ddd;">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <div style="flex:1;">
    <p>ราคา: <?php echo number_format($p['price'],2); ?> ฿</p>
    <p>ประเภท: <?php echo htmlspecialchars($p['category']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
    <form action="add_to_cart.php" method="get">
      <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
      <div class="form-row">
        <label>จำนวน</label>
        <input type="number" name="qty" value="1" min="1">
      </div>
      <button class="btn">หยิบลงตะกร้า</button>
    </form>
  </div>
</div>

<?php require 'footer.php'; ?>
