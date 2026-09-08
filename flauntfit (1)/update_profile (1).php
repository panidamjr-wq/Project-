<?php
require 'connectdb.php';
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$id = $_POST['id'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$address = trim($_POST['address']);
$password = $_POST['password'];

$params = [':name'=>$name,':email'=>$email,':address'=>$address,':id'=>$id];
$sql = "UPDATE users SET name=:name,email=:email,address=:address";
if (!empty($password)) {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $sql .= ", password=:password";
  $params[':password'] = $hash;
}
$sql .= " WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$_SESSION['user']['name'] = $name;
header("Location: profile.php?msg=updated");
exit;
