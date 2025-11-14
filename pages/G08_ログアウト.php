<?php
session_start();
// セッション内の customer 情報を削除してログイン画面へリダイレクト
unset($_SESSION['customer']);
// セッション固定化対策としてIDを再生成
session_regenerate_id(true);
header('Location: G08_ログイン.php');
exit();
