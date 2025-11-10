<?php
session_start();
// 商品IDは GET パラメータ `id` で受け取る想定です。
// DB接続情報は環境に合わせて修正してください（以下は仮の値）。
$dbHost = '127.0.0.1';
$dbName = 'fitfuel';
$dbUser = 'root';
$dbPass = '';

$product = null;
$error = null;
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error = '商品が指定されていません。';
} else {
    $productId = (int)$_GET['id'];
    try {
        $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $stmt = $pdo->prepare('SELECT product_id, product_name, product_price, product_image, product_nuts_image, product_stock FROM product WHERE product_id = :id AND product_status = 1 LIMIT 1');
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            $error = '指定された商品は存在しないか販売停止中です。';
        }
    } catch (PDOException $e) {
        $error = 'データベース接続に失敗しました。設定を確認してください。';
    }
}
// 画像はリポジトリ内の refpic フォルダに置く前提。product テーブルのフィールド値がファイル名であれば
// ../refpic/ をプレフィックスとして参照します。
function asset_path($filename) {
    if (!$filename) return '';
    // 既にパスが入っている場合はそのまま返す
    if (strpos($filename, '/') !== false) return $filename;
    return '../refpic/' . $filename;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G02_商品詳細.css" rel="stylesheet" type="text/css">
    <title>商品詳細</title>
</head>
<body>
    <main class="product-page container">
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error, ENT_QUOTES | ENT_HTML5) ?></div>
        <?php else: ?>
            <div class="cards">
                <div class="card image-card">
                    <?php $img = asset_path($product['product_image']); ?>
                    <?php if ($img): ?>
                        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-img">
                    <?php else: ?>
                        <div class="no-image">画像がありません</div>
                    <?php endif; ?>
                </div>

                <div class="card info-card">
                    <h1 class="product-title"><?= htmlspecialchars($product['product_name']) ?>　<span class="size">1kg</span></h1>

                    <div class="price-box">
                        <span class="price">¥<?= number_format($product['product_price']) ?></span>
                    </div>

                    <form action="G04_カート一覧.php" method="post" class="cart-form">
                        <label class="qty-label">数量：
                            <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['product_stock'] ?>">
                        </label>
                        <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                        <button type="submit" class="add-cart">カートに入れる 🛒</button>
                    </form>
                </div>
            </div>

            <section class="nutrition">
                <?php $nuts = asset_path($product['product_nuts_image']); ?>
                <?php if ($nuts): ?>
                    <img src="<?= htmlspecialchars($nuts) ?>" alt="栄養成分表" class="nuts-img">
                <?php else: ?>
                    <div class="no-image">栄養成分表の画像が設定されていません。</div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>