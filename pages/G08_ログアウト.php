<?php
session_start();
// ログイン状態とカート情報をクリア
unset($_SESSION['customer'], $_SESSION['cart']);
// セッション固定化対策としてIDを再生成
session_regenerate_id(true);
header('Location: G08_ログイン.php');
exit();
