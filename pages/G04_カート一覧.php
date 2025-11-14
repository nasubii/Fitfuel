<?php
session_start();

// DB 接続（config/database.php 経由）
require_once __DIR__ . '/../config/database.php';

$message = '';

// POST処理: 商品詳細ページからの追加、カート内更新、削除を処理する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 追加（商品詳細から来る想定）
    if (isset($_POST['product_id']) && isset($_POST['quantity']) && !isset($_POST['action'])) {
        $pid = (int)$_POST['product_id'];
        $qty = max(1, (int)$_POST['quantity']);
        if ($pid > 0) {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if (isset($_SESSION['cart'][$pid])) {
                $_SESSION['cart'][$pid] += $qty;
            } else {
                $_SESSION['cart'][$pid] = $qty;
            }
            // リダイレクトして二重送信を防ぐ
            header('Location: G04_カート一覧.php');
            exit;
        }
    }

    // カート操作（更新・削除）
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'update' && isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $pid => $q) {
                $pid = (int)$pid;
                $q = (int)$q;
                if ($pid <= 0) continue;
                if ($q > 0) {
                    $_SESSION['cart'][$pid] = $q;
                } else {
                    unset($_SESSION['cart'][$pid]);
                }
            }
            $message = 'カートを更新しました。';
        }

        if ($action === 'remove' && isset($_POST['remove_id'])) {
            $rid = (int)$_POST['remove_id'];
            if (isset($_SESSION['cart'][$rid])) unset($_SESSION['cart'][$rid]);
            $message = '商品をカートから削除しました。';
        }
    }
}

// 補助: 画像パス
function asset_path($filename) {
    if (!$filename) return '';
    if (strpos($filename, '/') !== false) return $filename;
    return '../refpic/' . $filename;
}

// カート内容取得
$cart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
$total = 0;

if (!empty($cart)) {
    try {
        // 商品IDリストを用意してプレースホルダを作る
        $ids = array_map('intval', array_keys($cart));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT product_id, product_name, product_price, product_image, product_stock, product_status FROM product WHERE product_id IN ($placeholders)");
        $stmt->execute($ids);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $pid = (int)$r['product_id'];
            $qty = isset($cart[$pid]) ? (int)$cart[$pid] : 0;
            $subtotal = ((int)$r['product_price']) * $qty;
            $products[$pid] = [
                'info' => $r,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }
    } catch (PDOException $e) {
        $message = 'データベースエラーが発生しました。設定を確認してください。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G04_カート一覧.css" rel="stylesheet" type="text/css">
    <title>カート一覧</title>
</head>
<body>
    <main class="cart-page container">
        <h1>カート一覧</h1>
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message, ENT_QUOTES | ENT_HTML5) ?></div>
        <?php endif; ?>

        <?php if (empty($cart) || empty($products)): ?>
            <p>カートに商品が入っていません。</p>
            <p><a href="../index.php">商品一覧に戻る</a></p>
        <?php else: ?>
            <form method="post" action="G04_カート一覧.php">
                <input type="hidden" name="action" value="update">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>商品</th>
                            <th>価格</th>
                            <th>数量</th>
                            <th>小計</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $pid => $p):
                            $info = $p['info'];
                            $qty = $p['qty'];
                            $subtotal = $p['subtotal'];
                            $img = asset_path($info['product_image']);
                            $available = ((int)$info['product_status'] === 1);
                        ?>
                        <tr>
                            <td class="product">
                                <?php if ($img): ?><img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($info['product_name']) ?>" class="thumb"><?php endif; ?>
                                <div class="meta">
                                    <div class="name"><?= htmlspecialchars($info['product_name']) ?></div>
                                    <?php if (!$available): ?><div class="note">※販売停止中</div><?php endif; ?>
                                </div>
                            </td>
                            <td>¥<?= number_format((int)$info['product_price']) ?></td>
                            <td>
                                <input type="number" name="quantities[<?= (int)$pid ?>]" value="<?= (int)$qty ?>" min="0" max="<?= (int)$info['product_stock'] ?>">
                            </td>
                            <td>¥<?= number_format($subtotal) ?></td>
                            <td>
                                <form method="post" action="G04_カート一覧.php" onsubmit="return confirm('本当に削除しますか？');">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="remove_id" value="<?= (int)$pid ?>">
                                    <button type="submit">削除</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-actions">
                    <button type="submit">カートを更新</button>
                    <a class="checkout" href="G05_購入.php">購入へ進む</a>
                </div>
            </form>

            <div class="summary">
                <div class="total">合計：<strong>¥<?= number_format($total) ?></strong></div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>