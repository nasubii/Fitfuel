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
        <div class="pd-layout">
            <article class="pd-card pd-card-visual">
                <div class="pd-carousel" data-images="<?= htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8') ?>">
                    <div class="pd-carousel-inner">
                        <?php $defaultImage = !empty($images[0]) ? $images[0] : 'refpic/no-image.png'; ?>
                        <img src="<?= htmlspecialchars($defaultImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <?php if (count($images) > 1): ?>
                        <button type="button" class="carousel-arrow prev" aria-label="前の画像">&lsaquo;</button>
                        <button type="button" class="carousel-arrow next" aria-label="次の画像">&rsaquo;</button>
                    <?php endif; ?>
                </div>
                <div class="pd-carousel-info">
                    <span>選ばれる理由 No.1</span>
                    <h1><?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?></h1>
                    <p>カテゴリ: <?= htmlspecialchars($product['category_name'] ?? 'その他', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </article>

            <article class="pd-card pd-card-meta">
                <div class="pd-card-head">
                    <div class="pd-card-title">
                        <h2><?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <span>1kg</span>
                    </div>
                    <div class="pd-card-price">¥<?= number_format((int)$product['product_price']) ?></div>
                </div>
                <div class="pd-card-stock">在庫：<?= (int)$product['product_stock'] ?>個</div>
                <form action="G04_カート一覧.php" method="post" class="pd-cart-form">
                    <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                    <label class="pd-quantity-label">
                        数量
                        <input type="number" name="quantity" value="1" min="1" max="10" required>
                    </label>
                    <button type="submit" class="add-cart">カートに入れる</button>
                </form>
            </article>
        </div>

        <section class="pd-nutrition">
            <div class="pd-nutrition-card">
                <div class="pd-nutrition-head">
                    <h2>栄養成分</h2>
                    <p>最新のデータを画像でご覧いただけます。</p>
                </div>
                <?php if (!empty($product['product_nuts_image'])): ?>
                    <div class="pd-nutrition-image">
                        <img src="<?= htmlspecialchars($product['product_nuts_image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?> の栄養成分表">
                    </div>
                <?php else: ?>
                    <p class="pd-muted">栄養成分画像は準備中です。</p>
                <?php endif; ?>
                
            </div>
        </section>
    <?php endif; ?>
</main>

<?php if (empty($error)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('.pd-carousel');
    if (!carousel) {
        return;
    }
    const images = JSON.parse(carousel.getAttribute('data-images') || '[]');
    if (images.length <= 1) {
        return;
    }
    const inner = carousel.querySelector('.pd-carousel-inner img');
    const prevBtn = carousel.querySelector('.carousel-arrow.prev');
    const nextBtn = carousel.querySelector('.carousel-arrow.next');
    let current = 0;
    let timer = null;

    const fadeToImage = (src) => {
        inner.style.opacity = '0';
        setTimeout(() => {
            inner.src = src;
            inner.style.opacity = '1';
        }, 220);
    };

    const showImage = (index) => {
        current = (index + images.length) % images.length;
        fadeToImage(images[current]);
    };

    const nextImage = () => showImage(current + 1);
    const prevImage = () => showImage(current - 1);

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextImage();
            resetTimer();
        });
    }
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevImage();
            resetTimer();
        });
    }

    const startTimer = () => {
        timer = setInterval(nextImage, 5000);
    };
    const resetTimer = () => {
        if (timer) {
            clearInterval(timer);
        }
        startTimer();
    };

    carousel.addEventListener('mouseenter', () => {
        if (timer) {
            clearInterval(timer);
        }
    });
    carousel.addEventListener('mouseleave', () => startTimer());

    startTimer();
});
</script>
<?php endif; ?>
<?php require 'G00_フッター.php'; ?>
</body>
</html>
