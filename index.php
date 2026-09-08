<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400&family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* === General Styles & Background === */
        body {
            font-family: 'Kanit', sans-serif;
            color: #ffffff;
            /* เทคนิคพื้นหลังแบบซ้อนกัน:
               1. linear-gradient: ไล่สีชมพู-ม่วงโปร่งแสงเป็น Overlay
               2. url(...): รูปภาพพื้นหลัง (สำคัญมาก!)
            */
            background: 
                linear-gradient(135deg, rgba(255, 179, 198, 0.8), rgba(175, 128, 192, 0.8)),
                url('https://images.unsplash.com/photo-1551232864-3f0890e58e48?q=80&w=1887&auto=format&fit=crop') 
                no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* === Animations === */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in-down {
            animation: fadeInDown 0.8s ease-out forwards;
        }
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        
        /* === Glassmorphism Card Style === */
        .glass-card {
            background: rgba(255, 255, 255, 0.1); /* พื้นหลังโปร่งแสง */
            backdrop-filter: blur(10px); /* เอฟเฟกต์เบลอพื้นหลัง (หัวใจของ Glassmorphism) */
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2); /* เส้นขอบโปร่งแสง */
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2); /* เงาเข้มขึ้นเล็กน้อย */
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease-in-out;
            text-decoration: none;
            display: block;
        }

        .glass-card:hover {
            transform: translateY(-10px) scale(1.03); /* ขยับและขยายเมื่อ hover */
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 25px rgba(255, 229, 236, 0.5); /* เพิ่มเงาเรืองแสงสีชมพู */
        }

        .card-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.75rem;
            color: #ffffff;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.2);
        }

        .card-subtitle {
            font-weight: 300;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body>
    <div class="container mx-auto px-4 text-center">

        <h1 class="text-6xl md:text-7xl font-bold text-white mb-4 fade-in-down" style="font-family: 'Montserrat', sans-serif; text-shadow: 3px 3px 10px rgba(0,0,0,0.3);">
            Welcome to FlauntFit
        </h1>
        <p class="text-lg text-gray-200 mb-12 fade-in-down" style="animation-delay: 0.2s;">
            จัดการระบบหลังบ้านและข้อมูลสินค้าได้อย่างง่ายดาย
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto fade-in-up" style="animation-delay: 0.4s;">
            <a href="admin_login.php" class="glass-card">
                <h2 class="card-title">เข้าสู่ระบบหลังบ้าน</h2>
                <p class="mt-2 card-subtitle">สำหรับผู้ดูแลระบบและจัดการข้อมูล</p>
            </a>
            <a href="register.php" class="glass-card">
                <h2 class="card-title">ลงทะเบียนสมาชิก</h2>
                <p class="mt-2 card-subtitle">สร้างบัญชีผู้ใช้งานใหม่</p>
            </a>
        </div>
        
    </div>
</body>
</html>