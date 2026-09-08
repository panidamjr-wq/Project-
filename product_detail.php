<?php
// ตั้งค่าเพื่อแสดงข้อผิดพลาด
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เริ่มการใช้งาน session เพื่อเก็บข้อมูลตะกร้าสินค้า
session_start();

// เชื่อมต่อฐานข้อมูล
include 'connectdb.php';

// ตรวจสอบว่ามีค่า 'id' ใน URL หรือไม่และเป็นตัวเลขหรือไม่
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h1 class='text-center text-3xl font-bold mt-10 text-red-600'>ไม่พบรายการสินค้าหรือ ID ไม่ถูกต้อง</h1>";
    exit();
}

$product_id = intval($_GET['id']);

// ดึงข้อมูลสินค้าจากตาราง products อย่างปลอดภัย
$product_sql = "SELECT * FROM `products` WHERE `product_id` = ?";
$product_stmt = mysqli_prepare($conn, $product_sql);
if ($product_stmt) {
    mysqli_stmt_bind_param($product_stmt, "i", $product_id);
    mysqli_stmt_execute($product_stmt);
    $product_result = mysqli_stmt_get_result($product_stmt);
    $product = mysqli_fetch_assoc($product_result);
    mysqli_stmt_close($product_stmt);
} else {
    echo "<h1 class='text-center text-3xl font-bold mt-10 text-red-600'>มีข้อผิดพลาดในการเตรียมคำสั่ง: " . mysqli_error($conn) . "</h1>";
    exit();
}

// ตรวจสอบว่าพบสินค้าหรือไม่
if (!$product) {
    echo "<h1 class='text-center text-3xl font-bold mt-10 text-red-600'>ไม่พบรายการสินค้า</h1>";
    exit();
}

// ดึงข้อมูล Variations (สีและรูปภาพ)
$variations = [];
$variation_sql = "SELECT * FROM `product_variations` WHERE `product_id` = ?";
$variation_stmt = mysqli_prepare($conn, $variation_sql);
if ($variation_stmt) {
    mysqli_stmt_bind_param($variation_stmt, "i", $product_id);
    mysqli_stmt_execute($variation_stmt);
    $variation_result = mysqli_stmt_get_result($variation_stmt);
    while ($row = mysqli_fetch_assoc($variation_result)) {
        $variations[] = $row;
    }
    mysqli_stmt_close($variation_stmt);
}

