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
$mail = trim((string)($_POST['mail'] ?? ''));
$password = $_POST['password'] ?? '';

// 入力チェック
if (empty($mail) || empty($password)) {
    $_SESSION['login_error'] = 'メールアドレスとパスワードを入力してください。';
    header('Location: G08_ログイン.php');
    exit();
}

// メールアドレス形式の簡易検証
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = '有効なメールアドレスを入力してください。';
    header('Location: G08_ログイン.php');
    exit();
}

// ユーザー取得（テーブル名：仮に `users` とします）
$sql = 'SELECT * FROM users WHERE mail = :mail LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':mail', $mail, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch();

// ログイン判定
if ($user && password_verify($password, $user['password'])) {
    // パスワードが一致したらログイン成功
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_mail'] = $user['mail'];
    header('Location: G06-2_ログイン中.php'); // ログイン後のページへ
    exit();
} else {
    // ログイン失敗
    $_SESSION['login_error'] = 'メールアドレスまたはパスワードが正しくありません。';
    header('Location: G08_ログイン.php');
    exit();
}
