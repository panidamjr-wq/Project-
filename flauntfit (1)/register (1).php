<?php
require 'connectdb.php';
session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $address = trim($_POST['address']);

  if (!$name || !$email || !$password) $errors[] = "กรอกข้อมูลให้ครบถ้วน";
  // ตรวจสอบ email ซ้ำ
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
  $stmt->execute([':email'=>$email]);
  if ($stmt->fetch()) $errors[] = "อีเมลนี้ลงทะเบียนแล้ว";

  if (empty($errors)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name,email,password,address,created_at) VALUES (:n,:e,:p,:a,NOW())");
    $stmt->execute([':n'=>$name,':e'=>$email,':p'=>$hash,':a'=>$address]);
    $_SESSION['user'] = ['id' => $pdo->lastInsertId(), 'name' => $name, 'email' => $email];
    header("Location: index.php");
    exit;
  }
}
require 'header.php';
?>
<h2>สมัครสมาชิก</h2>
<?php if($errors): foreach($errors as $err) echo "<p style='color:red;'>".htmlspecialchars($err)."</p>"; endforeach; ?>
<form method="post" action="register.php">
  <div class="form-row"><input name="name" placeholder="ชื่อ" required></div>
  <div class="form-row"><input name="email" type="email" placeholder="อีเมล" required></div>
  <div class="form-row"><input name="password" type="password" placeholder="รหัสผ่าน" required></div>
  <div class="form-row"><textarea name="address" placeholder="ที่อยู่จัดส่ง"></textarea></div>
  <button class="btn" type="submit">สมัคร</button>
</form>
<?php require 'footer.php'; ?>