// ดึงข้อมูล Stocks (ขนาดและจำนวน)
$stocks = [];
$stock_sql = "SELECT * FROM `product_stocks` ORDER BY `product_stocks`.`size_name` = ?";
$stock_stmt = mysqli_prepare($conn, $stock_sql);
if ($stock_stmt) {
    mysqli_stmt_bind_param($stock_stmt, "i", $product_id);
    mysqli_stmt_execute($stock_stmt);
    $stock_result = mysqli_stmt_get_result($stock_stmt);
    while ($row = mysqli_fetch_assoc($stock_result)) {
        if (!isset($stocks[$row['variation_id']])) {
            $stocks[$row['variation_id']] = [];
        }
        $stocks[$row['variation_id']][$row['size_name']] = $row['stock_quantity'];
    }
    mysqli_stmt_close($stock_stmt);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<a href="ii1dex.php" class="text-blue-500 hover:text-red-700 font-semibold">ย้อนกลับ</a>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - รายละเอียดสินค้า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #fce4ec; }
        .product-container { background-color: #fff; border-radius: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 2rem; max-width: 1200px; margin: 2rem auto; display: flex; flex-wrap: wrap; }
        .product-image { flex: 1 1 500px; max-width: 100%; object-fit: cover; border-radius: 1.5rem; transition: transform 0.3s ease; }
        .product-image:hover { transform: scale(1.02); }
        .product-info { flex: 1 1 400px; padding-left: 2rem; }
        .btn-add-to-cart { background-color: #f472b6; color: white; padding: 1rem 2rem; border-radius: 9999px; font-weight: bold; transition: background-color 0.3s; }
        .btn-add-to-cart:hover { background-color: #ec4899; }
        .variation-selector { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
        .variation-item { cursor: pointer; border: 2px solid transparent; border-radius: 0.75rem; transition: border-color 0.2s; position: relative; }
        .variation-item.selected { border-color: #f472b6; }
        .variation-image { width: 80px; height: 80px; object-fit: cover; border-radius: 0.75rem; }
        .size-selector { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 0.5rem; }
        .size-item { padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 9999px; cursor: pointer; transition: background-color 0.2s, border-color 0.2s; }
        .size-item.selected { background-color: #f472b6; color: white; border-color: #f472b6; }
        .size-item.disabled { background-color: #f3f4f6; color: #9ca3af; cursor: not-allowed; border-color: #e5e7eb; }
        .quantity-control { display: flex; align-items: center; gap: 0.5rem; }
        .quantity-btn { background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.5rem; transition: background-color 0.2s; }
        .quantity-btn:hover { background-color: #e5e7eb; }
        .message-box { position: fixed; top: 1rem; right: 1rem; padding: 1rem 1.5rem; border-radius: 0.75rem; color: white; font-weight: bold; transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out; transform: translateX(120%); opacity: 0; z-index: 1000; }
        .message-box.show { transform: translateX(0); opacity: 1; }
    </style>
</head>
<body class="min-h-screen">
    <div class="product-container flex flex-col md:flex-row">
        <!-- Image Section -->
        <div class="w-full md:w-1/2">
            <img id="main-product-image" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
        </div>

        <!-- Product Details Section -->
        <div class="w-full md:w-1/2 mt-8 md:mt-0 product-info">
            <h1 class="text-4xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="text-3xl font-bold text-pink-500 mb-6">฿<?php echo number_format($product['price'], 2); ?></p>
            <p class="text-gray-600 mb-6"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <div class="mb-4">
                <p class="font-semibold text-gray-700">รายละเอียดเพิ่มเติม:</p>
                <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($product['additional_details'])); ?></p>
            </div>
            
            <?php if (!empty($variations)): ?>
            <div class="mb-6">
                <p class="font-semibold text-gray-700">สี:</p>
                <div class="variation-selector">
                    <?php foreach ($variations as $variation): ?>
                        <div class="variation-item" data-variation-id="<?php echo $variation['variation_id']; ?>" data-image-url="<?php echo htmlspecialchars($variation['image_url']); ?>">
                            <img src="<?php echo htmlspecialchars($variation['image_url']); ?>" alt="<?php echo htmlspecialchars($variation['color_name']); ?>" class="variation-image">
                            <p class="text-center text-sm mt-1"><?php echo htmlspecialchars($variation['color_name']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-6">
                <p class="font-semibold text-gray-700">ขนาด:</p>
                <div class="size-selector" id="size-selector-container">
                    <!-- Sizes will be dynamically added here -->
                    <p class="text-gray-500">กรุณาเลือกสีก่อน</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="font-semibold text-gray-700">สต็อกคงเหลือ: <span id="stock-quantity" class="font-normal text-gray-500">-</span></p>
            </div>

            <div class="mb-6">
                <p class="font-semibold text-gray-700">จำนวน:</p>
                <div class="quantity-control">
                    <button class="quantity-btn" id="decrease-btn">-</button>
                    <input type="number" id="quantity-input" value="1" min="1" class="w-20 text-center border rounded-md" readonly>
                    <button class="quantity-btn" id="increase-btn">+</button>
                </div>
            </div>

            <button id="add-to-cart-btn" class="btn-add-to-cart w-full">เพิ่มลงในตะกร้า</button>
        </div>
    </div>
    
    <div id="message-box" class="message-box"></div>

    <script>
        const stocks = <?php echo json_encode($stocks); ?>;
        const variations = <?php echo json_encode($variations); ?>;
        
        const variationItems = document.querySelectorAll('.variation-item');
        const sizeSelectorContainer = document.getElementById('size-selector-container');
        const mainProductImage = document.getElementById('main-product-image');
        const stockQuantitySpan = document.getElementById('stock-quantity');
        const quantityInput = document.getElementById('quantity-input');
        const decreaseBtn = document.getElementById('decrease-btn');
        const increaseBtn = document.getElementById('increase-btn');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const messageBox = document.getElementById('message-box');

        let selectedVariationId = null;
        let selectedSize = null;
        let currentStock = 0;

        // Function to show a message
        function showMessage(message, type = 'success') {
            messageBox.textContent = message;
            if (type === 'success') {
                messageBox.style.backgroundColor = '#4CAF50';
                messageBox.style.color = 'white';
            } else if (type === 'error') {
                messageBox.style.backgroundColor = '#f44336';
                messageBox.style.color = 'white';
            }
            messageBox.classList.add('show');
            setTimeout(() => {
                messageBox.classList.remove('show');
            }, 3000);
        }

        // Function to update the available sizes
        function updateSizes(variationId) {
            sizeSelectorContainer.innerHTML = '';
            selectedSize = null;
            currentStock = 0;
            stockQuantitySpan.textContent = '-';
            quantityInput.value = 1;

            if (stocks[variationId]) {
                const sizes = Object.keys(stocks[variationId]);
                sizes.forEach(size => {
                    const stock = stocks[variationId][size];
                    const sizeItem = document.createElement('div');
                    sizeItem.className = 'size-item';
                    sizeItem.textContent = size;
                    sizeItem.dataset.size = size;
                    sizeItem.dataset.stock = stock;
                    if (stock === 0) {
                        sizeItem.classList.add('disabled');
                    }
                    sizeSelectorContainer.appendChild(sizeItem);
                });
            } else {
                sizeSelectorContainer.innerHTML = '<p class="text-gray-500">ไม่พบขนาดสำหรับสินค้านี้</p>';
            }
        }

        // Event listeners for color selection
        variationItems.forEach(item => {
            item.addEventListener('click', () => {
                // Clear previous selection
                variationItems.forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');

                selectedVariationId = item.dataset.variationId;
                const imageUrl = item.dataset.imageUrl;
                mainProductImage.src = imageUrl;

                // Update sizes based on the selected color
                updateSizes(selectedVariationId);
            });
        });

        // Event listener for size selection (delegated)
        sizeSelectorContainer.addEventListener('click', (event) => {
            const target = event.target;
            if (target.classList.contains('size-item') && !target.classList.contains('disabled')) {
                // Clear previous selection
                document.querySelectorAll('.size-item').forEach(s => s.classList.remove('selected'));
                target.classList.add('selected');

                selectedSize = target.dataset.size;
                currentStock = parseInt(target.dataset.stock);
                stockQuantitySpan.textContent = currentStock;

                // Reset quantity input and max value
                quantityInput.value = 1;
                quantityInput.max = currentStock;
            }
        });

        // Quantity control
        decreaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value);
            if (qty > 1) {
                quantityInput.value = qty - 1;
            }
        });

        increaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value);
            if (qty < currentStock) {
                quantityInput.value = qty + 1;
            }
        });

        // Add to cart functionality
        addToCartBtn.addEventListener('click', () => {
            if (selectedVariationId && selectedSize && quantityInput.value > 0 && parseInt(quantityInput.value) <= currentStock) {
                // Prepare data for cart
                const cartData = {
                    action: 'add',
                    product_id: <?php echo $product_id; ?>,
                    variation_id: selectedVariationId,
                    size: selectedSize,
                    quantity: parseInt(quantityInput.value)
                };
                
                // Use Fetch API to send data to cart.php
                fetch('cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(cartData)
                })
                .then(response => response.text()) // You might want to change this to response.json() if cart.php returns JSON
                .then(data => {
                    // Assuming cart.php returns a success message or redirects
                    showMessage('เพิ่มสินค้าลงในตะกร้าเรียบร้อยแล้ว!', 'success');
                    console.log('Cart updated:', data);
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    showMessage('เกิดข้อผิดพลาดในการเพิ่มสินค้าลงในตะกร้า', 'error');
                });

            } else {
                showMessage('กรุณาเลือกสีและขนาดให้ถูกต้อง', 'error');
            }
        });
    </script>
</body>
</html>
<?php
// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_close($conn);
?>