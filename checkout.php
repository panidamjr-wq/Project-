<?php
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || !isset($_POST['checkout_items']) || empty($_POST['checkout_items'])) {
    $message_type = 'error';
    $message = 'กรุณาเลือกสินค้าที่จะชำระเงิน';
} else {
    $cart_items = $_SESSION['cart'];
    $selected_keys = $_POST['checkout_items'];
    $checkout_items = [];
    $total_price = 0;

   
    foreach ($selected_keys as $key) {
        if (isset($cart_items[$key])) {
            $checkout_items[$key] = $cart_items[$key];
            $total_price += $cart_items[$key]['price'] * $cart_items[$key]['quantity'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ชำระเงิน - FlauntFit</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<style>
body {
    font-family: 'Kanit', sans-serif;
    background: linear-gradient(to right, #ffdde1, #ffdde1);
    color: #333;
}
.header-bar {
    background: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.main-content {
    background: #fff;
    border-radius: 1.5rem;
    padding: 2.5rem;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
}
.btn-submit {
    background: #ff758c;
    color: #fff;
    font-weight: 600;
    padding: 1rem 2.5rem;
    border-radius: 9999px;
    text-decoration: none;
    transition: background-color 0.3s;
}
.btn-submit:hover {
    background: #ff577b;
}
.input-field {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 0.5rem;
    transition: border-color 0.3s;
}
.input-field:focus {
    outline: none;
    border-color: #ff758c;
}
#message-box {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    padding: 15px 30px;
    border-radius: 10px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: none; 
    color: white;
    text-align: center;
    transition: all 0.5s ease-in-out;
}

#message-box.show {
    display: block;
    animation: fadeInOut 3s forwards;
}

@keyframes fadeInOut {
    0% { opacity: 0; }
    20% { opacity: 1; }
    80% { opacity: 1; }
    100% { opacity: 0; }
}

</style>
</head>
<body class="bg-gray-100 font-sans">
<div id="message-box" class="hidden"></div>

<header class="header-bar p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <a href="homepage.php" class="text-2xl font-bold text-pink-500">FlauntFit</a>
        <nav>
            <a href="cart.php" class="text-gray-600 hover:text-pink-500 transition-colors">ตะกร้าสินค้า</a>
        </nav>
    </div>
</header>


<main class="container mx-auto mt-8 p-4">
    <div class="main-content">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">ชำระเงิน</h1>
        
        <?php if (!empty($checkout_items)): ?>
            
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">สรุปคำสั่งซื้อ</h2>
                <div class="space-y-4">
                    <?php foreach ($checkout_items as $item): ?>
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl shadow-sm">
                            <span class="text-gray-700"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="text-gray-500">x <?php echo htmlspecialchars($item['quantity']); ?></span>
                            <span class="text-pink-600 font-bold">฿<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="border-t border-gray-200 mt-6 pt-6 flex justify-between items-center text-2xl font-bold">
                    <span>ยอดรวมทั้งหมด</span>
                    <span class="text-pink-600">฿<?php echo number_format($total_price, 2); ?></span>
                </div>
            </div>

            
            <form action="process_payment.php" method="POST">
                <h2 class="text-2xl font-semibold mb-4">ข้อมูลการจัดส่งและชำระเงิน</h2>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-gray-700 font-semibold mb-1">ชื่อ-นามสกุล:</label>
                        <input type="text" id="name" name="name" class="input-field" required>
                    </div>
                    <div>
                        <label for="phone" class="block text-gray-700 font-semibold mb-1">เบอร์โทรศัพท์:</label>
                        <input type="tel" id="phone" name="phone" class="input-field" required>
                    </div>
                    <div>
                        <label for="address" class="block text-gray-700 font-semibold mb-1">ที่อยู่:</label>
                        <textarea id="address" name="address" rows="4" class="input-field" required></textarea>
                    </div>
                    <div>
                        <label for="note" class="block text-gray-700 font-semibold mb-1">หมายเหตุ (ถ้ามี):</label>
                        <textarea id="note" name="note" rows="2" class="input-field"></textarea>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-2">ช่องทางการชำระเงิน</h3>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="payment-cod" name="payment_method" value="cod" class="mr-2" checked>
                        <label for="payment-cod" class="text-gray-700">เก็บเงินปลายทาง</label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio" id="payment-bank" name="payment_method" value="bank_transfer" class="mr-2">
                        <label for="payment-bank" class="text-gray-700">โอนเงินผ่านธนาคาร</label>
                    </div>
                </div>

                <div id="bank-info" class="hidden mt-6 p-4 rounded-xl bg-gray-100 text-gray-800">
                    <p class="font-bold">ข้อมูลการโอนเงิน</p>
                    <p>ธนาคาร: กสิกรไทย</p>
                    <p>ชื่อบัญชี: FlauntFit Co., Ltd.</p>
                    <p>เลขที่บัญชี: 123-4-56789-0</p>
                    <p class="mt-2 text-sm text-red-500">กรุณาโอนเงินเข้าบัญชีนี้ และแจ้งหลักฐานการโอนเงิน</p>
                </div>

                <?php foreach($selected_keys as $key): ?>
                    <input type="hidden" name="checkout_items[]" value="<?php echo $key; ?>">
                <?php endforeach; ?>

                <button type="submit" class="btn-submit w-full mt-8">ยืนยันการสั่งซื้อ</button>
            </form>
        <?php else: ?>
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-600">ไม่มีสินค้าที่เลือกสำหรับการชำระเงิน</h2>
                <a href="cart.php" class="mt-4 inline-block btn-submit">กลับสู่ตะกร้าสินค้า</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</main>

<footer class="bg-white shadow-lg mt-8 py-6 text-center text-gray-600">
    &copy; 2025 FlauntFit. All rights reserved.
</footer>

<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const bankRadio = document.getElementById('payment-bank');
        const bankInfo = document.getElementById('bank-info');
        const form = document.querySelector('form');
        const messageBox = document.getElementById('message-box');

        function showMessage(message, type = 'info') {
            messageBox.textContent = message;
            if (type === 'error') {
                messageBox.style.backgroundColor = '#f44336';
            } else if (type === 'success') {
                messageBox.style.backgroundColor = '#4CAF50';
            }
            messageBox.classList.add('show');
            setTimeout(() => {
                messageBox.classList.remove('show');
            }, 3000);
        }

        bankRadio.addEventListener('change', function() {
            if (this.checked) {
                bankInfo.classList.remove('hidden');
            }
        });

        document.getElementById('payment-cod').addEventListener('change', function() {
            if (this.checked) {
                bankInfo.classList.add('hidden');
            }
        });

        <?php if (isset($message_type) && $message_type === 'error'): ?>
            showMessage("<?php echo htmlspecialchars($message); ?>", "error");
        
            setTimeout(() => { window.location.href='cart.php'; }, 3000);
        <?php endif; ?>
    });
</script>
</body>
</html>
