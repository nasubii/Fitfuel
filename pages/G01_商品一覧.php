<?php
session_start();

$products = [];
$categoryOptions = [];
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';
$errorMessage = '';

$dbConfigPath = __DIR__ . '/config/database.php';
if (file_exists($dbConfigPath)) {
	require_once $dbConfigPath;
}

if (isset($pdo) && $pdo instanceof PDO) {
	try {
		$categoryStmt = $pdo->query('SELECT category_name FROM category ORDER BY category_id');
		$categoryOptions = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
	} catch (PDOException $e) {
		$categoryOptions = [];
	}

	$sql = 'SELECT p.product_id, p.product_name, p.product_price, p.product_stock, p.product_image, c.category_name
			FROM product p
			LEFT JOIN category c ON p.category_id = c.category_id
			WHERE p.product_status = 1';
	$params = [];
	if ($categoryFilter !== '') {
		$sql .= ' AND c.category_name = :category';
		$params[':category'] = $categoryFilter;
	}
	$sql .= ' ORDER BY p.product_id DESC';

	try {
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		$errorMessage = '商品情報の取得に失敗しました。';
		$products = [];
	}
} else {
	$categoryOptions = ['プロテイン'];
	$sampleProducts = [
		[
			'product_id' => 2,
			'product_name' => 'ホエイプロテイン-トロピカルマンゴー味',
			'product_price' => 4400,
			'product_stock' => 1874,
			'product_image' => 'pimage/トロピカルプロテイン1.jpeg',
			'category_name' => 'プロテイン',
		],
		[
			'product_id' => 3,
			'product_name' => 'ホエイプロテイン-リッチチョコ味',
			'product_price' => 4200,
			'product_stock' => 1211,
			'product_image' => 'pimage/チョコプロテイン1.jpeg',
			'category_name' => 'プロテイン',
		],
		[
			'product_id' => 4,
			'product_name' => 'ホエイプロテイン-スノーバニラ味',
			'product_price' => 4700,
			'product_stock' => 1311,
			'product_image' => 'pimage/雪プロテイン1.jpeg',
			'category_name' => 'プロテイン',
		],
		[
			'product_id' => 5,
			'product_name' => 'ホエイプロテイン-森薫る抹茶味',
			'product_price' => 5100,
			'product_stock' => 520,
			'product_image' => 'pimage/森プロテイン1.jpeg',
			'category_name' => 'プロテイン',
		],
		[
			'product_id' => 6,
			'product_name' => 'ホエイプロテイン-五種のベリー味',
			'product_price' => 3980,
			'product_stock' => 2911,
			'product_image' => 'pimage/紫プロテイン1.jpeg',
			'category_name' => 'プロテイン',
		],
	];

	if ($categoryFilter !== '') {
		foreach ($sampleProducts as $product) {
			if ($product['category_name'] === $categoryFilter) {
				$products[] = $product;
			}
		}
	} else {
		$products = $sampleProducts;
	}
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>商品一覧 | FitFuel</title>
	<link rel="stylesheet" href="css/G01_ホーム.css">
</head>
<body>
<?php require 'G00_ヘッダー.php'; ?>

<main class="all-products">
	<section class="ap-hero">
		<p class="ap-kicker">ALL PRODUCTS</p>
		<h1>ラインアップからお気に入りを見つけよう</h1>
		<p class="ap-lead">FitFuel が取り扱う全商品をカテゴリ別にチェック。気になるアイテムはその場で詳細ページへジャンプできます。</p>
	</section>

	<section class="ap-filter">
		<div class="ap-filter-row">
			<a class="ap-pill<?php echo ($categoryFilter === '' ? ' active' : ''); ?>" href="G01_商品一覧.php">すべて</a>
			<?php foreach ($categoryOptions as $categoryName): ?>
				<?php $isActive = ($categoryName === $categoryFilter); ?>
				<a class="ap-pill<?php echo $isActive ? ' active' : ''; ?>" href="G01_商品一覧.php?category=<?php echo urlencode($categoryName); ?>">
					<?php echo htmlspecialchars($categoryName, ENT_QUOTES); ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php if ($categoryFilter !== ''): ?>
			<p class="ap-filter-note">現在「<?php echo htmlspecialchars($categoryFilter, ENT_QUOTES); ?>」カテゴリを表示中です。</p>
		<?php endif; ?>
	</section>

	<section class="ap-results">
		<?php if ($errorMessage !== ''): ?>
			<div class="ap-alert"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></div>
		<?php endif; ?>

		<?php if (count($products) === 0): ?>
			<p class="ap-empty">該当する商品がありませんでした。</p>
		<?php else: ?>
			<div class="ap-grid">
				<?php foreach ($products as $product): ?>
					<article class="ap-card">
						<div class="ap-image">
							<img src="<?php echo htmlspecialchars($product['product_image'] ?: '../refpic/fitfuel_logo.svg', ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($product['product_name'], ENT_QUOTES); ?>">
						</div>
						<div class="ap-card-body">
							<p class="ap-category"><?php echo htmlspecialchars($product['category_name'] ?? 'その他', ENT_QUOTES); ?></p>
							<h2 class="ap-title"><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES); ?></h2>
							<p class="ap-price">¥<?php echo number_format((int)$product['product_price']); ?></p>
							<p class="ap-stock">在庫：<?php echo (int)$product['product_stock']; ?>個</p>
						</div>
						<div class="ap-card-footer">
							<a class="ap-link" href="G02_商品詳細.php?id=<?php echo htmlspecialchars($product['product_id'], ENT_QUOTES); ?>">詳細を見る</a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php require 'G00_フッター.php'; ?>
</body>
</html>
