<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlauntFit Backend Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #fce4ec; /* Pastel Pink Background */
            color: #4b5563;
        }
        .icon-card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease-in-out;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .icon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        .icon-card svg {
            width: 4rem;
            height: 4rem;
            color: #e91e63;
        }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        <header class="text-center mb-12">
            <h1 class="text-5xl font-extrabold text-[#e91e63] leading-tight">แดชบอร์ดหลังบ้าน</h1>
            <p class="mt-4 text-xl text-gray-600">จัดการข้อมูลทั้งหมดสำหรับร้านค้าของคุณ</p>
        </header>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Link to Add Product -->
            <a href="product_add.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">เพิ่มสินค้า</span>
                </div>
            </a>
            
            <!-- Link to View/Manage Products -->
            <a href="select_product.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">ดูและจัดการสินค้า</span>
                </div>
            </a>
            
            <!-- Link to Edit Products -->
            <a href="product_edit.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.5a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L15.232 5.232z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">แก้ไขสินค้า</span>
                </div>
            </a>
            
            <!-- Other Links -->
            <a href="#" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM21 17h-1v-2l-4-4H8l-4 4v2H3c-1.1 0-2 .9-2 2v2h22v-2c0-1.1-.9-2-2-2z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">จัดการผู้ใช้งาน</span>
                </div>
            </a>
            
            <!-- Back to Home Link -->
            <a href="index.html" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0v-9.75a1.75 1.75 0 011.75-1.75H12" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">กลับสู่หน้าหลัก</span>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
