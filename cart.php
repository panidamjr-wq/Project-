<?php
session_start();
include 'connectdb.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $cart_item_key = $_POST['cart_item_key'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if ($action === 'add') {
        $product_id = $_POST['product_id'];
        $variation_id = $_POST['variation_id'];
        $quantity = intval($_POST['quantity']);
        $sql = "SELECT product_name, price, image_url FROM products WHERE product_id=".intval($product_id);
        $res = mysqli_query($conn,$sql);
        if ($res && mysqli_num_rows($res)>0){
            $data = mysqli_fetch_assoc($res);
            $key = $product_id.'_'.$variation_id;
            if(isset($_SESSION['cart'][$key])){
                $_SESSION['cart'][$key]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$key] = [
                    'product_id'=>$product_id,
                    'variation_id'=>$variation_id,
                    'name'=>$data['product_name'],
                    'price'=>floatval($data['price']),
                    'image_url'=>$data['image_url'],
                    'quantity'=>$quantity
                ];
            }
        }
        echo json_encode(['success'=>true,'message'=>'เพิ่มสินค้าลงตะกร้าเรียบร้อย']);
        exit();
    } elseif ($action === 'update' && $cart_item_key) {
        $_SESSION['cart'][$cart_item_key]['quantity'] = intval($quantity);
        echo json_encode(['success'=>true]);
        exit();
    } elseif ($action === 'remove' && $cart_item_key) {
        unset($_SESSION['cart'][$cart_item_key]);
        echo json_encode(['success'=>true]);
        exit();
    } elseif ($action === 'clear') {
        unset($_SESSION['cart']);
        echo json_encode(['success'=>true]);
        exit();
    }
}

$cart_items = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ตะกร้าสินค้า - FlauntFit</title>

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body {
        font-family: 'Kanit', 'Montserrat', sans-serif;
        background: linear-gradient(135deg, #FFE5EC, #F5DCE0);
        color: #4a4a4a;
    }

    h1 {
        font-family: 'Montserrat', sans-serif;
        color: #E18AAA;
    }
    header {
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
    }
    header a.logo-text {
        color: #FB6F92;
    }
    nav a {
        transition: all 0.3s ease-in-out;
    }
    nav a:hover {
        color: #FF8FAB;
    }
    nav a.font-bold {
        color: #FB6F92;
    }
    .cart-container {
        background-color: #ffffff;
        border-radius: 1.25rem;
        box-shadow: 0 8px 30px rgba(236, 189, 196, 0.3);
        border: 1px solid #FFE5EC; 
    }
    .cart-item {
        border-bottom: 1px solid #F5DCE0; 
    .cart-item:last-child {
        border-bottom: none;
    }
    .quantity-input {
        border: 1px solid #EFCFD4; 
        border-radius: 0.5rem;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .quantity-input:focus {
        outline: none;
        border-color: #FFB3C6;
        box-shadow: 0 0 0 3px rgba(255, 179, 198, 0.4);
    }
    .total-price-text {
        color: #E18AAA;
    }
    .checkout-btn {
        background: linear-gradient(45deg, #FF8FAB, #FB6F92);
        color: white;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        border-radius: 50px;
        padding: 0.8rem 2.5rem;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 4px 15px rgba(251, 111, 146, 0.4);
        border: none;
    }
    .checkout-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(251, 111, 146, 0.5);
    }
    .remove-btn {
        background-color: transparent;
        color: #FF8FAB;
        border: 1px solid #FFC2D1; 
        font-weight: 500;
        border-radius: 50px;
        transition: all 0.3s ease-in-out;
    }
    .remove-btn:hover {
        background-color: #FB6F92;
        color: white;
        border-color: #FB6F92;
    }

</style>
</head>
<body>

<header class="bg-white shadow-lg py-4">
    <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
        <a href="homepage.php" class="text-3xl font-bold logo-text">FlauntFit</a>
        <nav class="mt-4 md:mt-0">
            <ul class="flex space-x-4 md:space-x-8">
                <li><a href="homepage.php" class="text-gray-600 hover:text-pink-500 font-semibold">หน้าหลัก</a></li>
                <li><a href="ii1dex.php" class="text-gray-600 hover:text-pink-500 font-semibold">สินค้าทั้งหมด</a></li>
                <li><a href="cart.php" class="font-bold">ตะกร้าสินค้า</a></li>
                <li><a href="order_history.php" class="text-gray-600 hover:text-pink-500 font-semibold">ประวัติการสั่งซื้อ</a></li>
                <li><a href="edit_my_profile.php" class="text-gray-600 hover:text-pink-500 font-semibold">แก้ไขข้อมูล</a></li>
                <li><a href="index.php" class="text-gray-600 hover:text-pink-500 font-semibold">ออกจากระบบ</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container mx-auto my-10 p-6 md:p-8 cart-container max-w-4xl">
<h1 class="text-3xl font-bold mb-8 text-center">ตะกร้าสินค้าของคุณ</h1>

<?php if(empty($cart_items)): ?>
<p class="text-center text-gray-500 py-10">ไม่มีสินค้าในตะกร้า</p>
<?php else: ?>
<form action="checkout.php" method="POST">
<?php
$total_price = 0;
foreach($cart_items as $key=>$item):
    $sub_total = $item['price']*$item['quantity'];
    $total_price += $sub_total;
?>
<div class="flex flex-wrap justify-between items-center py-4 cart-item">
    <div class="flex items-center space-x-4 w-full md:w-1/2 mb-4 md:mb-0">
        <input type="checkbox" name="checkout_items[]" value="<?php echo $key; ?>" class="w-5 h-5 form-check-input" checked>
        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="w-20 h-20 object-cover rounded-lg shadow-sm">
        <div>
            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
            <p class="text-gray-600">฿<?php echo number_format($item['price'],2); ?></p>
        </div>
    </div>
    <div class="flex items-center space-x-4 w-full md:w-auto justify-end">
        <input type="number" min="1" value="<?php echo $item['quantity']; ?>" class="w-16 text-center quantity-input py-1" onchange="updateCart('<?php echo $key; ?>', this.value)">
        <p class="font-bold w-24 text-right">฿<?php echo number_format($sub_total,2); ?></p>
        <button type="button" class="btn remove-btn px-3 py-1" onclick="removeFromCart('<?php echo $key; ?>')">ลบ</button>
    </div>
</div>
<?php endforeach; ?>

<div class="mt-8 flex justify-between items-center text-xl font-bold">
    <p>รวมทั้งหมด:</p>
    <p class="total-price-text">฿<?php echo number_format($total_price,2); ?></p>
</div>

<div class="mt-6 text-center">
    <button type="submit" class="bg-pink-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-pink-600 transition-colors">ชำระเงินสินค้าที่เลือก</button>
</div>

</form>
<?php endif; ?>
</div>

<script>
function updateCart(key, qty){
    fetch('cart.php',{method:'POST',body:new URLSearchParams({action:'update',cart_item_key:key,quantity:qty})})
    .then(res=>res.json()).then(res=>location.reload());
}
function removeFromCart(key){
    if(confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')){
        fetch('cart.php',{method:'POST',body:new URLSearchParams({action:'remove',cart_item_key:key})})
        .then(res=>res.json()).then(res=>location.reload());
    }
}
</script>
</body>
</html>