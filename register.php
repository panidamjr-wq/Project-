<?php
session_start();
require_once 'connectdb.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $shipping_address = trim($_POST['shipping_address']);
    $registered_at = date('Y-m-d H:i:s');

    if ($conn) {
       
        $sql_check = "SELECT username FROM users WHERE username = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);

        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "s", $username);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $error_message = "ชื่อผู้ใช้นี้มีผู้ใช้งานแล้ว";
            } else {
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO users (username, password, full_name, email, phone_number, shipping_address, registered_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = mysqli_prepare($conn, $sql_insert);

                if ($stmt_insert) {
                    mysqli_stmt_bind_param(
                        $stmt_insert,
                        "sssssss",
                        $username,
                        $hashed_password,
                        $full_name,
                        $email,
                        $phone_number,
                        $shipping_address,
                        $registered_at
                    );

                    if (mysqli_stmt_execute($stmt_insert)) {
                        
                        $_SESSION['username'] = $username;
                        $_SESSION['full_name'] = $full_name;
                        header("Location: homepage.php");
                        exit;
                    } else {
                        $error_message = "เกิดข้อผิดพลาด: " . mysqli_stmt_error($stmt_insert);
                    }
                } else {
                    $error_message = "เกิดข้อผิดพลาด SQL: " . mysqli_error($conn);
                }
            }
            mysqli_stmt_close($stmt_check);
        } else {
            $error_message = "เกิดข้อผิดพลาด SQL: " . mysqli_error($conn);
        }
    } else {
        $error_message = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlauntFit - สมัครสมาชิก</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #fce4ec;
            color: #4b5563;
        }
        .container { max-width: 600px; margin: 0 auto; padding: 2rem; }
        .form-card { background:#fff; border-radius:1.5rem; box-shadow:0 10px 30px rgba(0,0,0,0.08); padding:2rem; text-align:center; }
        .form-input { width:100%; padding:0.75rem 1rem; border-radius:0.75rem; border:1px solid #e2e8f0; margin-top:0.5rem; margin-bottom:1rem; }
        .form-input:focus { outline:none; border-color:#f687b3; box-shadow:0 0 0 3px rgba(246,135,179,0.4); }
        .btn { width:100%; padding:0.75rem 1rem; border-radius:9999px; font-weight:600; transition:all 0.3s; }
        .btn-primary { background:#4a90e2; color:#fff; }
        .btn-primary:hover { background:#357bd8; }
        .link { color:#4a90e2; font-weight:600; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="container">
        <div class="form-card">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">สมัครสมาชิก</h1>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="mb-4 text-left">
                    <label class="block font-semibold mb-1">ชื่อผู้ใช้</label>
                    <input type="text" name="username" class="form-input" required>
                </div>
                <div class="mb-4 text-left">
                    <label class="block font-semibold mb-1">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                <div class="mb-4 text-left">
                    <label class="block font-semibold mb-1">ชื่อ-นามสกุล</label>
                    <input type="text" name="full_name" class="form-input" required>
                </div>
                <div class="mb-4 text-left">
                    <label class="block font-semibold mb-1">อีเมล</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                <div class="mb-4 text-left">
                    <label class="block font-semibold mb-1">เบอร์โทรศัพท์</label>
                    <input type="tel" name="phone_number" class="form-input" required>
                </div>
                <div class="mb-6 text-left">
                    <label class="block font-semibold mb-1">ที่อยู่สำหรับจัดส่ง</label>
                    <textarea name="shipping_address" class="form-input" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            </form>

            <div class="mt-6">
                <p>มีบัญชีอยู่แล้ว? <a href="login.php" class="link">เข้าสู่ระบบ</a></p>
            </div>
        </div>
    </div>
</body>
</html>
