<?php
include '../../../../Users/Administrator/Documents/connectdb.php';


if(isset($_POST['new_category'])){
    $cat = mysqli_real_escape_string($conn, $_POST['new_category']);
    mysqli_query($conn, "INSERT INTO categories(categories_name) VALUES('$cat')");
}


if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM categories WHERE categories_id=$id");
}


if(isset($_POST['edit_id'])){
    $id = intval($_POST['edit_id']);
    $name = mysqli_real_escape_string($conn, $_POST['edit_name']);
    mysqli_query($conn, "UPDATE categories SET categories_name='$name' WHERE categories_id=$id");
}


$res = mysqli_query($conn, "SELECT * FROM `categories` ORDER BY categories_name ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการประเภทสินค้า</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 font-sans">
<h1 class="text-2xl font-bold mb-4">จัดการประเภทสินค้า</h1>

<form method="POST" class="mb-4">
  <input type="text" name="new_category" placeholder="เพิ่มประเภทใหม่" class="border p-2">
  <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded">เพิ่ม</button>
</form>

<table class="border w-full">
  <tr class="bg-gray-200">
    <th class="p-2">ID</th>
    <th class="p-2">ชื่อประเภท</th>
    <th class="p-2">การจัดการ</th>
  </tr>
  <?php while($row=mysqli_fetch_assoc($res)): ?>
  <tr>
    <td class="border p-2"><?=$row['categories_id']?></td>
    <td class="border p-2"><?=$row['categories_name']?></td>
    <td class="border p-2">
      <form method="POST" class="inline">
        <input type="hidden" name="edit_id" value="<?=$row['categories_id']?>">
        <input type="text" name="edit_name" value="<?=$row['categories_name']?>" class="border p-1">
        <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">แก้ไข</button>
      </form>
      <a href="" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('ลบ?')">ลบ</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
</body>
</html>