<?php
session_start();

// エラーメッセージ初期化
$errors = [];
$old = ['login_id' => '', 'mail' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 入力取得・整形
        $login_id = trim((string)($_POST['login_id'] ?? ''));
        $mail = trim((string)($_POST['mail'] ?? ''));
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        $old['login_id'] = $login_id;
        $old['mail'] = $mail;

        // バリデーション
        if ($login_id === '') {
                $errors['login_id'] = 'ログインIDを入力してください。';
        } elseif (mb_strlen($login_id) > 100) {
                $errors['login_id'] = 'ログインIDは100文字以内で入力してください。';
        }
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $errors['mail'] = 'メールアドレスの形式で入力してください。';
        } elseif (mb_strlen($mail) > 100) {
                $errors['mail'] = 'メールアドレスは100文字以内で入力してください。';
        }
        if (mb_strlen($password) < 8) {
                $errors['password'] = 'パスワードは八文字以上で入力してください。';
        }
        if ($password !== $password_confirm) {
                $errors['password_confirm'] = 'パスワードが一致していません';
        }

        // DBチェック・登録（バリデーション通過時）
        if (empty($errors)) {
                // DB接続情報（関数内では定数ではなく変数を使う）
                $server = 'mysql326.phy.lolipop.lan';
                $dbname = 'LAA1607731-fitfuel';
                $dbuser = 'LAA1607731';
                $dbpass = '6cK37BWhPZYcXkm';

                $dsn = 'mysql:host=' . $server . ';dbname=' . $dbname . ';charset=utf8';
                $pdoOptions = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ];
                try {
                            $pdo = new PDO($dsn, $dbuser, $dbpass, $pdoOptions);
                } catch (PDOException $e) {
                        $errors['db'] = 'データベース接続に失敗しました。';
                }
                if (empty($errors)) {
                        // ログインID・メールの重複チェック
                        $dupSql = 'SELECT user_id, user_email FROM `user` WHERE user_id = :user_id OR user_email = :user_email';
                        $stmt = $pdo->prepare($dupSql);
                        $stmt->bindValue(':user_id', $login_id, PDO::PARAM_STR);
                        $stmt->bindValue(':user_email', $mail, PDO::PARAM_STR);
                        $stmt->execute();
                        while ($row = $stmt->fetch()) {
                                if ($row['user_id'] === $login_id) {
                                        $errors['login_id'] = 'すでに使用されているIDです';
                                }
                                if ($row['user_email'] === $mail) {
                                        $errors['mail'] = 'すでに使用されているメールアドレスです';
                                }
                        }

                        if (empty($errors)) {
                                // 登録
                                $hash = password_hash($password, PASSWORD_DEFAULT);
                                $ins = 'INSERT INTO `user` (user_id, user_password, user_email) VALUES (:user_id, :user_password, :user_email)';
                                $ist = $pdo->prepare($ins);
                                $ist->bindValue(':user_id', $login_id, PDO::PARAM_STR);
                                $ist->bindValue(':user_password', $hash, PDO::PARAM_STR);
                                $ist->bindValue(':user_email', $mail, PDO::PARAM_STR);
                                $ist->execute();

                                // 登録後自動ログイン（セッション設定）
                                session_regenerate_id(true);
                                $_SESSION['customer'] = [
                                        'user_id' => $login_id,
                                        'user_email' => $mail,
                                ];
                                header('Location: G06-2_ログイン中.php');
                                exit();
                        }
                }
        }
}
?>
<?php require 'G00_ヘッダー.php'; ?>

<link rel="stylesheet" href="css/G07_ユーザー登録.css">

<main class="reg-main">
    <div class="reg-card">
        <h2>アカウント作成</h2>
        <?php if (!empty($errors['db'])): ?>
            <p class="error"><?= htmlspecialchars($errors['db'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form method="post" novalidate>
            <label>ログインID</label>
            <input type="text" name="login_id" value="<?= htmlspecialchars($old['login_id'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (!empty($errors['login_id'])): ?>
                                <p class="error"><?= htmlspecialchars($errors['login_id'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <label>メールアドレス</label>
            <input type="text" name="mail" value="<?= htmlspecialchars($old['mail'], ENT_QUOTES, 'UTF-8') ?>">
            <?php if (!empty($errors['mail'])): ?>
                <p class="error"><?= htmlspecialchars($errors['mail'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <label>パスワード</label>
            <input type="password" name="password">
            <?php if (!empty($errors['password'])): ?>
                <p class="error"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <label>パスワードの確認</label>
            <input type="password" name="password_confirm">
            <?php if (!empty($errors['password_confirm'])): ?>
                <p class="error"><?= htmlspecialchars($errors['password_confirm'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <button type="submit" class="submit-btn">新規登録</button>
        </form>
    </div>
</main>

<?php require 'G00_フッター.php'; ?>