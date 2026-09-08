<?php
require 'connectdb.php';
session_start();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $stmt = $pdo->prepare("SELECT id,name,password FROM users WHERE email = :e");
  $stmt->execute([':e'=>$email]);
  $u = $stmt->fetch();
  if ($u && password_verify($password, $u['password'])) {
    $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$email];
    header("Location: index.php");
    exit;
  } else $err = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
}
require 'header.php';
?>
<h2>เข้าสู่ระบบ</h2>
<?php if($err) echo "<p style='color:red;'>".htmlspecialchars($err)."</p>"; ?>
<form method="post" action="login.php">
  <div class="form-row"><input name="email" type="email" placeholder="อีเมล" required></div>
  <div class="form-row"><input name="password" type="password" placeholder="รหัสผ่าน" required></div>
  <button class="btn" type="submit">เข้าสู่ระบบ</button>
</form>
<?php require 'footer.php'; ?>
