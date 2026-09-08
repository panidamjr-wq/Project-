<?php
require 'connectdb.php';
$q = $_GET['q'] ?? '';
$stmt = $pdo->prepare("SELECT id,name,price,image FROM products WHERE name LIKE :q LIMIT 10");
$stmt->execute([':q'=>"%$q%"]);
echo json_encode($stmt->fetchAll());
