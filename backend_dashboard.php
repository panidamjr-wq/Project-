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
            color: #3570c4ff;
        }
        .icon-card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(252, 0, 210, 0.08);
            transition: all 0.3s ease-in-out;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .icon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(241, 25, 25, 0.1);
        }
        .icon-card svg {
            width: 3.5rem;
            height: 3.5rem;
            color: #f52de4ff; 
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-6">แผงควบคุมหลังบ้าน FlauntFit</h1>
        <p class="text-xl text-center text-gray-600 mb-12">จัดการข้อมูลสินค้า,คำสั่งซื้อ และผู้ใช้งาน</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            

            <a href="product_add.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">เพิ่มสินค้าใหม่</span>
                </div>
            </a>
            
            
            <a href="select_product.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">ดูรายการสินค้า</span>
                </div>
            </a>
            
            <a href="category_management.php" class="block no-underline">
                <div class="icon-card">
                    
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.529 1.258a6.39 6.39 0 014.442 0A2.977 2.977 0 0017.59 3.23c.319.294.62.592.903.885A3.012 3.012 0 0122.5 7.5c0 .641-.122 1.261-.36 1.83.167.319.293.649.378.989a6.39 6.39 0 010 4.442c-.085.34-.211.67-.378.989a3.012 3.012 0 01-1.928 2.385c-.283.293-.584.591-.903.885A2.977 2.977 0 0013.971 22.742a6.39 6.39 0 01-4.442 0 2.977 2.977 0 00-4.062-1.983c-.319-.294-.62-.592-.903-.885A3.012 3.012 0 011.5 16.5c0-.641.122-1.261.36-1.83-.167-.319-.293-.649-.378-.989a6.39 6.39 0 010-4.442c.085-.34.211-.67.378-.989a3.012 3.012 0 011.928-2.385c.283-.293.584-.591.903-.885A2.977 2.977 0 009.529 1.258zM12 12.75a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">จัดการประเภทสินค้า</span>
                </div>
            </a>
            
            <a href="order_management.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">สรุปจัดการคำสั่งซื้อ</span>
                </div>
            </a>

            
            <a href="user_list.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-1.667 0-4.667 1.333-6 4v2h12v-2c-1.333-2.667-4.333-4-6-4z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">จัดการผู้ใช้งาน</span>
                </div>
            </a>

             
             <a href="admin_orders.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12l5.25 5.25m-5.25 0l5.25-5.25M6 7.5h3.375a3.375 3.375 0 013.375 3.375v2.875" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">รายการออเดอร์</span>
                </div>
            </a>

            
            <a href="homepage.php" class="block no-underline">
                <div class="icon-card">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">กลับหน้าหลัก</span>
                </div>
            </a>
            <a href="index.php" class="block no-underline">
                <div class="icon-card logout-card">
                    
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H9" />
                    </svg>
                    <span class="mt-4 text-lg font-semibold text-gray-700">ออกจากระบบ</span>
                </div>
            </a>

        </div>
    </div>
</body>
</html>