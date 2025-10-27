<?php
// ホーム画面の静的ビュー。後続で動的データに差し替え予定。
?><!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitfuel | ホーム</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/Fitfuel_logo.png">
</head>
<body>
    <header class="site-header">
        <div class="promo-bar">
            <span>初回購入限定で15%OFF &amp; 送料無料キャンペーン実施中</span>
            <a href="#membership" class="promo-link">詳しく見る</a>
        </div>
        <div class="header-main">
            <a href="index.php" class="logo">
                <img src="assets/images/Fitfuel_logo.png" alt="Fitfuel ロゴ">
                <span>Fitfuel</span>
            </a>
            <button class="nav-toggle" aria-expanded="false" aria-controls="primary-nav">
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="nav-toggle-bar"></span>
                <span class="sr-only">メニューを開閉</span>
            </button>
            <nav id="primary-nav" class="nav">
                <ul>
                    <li><a href="index.php" class="active">ホーム</a></li>
                    <li><a href="#popular">商品一覧</a></li>
                    <li><a href="#concept">ブランド</a></li>
                    <li><a href="#stories">体験談</a></li>
                    <li><a href="#journal">ブログ</a></li>
                    <li><a href="#support">サポート</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="#login" class="link">ログイン</a>
                <a href="#cart" class="icon-button" aria-label="カート">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M7 4h-2l-1 2v2h2l3.6 7.59-1.35 2.41c-.16.28-.25.61-.25.95 0 1.1.9 2 2 2h10v-2h-10l1.1-2h7.45c.75 0 1.41-.41 1.75-1.03l3-5.48c.08-.14.12-.31.12-.49 0-.55-.45-1-1-1h-14.31l-.95-2h-1.49zm3 18c-1.11 0-2 .89-2 2s.89 2 2 2 2-.89 2-2-.89-2-2-2zm10 0c-1.11 0-2 .89-2 2s.89 2 2 2 2-.89 2-2-.89-2-2-2z"/>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero" id="hero">
            <div class="hero-content">
                <p class="hero-pill">パーソナライズされた栄養サポート</p>
                <h1>限界を超えたいあなたに、<br>最適なプロテインを。</h1>
                <p class="hero-lead">Fitfuelはアスリート・トレーニー・ライフスタイラーのために厳選したサプリメントをお届けします。目的別に最適化されたラインアップで日々のパフォーマンスを最大化しましょう。</p>
                <div class="hero-actions">
                    <a href="#popular" class="button primary">今すぐ購入</a>
                    <a href="#membership" class="button secondary">定期便をチェック</a>
                </div>
                <dl class="hero-stats">
                    <div>
                        <dt>顧客満足度</dt>
                        <dd>98<span>%</span></dd>
                    </div>
                    <div>
                        <dt>レビュー数</dt>
                        <dd>12,500<span>+</span></dd>
                    </div>
                    <div>
                        <dt>継続率</dt>
                        <dd>92<span>%</span></dd>
                    </div>
                </dl>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <img src="assets/images/banner.png" alt="プロテインシェイク" class="hero-image">
                    <div class="hero-card-overlay">
                        <p class="hero-card-title">アスリートの毎日を支える</p>
                        <p class="hero-card-body">新作のミールリプレイスメントシリーズが登場。トレーニング後の必要栄養をワンパックに凝縮。</p>
                        <a href="#journal" class="hero-card-link">詳細を見る</a>
                    </div>
                </div>
                <div class="floating-badge">
                    <img src="assets/images/dumbbell.png" alt="ダンベルアイコン">
                    <div>
                        <p>毎日のトレーニー応援</p>
                        <span>栄養士監修レシピ配信中</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="trust"
                 aria-label="Fitfuelが選ばれる理由">
            <div class="trust-card">
                <h2 id="concept">科学的根拠に基づくフォーミュラ</h2>
                <p>最新のスポーツ栄養学に基づき、原料の厳選から製造工程まで徹底した品質管理を実施しています。</p>
            </div>
            <div class="trust-card">
                <h2>専門家によるサポート</h2>
                <p>管理栄養士とトレーナーによる個別カウンセリング。目的に合わせた最適な継続プランをご提案。</p>
            </div>
            <div class="trust-card">
                <h2>環境にもやさしい</h2>
                <p>リサイクル素材のパッケージとカーボンニュートラル製造ラインでサステナブルな選択を。</p>
            </div>
        </section>

        <section class="popular" id="popular" aria-labelledby="popular-heading">
            <div class="section-header">
                <div>
                    <p class="section-pill">人気商品TOP3</p>
                    <h2 id="popular-heading">目的別に選べるベストセラー</h2>
                </div>
                <a href="#" class="section-link">全商品を見る</a>
            </div>
            <div class="product-grid">
                <article class="product-card">
                    <img src="assets/images/protein.png" alt="Whey Performance 100">
                    <div class="product-body">
                        <p class="product-tag">筋力アップ</p>
                        <h3>Whey Performance 100</h3>
                        <p class="product-copy">吸収スピード最優先のWPI製法。トレーニング直後のリカバリーを最速で。</p>
                        <ul class="product-meta">
                            <li>たんぱく質含有量 27g</li>
                            <li>糖質 1.2g</li>
                            <li>BCAA高配合</li>
                        </ul>
                        <div class="product-actions">
                            <span class="price">¥5,980</span>
                            <a href="#" class="button ghost">カートに追加</a>
                        </div>
                    </div>
                </article>
                <article class="product-card">
                    <img src="assets/images/supplement.png" alt="Recovery Night Blend">
                    <div class="product-body">
                        <p class="product-tag">コンディショニング</p>
                        <h3>Recovery Night Blend</h3>
                        <p class="product-copy">グルタミンとビタミン群を最適バランスで配合。睡眠中の回復力をサポート。</p>
                        <ul class="product-meta">
                            <li>就寝前におすすめ</li>
                            <li>ノンカフェイン</li>
                            <li>リラックスハーブ配合</li>
                        </ul>
                        <div class="product-actions">
                            <span class="price">¥4,200</span>
                            <a href="#" class="button ghost">カートに追加</a>
                        </div>
                    </div>
                </article>
                <article class="product-card">
                    <img src="assets/images/vitamin.png" alt="Pure Daily Essentials">
                    <div class="product-body">
                        <p class="product-tag">ボディメイク</p>
                        <h3>Pure Daily Essentials</h3>
                        <p class="product-copy">日々の栄養バランスを整えるマルチビタミン。食生活のベースを底上げします。</p>
                        <ul class="product-meta">
                            <li>ビタミン13種</li>
                            <li>ミネラル7種</li>
                            <li>添加物不使用</li>
                        </ul>
                        <div class="product-actions">
                            <span class="price">¥3,480</span>
                            <a href="#" class="button ghost">カートに追加</a>
                        </div>
                    </div>
                </article>
            </div>
            <p class="popular-note">※ 人気商品TOP3は実際にはデータベースから商品画像と情報を取得します。</p>
        </section>

        <section class="stories" id="stories" aria-labelledby="stories-heading">
            <div class="stories-inner">
                <div class="stories-copy">
                    <p class="section-pill">コミュニティボイス</p>
                    <h2 id="stories-heading">ユーザーのリアルな声</h2>
                    <p>トップアスリートからフィットネス初心者まで、幅広いユーザーがFitfuelでの成果をシェアしています。あなたの挑戦を後押しするストーリーをチェック。</p>
                    <a href="#" class="button primary">体験談を読む</a>
                </div>
                <ul class="stories-feed">
                    <li>
                        <h3>減量期もしっかり筋量維持</h3>
                        <p>「高たんぱくで低糖質なので、減量中でも安心して摂取できます。味も◎。」</p>
                        <span>@akira_train</span>
                    </li>
                    <li>
                        <h3>朝の習慣が変わりました</h3>
                        <p>「ビタミンパックで一日がスムーズにスタート。集中力が続きます。」</p>
                        <span>@miki_life</span>
                    </li>
                    <li>
                        <h3>リカバリー速度が段違い</h3>
                        <p>「ナイトブレンドで翌朝の疲労感が激減。ハードトレも怖くない。」</p>
                        <span>@tomo_fitness</span>
                    </li>
                </ul>
            </div>
        </section>

        <section class="membership" id="membership" aria-labelledby="membership-heading">
            <div class="membership-inner">
                <div>
                    <p class="section-pill">定期便プログラム</p>
                    <h2 id="membership-heading">Fitfuel Membership</h2>
                    <p>毎月のトレーニングサイクルに合わせてプロダクトを自動でお届け。スキップや変更もアプリで簡単。</p>
                </div>
                <ul class="membership-benefits">
                    <li>購入金額の5%をポイント還元</li>
                    <li>専門家による月1回カウンセリング</li>
                    <li>限定フレーバーを先行体験</li>
                </ul>
                <a href="#" class="button secondary">プランを比較</a>
            </div>
        </section>
    </main>

    <footer class="site-footer" id="support">
        <div class="footer-inner">
            <div class="footer-brand">
                <a href="index.php" class="logo">
                    <img src="assets/images/Fitfuel_logo.png" alt="Fitfuel ロゴ">
                    <span>Fitfuel</span>
                </a>
                <p>あなたのコンディションとパフォーマンスを最大化するプレミアムサプリメントブランド。</p>
                <div class="footer-social">
                    <a href="#" aria-label="Instagram">Instagram</a>
                    <a href="#" aria-label="YouTube">YouTube</a>
                    <a href="#" aria-label="LINE">LINE</a>
                </div>
            </div>
            <div class="footer-links">
                <div>
                    <h3>ショップ</h3>
                    <ul>
                        <li><a href="#popular">人気商品</a></li>
                        <li><a href="#">新商品</a></li>
                        <li><a href="#">ギフト</a></li>
                    </ul>
                </div>
                <div>
                    <h3>サポート</h3>
                    <ul>
                        <li><a href="#support">お問い合わせ</a></li>
                        <li><a href="#">配送・返品</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3>会社情報</h3>
                    <ul>
                        <li><a href="#concept">ブランドストーリー</a></li>
                        <li><a href="#">採用情報</a></li>
                        <li><a href="#">プレスリリース</a></li>
                    </ul>
                </div>
            </div>
            <form class="footer-newsletter" action="#" method="post">
                <h3>ニュースレター登録</h3>
                <p>限定セールや最新情報をメールでお届けします。</p>
                <div class="newsletter-field">
                    <label for="newsletter-email" class="sr-only">メールアドレス</label>
                    <input type="email" id="newsletter-email" name="email" placeholder="yourname@example.com" required>
                    <button type="submit" class="button primary">登録する</button>
                </div>
            </form>
        </div>
        <div class="footer-bottom">
            <small>© <?php echo date('Y'); ?> Fitfuel. All rights reserved.</small>
            <div class="footer-bottom-links">
                <a href="#">利用規約</a>
                <a href="#">プライバシーポリシー</a>
                <a href="#">特定商取引法に基づく表記</a>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
