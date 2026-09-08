<?php
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secret_code = $_POST['secret_code'] ?? '';

    if ($secret_code === 'มิตรภาพเป็นดั่งเวทมนต์') {
        $_SESSION['is_admin'] = true;
        header("Location: loginadmin.php"); // Redirect to profile selection page after admin code
        exit();
    } else {
        $message = 'โค้ดลับไม่ถูกต้อง!';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบหลังบ้าน - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f9fafb; }
        .container { max-width: 400px; margin: 4rem auto; padding: 2rem; background: #fff; border-radius: 1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { background: #4a90e2; color: #fff; padding: 0.75rem 1.5rem; border-radius: 9999px; font-weight: bold; transition: background 0.3s; border:none;}
        .btn-primary:hover { background: #357bd8; }
        .text-blue-600 { color: #4a90e2; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .p-3 { padding: 0.75rem; }
        .text-center { text-align: center; }
        .rounded { border-radius: 0.75rem; }
        .bg-red-100 { background-color: #fee2e2; }
        .text-red-700 { color: #b91c1c; }
        .block { display: block; }
        .font-semibold { font-weight: 600; }
        .w-full { width: 100%; }
        .border { border: 1px solid #e5e7eb; }
        .p-2 { padding: 0.5rem; }
        .mt-2 { margin-top: 0.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-2xl font-bold text-center mb-6 text-blue-600">เข้าสู่ระบบหลังบ้าน</h1>

        <?php if ($message): ?>
        <div class="mb-4 p-3 text-center rounded bg-red-100 text-red-700">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="secret_code" class="block text-gray-700 font-semibold">กรุณากรอกโค้ดลับ:</label>
                <input type="password" id="secret_code" name="secret_code" class="w-full border p-2 rounded mt-2" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn-primary">เข้าสู่ระบบ</button>
            </div>
        </form>
    </div>
</body>
</html>