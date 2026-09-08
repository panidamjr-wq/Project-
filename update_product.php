<?php
session_start();

if (!file_exists('connectdb.php')) {
    $_SESSION['error_message'] = "ไม่พบไฟล์ connectdb.php";
    header('Location: select_product.php');
    exit();
}
include 'connectdb.php';

function uploadImage($file, $upload_dir = "images/") {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_name = uniqid() . "_" . basename($file['name']);
    $target_file = $upload_dir . $file_name;
    $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array($ext,$allowed)) return false;
    return move_uploaded_file($file['tmp_name'], $target_file) ? $target_file : false;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: select_product.php');
    exit();
}

$product_id = $_POST['product_id'] ?? null;

if (empty($product_id)) {
    $_SESSION['error_message'] = "ไม่พบรหัสสินค้าที่ต้องการแก้ไข";
    header('Location: select_product.php');
    exit();
}

$productName = trim($_POST['productName'] ?? '');
$productPrice = floatval($_POST['productPrice'] ?? 0);
$productDescription = trim($_POST['productDescription'] ?? '');
$additionalDetails = trim($_POST['additionalDetails'] ?? '');
$oldMainImage = $_POST['oldMainImage'] ?? '';

$variation_ids = $_POST['variation_id'] ?? []; 
$colors = $_POST['colors'] ?? []; 
$old_variation_images = $_POST['variation_images_old'] ?? []; 
$stocks_data = $_POST['stocks'] ?? []; 
$sizes_data = $_POST['sizes'] ?? []; 


if (empty($productName) || $productPrice <= 0) {
    $_SESSION['error_message'] = "กรุณากรอกชื่อสินค้าและราคาสินค้าที่ถูกต้อง";
    header('Location: product_edit.php?id=' . $product_id);
    exit();
}


mysqli_begin_transaction($conn);
$is_success = true;

