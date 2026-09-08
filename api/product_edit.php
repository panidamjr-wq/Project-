<?php
// Include the database connection file
require_once 'connectdb.php';

// Function to handle image uploads
function uploadImage($file, $upload_dir = "uploads/") {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_name = uniqid() . '_' . basename($file['name']);
    $target_file = $upload_dir . $file_name;
    $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed)) return false;
    return move_uploaded_file($file['tmp_name'], $target_file) ? $target_file : false;
}

// Check if a product ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ไม่พบรหัสสินค้าที่ต้องการแก้ไข");
}

$product_id = intval($_GET['id']);
$product = null;
$variations = [];
$categories = [];

// --- POST REQUEST: Handle product update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = mysqli_real_escape_string($conn, $_POST['productName']);
    $description = mysqli_real_escape_string($conn, $_POST['productDescription']);
    $price = floatval($_POST['productPrice']);
    $category = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $main_image_url = mysqli_real_escape_string($conn, $_POST['existingMainImage']);

    // Handle new main image upload
    if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] === UPLOAD_ERR_OK) {
        $new_main_image = uploadImage($_FILES['mainImage']);
        if ($new_main_image) {
            $main_image_url = $new_main_image;
        }
    }

    // Update main product data
    $sql_update_product = "UPDATE `products` SET 
                            `product_name` = ?, 
                            `description` = ?, 
                            `price` = ?, 
                            `category` = ?,
                            `image_url` = ?
                            WHERE `product_id` = ?";
    $stmt_product = mysqli_prepare($conn, $sql_update_product);
    mysqli_stmt_bind_param($stmt_product, "ssdsis", $product_name, $description, $price, $category, $main_image_url, $product_id);
    mysqli_stmt_execute($stmt_product);
    mysqli_stmt_close($stmt_product);

    // --- Handle Variations Update ---
    $submitted_variations = [];
    if (isset($_POST['variation_ids']) && is_array($_POST['variation_ids'])) {
        foreach ($_POST['variation_ids'] as $key => $var_id) {
            $submitted_variations[intval($var_id)] = [
                'color' => $_POST['colors'][$key],
                'image' => $_FILES['variation_images'],
                'sizes' => isset($_POST['sizes'][$var_id]) ? $_POST['sizes'][$var_id] : [],
                'stocks' => isset($_POST['stocks'][$var_id]) ? $_POST['stocks'][$var_id] : []
            ];
        }
    }
    
    // Get existing variation IDs for comparison
    $existing_variations_ids = [];
    $sql_get_existing = "SELECT `variation_id` FROM `product_variations` WHERE `product_id` = ?";
    $stmt_get_existing = mysqli_prepare($conn, $sql_get_existing);
    mysqli_stmt_bind_param($stmt_get_existing, "i", $product_id);
    mysqli_stmt_execute($stmt_get_existing);
    $result_existing = mysqli_stmt_get_result($stmt_get_existing);
    while ($row = mysqli_fetch_assoc($result_existing)) {
        $existing_variations_ids[] = intval($row['variation_id']);
    }
    mysqli_stmt_close($stmt_get_existing);

    // Check for variations to delete
    $variations_to_delete = array_diff($existing_variations_ids, array_keys($submitted_variations));
    if (!empty($variations_to_delete)) {
        $ids_to_delete = implode(',', $variations_to_delete);
        $sql_delete = "DELETE FROM `product_variations` WHERE `variation_id` IN ($ids_to_delete)";
        mysqli_query($conn, $sql_delete);
    }
    
    // Handle updates and new insertions
    foreach ($_POST['colors'] as $key => $color_name) {
        $var_id = $_POST['variation_ids'][$key];
        $color_name = mysqli_real_escape_string($conn, $color_name);
        $variation_image_url = isset($_POST['existingVariationImages'][$var_id]) ? mysqli_real_escape_string($conn, $_POST['existingVariationImages'][$var_id]) : '';
        
        // Handle new variation image upload
        if (isset($_FILES['variation_images']) && $_FILES['variation_images']['error'][$key] === UPLOAD_ERR_OK) {
            $variation_file = [
                'name' => $_FILES['variation_images']['name'][$key],
                'type' => $_FILES['variation_images']['type'][$key],
                'tmp_name' => $_FILES['variation_images']['tmp_name'][$key],
                'error' => $_FILES['variation_images']['error'][$key],
                'size' => $_FILES['variation_images']['size'][$key]
            ];
            $new_variation_image = uploadImage($variation_file);
            if ($new_variation_image) {
                $variation_image_url = $new_variation_image;
            }
        }

        $sizes = isset($_POST['sizes']) && isset($_POST['sizes'][$var_id]) ? $_POST['sizes'][$var_id] : [];
        $stocks = isset($_POST['stocks']) && isset($_POST['stocks'][$var_id]) ? $_POST['stocks'][$var_id] : [];
        
        // Encode sizes and stocks as JSON
        $sizes_json = json_encode($sizes);
        $stocks_json = json_encode($stocks);
        
        if ($var_id == 'new') {
            // New variation: INSERT
            $sql_variation = "INSERT INTO `product_variations` (`product_id`, `color_name`, `image_url`, `sizes`, `stocks`)
                              VALUES (?, ?, ?, ?, ?)";
            $stmt_variation = mysqli_prepare($conn, $sql_variation);
            mysqli_stmt_bind_param($stmt_variation, "issss", $product_id, $color_name, $variation_image_url, $sizes_json, $stocks_json);
        } else {
            // Existing variation: UPDATE
            $sql_variation = "UPDATE `product_variations` SET 
                              `color_name` = ?, 
                              `image_url` = ?,
                              `sizes` = ?, 
                              `stocks` = ?
                              WHERE `variation_id` = ?";
            $stmt_variation = mysqli_prepare($conn, $sql_variation);
            mysqli_stmt_bind_param($stmt_variation, "ssssi", $color_name, $variation_image_url, $sizes_json, $stocks_json, $var_id);
        }
        mysqli_stmt_execute($stmt_variation);
        mysqli_stmt_close($stmt_variation);
    }
    
    // Redirect after successful update
    header("Location: product_management.php");
    exit();
}


