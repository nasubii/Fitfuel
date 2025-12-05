<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G00_ヘッダー.css" rel="stylesheet" type="text/css">
        <link rel="icon" type="image/x-icon" href="/fabicon/logo.ico">
    <style>
        /* フォールバック: 外部CSSが読み込まれない場合でもヘッダーを固定し中央揃え */
        .site-header{position:fixed;top:0;left:0;right:0;z-index:1000;width:100%;display:flex;flex-direction:column;align-items:center}
        .site-header > *{width:100%;display:flex;justify-content:center}
        body{padding-top:72px}
    </style>
    <title>Fitfuel</title>
</head>
<body>
    <header class="site-header">
        <div class="header-top">
            <div class="container">
                <div class="logo">
                    <a href="../Fitfuel/index.php" aria-label="FitFuel ホーム">
                        <img src="refpic/fitfuel_logo.png" alt="FitFuel">
                    </a>
                </div>

                <div class="search">
                    <form action="G03_検索結果.php" method="get">
                        <?php
                        // 検索結果画面に遷移した場合は検索窓をリセットする
                        $current = basename($_SERVER['PHP_SELF']);
                        if ($current === 'G03_検索結果.php') {
                            $q = '';
                        } else {
                            $q = isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES) : '';
                        }
                        ?>
                        <input type="text" name="q" placeholder="検索" aria-label="検索" value="<?php echo $q; ?>">
                        <button type="submit">検索</button>
                    </form>
                </div>

                <div class="icons">
                    <a class="icon" href="G05_商品一覧.php" title="商品一覧">📦</a>
                    <a class="icon" href="G06-1_ユーザー管理.php" title="マイページ">👤</a>
                    <a class="icon" href="G04_カート一覧.php" title="カート">🛒</a>
                    <a class="icon" href="index.php" title="ホーム">🏠</a>
                </div>
            </div>
        </div>

        <nav class="category-nav">
            <div class="container nav-inner">
                    <?php
                    // 検索結果ページにいる場合は q を渡さない（検索後は検索窓をリセットするため）
                    $current = basename($_SERVER['PHP_SELF']);
                    $qParam = '';
                    if ($current !== 'G03_検索結果.php' && isset($_GET['q']) && $_GET['q'] !== '') {
                        $qParam = '&q='.urlencode($_GET['q']);
                    }
                    ?>
                    <a class="icon" href="G05_商品一覧.php" title="商品一覧">商品一覧</a>
                    <a href="G03_検索結果.php?category=%E3%83%93%E3%82%BF%E3%83%9F%E3%83%B3<?php echo $qParam; ?>">ビタミン</a>
                    <a href="G03_検索結果.php?category=%E3%83%97%E3%83%AD%E3%83%86%E3%82%A4%E3%83%B3<?php echo $qParam; ?>">プロテイン</a>
                    <a href="G03_検索結果.php?category=%E3%82%B5%E3%83%97%E3%83%AA%E3%83%A1%E3%83%B3%E3%83%88<?php echo $qParam; ?>">サプリメント</a>
                </div>
        </nav>
    </header>
    <script>
        (function(){
            var header = document.querySelector('.site-header');
            function updateBodyPadding(){
                if(!header) return;
                var h = header.getBoundingClientRect().height;
                document.body.style.paddingTop = h + 'px';
            }
            window.addEventListener('resize', updateBodyPadding);
            document.addEventListener('DOMContentLoaded', updateBodyPadding);
            if(document.readyState === 'complete' || document.readyState === 'interactive') updateBodyPadding();
        })();
    </script>
</body>
</html>