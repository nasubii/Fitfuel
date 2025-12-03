<?php
session_start();

require_once 'config/database.php';
require_once __DIR__ . '/helpers/cart.php';

if (!isset($_SESSION['customer'])) {
    $_SESSION['login_error'] = '購入手続きにはログインが必要です。';
    header('Location: G08_ログイン.php');
    exit();
}

$userId = $_SESSION['customer']['user_id'];
$sessionCart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartData = getCartDetails($pdo, $sessionCart);
$cartItems = $cartData['items'];

// 既存の配送情報を取得してフォーム初期値に反映
$formData = [
    'methods_name' => '',
    'methods_postal_code' => '',
    'methods_address' => '',
    'methods_phone' => '',
    'card_number' => '',
    'card_holder' => '',
    'card_exp_month' => '',
    'card_exp_year' => '',
    'card_cvv' => '',
];

try {
    $stmt = $pdo->prepare('SELECT methods_name, methods_postal_code, methods_address, methods_phone, methods_payment FROM methods WHERE user_id = :user_id');
    $stmt->execute([':user_id' => $userId]);
    $existingInfo = $stmt->fetch();
    if ($existingInfo) {
        $formData = array_merge($formData, array_intersect_key($existingInfo, $formData));
    }
} catch (PDOException $e) {
    error_log('Failed to fetch methods info: ' . $e->getMessage());
}

$errors = [];

if (!empty($_SESSION['checkout_form'])) {
    $formData = array_merge($formData, $_SESSION['checkout_form']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['methods_name'] = trim($_POST['methods_name'] ?? '');
    $formData['methods_postal_code'] = trim($_POST['methods_postal_code'] ?? '');
    $formData['methods_address'] = trim($_POST['methods_address'] ?? '');
    $formData['methods_phone'] = trim($_POST['methods_phone'] ?? '');
    $formData['card_number'] = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
    $formData['card_holder'] = trim($_POST['card_holder'] ?? '');
    $formData['card_exp_month'] = trim($_POST['card_exp_month'] ?? '');
    $formData['card_exp_year'] = trim($_POST['card_exp_year'] ?? '');
    $formData['card_cvv'] = trim($_POST['card_cvv'] ?? '');

    if ($formData['methods_name'] === '') {
        $errors[] = 'お名前を入力してください。';
    }

    if ($formData['methods_postal_code'] === '') {
        $errors[] = '郵便番号を入力してください。';
    } elseif (!preg_match('/^\d{3}-?\d{4}$/', $formData['methods_postal_code'])) {
        $errors[] = '郵便番号は「123-4567」の形式で入力してください。';
    }

    if ($formData['methods_address'] === '') {
        $errors[] = '住所を入力してください。';
    }

    if ($formData['methods_phone'] !== '' && !preg_match('/^0\d{1,4}-?\d{1,4}-?\d{3,4}$/', $formData['methods_phone'])) {
        $errors[] = '電話番号は半角数字とハイフンで入力してください。';
    }

    if (!preg_match('/^\d{13,16}$/', $formData['card_number'])) {
        $errors[] = 'カード番号は半角数字13〜16桁で入力してください。';
    }

    if ($formData['card_holder'] === '') {
        $errors[] = 'カード名義人を入力してください。';
    }

    if (!preg_match('/^(0?[1-9]|1[0-2])$/', $formData['card_exp_month'])) {
        $errors[] = '有効期限(月)は1〜12の半角数字で入力してください。';
    }

    if (!preg_match('/^\d{2}$/', $formData['card_exp_year'])) {
        $errors[] = '有効期限(年)は下2桁の半角数字で入力してください。';
    }

    if (!preg_match('/^\d{3,4}$/', $formData['card_cvv'])) {
        $errors[] = 'セキュリティコードは3〜4桁の半角数字で入力してください。';
    }

    if (empty($cartItems)) {
        $errors[] = 'カートに商品がありません。';
    }

    if (empty($errors)) {
        $_SESSION['checkout_form'] = $formData;
        header('Location: G05_購入確認.php');
        exit();
    }
}

$cartSubtotal = $cartData['total'];
$shippingFee = $cartSubtotal >= 10000 ? 0 : 600;
$cartTotal = $cartSubtotal + ($cartSubtotal > 0 ? $shippingFee : 0);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G05_購入.css" rel="stylesheet" type="text/css">
    <title>購入手続き | FitFuel</title>
</head>
<body>
    <?php require 'G00_ヘッダー.php'; ?>

    <main class="checkout-wrapper">
        <div class="checkout-title">
            <h1>購入手続き</h1>
            <p>お届け先情報を入力し、注文内容をご確認ください。</p>
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
                <h2>配送先情報</h2>
                <form method="post" class="buyer-form">
                    <div class="form-group">
                        <label for="methods_name">お名前<span class="required">必須</span></label>
                        <input type="text" id="methods_name" name="methods_name" value="<?= htmlspecialchars($formData['methods_name'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="例）山田 太郎" required>
                    </div>

                    <div class="form-inline">
                        <div class="form-group">
                            <label for="methods_postal_code">郵便番号<span class="required">必須</span></label>
                            <input type="text" id="methods_postal_code" name="methods_postal_code" value="<?= htmlspecialchars($formData['methods_postal_code'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="123-4567" maxlength="8" required>
                        </div>
                        <div class="form-group">
                            <label for="methods_phone">電話番号</label>
                            <input type="text" id="methods_phone" name="methods_phone" value="<?= htmlspecialchars($formData['methods_phone'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="090-1234-5678" maxlength="15">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="methods_address">住所<span class="required">必須</span></label>
                        <textarea id="methods_address" name="methods_address" rows="3" placeholder="例）東京都渋谷区○○ 1-2-3" required><?= htmlspecialchars($formData['methods_address'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>お支払い方法<span class="required">必須</span></label>
                        <p class="payment-label">クレジットカード</p>
                    </div>

                    <div class="card-wrapper">
                        <h3>カード情報</h3>
                        <div class="form-group">
                            <label for="card_number">カード番号<span class="required">必須</span></label>
                            <input type="text" id="card_number" name="card_number" inputmode="numeric" placeholder="1234 5678 9012 3456" minlength="16" maxlength="16" value="<?= htmlspecialchars($formData['card_number'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="form-inline">
                            <div class="form-group">
                                <label for="card_exp_month">有効期限(月)<span class="required">必須</span></label>
                                <input type="text" id="card_exp_month" name="card_exp_month" placeholder="MM" maxlength="2" value="<?= htmlspecialchars($formData['card_exp_month'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="card_exp_year">有効期限(年)<span class="required">必須</span></label>
                                <input type="text" id="card_exp_year" name="card_exp_year" placeholder="YY" maxlength="2" value="<?= htmlspecialchars($formData['card_exp_year'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="card_cvv">セキュリティコード<span class="required">必須</span></label>
                                <input type="text" id="card_cvv" name="card_cvv" placeholder="123" maxlength="3" value="<?= htmlspecialchars($formData['card_cvv'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="card_holder">カード名義人<span class="required">必須</span></label>
                            <input type="text" id="card_holder" name="card_holder" placeholder="TARO YAMADA" value="<?= htmlspecialchars($formData['card_holder'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                    </div>

                    <button type="submit" class="primary-btn">確認画面へ進む</button>
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