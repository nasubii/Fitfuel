<?php
// データベース接続設定

$host = 'mysql326.phy.lolipop.lan';
$dbname = 'LAA1607731-fitfuel';
$username = 'LAA1607731';
$password = '6cK37BWhPZYcXkm';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("データベース接続エラーが発生しました。");
}
