<?php
// เริ่มการทำงานของ session
session_start();

// ตรวจสอบว่ามี user_id ใน session หรือไม่
// ถ้าไม่มี ให้ตั้งค่าเป็นค่าว่าง (สำหรับผู้ใช้ที่ยังไม่ login)
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopdb";
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งค่า product_id มาใน URL หรือไม่
if (!isset($_GET['id'])) {
    header("Location: 1.html");
    exit();
}

$productId = $_GET['id'];

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT * FROM `products` WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}

// โค้ดสำหรับเพิ่มสินค้าลงตะกร้า (เมื่อกดปุ่ม)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!$userId) {
        // Redirect ไปหน้า login ถ้ายังไม่ได้เข้าสู่ระบบ
        header("Location: login.html");
        exit();
    }
    
    $quantity = $_POST['quantity'];
    
    // ตรวจสอบว่าสินค้ามีอยู่ในตะกร้าอยู่แล้วหรือไม่
    $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $userId, $productId);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // ถ้ามีอยู่แล้ว ให้อัปเดตจำนวนสินค้า
        $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $quantity, $userId, $productId);
        $update_stmt->execute();
    } else {
        // ถ้ายังไม่มี ให้เพิ่มสินค้าใหม่ลงในตะกร้า
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iii", $userId, $productId, $quantity);
        $insert_stmt->execute();
    }
    
    // ตั้งค่า session message เพื่อแสดงผล
    $_SESSION['message'] = "เพิ่มสินค้าลงตะกร้าแล้ว!";
    header("Location: product.php?id=" . $productId);
    exit();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - FlauntFit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .flauntfit-pink { background-color: #FFC0CB; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="flauntfit-pink text-black p-4">
        <div class="container mx-auto flex items-center justify-between">
            <a href="index.html" class="text-2xl font-bold">FlauntFit</a>
            <div class="flex-grow flex justify-center">
                <input type="text" placeholder="ค้นหาสินค้า" class="w-96 p-2 rounded-lg text-black">
            </div>
            <nav id="auth-links">
                <div id="logged-out-view">
                    <a href="login.html" class="text-white hover:underline mr-4">เข้าสู่ระบบ</a>
                    <a href="register.html" class="text-white hover:underline">สมัครสมาชิก</a>
                </div>
                <div id="logged-in-view" class="hidden">
                    <a href="#" class="text-white hover:underline mr-4">ชื่อผู้ใช้</a>
                    <a href="#" id="logout-link" class="text-white hover:underline">ออกจากระบบ</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-4 mt-8">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="flex flex-col md:flex-row gap-8">
                <div class="md:w-1/2">
                    <img src="https://placehold.co/600x600/f5f5f5/333333?text=<?php echo urlencode($product['product_name']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="w-full h-auto rounded-lg">
                </div>
                <div class="md:w-1/2">
                    <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    <p class="text-2xl text-orange-500 font-bold mb-4">฿<?php echo number_format($product['price'], 2); ?></p>
                    <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($product['description']); ?></p>
                    
                    <form method="POST" action="product.php?id=<?php echo $productId; ?>">
                        <div class="flex items-center mb-4">
                            <label for="quantity" class="mr-4">จำนวน:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" class="w-20 p-2 border rounded-lg">
                        </div>
                        <button type="submit" name="add_to_cart" class="bg-blue-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-600 transition-colors w-full">หยิบสินค้าลงตะกร้า</button>
                    </form>
                    
                    <?php if (isset($_SESSION['message'])): ?>
                    <div class="mt-4 text-center text-green-600">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-8 mt-12 text-center">
        <p>&copy; 2024 ร้านค้าเสื้อผ้า. สงวนลิขสิทธิ์.</p>
    </footer>
</body>
</html>