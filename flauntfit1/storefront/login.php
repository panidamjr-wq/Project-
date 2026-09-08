<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlauntFit - เข้าสู่ระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
           
        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
        }
        .logo-section {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 1.5rem 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .logo-section:hover {
            transform: translateY(-5px);
        }
        .logo {
            width: 5rem;
            height: auto;
            border-radius: 0.75rem;
            margin-right: 1.5rem;
        }
        .form-container {
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body class="bg-#FFF -100 flex items-center justify-center h-screen">

    <!-- Login Container -->
    <div class="main-content">
        <!-- Logo Section -->
        <div class="logo-section">
            <img src="lg.png" alt="FlauntFit Logo" class="logo">
            <h1 class="text-4xl font-bold text-gray-800">FlauntFit</h1>
        </div>

        <!-- Login Form -->
        <div class="bg-white p-8 rounded-3xl shadow-2xl form-container bg-opacity-90 backdrop-blur-md">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">เข้าสู่ระบบ</h2>
            <form id="loginForm" action="login.php" method="POST">
                <!-- Username Input -->
                <div class="mb-4">
                    <input type="text" id="username" name="username" placeholder="ชื่อผู้ใช้" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-blue-500 transition duration-300">
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <input type="password" id="password" name="password" placeholder="รหัสผ่าน" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-blue-500 transition duration-300">
                </div>
                
                <!-- Login Button -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 rounded-xl font-semibold shadow-md hover:from-blue-600 hover:to-indigo-700 transition duration-300 ease-in-out transform hover:scale-105">
                    เข้าสู่ระบบ
                </button>
            </form>

            <div class="mt-4 text-center text-gray-600">
                ยังไม่มีบัญชีใช่ไหม? <a href="index.html" class="text-blue-600 hover:underline font-semibold">ลงทะเบียนที่นี่</a>
            </div>
            
            <!-- Message Box -->
            <div id="messageBox" class="mt-4 p-3 rounded-xl hidden text-sm font-medium text-center transition-all duration-300 ease-in-out"></div>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const messageBox = document.getElementById('messageBox');


        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            
            // Clear previous message
            messageBox.textContent = '';
            messageBox.className = 'mt-4 p-3 rounded-xl hidden text-sm font-medium text-center';

            try {
                const response = await fetch('login.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                messageBox.textContent = result.message;
                messageBox.classList.remove('hidden');

                if (result.success) {
                    messageBox.classList.add('bg-green-100', 'text-green-800');
                    // Redirect or show success message
                    console.log("Login successful!");
                    setTimeout(() => {
                        window.location.href = "welcome.html"; // Replace with your desired welcome page
                    }, 1500);
                } else {
                    messageBox.classList.add('bg-red-100', 'text-red-800');
                }

            } catch (error) {
                messageBox.textContent = "เกิดข้อผิดพลาดในการเชื่อมต่อ";
                messageBox.classList.remove('hidden');
                messageBox.classList.add('bg-red-100', 'text-red-800');
                console.error("Fetch error:", error);
            }
        });
    </script>

</body>
</html>
