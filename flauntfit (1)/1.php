<?php
require 'connectdb.php';
require 'header.php';

// รับพารามิเตอร์การค้นหา/ประเภท
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// สร้าง query แบบปลอดภัย
$sql = "SELECT * FROM products WHERE 1";
$params = [];
if ($q !== '') {
    $sql .= " AND (name LIKE :q OR description LIKE :q)";
    $params[':q'] = "%$q%";
}
if ($category !== '') {
    $sql .= " AND category = :cat";
    $params[':cat'] = $category;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// ดึง list หมวดหมู่
$cats = $pdo->query("SELECT DISTINCT category FROM products")->fetchAll(PDO::FETCH_COLUMN);
?>

<h2>สินค้า</h2>

<form method="get" action="index.php">
  <div class="form-row">
    <input type="text" name="q" placeholder="ค้นหาสินค้า" value="<?php echo htmlspecialchars($q); ?>">
  </div>
  <div class="form-row">
    <select name="category">
      <option value="">ทุกประเภท</option>
      <?php foreach($cats as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>" <?php if($c==$category) echo 'selected'; ?>><?php echo htmlspecialchars($c); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button class="btn" type="submit">ค้นหา</button>
</form>

<div class="product-grid">
  <?php foreach($products as $p): ?>
    <div class="product-card">
      <a href="product.php?id=<?php echo $p['id']; ?>">
        <img src="/flauntfit/images/<?php echo htmlspecialchars($p['image'] ?: 'placeholder.jpg'); ?>" alt="">
      </a>
      <h3><?php echo htmlspecialchars($p['name']); ?></h3>
      <p><?php echo number_format($p['price'],2); ?> ฿</p>
      <a class="btn" href="add_to_cart.php?id=<?php echo $p['id']; ?>&qty=1">หยิบใส่ตะกร้า</a>
      <a class="btn" style="background:#555" href="product.php?id=<?php echo $p['id']; ?>">ดูรายละเอียด</a>
    </div>
  <?php endforeach; ?>
</div>

<?php require 'footer.php'; ?>
