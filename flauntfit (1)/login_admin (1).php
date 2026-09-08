<?php
require '../connectdb.php';
session_start();
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $user = $_POST['username']; $pass = $_POST['password'];
  $stmt = $pdo->prepare("SELECT id,username,password_hash FROM admins WHERE username=:u");
  $stmt->execute([':u'=>$user]);
  $a = $stmt->fetch();
  if ($a && password_verify($pass, $a['password_hash'])) {
    $_SESSION['admin'] = ['id'=>$a['id'],'username'=>$a['username']];
    header("Location: dashboard.php");
    exit;
  } else $err = "ไม่ถูกต้อง";
}
?>
<!-- form login -->
