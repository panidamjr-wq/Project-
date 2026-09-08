<?php
include 'connectdb.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ต้องระบุ Product ID ที่ถูกต้องสำหรับการลบ");
}

$product_id = intval($_GET['id']);

$redirect_url = 'select_product.php'; 

if (isset($_GET['return_url']) && !empty($_GET['return_url'])) {
    $potential_url = urldecode($_GET['return_url']);

    if (strpos($potential_url, 'select_product.php') !== false) {
        $redirect_url = $potential_url;
    }
}


mysqli_begin_transaction($conn);
$is_success = true;

try {
    $sql_stock = "DELETE ps FROM product_stocks ps 
                  JOIN product_variations pv ON ps.variation_id = pv.variation_id 
                  WHERE pv.product_id = ?";
    $stmt_stock = mysqli_prepare($conn, $sql_stock);
    if ($stmt_stock) {
        mysqli_stmt_bind_param($stmt_stock, "i", $product_id);
        if (!mysqli_stmt_execute($stmt_stock)) throw new Exception("Error deleting stock: " . mysqli_stmt_error($stmt_stock));
        mysqli_stmt_close($stmt_stock);
    }

    $sql_variation = "DELETE FROM product_variations WHERE product_id = ?";
    $stmt_variation = mysqli_prepare($conn, $sql_variation);
    if ($stmt_variation) {
        mysqli_stmt_bind_param($stmt_variation, "i", $product_id);
        if (!mysqli_stmt_execute($stmt_variation)) throw new Exception("Error deleting variation: " . mysqli_stmt_error($stmt_variation));
        mysqli_stmt_close($stmt_variation);
    }

    $sql_product = "DELETE FROM products WHERE product_id = ?";
    $stmt_product = mysqli_prepare($conn, $sql_product);

    if ($stmt_product) {
        mysqli_stmt_bind_param($stmt_product, "i", $product_id);
        if (!mysqli_stmt_execute($stmt_product)) throw new Exception("Error deleting product: " . mysqli_stmt_error($stmt_product));
        mysqli_stmt_close($stmt_product);
    } else {
        throw new Exception("Error preparing product deletion statement: " . mysqli_error($conn));
    }
    
    
    mysqli_commit($conn);

    $message_key = 'msg';
    $message_text = urlencode("ลบสินค้า ID: {$product_id} สำเร็จแล้ว");

} catch (Exception $e) {
   
    mysqli_rollback($conn);
    $is_success = false;
    $message_key = 'error';
    $message_text = urlencode("ไม่สามารถลบสินค้าได้: " . $e->getMessage());
}

mysqli_close($conn);


$separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
header("Location: {$redirect_url}{$separator}{$message_key}={$message_text}");
exit();
?>