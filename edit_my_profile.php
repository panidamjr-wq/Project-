<?php
session_start();
require_once 'connectdb.php';


if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบเพื่อแก้ไขข้อมูลส่วนตัว'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

$sql = "SELECT username, email, phone_number, shipping_address FROM `users` WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('ไม่พบข้อมูลผู้ใช้ กรุณาลองใหม่อีกครั้ง'); window.location.href='index.php';</script>";
    exit();
}

$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'] ?? '';
    $new_email = $_POST['email'] ?? '';
    $new_phone_number = $_POST['phone_number'] ?? '';
    $new_shipping_address = $_POST['shipping_address'] ?? '';
    $new_password = $_POST['password'] ?? '';
    
    if (empty($new_username) || empty($new_email)) {
        $message = 'กรุณากรอกชื่อผู้ใช้และอีเมลให้ครบถ้วน';
    } else {
    
        $update_sql = "UPDATE `users` SET username = ?, email = ?, phone_number = ?, shipping_address = ?";
        $params = [$new_username, $new_email, $new_phone_number, $new_shipping_address];
        $types = "ssss";

        if (!empty($new_password)) {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }
        
        $update_sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";
        
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, $types, ...$params);

        if (mysqli_stmt_execute($update_stmt)) {
            $message = 'อัปเดตข้อมูลสำเร็จ';
        
            $user['username'] = $new_username;
            $user['email'] = $new_email;
            $user['phone_number'] = $new_phone_number;
            $user['shipping_address'] = $new_shipping_address;
        } else {
            $message = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($update_stmt);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<a href="homepage.php" class="text-blue-500 hover:text-red-700 font-semibold">ย้อนกลับ</a>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลส่วนตัว - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f7f9fc; color: #4b5563; }
        .container { max-width: 800px; margin: 2rem auto; padding: 2.5rem; background-color: #ffffff; border-radius: 1.5rem; box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1); }
        .input-field { width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; margin-top: 0.5rem; }
        .btn-primary { background-color: #ff69b4; color: #ffffff; font-weight: 700; padding: 1rem 2rem; border-radius: 9999px; transition: background-color 0.3s; }
        .btn-primary:hover { background-color: #ff1493; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-4xl font-bold mb-6 text-center text-pink-500">แก้ไขข้อมูลส่วนตัว</h1>
        
        <?php if ($message): ?>
        <div class="p-4 rounded-lg mb-4 text-center <?php echo strpos($message, 'สำเร็จ') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <form action="edit_my_profile.php" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-gray-700 font-semibold">ชื่อผู้ใช้:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="input-field" required>
            </div>
            <div>
                <label for="email" class="block text-gray-700 font-semibold">อีเมล:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="input-field" required>
            </div>
            <div>
                <label for="phone_number" class="block text-gray-700 font-semibold">เบอร์โทรศัพท์:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" class="input-field">
            </div>
            <div>
                <label for="shipping_address" class="block text-gray-700 font-semibold">ที่อยู่:</label>
                <textarea id="shipping_address" name="shipping_address" rows="4" class="input-field"><?php echo htmlspecialchars($user['shipping_address']); ?></textarea>
            </div>
            <div class="bg-gray-100 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-2">หากต้องการเปลี่ยนรหัสผ่าน ให้กรอกรหัสผ่านใหม่:</p>
                <label for="password" class="block text-gray-700 font-semibold">รหัสผ่านใหม่:</label>
                <input type="password" id="password" name="password" class="input-field">
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn-primary">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</body>
</html>
