<?php
// Include the database connection file
require_once 'connectdb.php';

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to handle product actions (Add, Edit, Delete)
function handleProductAction($conn) {
    // Logic to add, edit, or delete a product
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_action'])) {
        $action = $_POST['product_action'];
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $price = floatval($_POST['price']);
        
        $image_url = '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            $image_name = uniqid() . '-' . basename($_FILES['image_file']['name']);
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
                $image_url = $target_dir . $image_name;
            }
        }

        if ($action == 'add' && $image_url) {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (product_name, description, price, image_url, status) VALUES (?, ?, ?, ?, 'active')");
            mysqli_stmt_bind_param($stmt, "ssds", $product_name, $description, $price, $image_url);
            mysqli_stmt_execute($stmt);
        } elseif ($action == 'edit' && $product_id) {
            if ($image_url) {
                $stmt = mysqli_prepare($conn, "UPDATE products SET product_name=?, description=?, price=?, image_url=? WHERE product_id=?");
                mysqli_stmt_bind_param($stmt, "ssdsi", $product_name, $description, $price, $image_url, $product_id);
            } else {
                $stmt = mysqli_prepare($conn, "UPDATE products SET product_name=?, description=?, price=? WHERE product_id=?");
                mysqli_stmt_bind_param($stmt, "ssdi", $product_name, $description, $price, $product_id);
            }
            mysqli_stmt_execute($stmt);
        } elseif ($action == 'delete' && $product_id) {
            $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE product_id=?");
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
        }
    }
}

// Function to handle category actions (Add, Edit, Delete)
function handleCategoryAction($conn) {
    // Logic to add, edit, or delete a category
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_action'])) {
        $action = $_POST['category_action'];
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
        
        if ($action == 'add') {
            $stmt = mysqli_prepare($conn, "INSERT INTO categories (categories_name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $category_name);
            mysqli_stmt_execute($stmt);
        } elseif ($action == 'edit' && $category_id) {
            $stmt = mysqli_prepare($conn, "UPDATE categories SET categories_name=? WHERE categories_id=?");
            mysqli_stmt_bind_param($stmt, "si", $category_name, $category_id);
            mysqli_stmt_execute($stmt);
        } elseif ($action == 'delete' && $category_id) {
            $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE categories_id=?");
            mysqli_stmt_bind_param($stmt, "i", $category_name, $category_id);
            mysqli_stmt_execute($stmt);
        }
    }
}

// Handle form submissions
handleProductAction($conn);
handleCategoryAction($conn);

// Fetch data from the database
$search_query = isset($_GET['search_product']) ? mysqli_real_escape_string($conn, $_GET['search_product']) : '';
$sql = "SELECT * FROM products WHERE product_name LIKE '%$search_query%' ORDER BY product_id DESC";
$products_result = mysqli_query($conn, $sql);

