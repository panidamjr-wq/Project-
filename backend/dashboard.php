<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 4rem auto;
            padding: 2rem;
        }
        .card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            text-align: center;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        .btn {
            display: inline-block;
            background-color: #4a90e2;
            color: #ffffff;
            font-weight: 600;
            padding: 1rem 2.5rem;
            border-radius: 9999px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #357bd8;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="container text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-6">ยินดีต้อนรับสู่ FlauntFit</h1>
        <p class="text-lg text-gray-600 mb-8">จัดการระบบหลังบ้านและข้อมูลสินค้าได้อย่างง่ายดาย</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <a href="backend_dashboard.php" class="block">
                <div class="card">
                    <h2 class="text-2xl font-bold text-gray-700">เข้าสู่ระบบหลังบ้าน</h2>
                    <p class="mt-2 text-gray-500">สำหรับผู้ดูแลระบบและจัดการข้อมูล</p>
                </div>
            </a>
            <a href="register.html" class="block">
                <div class="card">
                    <h2 class="text-2xl font-bold text-gray-700">ลงทะเบียนสมาชิก</h2>
                    <p class="mt-2 text-gray-500">สร้างบัญชีผู้ใช้งานใหม่</p>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
