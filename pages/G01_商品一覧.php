<?php
session_start();

$products = [];
$sortKey = isset($_GET['sort']) ? $_GET['sort'] : 'popular';
$validSorts = ['popular','name','price'];
$sortLabels = [
	'popular' => '人気順',
	'name' => '商品名順 (A→Z)',
	'price' => '価格が安い順',
];
if (!in_array($sortKey, $validSorts, true)) {
	$sortKey = 'popular';
}
$errorMessage = '';

$dbConfigPath = __DIR__ . '/config/database.php';
if (file_exists($dbConfigPath)) {
	require_once $dbConfigPath;
}

if (isset($pdo) && $pdo instanceof PDO) {
	$orderClause = 'total_sales DESC, p.product_id DESC';
	if ($sortKey === 'name') {
		$orderClause = 'p.product_name ASC';
	} elseif ($sortKey === 'price') {
		$orderClause = 'p.product_price ASC';
	}

	$sql = 'SELECT p.product_id, p.product_name, p.product_price, p.product_stock, p.product_image, c.category_name,
		   COALESCE(st.total_sales,0) AS total_sales
		FROM product p
		LEFT JOIN category c ON p.category_id = c.category_id
		LEFT JOIN (
			SELECT product_id, SUM(sales_sold) AS total_sales
			FROM sales
			GROUP BY product_id
		) st ON p.product_id = st.product_id
		WHERE p.product_status = 1
		ORDER BY ' . $orderClause;

	try {
		$stmt = $pdo->query($sql);
		$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		$errorMessage = '商品情報の取得に失敗しました。';
		$products = [];
	}
} else {
	$sampleProducts = [
		[
			'product_id' => 2,
			'product_name' => 'ホエイプロテイン-トロピカルマンゴー味',
			'product_price' => 4400,
			'product_stock' => 1874,
			'product_image' => 'pimage/トロピカルプロテイン1.jpeg',
			'category_name' => 'プロテイン',
			'total_sales' => 980,
		],
		[
			'product_id' => 3,
			'product_name' => 'ホエイプロテイン-リッチチョコ味',
			'product_price' => 4200,
			'product_stock' => 1211,
			'product_image' => 'pimage/チョコプロテイン1.jpeg',
			'category_name' => 'プロテイン',
			'total_sales' => 1230,
		],
		[
			'product_id' => 4,
			'product_name' => 'ホエイプロテイン-スノーバニラ味',
			'product_price' => 4700,
			'product_stock' => 1311,
			'product_image' => 'pimage/雪プロテイン1.jpeg',
			'category_name' => 'プロテイン',
			'total_sales' => 860,
		],
		[
			'product_id' => 5,
			'product_name' => 'ホエイプロテイン-森薫る抹茶味',
			'product_price' => 5100,
			'product_stock' => 520,
			'product_image' => 'pimage/森プロテイン1.jpeg',
			'category_name' => 'プロテイン',
			'total_sales' => 640,
		],
		[
			'product_id' => 6,
			'product_name' => 'ホエイプロテイン-五種のベリー味',
			'product_price' => 3980,
			'product_stock' => 2911,
			'product_image' => 'pimage/紫プロテイン1.jpeg',
			'category_name' => 'プロテイン',
			'total_sales' => 1100,
		],
	];

	$products = $sampleProducts;
	usort($products, function ($a, $b) use ($sortKey) {
		if ($sortKey === 'name') {
			return strcmp($a['product_name'], $b['product_name']);
		}
		if ($sortKey === 'price') {
			return $a['product_price'] <=> $b['product_price'];
		}
		return ($b['total_sales'] ?? 0) <=> ($a['total_sales'] ?? 0);
	});
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>商品一覧 | FitFuel</title>
	<link rel="stylesheet" href="css/G01_商品一覧.css">
</head>
<body>
<?php require 'G00_ヘッダー.php'; ?>

<main class="all-products">
	<section class="ap-hero">
		<p class="ap-kicker">ALL PRODUCTS</p>
		<h1>ラインアップからお気に入りを見つけよう</h1>
		<p class="ap-lead">FitFuel が取り扱う全商品をカテゴリ別にチェック。気になるアイテムはその場で詳細ページへジャンプできます。</p>
	</section>

	<section class="ap-sort">
		<form method="get" class="ap-sort-form">
			<label for="sort" class="ap-sort-label">表示順</label>
			<div class="ap-sort-control">
				<select id="sort" name="sort" class="ap-sort-select" onchange="this.form.submit()">
					<?php foreach ($sortLabels as $key => $label): ?>
						<option value="<?php echo $key; ?>" <?php echo $sortKey === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
					<?php endforeach; ?>
				</select>
				<noscript>
					<button type="submit" class="ap-sort-button">並び替える</button>
				</noscript>
			</div>
			<p class="ap-sort-note">人気順は売上集計（indexのランキングと同じ sales テーブル）をもとに計算しています。</p>
		</form>
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
							<p class="ap-sales">累計販売：<?php echo number_format((int)($product['total_sales'] ?? 0)); ?>杯</p>
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
