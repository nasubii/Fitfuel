<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="G00_フッター.css" rel="stylesheet" type="text/css">
    <title>フッター</title>
</head>
<body>
    
</body>
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-col footer-about">
                <h4>Fitfuel</h4>
                <p>最高品質のプロテインを、健やかな毎日に。</p>
            </div>
            <nav class="footer-col footer-nav" aria-label="フッター ナビゲーション">
                <h4>サイト</h4>
                <ul>
                    <li><a href="G01_ホーム.php">ホーム</a></li>
                    <li><a href="G02_商品詳細.php">商品一覧</a></li>
                    <li><a href="G04_カート一覧.php">カート</a></li>
                    <li><a href="G05_購入.php">購入手続き</a></li>
                </ul>
            </nav>
            <div class="footer-col footer-info">
                <h4>企業情報</h4>
                <address>
                    Fitfuel 株式会社<br>
                    〒000-0000 東京都○○区○○ 1-2-3
                </address>
                <p class="contact"><a href="#">お問い合わせ</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <span id="year"></span> Fitfuel. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // フッターの年を自動で表示
        (function(){
            var y = new Date().getFullYear();
            var el = document.getElementById('year');
            if(el) el.textContent = y;
        })();
    </script>