<?php session_start(); ?>
<?php require 'G00_ヘッダー.php'; ?>

<link rel="stylesheet" href="css/G08_ログイン.css">

<div class="login-box">
  <h2>ログイン</h2>

  <form action="G08_ログインoutput.php" method="post">
    <label>ログインID</label>
    <input type="text" name="mail" required>

    <label>パスワード</label>
    <input type="password" name="password" required>

    <?php if (!empty($_SESSION['login_error'])): ?>
      <p class="login-error">
        <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8'); ?>
      </p>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <button type="submit">ログイン</button>
  </form>
</div>

<?php require 'footer.php'; ?>
