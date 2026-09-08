<?php
session_start();
require_once 'connectdb.php';


if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้'); window.location.href='index.php';</script>";
    exit();
}

$user_id = $_GET['id'] ?? null;

if (!$user_id || !is_numeric($user_id)) {
    $_SESSION['error_message'] = "ไม่พบรหัสผู้ใช้ที่ต้องการแก้ไข";
    header('Location: user_list.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['user_id'] ?? null;
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');

    if (empty($username) || empty($email) || empty($post_id) || $post_id != $user_id) {
        $_SESSION['error_message'] = "ข้อมูลไม่สมบูรณ์หรือไม่ถูกต้อง";
        header('Location: edit_user.php?id=' . $user_id);
        exit();
    }
    
    $sql_update = "UPDATE users SET username = ?, email = ?, phone_number = ?, shipping_address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql_update);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $phone_number, $shipping_address, $post_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "บันทึกการแก้ไขข้อมูลผู้ใช้: " . htmlspecialchars($username) . " สำเร็จแล้ว!";
           
            header('Location: user_list.php');
            exit();
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($conn);
    }
    
    header('Location: edit_user.php?id=' . $user_id);
    exit();
}


$sql_fetch = "SELECT id, username, email, phone_number, shipping_address FROM users WHERE id = ?";
$stmt_fetch = mysqli_prepare($conn, $sql_fetch);

if ($stmt_fetch) {
    mysqli_stmt_bind_param($stmt_fetch, "i", $user_id);
    mysqli_stmt_execute($stmt_fetch);
    $result_fetch = mysqli_stmt_get_result($stmt_fetch);
    $user_data = mysqli_fetch_assoc($result_fetch);
    mysqli_stmt_close($stmt_fetch);

    if (!$user_data) {
        $_SESSION['error_message'] = "ไม่พบข้อมูลผู้ใช้ ID: " . $user_id;
        header('Location: user_list.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้";
    header('Location: user_list.php');
    exit();
}

mysqli_close($conn);

$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขผู้ใช้งาน ID: <?= htmlspecialchars($user_data['id']) ?> - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f0f4f8; }
        .card { max-width: 600px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 1rem; box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .form-input { border: 1px solid #ddd; padding: 0.75rem; border-radius: 0.5rem; width: 100%; }
        #message-box { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="p-4">
    <div id="message-box" class="fixed top-4 right-4 p-3 rounded-lg shadow-xl hidden z-20 bg-red-100 text-red-800 border border-red-400" role="alert"><?= htmlspecialchars($error_message) ?></div>
    
    <div class="card">
        <header class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-blue-600">แก้ไขผู้ใช้: <?= htmlspecialchars($user_data['username']) ?></h1>
            <a href="user_list.php" class="text-gray-600 hover:text-blue-500 font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
                กลับไปหน้ารายชื่อ
            </a>
        </header>

        <form action="edit_user.php?id=<?= htmlspecialchars($user_data['id']) ?>" method="POST" class="space-y-4">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_data['id']) ?>">
            
            <div>
                <label class="block mb-1 font-medium">ชื่อผู้ใช้</label>
                <input type="text" name="username" class="form-input" value="<?= htmlspecialchars($user_data['username']) ?>" required>
            </div>
            
            <div>
                <label class="block mb-1 font-medium">อีเมล</label>
                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($user_data['email']) ?>" required>
            </div>

            <div>
                <label class="block mb-1 font-medium">เบอร์โทรศัพท์</label>
                <input type="text" name="phone_number" class="form-input" value="<?= htmlspecialchars($user_data['phone_number']) ?>">
            </div>

            <div>
                <label class="block mb-1 font-medium">ที่อยู่จัดส่ง</label>
                <textarea name="shipping_address" rows="4" class="form-input"><?= htmlspecialchars($user_data['shipping_address']) ?></textarea>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition duration-200">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>

    <script>
        
        function showMessage(text, isError = true) {
            const messageBox = document.getElementById('message-box');
            messageBox.textContent = text;
            messageBox.classList.remove('hidden', 'bg-red-100', 'text-red-800', 'bg-green-100', 'text-green-800', 'border', 'border-red-400', 'border-green-400');
            
            if (isError) {
                messageBox.classList.add('bg-red-100', 'text-red-800', 'border', 'border-red-400');
            } else {
                messageBox.classList.add('bg-green-100', 'text-green-800', 'border', 'border-green-400');
            }
            
            setTimeout(() => {
                messageBox.classList.add('hidden');
            }, 3000);
        }

    
        const errorMessage = "<?= htmlspecialchars($error_message) ?>";
        if (errorMessage) {
            showMessage(errorMessage, true);
        }
    </script>
</body>
</html>