<?php
session_start();
include 'connectdb.php';

function uploadImage($file, $upload_dir = "images/") {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_name = uniqid() . "_" . basename($file['name']);
    $target_file = $upload_dir . $file_name;
    $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array($ext,$allowed)) return false;
    return move_uploaded_file($file['tmp_name'], $target_file) ? $target_file : false;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ไม่พบรหัสสินค้าที่ต้องการแก้ไข";
    header('Location: select_product.php');
    exit();
}
$product_id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "SELECT product_name, price, description, additional_details, image_url FROM `products` WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product) {
    $_SESSION['error_message'] = "ไม่พบข้อมูลสินค้าที่ต้องการแก้ไข";
    header('Location: select_product.php');
    exit();
}

$sql_variations = "SELECT pv.variation_id, pv.color_name, pv.image_url, ps.size_name, ps.stock_quantity
                    FROM `product_variations` pv
                    LEFT JOIN `product_stocks` ps ON pv.variation_id = ps.variation_id
                    WHERE pv.product_id = ?
                    ORDER BY pv.variation_id, ps.size_name";

$stmt_variations = mysqli_prepare($conn, $sql_variations);
mysqli_stmt_bind_param($stmt_variations, "i", $product_id);
mysqli_stmt_execute($stmt_variations);
$result_variations = mysqli_stmt_get_result($stmt_variations);

$variations = [];
if ($result_variations) {
    while ($row = mysqli_fetch_assoc($result_variations)) {
        $v_id = $row['variation_id'];
        if (!isset($variations[$v_id])) {
            $variations[$v_id] = [
                'color_name' => $row['color_name'],
                'image_url' => $row['image_url'],
                'stocks' => []
            ];
        }

        if ($row['size_name'] !== null) {
             $variations[$v_id]['stocks'][$row['size_name']] = $row['stock_quantity'];
        }
    }
}
mysqli_stmt_close($stmt_variations);
mysqli_close($conn);

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

