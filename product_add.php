<?php
include 'connectdb.php';

$categories = [];
$cat_sql = "SELECT categories_name FROM `categories` ORDER BY categories_name ASC";
$cat_result = mysqli_query($conn, $cat_sql);
if ($cat_result) {
    while ($row = mysqli_fetch_assoc($cat_result)) {
        $categories[] = $row['categories_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เพิ่มสินค้า - FlauntFit</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family:'Kanit',sans-serif; background:#fce4ec; }
    .card { background:#fff; border-radius:1rem; padding:2rem; box-shadow:0 8px 20px rgba(0,0,0,.1); }
    .form-input { border:1px solid #ddd; padding:.5rem; border-radius:.5rem; width:100%; }
    .variation-row { border:1px solid #ddd; border-radius:.5rem; padding:1rem; margin-bottom:1rem; }
  </style>
  <a href="backend_dashboard.php" class="text-blue-500 hover:text-blue-700 font-semibold">กลับสู่แดชบอร์ด</a>
</head>
<body class="p-8">
<div class="max-w-4xl mx-auto card">
  <h1 class="text-2xl font-bold text-pink-500 mb-6">เพิ่มสินค้าใหม่</h1>
  

  <?php if(isset($_GET['success']) && $_GET['success']==1): ?>
    <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
      ✅ บันทึกสินค้าเรียบร้อยแล้ว
    </div>
  <?php endif; ?>

  <form action="save_product.php" method="POST" enctype="multipart/form-data" class="space-y-6">
    
    <div>
      <label>ชื่อสินค้า</label>
      <input type="text" name="productName" class="form-input" required>
    </div>
    <div>
      <label>ราคา</label>
      <input type="number" name="productPrice" step="0.01" class="form-input" required>
    </div>
    <div>
      <label>หมวดหมู่</label>
      <select name="productCategory" class="form-input" required>
        <option value="">-- เลือก --</option>
        <?php foreach($categories as $c): ?>
          <option value="<?=htmlspecialchars($c)?>"><?=$c?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>รายละเอียดสินค้า</label>
      <textarea name="productDescription" rows="3" class="form-input" required></textarea>
    </div>
    <div>
      <label>รายละเอียดเพิ่มเติม</label>
      <textarea name="additionalDetails" rows="3" class="form-input"></textarea>
    </div>
    <div>
      <label>รูปภาพหลัก</label>
      <input type="file" name="mainImage" class="form-input" accept="image/*" required>
    </div>

    <hr class="my-6">
    <h2 class="text-xl font-bold text-gray-700 mb-2">สี / ไซส์ / สต็อก / รูป</h2>
    <div id="variations-container"></div>
    <button type="button" onclick="addVariationRow()" class="text-pink-500 font-semibold">+ เพิ่มสี/ไซส์</button>

    <div class="text-center mt-4">
      <button type="submit" class="px-6 py-2 bg-pink-500 text-white rounded-lg">บันทึกสินค้า</button>
    </div>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", ()=>addVariationRow());

function addVariationRow(){
  const container=document.getElementById("variations-container");
  let timestamp = Date.now(); 
  let div=document.createElement("div");
  div.className="variation-row";
  div.innerHTML=`
    <label>สี</label>
    <input type="text" name="colors[]" class="form-input mb-2" placeholder="ชื่อสี" required>
    <label>รูปภาพ</label>
    <input type="file" name="variation_images[]" class="form-input mb-2" accept="image/*" required>

    <p class="font-semibold">เลือกไซส์:</p>
    <div class="grid grid-cols-3 gap-2">
      ${["XS","S","M","L","XL","2XL"].map(size=>`
        <div>
          <label><input type="checkbox" name="sizes[${timestamp}][]" value="${size}"> ${size}</label>
          <input type="number" name="stocks[${timestamp}][${size}]" min="0" placeholder="สต็อก ${size}" class="form-input mt-1">
        </div>
      `).join('')}
    </div>

    <button type="button" onclick="this.parentNode.remove()" class="text-red-500 mt-2">ลบ</button>
  `;
  container.appendChild(div);
}
</script>
</body>
</html>
<?php mysqli_close($conn); ?>