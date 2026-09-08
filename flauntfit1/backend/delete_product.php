<?php
// Include the database connection file
include 'connectdb.php';

// Check if a product ID is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];

    // SQL query to delete the product
    $sql = "DELETE FROM `products` WHERE product_id = ?";
    
    // Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to the product management page
        header("Location: product_management.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Invalid product ID.";
}

mysqli_close($conn);
?>
