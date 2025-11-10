<?php session_start(); ?>
<?php require 'G00_ヘッダー.php'; ?>

<link rel="stylesheet" href="css/G06-2_ログイン中.css">

<div class="success-box">
  <?php if (isset($_SESSION['customer'])): ?>
    <p>いらっしゃいませ、<?= htmlspecialchars($_SESSION['customer']['name'], ENT_QUOTES, 'UTF-8'); ?>さん。</p>
    <form action="logout.php" method="post">
      <button type="submit" class="logout-btn">ログアウト</button>
    </form>
    <a href="index.php">トップへ戻る</a>
  <?php else: ?>
    <p>ログイン情報が確認できません。</p>
    <a href="G08_ログイン.php">ログイン画面へ戻る</a>
  <?php endif; ?>
</div>

<?php require 'footer.php'; ?>