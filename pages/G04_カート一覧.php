<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/cart.php';

// フラッシュメッセージ取得
$flashMessage = $_SESSION['cart_flash'] ?? '';
unset($_SESSION['cart_flash']);

// POST処理: 追加・削除
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 商品詳細からの追加
    if (isset($_POST['product_id'], $_POST['quantity']) && !isset($_POST['action'])) {
        $pid = (int)$_POST['product_id'];
        $qty = max(1, (int)$_POST['quantity']);
        if ($pid > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + $qty;
            $_SESSION['cart_flash'] = '商品をカートに追加しました。';
            header('Location: G04_カート一覧.php');
            exit;
        }
    }

    $action = $_POST['action'] ?? '';
    if ($action === 'remove' && isset($_POST['remove_id'])) {
        $removeId = (int)$_POST['remove_id'];
        if (isset($_SESSION['cart'][$removeId])) {
            unset($_SESSION['cart'][$removeId]);
            $_SESSION['cart_flash'] = '商品をカートから削除しました。';
        }
        header('Location: G04_カート一覧.php');
        exit;
    }
}

$sessionCart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartData = getCartDetails($pdo, $sessionCart);
$cartItems = $cartData['items'];
$cartTotal = $cartData['total'];
$cartCount = array_sum($sessionCart);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G04_カート一覧.css" rel="stylesheet" type="text/css">
    <title>カート一覧 | FitFuel</title>
</head>
<body>
    <?php require 'G00_ヘッダー.php'; ?>

    <main class="cart-page">
        <section class="cart-card">
            <h1>カート内</h1>
            <p class="cart-count"><?= $cartCount ?>点</p>

            <?php if ($flashMessage !== ''): ?>
                <div class="cart-message"><?= htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <p class="empty-text">カートに商品がありません。</p>
                <a class="primary-btn" href="index.php">商品を探す</a>
            <?php else: ?>
                <ul class="cart-list">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="cart-item">
                            <div class="item-details">
                                <p class="product-name"><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="product-info">¥<?= number_format((int)$item['product_price']); ?></p>
                                <p class="product-qty">個数: <?= (int)$item['quantity']; ?></p>
                            </div>
                            <div class="item-actions">
                                <p class="item-subtotal">¥<?= number_format((int)$item['subtotal']); ?></p>
                                <form method="post" action="G04_カート一覧.php">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="remove_id" value="<?= (int)$item['product_id']; ?>">
                                    <button type="submit" class="remove-btn">削除</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="cart-total">
                    <span>合計：</span><span class="total-amount">¥<?= number_format($cartTotal); ?></span>
                </div>

                <a class="primary-btn" href="G05_購入.php">購入画面に進む</a>
            <?php endif; ?>
        </section>
    </main>

    <?php require 'G00_フッター.php'; ?>
</body>
</html>