$initial_new_v_index = 0; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family:'Kanit',sans-serif; background:#fce4ec; }
        .card { background:#fff; border-radius:1rem; padding:2rem; box-shadow:0 8px 20px rgba(0,0,0,.1); }
        .form-input { border:1px solid #ddd; padding:.5rem; border-radius:.5rem; width:100%; }
        .variation-row { border:1px solid #ddd; border-radius:.5rem; padding:1rem; margin-bottom:1rem; }
        #message-box { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="p-8">

<div id="message-box" class="fixed top-4 right-4 p-3 rounded-lg shadow-xl hidden z-20" role="alert"></div>

<div class="max-w-4xl mx-auto card">
    <header class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-pink-500">แก้ไขสินค้า: <?= htmlspecialchars($product['product_name']) ?></h1>
        <a href="select_product.php" class="text-gray-600 hover:text-pink-500 font-medium">&larr; กลับไปหน้ารวมสินค้า</a>
    </header>
    
    <form action="update_product.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-medium">ชื่อสินค้า</label>
                <input type="text" name="productName" class="form-input" value="<?= htmlspecialchars($product['product_name']) ?>" required>
            </div>
            <div>
                <label class="block mb-1 font-medium">ราคา</label>
                <input type="number" name="productPrice" step="0.01" min="0.01" class="form-input" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
        </div>

        <div>
            <label class="block mb-1 font-medium">รายละเอียดสินค้า</label>
            <textarea name="productDescription" rows="3" class="form-input" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div>
            <label class="block mb-1 font-medium">รายละเอียดเพิ่มเติม</label>
            <textarea name="additionalDetails" rows="3" class="form-input"><?= htmlspecialchars($product['additional_details'] ?? '') ?></textarea>
        </div>
        
        <div>
            <label class="block mb-1 font-medium">รูปภาพหลัก (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
            <input type="file" name="mainImage" class="form-input" accept="image/*">
            <?php if (!empty($product['image_url'])): ?>
                <p class="text-sm text-gray-500 mt-1">รูปภาพปัจจุบัน: <a href="<?= htmlspecialchars($product['image_url']) ?>" target="_blank" class="text-blue-500 hover:underline"><?= basename($product['image_url']) ?></a></p>
            <?php endif; ?>
    
            <input type="hidden" name="oldMainImage" value="<?= htmlspecialchars($product['image_url']) ?>">
        </div>

        <hr class="my-6">
        <h2 class="text-xl font-bold text-gray-700 mb-2">สี / ไซส์ / สต็อก / รูป</h2>
        <div id="variations-container">
            <?php 
            $v_count = 0;
            
            if (!empty($variations)):
                foreach ($variations as $variation_id => $variation):
                    $v_count++;
            ?>
            <div class="variation-row" data-v-id="<?= $variation_id ?>">
               
                <input type="hidden" name="variation_id[<?= $v_count ?>]" value="<?= $variation_id ?>"> 
                
                <label class="block mb-1 font-medium">สี</label>
                <input type="text" name="colors[<?= $v_count ?>]" class="form-input mb-2" value="<?= htmlspecialchars($variation['color_name']) ?>" required>
                
                <label class="block mb-1 font-medium">รูปภาพ (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
               
                <input type="file" name="variation_images_file[<?= $v_count ?>]" class="form-input mb-2" accept="image/*">
                <?php if (!empty($variation['image_url'])): ?>
                    <p class="text-sm text-gray-500 mt-1">รูปภาพปัจจุบัน: <a href="<?= htmlspecialchars($variation['image_url']) ?>" target="_blank" class="text-blue-500 hover:underline"><?= basename($variation['image_url']) ?></a></p>
                <?php endif; ?>
                
                <input type="hidden" name="variation_images_old[<?= $v_count ?>]" value="<?= htmlspecialchars($variation['image_url']) ?>">

                <p class="font-semibold mt-4">เลือกไซส์และสต็อก:</p>
                <div class="grid grid-cols-3 gap-2">
                    <?php 
                    $allSizes = ["XS","S","M","L","XL","2XL"];
                    foreach($allSizes as $size): 
                        $stock_quantity = $variation['stocks'][$size] ?? '';
                        $isChecked = isset($variation['stocks'][$size]) ? 'checked' : '';
                    ?>
                    <div>
                        
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="sizes[<?= $v_count ?>][]" value="<?= $size ?>" <?= $isChecked ?> class="w-4 h-4 text-pink-600 bg-gray-100 border-gray-300 rounded focus:ring-pink-500"> 
                            <span class="font-normal"><?= $size ?></span>
                        </label>
                       
                        <input type="number" name="stocks[<?= $v_count ?>][<?= $size ?>]" min="0" placeholder="สต็อก <?= $size ?>" class="form-input mt-1" value="<?= htmlspecialchars($stock_quantity) ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="removeVariationRow(this)" class="text-red-500 font-medium hover:text-red-700 mt-4">ลบตัวเลือกสีนี้</button>
            </div>
            <?php
                endforeach;
            else:
            
            ?>
             <div class="variation-row" data-v-id="0">
                <input type="hidden" name="variation_id[1]" value="0"> 
                <label class="block mb-1 font-medium">สี</label>
                <input type="text" name="colors[1]" class="form-input mb-2" placeholder="เช่น สีแดง" required>
                <label class="block mb-1 font-medium">รูปภาพ</label>
                <input type="file" name="variation_images_file[1]" class="form-input mb-2" accept="image/*">
                <input type="hidden" name="variation_images_old[1]" value="">

                <p class="font-semibold mt-4">เลือกไซส์และสต็อก:</p>
                <div class="grid grid-cols-3 gap-2">
                    <?php $allSizes = ["XS","S","M","L","XL","2XL"]; foreach($allSizes as $size): ?>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="sizes[1][]" value="<?= $size ?>" class="w-4 h-4 text-pink-600 bg-gray-100 border-gray-300 rounded focus:ring-pink-500"> 
                            <span class="font-normal"><?= $size ?></span>
                        </label>
                        <input type="number" name="stocks[1][<?= $size ?>]" min="0" placeholder="สต็อก <?= $size ?>" class="form-input mt-1" value="0">
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="removeVariationRow(this)" class="text-red-500 font-medium hover:text-red-700 mt-4">ลบตัวเลือกสีนี้</button>
            </div>
            <?php
            endif;
            ?>
        </div>
        <button type="button" onclick="addVariationRow()" class="text-pink-500 font-semibold hover:text-pink-700 border border-pink-500 px-3 py-1 rounded-lg">+ เพิ่มสี/ไซส์</button>

        <div class="text-center mt-8 pt-4 border-t">
            <button type="submit" class="px-8 py-3 bg-pink-500 text-white font-bold rounded-lg shadow-md hover:bg-pink-600 transition duration-200">บันทึกการแก้ไข</button>
        </div>
    </form>
</div>

<script>
    
    let newVariationIndex = <?= $v_count > 0 ? $v_count + 1 : 2; ?>;

    
    function showMessage(text, isError = true) {
        const messageBox = document.getElementById('message-box');
        messageBox.textContent = text;
        messageBox.classList.remove('hidden', 'bg-red-100', 'text-red-800', 'bg-green-100', 'text-green-800');
        
        if (isError) {
            messageBox.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-400');
        } else {
            messageBox.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-400');
        }
        
        setTimeout(() => {
            messageBox.classList.add('hidden');
        }, 3000);
    }
    
   
    <?php if ($success_message): ?>
        showMessage("<?= htmlspecialchars($success_message) ?>", false);
    <?php endif; ?>
    <?php if ($error_message): ?>
        showMessage("<?= htmlspecialchars($error_message) ?>", true);
    <?php endif; ?>

    function addVariationRow() {
        const container = document.getElementById('variations-container');
        const vIndex = newVariationIndex++;
        
        const allSizes = ["XS","S","M","L","XL","2XL"];
        let stocksHtml = '';
        allSizes.forEach(size => {
            stocksHtml += `
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="sizes[${vIndex}][]" value="${size}" class="w-4 h-4 text-pink-600 bg-gray-100 border-gray-300 rounded focus:ring-pink-500"> 
                        <span class="font-normal">${size}</span>
                    </label>
                    <input type="number" name="stocks[${vIndex}][${size}]" min="0" placeholder="สต็อก ${size}" class="form-input mt-1" value="0">
                </div>
            `;
        });

        const newRow = document.createElement('div');
        newRow.className = 'variation-row';
        newRow.setAttribute('data-v-id', '0'); 
        newRow.innerHTML = `
            <input type="hidden" name="variation_id[${vIndex}]" value="0">
            <label class="block mb-1 font-medium">สี</label>
            <input type="text" name="colors[${vIndex}]" class="form-input mb-2" placeholder="เช่น สีน้ำเงิน" required>
            <label class="block mb-1 font-medium">รูปภาพ</label>
            <input type="file" name="variation_images_file[${vIndex}]" class="form-input mb-2" accept="image/*">
            <input type="hidden" name="variation_images_old[${vIndex}]" value="">

            <p class="font-semibold mt-4">เลือกไซส์และสต็อก:</p>
            <div class="grid grid-cols-3 gap-2">
                ${stocksHtml}
            </div>
            <button type="button" onclick="removeVariationRow(this)" class="text-red-500 font-medium hover:text-red-700 mt-4">ลบตัวเลือกสีนี้</button>
        `;

        container.appendChild(newRow);
    }

    function removeVariationRow(button) {
        const container = document.getElementById('variations-container');
        if (container.querySelectorAll('.variation-row').length > 1) {
             button.parentNode.remove();
        } else {
             showMessage('ต้องมีตัวเลือกสินค้าอย่างน้อย 1 สี/แบบ', true);
        }
    }
</script>
</body>
</html>