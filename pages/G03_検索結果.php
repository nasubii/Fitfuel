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
    // 簡易サンプル商品データ（将来DBに差し替え）
    $products = [
        [
            'id' => 1,
            'title' => 'ホエイプロテイン カカオ味 1kg',
            'price' => 2900,
            'image' => 'refpic/protein.png',
            'category' => 'プロテイン',
        ],
        [
            'id' => 2,
            'title' => 'ソイプロテイン ビターショコラ味 1kg',
            'price' => 2900,
            'image' => 'refpic/protein.png',
            'category' => 'プロテイン',
        ],
        [
            'id' => 3,
            'title' => 'マルチビタミン',
            'price' => 1200,
            'image' => 'refpic/vitamin.png',
            'category' => 'ビタミン',

        ],
    ];

    // 検索キーワードとカテゴリを取得（GETパラメータ q, category）
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $category = isset($_GET['category']) ? trim($_GET['category']) : '';

    // 絞り込み（大文字小文字を区別せず部分一致、カテゴリは完全一致）
    $results = [];
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