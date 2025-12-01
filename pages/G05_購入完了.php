<?php
session_start();

if (!isset($_SESSION['customer'])) {
    header('Location: G08_ログイン.php');
    exit();
}

$orderSummary = $_SESSION['checkout_complete'] ?? null;

if ($orderSummary === null) {
    header('Location: G04_カート一覧.php');
    exit();
}

unset($_SESSION['checkout_complete']);

$orderName = $orderSummary['name'] ?? ($_SESSION['customer']['user_name'] ?? '');
$orderTotal = isset($orderSummary['total']) ? (int)$orderSummary['total'] : 0;
$orderCount = max(0, (int)($orderSummary['count'] ?? 0));
$estimatedArrival = date('n月j日', strtotime('+3 day'));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G05_購入完了.css" rel="stylesheet" type="text/css">
    <title>ご注文完了 | FitFuel</title>
</head>
<body>
<?php require 'G00_ヘッダー.php'; ?>

<main class="complete-wrapper">
    <section class="complete-card">
        <div class="complete-icon">✔</div>
        <h1>ご注文ありがとうございます！</h1>
        <p class="lead">商品の発送準備が整い次第、メールにてご連絡いたします。</p>

        <ul class="order-stats">
            <li>
                <span class="label">ご注文者</span>
                <span class="value"><?= htmlspecialchars($orderName, ENT_QUOTES, 'UTF-8'); ?></span>
            </li>
            <li>
                <span class="label">ご注文数</span>
                <span class="value"><?= number_format($orderCount); ?>点</span>
            </li>
            <li>
                <span class="label">お支払い金額</span>
                <span class="value">¥<?= number_format($orderTotal); ?></span>
            </li>
            <li>
                <span class="label">お届け予定</span>
                <span class="value">最短 <?= $estimatedArrival; ?> ごろ</span>
            </li>
        </ul>

        <div class="next-actions">
            <a href="index.php" class="primary-btn">ショッピングを続ける</a>
            <a href="G04_カート一覧.php" class="secondary-btn">カートに戻る</a>
        </div>
        <p class="note">※メールが届かない場合は迷惑メールフォルダもご確認ください。</p>
    </section>
</main>

<?php require 'G00_フッター.php'; ?>
</body>
</html>
