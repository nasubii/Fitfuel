<?php
session_start();

require_once '../config/database.php';
require_once __DIR__ . '/helpers/cart.php';

if (!isset($_SESSION['customer'])) {
    $_SESSION['login_error'] = '購入手続きにはログインが必要です。';
    header('Location: G08_ログイン.php');
    exit();
}

if (empty($_SESSION['checkout_form'])) {
    header('Location: G05_購入.php');
    exit();
}

$userId = $_SESSION['customer']['user_id'];
$formData = $_SESSION['checkout_form'];
$sessionCart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartData = getCartDetails($pdo, $sessionCart);
$cartItems = $cartData['items'];

$cartSubtotal = $cartData['total'];
$shippingFee = $cartSubtotal >= 10000 ? 0 : 600;
$cartTotal = $cartSubtotal + ($cartSubtotal > 0 ? $shippingFee : 0);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'complete') {
    if (empty($cartItems)) {
        $errors[] = 'カートに商品がありません。';
    }

    if (empty($errors)) {
        foreach ($cartItems as $item) {
            $available = isset($item['product_stock']) ? (int)$item['product_stock'] : null;
            $qty = max(1, (int)$item['quantity']);
            if ($available !== null && $available < $qty) {
                $errors[] = sprintf('%sの在庫が足りません。(在庫: %d)', $item['product_name'], $available);
            }
        }
    }

    if (empty($errors)) {
        $orderSummary = [
            'name' => $formData['methods_name'],
            'total' => $cartTotal,
            'count' => array_sum(array_map(static fn($item) => (int)$item['quantity'], $cartItems)),
        ];

        try {
            $pdo->beginTransaction();

            $sql = 'INSERT INTO methods (user_id, methods_name, methods_address, methods_postal_code, methods_payment, methods_phone)
                    VALUES (:user_id, :methods_name, :methods_address, :methods_postal_code, :methods_payment, :methods_phone)
                    ON DUPLICATE KEY UPDATE
                        methods_name = VALUES(methods_name),
                        methods_address = VALUES(methods_address),
                        methods_postal_code = VALUES(methods_postal_code),
                        methods_payment = VALUES(methods_payment),
                        methods_phone = VALUES(methods_phone)';

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':methods_name' => $formData['methods_name'],
                ':methods_address' => $formData['methods_address'],
                ':methods_postal_code' => $formData['methods_postal_code'],
                ':methods_payment' => 'クレジットカード',
                ':methods_phone' => $formData['methods_phone'] === '' ? null : $formData['methods_phone'],
            ]);

            $updateStockStmt = $pdo->prepare('UPDATE product SET product_stock = product_stock - :qty_subtract WHERE product_id = :pid AND product_stock >= :qty_required');
            $insertSalesStmt = $pdo->prepare('INSERT INTO sales (product_id, user_id, sales_sold, sold_at) VALUES (:pid, :uid, :qty, NOW())');

            foreach ($cartItems as $item) {
                $pid = (int)$item['product_id'];
                $qty = max(1, (int)$item['quantity']);

                $updateStockStmt->execute([
                    ':qty_subtract' => $qty,
                    ':qty_required' => $qty,
                    ':pid' => $pid,
                ]);

                if ($updateStockStmt->rowCount() === 0) {
                    throw new RuntimeException('在庫が不足しています: ' . $item['product_name']);
                }

                $insertSalesStmt->execute([
                    ':pid' => $pid,
                    ':uid' => $userId,
                    ':qty' => $qty,
                ]);
            }

            $pdo->commit();

            unset($_SESSION['checkout_form']);
            unset($_SESSION['cart']);
            $_SESSION['checkout_complete'] = $orderSummary;

            header('Location: G05_購入完了.php');
            exit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Checkout error: ' . $e->getMessage());
            $errors[] = (strpos($e->getMessage(), '在庫') !== false)
                ? $e->getMessage()
                : '注文の登録に失敗しました。時間をおいて再度お試しください。';
        }
    }
}

$maskedCard = str_repeat('*', max(0, strlen($formData['card_number']) - 4)) . substr($formData['card_number'], -4);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G05_購入.css" rel="stylesheet" type="text/css">
    <title>購入内容確認 | FitFuel</title>
