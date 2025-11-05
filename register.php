<?php
session_start(); // セッションを開始
require 'db_config.php'; // データベース接続ファイルを読み込む

$message = '';    // ユーザーへのメッセージ
$message_type = ''; // メッセージのタイプ (Bootstrapのアラート用)

// フォームが送信された（POSTリクエスト）かどうかをチェック
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // フォームからデータを取得
    $email = $_POST['email'] ?? '';
    $pwf1 = $_POST['pwf1'] ?? '';
    $pwf2 = $_POST['pwf2'] ?? '';

    // バリデーション（入力チェック）
    if (empty($email) || empty($pwf1) || empty($pwf2)) {
        $message = 'すべて必須項目です。';
        $message_type = 'danger';
    } elseif ($pwf1 !== $pwf2) {
        $message = 'パスワードが一致しません。';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '有効なメールアドレスを入力してください。';
        $message_type = 'danger';
    } else {
        // メールアドレスが既に登録済みかチェック
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetchColumn() > 0) {
            $message = 'このメールアドレスは既に使用されています。';
            $message_type = 'danger';
        } else {
            // パスワードをハッシュ化 (PHPの標準関数)
            $password_hash = password_hash($pwf1, PASSWORD_DEFAULT);

            // データベースに登録
            try {
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
                $stmt->execute([$email, $password_hash]);
                
                $message = '登録が完了しました！ ログインページに移動します。';
                $message_type = 'success';
                
                // 3秒後にログインページへリダイレクト
                header("refresh:3;url=login.php");

            } catch (PDOException $e) {
                $message = '登録に失敗しました。時間をおいて再度お試しください。';
                $message_type = 'danger';
                // (実際の開発ではエラーログを記録する)
            }
        }
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>新規登録</title>
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
            <h2 class="text-center mb-4">新規登録</h2>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">メールアドレス</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="pwf1" class="form-label">パスワード</label>
                    <input type="password" class="form-control" id="pwf1" name="pwf1" required>
                </div>
                <div class="mb-3">
                    <label for="pwf2" class="form-label">パスワード（確認用）</label>
                    <input type="password" class="form-control" id="pwf2" name="pwf2" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-custom-primary btn-lg">登録する</button>
                </div>
            </form>

            <hr>
            
            <p class="text-center mb-0">
                アカウントをお持ちですか？ <a href="login.php">ログインはこちら</a>
            </p>
            <p class="text-center mt-2">
                <a href="index.php">トップページに戻る</a>
            </p>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>