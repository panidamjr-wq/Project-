<?php
require_once 'connectdb.php';

if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $order_id_to_delete = intval($_GET['delete_id']);

    $sql_delete = "DELETE FROM `orders` WHERE orders_id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);

    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $order_id_to_delete);
        if (mysqli_stmt_execute($stmt_delete)) {
           
            header("Location: order_management.php");
            exit();
        } else {
            
            echo "Error deleting record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

$sql = "SELECT * FROM `orders` ORDER BY orders_id DESC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ - FlauntFit</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #fce4ec; 
            color: #4b5563;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .header {
            border-bottom: 2px solid #f0f4f8;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        th, td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f7fafc;
            font-weight: 600;
        }
        tr:hover {
            background-color: #fafcfd;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        .btn-delete {
            background-color: #e53e3e;
            color: #ffffff;
        }
        .btn-delete:hover {
            background-color: #c53030;
        }
        .btn-add {
            background-color: #48bb78;
            color: #ffffff;
        }
        .btn-add:hover {
            background-color: #38a169;
        }
        .back-link {
            color: #4a90e2;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="text-3xl font-bold">จัดการคำสั่งซื้อ</h1>
            <a href="backend_dashboard.php" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                กลับไปที่แดชบอร์ด
            </a>
        </div>
        <div class="table-container">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col">รหัสคำสั่งซื้อ</th>
                        <th scope="col">รหัสผู้ใช้งาน</th>
                        <th scope="col">ยอดรวม</th>
                        <th scope="col">สถานะ</th>
                    
                        <th scope="col">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="bg-white border-b">
                                <td data-label="รหัสคำสั่งซื้อ" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($row['orders_id']) ?></td>
                                <td data-label="รหัสผู้ใช้งาน" class="py-4 px-6"><?= htmlspecialchars($row['user_id']) ?></td>
                                <td data-label="ยอดรวม" class="py-4 px-6"><?= htmlspecialchars(number_format($row['total_amount'], 2)) ?></td>
                                <td data-label="สถานะ" class="py-4 px-6"><?= htmlspecialchars($row['status']) ?></td>
                                
                                <td class="p-2 border text-center">
                                    <a href="edit_order.php?order_id=<?php echo $row['orders_id']; ?>" 
                                    class="px-3 py-1 bg-yellow-500 text-white rounded">แก้ไข</a>
                                    <a href="delete_order.php?order_id=<?php echo $row['orders_id']; ?>" 
                                    class="px-3 py-1 bg-red-500 text-white rounded"
                                    onclick="return confirm('ยืนยันการลบคำสั่งซื้อนี้?');">ลบ</a>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">ไม่พบข้อมูลคำสั่งซื้อ</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>