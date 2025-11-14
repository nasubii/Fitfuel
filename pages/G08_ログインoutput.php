<?php
session_start();

// DB接続定数
const SERVER = 'mysql326.phy.lolipop.lan';
const DBNAME = 'LAA1607731-fitfuel';
const USER = 'LAA1607731';
const PASS = '6cK37BWhPZYcXkm';

// DSN と PDO オプション
$dsn = 'mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8';
$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, USER, PASS, $pdoOptions);
} catch (PDOException $e) {
    // 実運用では詳細を画面に出力しない（ログに記録する）
    exit('データベース接続失敗。');
}

// フォームから送信されたデータ（基本的な整形・検証を追加）
$userId = trim((string)($_POST['user_id'] ?? ''));
$password = $_POST['password'] ?? '';

// 入力チェック
if ($userId === '' || $password === '') {
    $_SESSION['login_error'] = 'ログインIDとパスワードを入力してください。';
    header('Location: G08_ログイン.php');
    exit();
}

// ユーザー取得（テーブル `user` のログインIDを検索）
$sql = 'SELECT user_id, user_email, user_password FROM `user` WHERE user_id = :user_id LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch();

// ログイン判定
if ($user && password_verify($password, $user['user_password'])) {
    // パスワードが一致したらログイン成功
    session_regenerate_id(true);
    // セッション名を `customer` に統一（id, mail, name を格納）
    $_SESSION['customer'] = [
        'user_id' => $user['user_id'],
        'user_email' => $user['user_email'],
    ];
    header('Location: G06-2_ログイン中.php'); // ログイン後のページへ
    exit();
} else {
    // ログイン失敗
    $_SESSION['login_error'] = 'ログインIDまたはパスワードが正しくありません。';
    header('Location: G08_ログイン.php');
    exit();
}
