<?php
session_start();
require_once 'connectdb.php';

if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้'); window.location.href='index.php';</script>";
    exit();
}

$sql = "SELECT id, username, email, phone_number, shipping_address FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้งาน - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Kanit', sans-serif; 
            background: #f0f4f8; 
            color: #333;
        }
        .container { 
            max-width: 1200px; 
            margin: 2rem auto; 
            padding: 2rem; 
            background: #fff; 
            border-radius: 1rem; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.08); 
        }
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
        }
        th, td { 
            padding: 1rem; 
            border-bottom: 1px solid #e5e7eb; 
            text-align: left; 
        }
        th { 
            background: #f3f4f6; 
            color: #1f2937;
            font-weight: 700;
        }
        
        .btn-action { 
            padding: 0.5rem 0.75rem; 
            border-radius: 0.5rem; 
            font-weight: 600; 
            text-decoration: none; 
            transition: background 0.2s; 
            display: inline-flex;
            align-items: center;
        }
        .btn-edit { background: #4a90e2; color: white; }
        .btn-edit:hover { background: #357bd8; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-delete:hover { background: #dc2626; }
        .btn-icon { margin-right: 0.25rem; width: 1rem; height: 1rem; } 
        #message-box { transition: all 0.3s ease-in-out; }
    </style>
</head>
<body class="p-4">
    <div id="message-box" class="fixed top-4 right-4 p-3 rounded-lg shadow-xl hidden z-20" role="alert"></div>

    <div class="container">
        <header class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-3xl font-bold text-blue-600">รายชื่อผู้ใช้งาน</h1>
            <a href="backend_dashboard.php" class="text-gray-600 hover:text-blue-500 font-medium flex items-center">
            <a href="backend_dashboard.php" class="text-blue-500 hover:text-blue-700 font-semibold">กลับสู่แดชบอร์ด</a>
        </header>

        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th class="w-12">ID</th>
                        <th class="w-40">ชื่อผู้ใช้</th>
                        <th class="w-48">อีเมล</th>
                        <th class="w-32">เบอร์โทรศัพท์</th>
                        <th>ที่อยู่จัดส่ง</th>
                        <th class="w-40 text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['shipping_address'] ?? ' - ', 0, 50)) . (strlen($row['shipping_address'] ?? '') > 50 ? '...' : ''); ?></td>
                                
                               
                                <td class="text-center">
                                    <div class="flex space-x-2 justify-center">
                                        
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.828 2.828l-8.586 8.586H3v-2.828l8.586-8.586 2.828-2.828z" /></svg>
                                            แก้ไข
                                        </a>
                                        
                                        <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')" class="btn-action btn-delete text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.86 10.32A2 2 0 0116.14 19H7.86a2 2 0 01-1.995-1.68L5 7m4 0V5a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6" /></svg>
                                            ลบ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-gray-500 py-6">ยังไม่มีผู้ใช้งานในระบบ</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

   
    <div id="confirmation-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-2xl max-w-sm w-full">
            <h3 class="text-xl font-bold text-red-600 mb-4">ยืนยันการลบ</h3>
            <p id="modal-text" class="mb-6 text-gray-700">คุณแน่ใจหรือไม่ที่จะลบผู้ใช้ชื่อ...</p>
            <div class="flex justify-end space-x-3">
                <button id="modal-cancel" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">ยกเลิก</button>
                <button id="modal-confirm" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">ลบ</button>
            </div>
        </div>
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

       
        <?php if ($success_message): ?>
            showMessage("<?php echo htmlspecialchars($success_message); ?>", false);
        <?php endif; ?>
        <?php if ($error_message): ?>
            showMessage("<?php echo htmlspecialchars($error_message); ?>", true);
        <?php endif; ?>

        let deleteUserId = null;
        const modal = document.getElementById('confirmation-modal');
        const modalText = document.getElementById('modal-text');
        const modalCancel = document.getElementById('modal-cancel');
        const modalConfirm = document.getElementById('modal-confirm');

        function confirmDelete(userId, username) {
            deleteUserId = userId;
            modalText.innerHTML = `คุณแน่ใจหรือไม่ที่จะลบผู้ใช้ชื่อ <span class="font-semibold text-red-600">'${username}'</span> (ID: ${userId})? การดำเนินการนี้ไม่สามารถย้อนกลับได้`;
            modal.classList.remove('hidden');
        }

        modalCancel.onclick = function() {
            modal.classList.add('hidden');
            deleteUserId = null;
        }

        modalConfirm.onclick = function() {
            if (deleteUserId !== null) {
                
                window.location.href = `delete_user.php?id=${deleteUserId}`;
            }
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>
<?php
mysqli_close($conn);
?>