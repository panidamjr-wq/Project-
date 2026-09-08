<?php
session_start();
require_once 'connectdb.php'; 

 
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header("Location: login.php"); 
    exit();
}

$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category_name = '';
$page_title = 'เพิ่มประเภทสินค้าใหม่';

if ($category_id > 0) {
    $page_title = 'แก้ไขประเภทสินค้า';
    $sql = "SELECT categories_name FROM categories WHERE categories_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $category_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $category_name = $row['categories_name'];
        } else {
            $_SESSION['error_message'] = "ไม่พบประเภทสินค้า ID: {$category_id}";
            header("Location: category_management.php");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
         $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับดึงข้อมูล: " . mysqli_error($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_category_name = trim($_POST['category_name']);
    $post_category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    if (empty($new_category_name)) {
        $error_message = "กรุณากรอกชื่อประเภทสินค้า";
    } else {

        if ($post_category_id > 0) {
            
            $sql = "UPDATE categories SET categories_name = ? WHERE categories_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $new_category_name, $post_category_id);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "แก้ไขประเภทสินค้า ID: {$post_category_id} สำเร็จแล้ว";
                    header("Location: category_management.php");
                    exit();
                } else {
                    $error_message = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            
            $sql = "INSERT INTO categories (categories_name) VALUES (?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $new_category_name);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success_message'] = "เพิ่มประเภทสินค้า '{$new_category_name}' สำเร็จแล้ว";
                    header("Location: category_management.php");
                    exit();
                } else {
                    $error_message = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    
    $category_name = $new_category_name;
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; }
        .form-container { 
            max-width: 500px; 
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .submit-btn { transition: all 0.2s; }
        .submit-btn:hover { transform: translateY(-1px); }
    </style>
</head>
<body class="bg-gray-100">

<div class="form-container">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center"><?php echo $page_title; ?></h1>

    
    <?php if (isset($error_message)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="category_edit.php">
        <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">

        <div class="mb-5">
            <label for="category_name" class="block text-gray-700 text-sm font-bold mb-2">ชื่อประเภทสินค้า:</label>
            <input type="text" id="category_name" name="category_name" 
                   value="<?php echo htmlspecialchars($category_name); ?>"
                   class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="เช่น เสื้อยืด, กางเกงวิ่ง" required>
        </div>
        
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl focus:outline-none focus:shadow-outline submit-btn">
                บันทึกข้อมูล
            </button>
            <a href="category_management.php" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800">
                ยกเลิกและกลับ
            </a>
        </div>
    </form>
</div>

</body>
</html>