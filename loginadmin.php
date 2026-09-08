<?php
session_start();

$users = [
    'bowwie' => ['name' => 'Bowwie', 'password' => '66010916016', 'avatar' => '1.jpg'],
    'opal'   => ['name' => 'Opal', 'password' => '66010916018', 'avatar' => '2.jpg'],
    'torfun' => ['name' => 'torfun', 'password' => '66010916036', 'avatar' => '3.jpg'],
    'baimon' => ['name' => 'Baimon', 'password' => '66010916040', 'avatar' => '4.jpg'],
    'premmy' => ['name' => 'Premmy', 'password' => '66010916052', 'avatar' => '5.jpg'],
];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_user = $_POST['selected_user'] ?? '';
    $password = $_POST['password'] ?? '';
    if (isset($users[$selected_user]) && $users[$selected_user]['password'] === $password) {
        $_SESSION['user'] = $users[$selected_user]['name'];
        header("Location: backend_dashboard.php");
        exit();
    } else {
        $error = 'รหัสผ่านไม่ถูกต้อง หรือไม่มีผู้ใช้!';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เลือกผู้ใช้ | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #181818; color: #fff; font-family: 'Kanit', sans-serif; margin:0; }
        h1 { text-align:center; margin-top:2rem; font-size:2rem; font-weight:700; }
        .user-select { display:flex; justify-content:center; gap:2rem; margin-top:2.5rem; }
        .user-box { text-align:center; cursor:pointer; transition:transform 0.15s; }
        .user-box.selected { outline:3px solid #4a90e2; border-radius:14px; transform:scale(1.06);}
        .avatar { width:100px; height:100px; border-radius:14px; margin-bottom:0.6em; object-fit:cover; background:#222;}
        .user-name { font-size:1.07rem; font-weight:500; }
        .login-form { max-width:340px; margin:2.5rem auto 0 auto; padding:2rem 2rem 1.5rem 2rem; background:#222; border-radius:1.2rem; }
        .login-form label { font-size:1rem; margin-bottom:0.5rem; display:block; color:#fff;}
        .login-form input[type="password"] { width:100%; padding:0.7em; border-radius:9px; border:1px solid #333; margin-bottom:1.2em; background:#181818; color:#fff;}
        .login-form button { width:100%; padding:0.8em; border-radius:999px; border:none; background:#4a90e2; color:#fff; font-size:1.1rem; font-weight:700; cursor:pointer;}
        .login-form button:hover { background:#357bd8; }
        .error { color:#ff7675; text-align:center; margin-bottom:1em; }
    </style>
    <script>
        function selectUser(key) {
            document.querySelectorAll('.user-box').forEach(el=>el.classList.remove('selected'));
            document.getElementById('user-'+key).classList.add('selected');
            document.getElementById('selected_user').value = key;
        }
        window.onload = function() {
            <?php if (isset($_POST['selected_user'])): ?>
            selectUser("<?= addslashes($_POST['selected_user']) ?>");
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <h1>เลือกผู้ใช้</h1>
    <form method="POST" autocomplete="off">
        <div class="user-select">
        <?php foreach ($users as $key => $user): ?>
            <div class="user-box" id="user-<?= htmlspecialchars($key) ?>" onclick="selectUser('<?= htmlspecialchars($key) ?>')">
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($user['name']) ?>" class="avatar">
                <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
            </div>
        <?php endforeach; ?>
        </div>
        <input type="hidden" name="selected_user" id="selected_user" required>
        <div class="login-form">
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <label for="password">กรอกรหัสผ่าน</label>
            <input type="password" name="password" id="password" required autocomplete="off">
            <button type="submit">เข้าสู่ระบบ</button>
        </div>
    </form>
</body>
</html>