$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY categories_id DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f8f9fa; }
        .container { max-width: 1200px; }
        .card { border-radius: 1rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .table-responsive { overflow-x: auto; }
        .table img { width: 80px; height: auto; object-fit: cover; border-radius: 0.5rem; }
        .btn-action { margin-right: 5px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Edit information</h1>

    <!-- Tabs for Product and Category Management -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab" aria-controls="product" aria-selected="true">จัดการสินค้า</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="category-tab" data-bs-toggle="tab" data-bs-target="#category" type="button" role="tab" aria-controls="category" aria-selected="false">จัดการประเภทสินค้า</button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-3">
        <!-- Product Management Tab -->
        <div class="tab-pane fade show active" id="product" role="tabpanel" aria-labelledby="product-tab">
            <div class="card p-4">
                <h2 class="mb-3">รายการสินค้า</h2>
                
                <!-- Search Form -->
                <form class="d-flex mb-3" method="GET" action="">
                    <input class="form-control me-2" type="search" placeholder="ค้นหาสินค้า" aria-label="Search" name="search_product">
                    <button class="btn btn-outline-success" type="submit">ค้นหา</button>
                </form>

                <!-- Product Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>รูปภาพ</th>
                                <th>ชื่อสินค้า</th>
                                <th>รายละเอียด</th>
                                <th>ราคา</th>
                                <th>สถานะ</th>
                                <th>วันที่สร้าง</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($products_result) > 0) {
                                while($row = mysqli_fetch_assoc($products_result)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
                                    echo "<td><img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['product_name']) . "'></td>";
                                    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-warning btn-sm btn-action' onclick='editProduct(" . json_encode($row) . ")'>แก้ไข</button>";
                                    echo "<a href='?product_action=delete&product_id=" . $row['product_id'] . "' class='btn btn-danger btn-sm btn-action' onclick='return confirm(\"แน่ใจหรือไม่ว่าต้องการลบสินค้านี้?\")'>ลบ</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>ไม่พบสินค้า</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Product Form (Add/Edit) -->
                <h3 class="mt-4" id="productFormTitle">เพิ่มสินค้า</h3>
                <form id="productForm" method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="product_action" id="productAction" value="add">
                    <input type="hidden" name="product_id" id="productId">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">ชื่อสินค้า</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">รายละเอียด</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">ราคา</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="image_file" class="form-label">รูปภาพ</label>
                        <input type="file" class="form-control" id="image_file" name="image_file">
                    </div>
                    <button type="submit" class="btn btn-primary">บันทึกสินค้า</button>
                    <button type="reset" class="btn btn-secondary" onclick="resetProductForm()">ยกเลิก</button>
                </form>
            </div>
        </div>

        <!-- Category Management Tab -->
        <div class="tab-pane fade" id="category" role="tabpanel" aria-labelledby="category-tab">
            <div class="card p-4">
                <h2 class="mb-3">รายการประเภทสินค้า</h2>

                <!-- Category Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อประเภท</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($categories_result) > 0) {
                                while($row = mysqli_fetch_assoc($categories_result)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['categories_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['categories_name']) . "</td>";
                                    echo "<td>";
                                    echo "<button class='btn btn-warning btn-sm btn-action' onclick='editCategory(" . json_encode($row) . ")'>แก้ไข</button>";
                                    echo "<a href='?category_action=delete&category_id=" . $row['categories_id'] . "' class='btn btn-danger btn-sm btn-action' onclick='return confirm(\"แน่ใจหรือไม่ว่าต้องการลบประเภทสินค้านี้?\")'>ลบ</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>ไม่พบประเภทสินค้า</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Category Form (Add/Edit) -->
                <h3 class="mt-4" id="categoryFormTitle">เพิ่มประเภทสินค้า</h3>
                <form id="categoryForm" method="POST" action="">
                    <input type="hidden" name="category_action" id="categoryAction" value="add">
                    <input type="hidden" name="category_id" id="categoryId">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">ชื่อประเภทสินค้า</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">บันทึกประเภทสินค้า</button>
                    <button type="reset" class="btn btn-secondary" onclick="resetCategoryForm()">ยกเลิก</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Functions for Product Management
    function editProduct(product) {
        document.getElementById('productFormTitle').innerText = 'แก้ไขสินค้า';
        document.getElementById('productAction').value = 'edit';
        document.getElementById('productId').value = product.product_id;
        document.getElementById('product_name').value = product.product_name;
        document.getElementById('description').value = product.description;
        document.getElementById('price').value = product.price;
        // Note: The file input field cannot be pre-filled for security reasons
    }

    function resetProductForm() {
        document.getElementById('productFormTitle').innerText = 'เพิ่มสินค้า';
        document.getElementById('productAction').value = 'add';
        document.getElementById('productForm').reset();
    }

    // Functions for Category Management
    function editCategory(category) {
        document.getElementById('categoryFormTitle').innerText = 'แก้ไขประเภทสินค้า';
        document.getElementById('categoryAction').value = 'edit';
        document.getElementById('categoryId').value = category.categories_id;
        document.getElementById('category_name').value = category.categories_name;
    }

    function resetCategoryForm() {
        document.getElementById('categoryFormTitle').innerText = 'เพิ่มประเภทสินค้า';
        document.getElementById('categoryAction').value = 'add';
        document.getElementById('categoryForm').reset();
    }

    // Handle form submissions to prevent page reload and show messages (optional but recommended)
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        this.submit();
    });

    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        this.submit();
    });
</script>

</body>
</html>
<?php
mysqli_close($conn);
?>
