<?php
ini_set('display_errors', 1); // エラーを表示する
error_reporting(E_ALL);       // すべてのエラーを報告する

session_start(); // セッションを開始
require 'db_config.php'; // データベース接続

$message = '';
$message_type = 'danger'; // デフォルトはエラー用

// フォームがPOST送信されたかチェック
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = 'メールアドレスとパスワードを入力してください。';
    } else {
        // 1. メールアドレスでユーザーを検索
        $stmt = $pdo->prepare("SELECT user_id, email, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(); // ユーザー情報を取得

        // 2. ユーザーが存在し、かつパスワードが一致するか検証
        if ($user && password_verify($password, $user['password_hash'])) {
            // ログイン成功
            
            // セッション情報をリセット（セキュリティ対策）
            session_regenerate_id(true); 
            
            // セッションにユーザー情報を保存
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            
            // メインページ（map.php）へリダイレクト（移動）
            header("Location: map.php");
            exit; // header()の後は必ずexit()を呼ぶ

        } else {
            // ログイン失敗
            $message = 'メールアドレスまたはパスワードが間違っています。';
        }
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fdfaf6; }
        .form-wrapper { max-width: 400px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-custom-primary { background-color: #f4a261; border: none; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-wrapper">
            <h2 class="text-center mb-4">ログイン</h2>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">メールアドレス</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">パスワード</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-custom-primary btn-lg">ログイン</button>
                </div>
            </form>
            
            <hr>

            <p class="text-center mb-0">
                アカウントがありませんか？ <a href="register.php">新規登録はこちら</a>
            </p>
            <p class="text-center mt-2">
                <a href="index.php">トップページに戻る</a>
            </p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>