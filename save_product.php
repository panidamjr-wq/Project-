<?php
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
    if (!in_array($ext, $allowed)) return false;
    return move_uploaded_file($file['tmp_name'], $target_file) ? $target_file : false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = mysqli_real_escape_string($conn, $_POST['productName']);
    $price = (float)$_POST['productPrice'];
    $cat = mysqli_real_escape_string($conn, $_POST['productCategory']);
    $desc = mysqli_real_escape_string($conn, $_POST['productDescription']);
    $extra = mysqli_real_escape_string($conn, $_POST['additionalDetails']);

    $main_image_url = uploadImage($_FILES['mainImage']);

    $sql_product = "INSERT INTO `products` (product_name, price, category, description, image_url, additional_details) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_product = mysqli_prepare($conn, $sql_product);
    mysqli_stmt_bind_param($stmt_product, "sdssss", $name, $price, $cat, $desc, $main_image_url, $extra);
    
    if (mysqli_stmt_execute($stmt_product)) {
        $product_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt_product);

        if (isset($_POST['colors']) && is_array($_POST['colors'])) {
            foreach ($_POST['colors'] as $index => $color) {
                $color_name = mysqli_real_escape_string($conn, $color);
                $variation_image_url = null;

                if (isset($_FILES['variation_images']['tmp_name'][$index])) {
                    $variation_image_url = uploadImage([
                        'name' => $_FILES['variation_images']['name'][$index],
                        'type' => $_FILES['variation_images']['type'][$index],
                        'tmp_name' => $_FILES['variation_images']['tmp_name'][$index],
                        'error' => $_FILES['variation_images']['error'][$index],
                        'size' => $_FILES['variation_images']['size'][$index],
                    ]);
                }

                $sql_variation = "INSERT INTO `product_variations` (product_id, color_name, image_url) VALUES (?, ?, ?)";
                $stmt_variation = mysqli_prepare($conn, $sql_variation);
                mysqli_stmt_bind_param($stmt_variation, "iss", $product_id, $color_name, $variation_image_url);
                mysqli_stmt_execute($stmt_variation);
                $variation_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt_variation);
                
                
                if (isset($_POST['sizes']) && is_array($_POST['sizes'])) {
                    
                    $timestamp_key = array_keys($_POST['sizes'])[$index];

                    if (isset($_POST['sizes'][$timestamp_key])) {
                        $sql_stock = "INSERT INTO `product_stocks` (variation_id, size_name, stock_quantity) VALUES (?, ?, ?)";
                        $stmt_stock = mysqli_prepare($conn, $sql_stock);
                        
                        foreach ($_POST['sizes'][$timestamp_key] as $size) {
                            $size_name = mysqli_real_escape_string($conn, $size);
                            $stock_quantity = intval($_POST['stocks'][$timestamp_key][$size_name] ?? 0);
                            
                           
                            mysqli_stmt_bind_param($stmt_stock, "isi", $variation_id, $size_name, $stock_quantity);
                            mysqli_stmt_execute($stmt_stock);
                        }
                        mysqli_stmt_close($stmt_stock);
                    }
                }
            }
        }
        
        header("Location: product_add.php?success=1");
        exit();

    } else {
        echo "Error saving main data: " . mysqli_error($conn);
    }
}
?>