// --- GET REQUEST: Load existing data for the form ---
// Fetch main product data
$sql = "SELECT * FROM products WHERE product_id = ...";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) === 0) {
    die("ไม่พบข้อมูลสินค้า");
}
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Fetch product variations
$sql_variations = "SELECT * FROM `product_variations` WHERE product_id = ?";
$stmt_variations = mysqli_prepare($conn, $sql_variations);
mysqli_stmt_bind_param($stmt_variations, "i", $product_id);
mysqli_stmt_execute($stmt_variations);
$result_variations = mysqli_stmt_get_result($stmt_variations);
while ($row = mysqli_fetch_assoc($result_variations)) {
    $row['size_name'] = json_decode($row['size_name'], true);
    $row['stock'] = json_decode($row['stock'], true);
    $variations[] = $row;
}
mysqli_stmt_close($stmt_variations);

// Fetch categories from the database
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
  <title>แก้ไขสินค้า - FlauntFit</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family:'Kanit',sans-serif; background:#fce4ec; }
    .card { background:#fff; border-radius:1rem; padding:2rem; box-shadow:0 8px 20px rgba(0,0,0,.1); }
    .form-input { border:1px solid #ddd; padding:.5rem; border-radius:.5rem; width:100%; }
    .variation-row { border:1px solid #ddd; border-radius:.5rem; padding:1rem; margin-bottom:1rem; position: relative; }
    .remove-btn { position: absolute; top: 0.5rem; right: 0.5rem; background: none; border: none; color: #ef4444; font-size: 1.5rem; }
    .thumbnail { width: 80px; height: 80px; object-fit: cover; border-radius: 0.5rem; border: 1px solid #ddd; }
  </style>
</head>
<body class="p-8">

<div class="max-w-4xl mx-auto card">
  <h1 class="text-3xl font-bold text-center mb-8">แก้ไขสินค้า</h1>
  
  <form id="productForm" action="product_edit.php?id=<?= htmlspecialchars($product_id) ?>" method="POST" enctype="multipart/form-data">
    <div class="mb-4">
      <label class="block font-semibold mb-2">ชื่อสินค้า</label>
      <input type="text" name="productName" value="<?= htmlspecialchars($product['product_name']) ?>" class="form-input" required>
    </div>
    <div class="mb-4">
      <label class="block font-semibold mb-2">รายละเอียดสินค้า</label>
      <textarea name="productDescription" class="form-input" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <div class="mb-4">
      <label class="block font-semibold mb-2">ราคา</label>
      <input type="number" name="productPrice" value="<?= htmlspecialchars($product['price']) ?>" class="form-input" step="0.01" min="0" required>
    </div>
    <div class="mb-4">
      <label class="block font-semibold mb-2">หมวดหมู่</label>
      <select name="productCategory" class="form-input" required>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat) ?>" <?= ($cat === $product['category']) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-4">
      <label class="block font-semibold mb-2">รูปภาพหลัก</label>
      <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Main Product Image" class="mb-2 thumbnail">
      <input type="file" name="mainImage" class="form-input" accept="image/*">
      <input type="hidden" name="existingMainImage" value="<?= htmlspecialchars($product['image_url']) ?>">
    </div>

    <h2 class="text-2xl font-bold mt-8 mb-4">ตัวเลือกสินค้า (สี/ไซส์)</h2>
    <div id="variations-container">
      <?php if (!empty($variations)): ?>
        <?php foreach ($variations as $var): ?>
          <div class="variation-row" data-variation-id="<?= htmlspecialchars($var['variation_id']) ?>">
            <button type="button" class="remove-btn" onclick="removeVariation(this)">&times;</button>
            <input type="hidden" name="variation_ids[]" value="<?= htmlspecialchars($var['variation_id']) ?>">
            <label>สี</label>
            <input type="text" name="colors[]" value="<?= htmlspecialchars($var['color_name']) ?>" class="form-input mb-2" placeholder="ชื่อสี" required>
            <label>รูปภาพ</label>
            <img src="<?= htmlspecialchars($var['image_url']) ?>" alt="<?= htmlspecialchars($var['color_name']) ?>" class="mb-2 thumbnail">
            <input type="file" name="variation_images[]" class="form-input mb-2" accept="image/*">
            <input type="hidden" name="existingVariationImages[<?= htmlspecialchars($var['variation_id']) ?>]" value="<?= htmlspecialchars($var['image_url']) ?>">
            
            <p class="font-semibold mt-4">เลือกไซส์:</p>
            <div class="grid grid-cols-3 gap-2">
              <?php
              $sizes = ["XS", "S", "M", "L", "XL", "2XL"];
              foreach ($sizes as $size):
                $checked = in_array($size_name, $var['size_name']) ? 'checked' : '';
                $stock_value = isset($var['stock'][$size]) ? htmlspecialchars($var['stock'][$size]) : 0;
              ?>
                <div>
                  <label>
                    <input type="checkbox" name="sizes[<?= htmlspecialchars($var['variation_id']) ?>][]" value="<?= htmlspecialchars($size) ?>" <?= $checked ?>>
                    <?= htmlspecialchars($size) ?>
                  </label>
                  <input type="number" name="stocks[<?= htmlspecialchars($var['variation_id']) ?>][<?= htmlspecialchars($size) ?>]" value="<?= $stock_value ?>" class="form-input" min="0">
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="variation-row">
            <button type="button" class="remove-btn" onclick="removeVariation(this)">&times;</button>
            <input type="hidden" name="variation_ids[]" value="new">
            <label>สี</label>
            <input type="text" name="colors[]" class="form-input mb-2" placeholder="ชื่อสี" required>
            <label>รูปภาพ</label>
            <input type="file" name="variation_images[]" class="form-input mb-2" accept="image/*">
            <p class="font-semibold mt-4">เลือกไซส์:</p>
            <div class="grid grid-cols-3 gap-2">
                <?php $timestamp = time(); ?>
                <?php $sizes = ["XS", "S", "M", "L", "XL", "2XL"]; ?>
                <?php foreach ($sizes as $size): ?>
                    <div>
                        <label>
                            <input type="checkbox" name="sizes[new-<?= $timestamp ?>][]" value="<?= htmlspecialchars($size) ?>">
                            <?= htmlspecialchars($size) ?>
                        </label>
                        <input type="number" name="stocks[new-<?= $timestamp ?>][<?= htmlspecialchars($size) ?>]" class="form-input" min="0">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
      <?php endif; ?>
    </div>

    <button type="button" onclick="addVariationRow()" class="text-pink-500 font-semibold">+ เพิ่มสี/ไซส์</button>

    <div class="text-center mt-4">
      <button type="submit" class="px-6 py-2 bg-pink-500 text-white rounded-lg">บันทึกการแก้ไข</button>
    </div>
  </form>
</div>

<script>
let variationCounter = Date.now();

function addVariationRow() {
    const container = document.getElementById("variations-container");
    const div = document.createElement("div");
    div.className = "variation-row";
    const newId = 'new-' + variationCounter++;

    div.innerHTML = `
        <button type="button" class="remove-btn" onclick="removeVariation(this)">&times;</button>
        <input type="hidden" name="variation_ids[]" value="${newId}">
        <label>สี</label>
        <input type="text" name="colors[]" class="form-input mb-2" placeholder="ชื่อสี" required>
        <label>รูปภาพ</label>
        <input type="file" name="variation_images[]" class="form-input mb-2" accept="image/*">
        <p class="font-semibold mt-4">เลือกไซส์:</p>
        <div class="grid grid-cols-3 gap-2">
            ${["XS", "S", "M", "L", "XL", "2XL"].map(size => `
                <div>
                    <label>
                        <input type="checkbox" name="sizes[${newId}][]" value="${size}">
                        ${size}
                    </label>
                    <input type="number" name="stocks[${newId}][${size}]" class="form-input" min="0">
                </div>
            `).join('')}
        </div>
    `;
    container.appendChild(div);
}

function removeVariation(btn) {
    if (confirm('ยืนยันการลบตัวเลือกนี้?')) {
        btn.closest('.variation-row').remove();
    }
}
</script>

</body>
</html>
<?php
mysqli_close($conn);
?>