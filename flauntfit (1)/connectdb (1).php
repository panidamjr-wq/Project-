<?php
// connectdb.php
$DB_HOST = 'localhost';   // หรือ 127.0.0.1
$DB_NAME = 'flauntfit';   // ใช้ชื่อนี้เลย
$DB_USER = 'root';        // หรือชื่อผู้ใช้ MySQL ของคุณ
$DB_PASS = '';            // รหัสผ่าน MySQL ของคุณ (ถ้ามีใส่ให้ถูกต้อง)

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
