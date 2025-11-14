<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G00_ヘッダー.css" rel="stylesheet" type="text/css">
    <link href="css/G03_検索結果.css" rel="stylesheet" type="text/css">
    <?php require_once 'G00_ヘッダー.php'; ?>
    <title>検索結果</title>
</head>
<body>
    <?php
    // DB接続（PDO）を利用する
    // config/database.php に PDO 接続情報がある前提
    $results = [];
    $dbConfigPath = __DIR__ . '/../config/database.php';
    if (file_exists($dbConfigPath)) {
        require_once $dbConfigPath; // provides $pdo or dies on error
    }

    // 検索キーワードとカテゴリを取得（GETパラメータ q, category）
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';

    if (isset($pdo) && $pdo instanceof PDO) {
        $sql = "SELECT p.product_id, p.product_name AS title, p.product_price AS price, p.product_image AS image, c.category_name AS category
                FROM product p
                JOIN category c ON p.category_id = c.category_id
                WHERE p.product_status = 1";

        $params = [];
        if ($category !== '') {
            $sql .= ' AND c.category_name = :category';
            $params[':category'] = $category;
        }
        if ($q !== '') {
            $sql .= ' AND p.product_name LIKE :q';
            $params[':q'] = '%' . $q . '%';
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $results[] = [
                'id' => $row['product_id'],
                'title' => $row['title'],
                'price' => (int)$row['price'],
                'image' => $row['image'] ? ($row['image']) : '../refpic/fitfuel_logo.svg',
                'category' => $row['category'],
            ];
        }
    } else {
        // DB未接続時のフォールバック
        error_log('DB未接続: フォールバックのサンプルデータを使用します');
        $products = [
            [
                'id' => 1,
                'title' => 'ホエイプロテイン カカオ味 1kg',
                'price' => 2900,
                'image' => '../refpic/fitfuel_logo.svg',
                'category' => 'プロテイン',
            ],
            [
                'id' => 2,
                'title' => 'ソイプロテイン ビターショコラ味 1kg',
                'price' => 2900,
                'image' => '../refpic/fitfuel_logo.svg',
                'category' => 'プロテイン',
            ],
            [
                'id' => 3,
                'title' => 'マルチビタミン',
                'price' => 1200,
                'image' => '../refpic/fitfuel_logo.svg',
                'category' => 'ビタミン',
            ],
        ];

        foreach ($products as $p) {
            $matchQ = true;
            $matchCat = true;

            if ($q !== '') {
                $matchQ = (mb_strpos(mb_strtolower($p['title'], 'UTF-8'), mb_strtolower($q, 'UTF-8')) !== false);
            }
            if ($category !== '') {
                $matchCat = ($p['category'] === $category);
            }

            if ($matchQ && $matchCat) {
                $results[] = $p;
            }
        }
    }
    ?>

    <main class="search-results-page">
        <section class="results-panel">
            <h1 class="results-title"><?php echo ($q === '') ? '検索結果' : '「'.htmlspecialchars($q, ENT_QUOTES).'」の検索結果'; ?></h1>

            <div class="cards">
                <?php if (count($results) === 0): ?>
                    <div class="no-results">該当する商品は見つかりませんでした。</div>
                <?php else: ?>
                    <?php foreach ($results as $p): ?>
                        <article class="card">
                            <div class="card-image">
                                <img src="<?php echo htmlspecialchars($p['image'], ENT_QUOTES); ?>" alt="商品画像">
                            </div>
                            <div class="card-body">
                                <h2 class="product-title"><?php echo htmlspecialchars($p['title'], ENT_QUOTES); ?></h2>
                                <div class="price">¥<?php echo number_format($p['price']); ?></div>
                                <button class="buy">購入</button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php require_once 'G00_フッター.php'; ?>
</body>
</html>