try {
    $newMainImage = uploadImage($_FILES['mainImage'] ?? null);
    $finalMainImage = $newMainImage ? $newMainImage : $oldMainImage;

    $sql_update_product = "UPDATE `products` SET 
                            product_name = ?, price = ?, description = ?, 
                            additional_details = ?, image_url = ?
                           WHERE product_id = ?";
    
    $stmt_product = mysqli_prepare($conn, $sql_update_product);
    if (!$stmt_product) throw new Exception("Prepare Product Failed: " . mysqli_error($conn));

    mysqli_stmt_bind_param($stmt_product, "sdsssi", 
        $productName, $productPrice, $productDescription, 
        $additionalDetails, $finalMainImage, $product_id
    );

    if (!mysqli_stmt_execute($stmt_product)) throw new Exception("Execute Product Failed: " . mysqli_stmt_error($stmt_product));
    mysqli_stmt_close($stmt_product);

   
    $existing_variation_ids = [];
    $sql_get_vids = "SELECT variation_id FROM product_variations WHERE product_id = ?";
    $stmt_get_vids = mysqli_prepare($conn, $sql_get_vids);
    if ($stmt_get_vids) {
        mysqli_stmt_bind_param($stmt_get_vids, "i", $product_id);
        mysqli_stmt_execute($stmt_get_vids);
        $result_vids = mysqli_stmt_get_result($stmt_get_vids);
        while ($row = mysqli_fetch_assoc($result_vids)) {
            $existing_variation_ids[] = $row['variation_id'];
        }
        mysqli_stmt_close($stmt_get_vids);
    }
    
    if (!empty($existing_variation_ids)) {
        $in_placeholders = implode(',', array_fill(0, count($existing_variation_ids), '?'));
        $sql_delete_stocks = "DELETE FROM `product_stocks` WHERE variation_id IN ($in_placeholders)";
        $stmt_del_stocks = mysqli_prepare($conn, $sql_delete_stocks);
        if ($stmt_del_stocks) {
           
            $types = str_repeat('i', count($existing_variation_ids));
            mysqli_stmt_bind_param($stmt_del_stocks, $types, ...$existing_variation_ids);
            mysqli_stmt_execute($stmt_del_stocks);
            mysqli_stmt_close($stmt_del_stocks);
        }
    }
    
    
    $sql_delete_variations = "DELETE FROM `product_variations` WHERE product_id = ?";
    $stmt_del_variations = mysqli_prepare($conn, $sql_delete_variations);
    if (!$stmt_del_variations) throw new Exception("Prepare Delete Variations Failed: " . mysqli_error($conn));
    mysqli_stmt_bind_param($stmt_del_variations, "i", $product_id);
    mysqli_stmt_execute($stmt_del_variations);
    mysqli_stmt_close($stmt_del_variations);


    $new_variation_index = 0;
    
    foreach ($colors as $v_index => $color_name) {
        $color_name = trim($color_name);
        
        if (empty($color_name) || !isset($sizes_data[$v_index])) {
            continue; 
        }

        $v_image_file = $_FILES['variation_images_file']['tmp_name'][$v_index] ?? null;
        $old_v_image_url = $old_variation_images[$v_index] ?? '';
        $final_v_image = $old_v_image_url;

        if ($v_image_file) {
            $v_file_array = [
                'name' => $_FILES['variation_images_file']['name'][$v_index],
                'type' => $_FILES['variation_images_file']['type'][$v_index],
                'tmp_name' => $v_image_file,
                'error' => $_FILES['variation_images_file']['error'][$v_index],
                'size' => $_FILES['variation_images_file']['size'][$v_index]
            ];
            $new_v_image = uploadImage($v_file_array);
            if ($new_v_image) {
                $final_v_image = $new_v_image;
            } else {
                
            }
        }

        $sql_insert_variation = "INSERT INTO `product_variations` (product_id, color_name, image_url) VALUES (?, ?, ?)";
        $stmt_insert_variation = mysqli_prepare($conn, $sql_insert_variation);
        if (!$stmt_insert_variation) throw new Exception("Prepare Insert Variation Failed: " . mysqli_error($conn));
        
        mysqli_stmt_bind_param($stmt_insert_variation, "iss", $product_id, $color_name, $final_v_image);
        if (!mysqli_stmt_execute($stmt_insert_variation)) throw new Exception("Execute Insert Variation Failed: " . mysqli_stmt_error($stmt_insert_variation));
        
        $new_variation_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt_insert_variation);

        $selected_sizes = $sizes_data[$v_index];
        $stock_quantities = $stocks_data[$v_index];

        foreach ($selected_sizes as $size_name) {
            $size_name = trim($size_name);
            $quantity = intval($stock_quantities[$size_name] ?? 0);

            if (!empty($size_name)) {
                $sql_insert_stock = "INSERT INTO `product_stocks` (variation_id, size_name, stock_quantity) VALUES (?, ?, ?)";
                $stmt_insert_stock = mysqli_prepare($conn, $sql_insert_stock);
                if (!$stmt_insert_stock) throw new Exception("Prepare Insert Stock Failed: " . mysqli_error($conn));
                
                mysqli_stmt_bind_param($stmt_insert_stock, "isi", $new_variation_id, $size_name, $quantity);
                if (!mysqli_stmt_execute($stmt_insert_stock)) throw new Exception("Execute Insert Stock Failed: " . mysqli_stmt_error($stmt_insert_stock));
                
                mysqli_stmt_close($stmt_insert_stock);
            }
        }
    }

    mysqli_commit($conn);
    $_SESSION['success_message'] = "บันทึกการแก้ไขสินค้า " . htmlspecialchars($productName) . " สำเร็จแล้ว!";
    header('Location: select_product.php');
    exit();

} catch (Exception $e) {
    
    mysqli_rollback($conn);
    $is_success = false;
    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการบันทึก: " . $e->getMessage();
    header('Location: product_edit.php?id=' . $product_id);
    exit();
} finally {
    mysqli_close($conn);
}
?>