</head>
<body>
<?php require 'G00_ヘッダー.php'; ?>

<main class="checkout-wrapper">
    <div class="checkout-title">
        <h1>購入内容の確認</h1>
        <p>入力内容と注文内容をご確認のうえ「注文を確定する」を押してください。</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>


    <div class="checkout-grid">
        <section class="buyer-info">
            <h2>配送先・お支払い情報</h2>
            <form method="post" class="buyer-form confirmation-form">
                <div class="confirm-block">
                    <h3>お届け先</h3>
                    <dl class="confirm-list">
                        <div class="confirm-row">
                            <dt>お名前</dt>
                            <dd><?= htmlspecialchars($formData['methods_name'], ENT_QUOTES, 'UTF-8'); ?></dd>
                        </div>
                        <div class="confirm-row">
                            <dt>郵便番号</dt>
                            <dd><?= htmlspecialchars($formData['methods_postal_code'], ENT_QUOTES, 'UTF-8'); ?></dd>
                        </div>
                        <div class="confirm-row">
                            <dt>電話番号</dt>
                            <dd><?= htmlspecialchars($formData['methods_phone'], ENT_QUOTES, 'UTF-8'); ?></dd>
                        </div>
                        <div class="confirm-row">
                            <dt>住所</dt>
                            <dd class="multiline"><?= nl2br(htmlspecialchars($formData['methods_address'], ENT_QUOTES, 'UTF-8')); ?></dd>
                        </div>
                    </dl>
                </div>

                <div class="confirm-block">
                    <h3>お支払い</h3>
                    <dl class="confirm-list">
                        <div class="confirm-row">
                            <dt>お支払い方法</dt>
                            <dd><span class="payment-label">クレジットカード</span></dd>
                        </div>
                    </dl>
                </div>

                <div class="confirm-block">
                    <h3>カード情報</h3>
                    <dl class="confirm-list">
                        <div class="confirm-row">
                            <dt>カード番号</dt>
                            <dd><?= htmlspecialchars($maskedCard, ENT_QUOTES, 'UTF-8'); ?></dd>
                        </div>
                        <div class="confirm-row">
                            <dt>有効期限</dt>
                            <dd><?= htmlspecialchars($formData['card_exp_month'], ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars($formData['card_exp_year'], ENT_QUOTES, 'UTF-8'); ?></dd>
                        </div>
                        <div class="confirm-row">
                            <dt>セキュリティコード</dt>
                            <dd>***</dd>
                        </div>
                        <div class="confirm-row">
                            <dt>名義人</dt>
                            <dd><?= htmlspecialchars($formData['card_holder'], ENT_QUOTES, 'UTF-8'); ?></dd>
                        </div>
                    </dl>
                </div>

                <div class="confirm-buttons">
                    <a class="secondary-btn" href="G05_購入.php">入力画面に戻る</a>
                    <button type="submit" name="action" value="complete" class="primary-btn">注文を確定する</button>
                </div>
            </form>
        </section>

        <aside class="order-summary">
            <h2>購入内容</h2>
            <?php if (empty($cartItems)): ?>
                <p class="empty-cart">カートに商品がありません。</p>
            <?php else: ?>
                <ul class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="cart-item">
                            <div class="item-info">
                                <p class="item-name"><?= htmlspecialchars($item['product_name'] ?? '商品名未設定', ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="item-meta">数量: <?= (int)$item['quantity']; ?> / ¥<?= number_format((int)$item['product_price']); ?></p>
                            </div>
                            <p class="item-subtotal">¥<?= number_format((int)$item['subtotal']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="price-breakdown">
                    <div class="price-row">
                        <span>商品合計</span>
                        <span>¥<?= number_format($cartSubtotal); ?></span>
                    </div>
                    <div class="price-row">
                        <span>送料</span>
                        <span><?= $shippingFee === 0 ? '無料' : '¥' . number_format($shippingFee); ?></span>
                    </div>
                    <div class="price-total">
                        <span>お支払い総額</span>
                        <span>¥<?= number_format($cartTotal); ?></span>
                    </div>
                </div>
                <p class="note">※10,000円以上のご購入で送料が無料になります。</p>
            <?php endif; ?>
        </aside>
    </div>
</main>

<?php require 'G00_フッター.php'; ?>
</body>
</html>
