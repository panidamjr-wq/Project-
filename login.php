<?php
session_start();
require_once 'connectdb.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($conn) {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                
                if (password_verify($password, $row['password'])) {
                    
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    header("Location: homepage.php"); 
                    exit();
                } else {
                    $error_message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
                }
            } else {
                $error_message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($conn);
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
    <title>FlauntFit - เข้าสู่ระบบ</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #fce4ec; 
            color: #4b5563;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
        }
        .form-card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            text-align: center;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        .form-input:focus {
            outline: none;
            border-color: #f687b3;
            box-shadow: 0 0 0 3px rgba(246, 135, 179, 0.4);
        }
        .btn {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background-color: #4a90e2;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #357bd8;
        }
        .link {
            color: #4a90e2;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .link:hover {
            color: #357bd8;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="container">
        <div class="form-card">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">เข้าสู่ระบบ</h1>
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="mb-4 text-left">
                    <label for="username" class="block text-gray-700 font-semibold mb-1">ชื่อผู้ใช้</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="ป้อนชื่อผู้ใช้" required>
                </div>
                <div class="mb-6 text-left">
                    <label for="password" class="block text-gray-700 font-semibold mb-1">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="ป้อนรหัสผ่าน" required>
                </div>
                <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
            </form>
            <div class="mt-6 text-center">
                <p class="text-gray-600">ยังไม่มีบัญชี? <a href="register.php" class="link">สมัครสมาชิก</a></p>
            </div>
        </div>
    </div>
</body>
</html>
