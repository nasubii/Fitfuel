<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/G00_ヘッダー.css" rel="stylesheet" type="text/css">
    <title>ヘッダー</title>
</head>
<body>
    <header class="site-header">
        <div class="header-top">
            <div class="container">
                <div class="logo">
                    <a href="../index.php" aria-label="FitFuel ホーム">
                        <img src="refpic/Fitfuel_logo.png" alt="FitFuel">
                    </a>
                </div>

                <div class="search">
                    <form action="../pages/G03_検索結果.php" method="get">
                        <input type="text" name="q" placeholder="検索" aria-label="検索">
                        <button type="submit">検索</button>
                    </form>
                </div>

                <div class="icons">
                    <a class="icon" href="#" title="マイページ">👤</a>
                    <a class="icon" href="../pages/G04_カート一覧.php" title="カート">🛒</a>
                    <a class="icon" href="../index.php" title="ホーム">🏠</a>
                </div>
            </div>
        </div>

        <nav class="category-nav">
            <div class="container nav-inner">
                <a href="#">ビタミン</a>
                <a href="#">プロテイン</a>
                <a href="#">サプリメント</a>
            </div>
        </nav>
    </header>
</body>
</html>