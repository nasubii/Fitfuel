<?php
session_start();

// すでにログイン済みならログイン中ページへリダイレクト
if (isset($_SESSION['customer'])) {
    header('Location: G06-2_ログイン中.php');
    exit();
}
?>

<?php require 'G00_ヘッダー.php'; ?>

<link rel="stylesheet" href="css/G06-1_ユーザー管理.css">

<main class="account-gate">
    <div class="gate-card">
        <a class="gate-btn gate-btn--login" href="G08_ログイン.php">ログイン</a>
        <a class="gate-btn gate-btn--register" href="G07_ユーザー登録.php">登録する</a>
    </div>
</main>

<?php require 'G00_フッター.php'; ?>