<?php
session_start();

// DB 接続（config/database.php 経由）
require_once __DIR__ . '/config/database.php';

// 商品ID を GET で取得。パラメータ名は id を想定（product_id でも可）
$id = null;
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
} elseif (!empty($_GET['product_id'])) {
    $id = $_GET['product_id'];
}

// 簡易検証（user 提供のスキーマでは product.product_id は数値）
if ($id === null || !ctype_digit((string)$id)) {
    $error = '不正な商品指定です。';
    $product = null;
} else {
        // DBから商品情報を取得
        $sql = 'SELECT p.product_id, p.product_name, p.product_price, p.product_image, p.product_image2, p.product_nuts_image, p.product_stock, p.product_status, c.category_name
                FROM `product` p
                LEFT JOIN `category` c ON p.category_id = c.category_id
                WHERE p.product_id = :id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch();
    if (!$product) {
        $error = '指定された商品は見つかりません。';
    } elseif ((int)$product['product_status'] !== 1) {
        $error = 'この商品は現在販売停止中です。';
    } else {
        $error = null;
    }
}

$images = [];
if (!empty($product['product_image'])) {
    $images[] = $product['product_image'];
}
if (!empty($product['product_image2'])) {
    $images[] = $product['product_image2'];
}
$images = array_values(array_unique($images));
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
<?php require 'G00_ヘッダー.php'; ?>

<main class="product-detail">
    <?php if (!empty($error)): ?>
        <div class="error-box"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php else: ?>
        <section class="pd-top">
            <div class="pd-image">
                <?php
                $img = $product['product_image'] ? htmlspecialchars($product['product_image'], ENT_QUOTES, 'UTF-8') : 'refpic/no-image.png';
                ?>
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="pd-info">
                <h1 class="pd-title"><?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="pd-price">¥<?= number_format((int)$product['product_price']) ?></div>
                <div class="pd-stock">在庫: <?= (int)$product['product_stock'] ?></div>

                <form action="G04_カート一覧.php" method="post" class="pd-cart-form">
                    <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                    <label>数量
                        <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['product_stock'] ?>">
                    </label>
                    <button type="submit" class="add-cart">カートに入れる</button>
                </form>
            </div>
        </section>

        <?php if (!empty($product['product_nuts_image'])): ?>
            <section class="pd-nutrition">
                <h2>栄養成分</h2>
                <img src="<?= htmlspecialchars($product['product_nuts_image'], ENT_QUOTES, 'UTF-8') ?>" alt="栄養成分表">
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require 'G00_フッター.php'; ?>

</body>
</html>