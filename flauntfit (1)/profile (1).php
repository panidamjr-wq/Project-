<?php
require 'connectdb.php';
require 'header.php';
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$stmt = $pdo->prepare("SELECT id,name,email,address FROM users WHERE id=:id");
$stmt->execute([':id'=>$_SESSION['user']['id']]);
$user = $stmt->fetch();
?>
<h2>โปรไฟล์</h2>
<form method="post" action="update_profile.php">
  <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
  <div class="form-row"><input name="name" value="<?php echo htmlspecialchars($user['name']); ?>"></div>
  <div class="form-row"><input name="email" value="<?php echo htmlspecialchars($user['email']); ?>" type="email" required></div>
  <div class="form-row"><textarea name="address"><?php echo htmlspecialchars($user['address']); ?></textarea></div>
  <div class="form-row"><input name="password" placeholder="เปลี่ยนรหัส (เว้นว่างถ้าไม่เปลี่ยน)" type="password"></div>
  <button class="btn" type="submit">บันทึก</button>
</form>
<?php require 'footer.php'; ?>
