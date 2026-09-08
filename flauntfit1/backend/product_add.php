<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlauntFit - เพิ่มสินค้า</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fc;
        }
        .main-container {
            max-width: 900px;
            margin: 2rem auto;
        }
        .card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
        }
        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="main-container">
    <div class="card">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">เพิ่ม/แก้ไขรายละเอียดสินค้า</h2>

        <!-- Product Form -->
        <form id="productForm" action="save_product.php" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Image Section -->
                <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-gray-100 transition duration-200 cursor-pointer">
                    <label for="imageUpload" class="text-center text-gray-500 font-medium">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M16 12a4 4 0 100-8 4 4 0 000 8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="mt-2 block">อัปโหลดรูปภาพสินค้า</span>
                        <input type="file" id="imageUpload" name="imageFile" class="hidden" accept="image/*">
                    </label>
                    <div id="imagePreview" class="mt-4 hidden max-w-full h-auto rounded-xl shadow-lg"></div>
                </div>

                <!-- Product Details Section -->
                <div class="flex flex-col gap-6">
                    <div>
                        <label for="productName" class="block text-gray-700 font-medium mb-2">ชื่อสินค้า</label>
                        <input type="text" id="productName" name="productName" placeholder="เช่น เสื้อยืดออกกำลังกาย" class="form-input">
                    </div>
<div class="mb-3">
                <label for="categories_id" class="form-label">ประเภทสินค้า</label>
                <select class="form-control" id="categories_id" name="categories_id" required>
                    <option value="">เลือกประเภทสินค้า</option>
                    <option value="1">เสื้อ</option>
                    <option value="2">กางเกง/กระโปรง</option>
                    <option value="3">ชุดเดรส</option>
                    <option value="4">ชุดนอน</option>
                </select>
            </div>
                    <div>
                        <label for="productPrice" class="block text-gray-700 font-medium mb-2">ราคา (บาท)</label>
                        <input type="number" id="productPrice" name="productPrice" placeholder="เช่น 599" class="form-input">
                    </div>
                    
                    <div>
                        <label for="productDescription" class="block text-gray-700 font-medium mb-2">รายละเอียดสินค้า</label>
                        <textarea id="productDescription" name="productDescription" rows="5" placeholder="คุณสมบัติ, วัสดุ, ไซส์" class="form-input"></textarea>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="btn-primary">
                    บันทึกสินค้า
                </button>
            </div>
        </form>

        <!-- Message Box -->
        <div id="messageBox" class="mt-6 p-4 rounded-xl hidden text-center font-medium transition-all duration-300 ease-in-out"></div>
    </div>
</div>

<script>
    const productForm = document.getElementById('productForm');
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    const messageBox = document.getElementById('messageBox');

    imageUpload.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.src = '';
            imagePreview.classList.add('hidden');
        }
    });

    productForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        messageBox.textContent = 'กำลังบันทึกข้อมูล...';
        messageBox.classList.remove('hidden', 'bg-red-100', 'text-red-800', 'bg-green-100', 'text-green-800');
        messageBox.classList.add('bg-blue-100', 'text-blue-800');

        const formData = new FormData(this);

        try {
            const response = await fetch('save_product.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                messageBox.textContent = result.message;
                messageBox.classList.remove('bg-blue-100', 'text-blue-800');
                messageBox.classList.add('bg-green-100', 'text-green-800');
                productForm.reset();
                imagePreview.src = '';
                imagePreview.classList.add('hidden');
            } else {
                messageBox.textContent = result.message;
                messageBox.classList.remove('bg-blue-100', 'text-blue-800');
                messageBox.classList.add('bg-red-100', 'text-red-800');
            }
        } catch (error) {
            messageBox.textContent = "เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error.message;
            messageBox.classList.remove('bg-blue-100', 'text-blue-800');
            messageBox.classList.add('bg-red-100', 'text-red-800');
            console.error('Fetch error:', error);
        }
    });
</script>

</body>
</html>