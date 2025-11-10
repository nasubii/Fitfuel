<?php
// データベース接続設定(実際の環境に合わせて変更してください)
require_once 'config/database.php';

// 人気商品TOP3を取得(売上数が多い順)
$sql_top3 = "
    SELECT p.product_id, p.product_name, p.product_price, p.product_image, 
           COALESCE(SUM(s.sales_sold), 0) as total_sales
    FROM product p
    LEFT JOIN sales s ON p.product_id = s.product_id
    WHERE p.product_status = 1
    GROUP BY p.product_id, p.product_name, p.product_price, p.product_image
    ORDER BY total_sales DESC
    LIMIT 3
";

try {
    $stmt = $pdo->query($sql_top3);
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $top_products = [];
    error_log("Database error: " . $e->getMessage());
}

// カテゴリー情報を取得
$sql_categories = "SELECT category_id, category_name FROM category ORDER BY category_id";
try {
    $stmt = $pdo->query($sql_categories);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    error_log("Database error: " . $e->getMessage());
}
?>
<?php require 'G00_ヘッダー.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitFuel - あなたの体作りを飲食からサポート</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- メインバナーセクション -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1 class="hero-text">あなたの体作りを<br>飲食からサポート</h1>
            
        </div>
    </section>

    <!-- 人気商品TOP3セクション -->
    <section class="popular-products">
        <h2 class="section-title">人気商品TOP3</h2>
        <div class="products-container">
            <?php if (!empty($top_products)): ?>
                <?php foreach ($top_products as $product): ?>
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <?php if (!empty($product['product_image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['product_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <img src="../refpic/protein.png" 
                                     alt="商品画像" 
                                     class="product-image">
                            <?php endif; ?>
                        </div>
                        <div class="product-divider"></div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-price">¥<?php echo number_format($product['product_price']); ?></p>
                        <button class="buy-button" onclick="location.href='product_detail.php?id=<?php echo $product['product_id']; ?>'">購入</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- サンプル商品データ(データベースに商品がない場合) -->
                <div class="product-card">
                    <div class="product-image-wrapper">
                        <img src="../refpic/protein.png" alt="ホエイプロテイン カカオ味 1kg" class="product-image">
                    </div>
                    <div class="product-divider"></div>
                    <h3 class="product-name">ホエイプロテイン カカオ味 1kg</h3>
                    <p class="product-price">¥2900</p>
                    <button class="buy-button">購入</button>
                </div>
                <div class="product-card">
                    <div class="product-image-wrapper">
                        <img src="../refpic/vitamin.png" alt="Fuelビタミン 50錠" class="product-image">
                    </div>
                    <div class="product-divider"></div>
                    <h3 class="product-name">Fuelビタミン 50錠</h3>
                    <p class="product-price">¥4100</p>
                    <button class="buy-button">購入</button>
                </div>
                <div class="product-card">
                    <div class="product-image-wrapper">
                        <img src="../refpic/supplement.png" alt="Fuelクレアチン 1kg" class="product-image">
                    </div>
                    <div class="product-divider"></div>
                    <h3 class="product-name">Fuelクレアチン 1kg</h3>
                    <p class="product-price">¥1800</p>
                    <button class="buy-button">購入</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- カテゴリーセクション -->

    <?php require 'G00_フッター.php'; ?>
</body>
</html>
