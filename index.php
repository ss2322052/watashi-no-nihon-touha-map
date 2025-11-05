<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #fdfaf6; /* 背景色 */
            color: #333; /* 文字色 */
        }
        .navbar-custom {
            background-color: #fdfaf6; /* ナビゲーションバーの背景色 */
        }
        .btn-custom-primary {
            background-color: #f4a261; /* オレンジ系のボタン */
            border: none;
            color: white;
        }
        .btn-custom-secondary {
            background-color: #89b0ae; /* 緑系のボタン */
            border: none;
            color: white;
        }
    </style>

    <title>わたしの日本踏破マップ</title>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">わたしの日本踏破マップ</a>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        
        <h1 class="mb-4">わたしの日本踏破マップ</h1> 
        
        <p class="mb-4">あなたの訪問経験をマップにしよう</p>
        
        <div class="d-grid gap-2 col-6 mx-auto">
            <a href="login.php" class="btn btn-custom-primary btn-lg mb-3">ログイン</a>
            <a href="register.php" class="btn btn-custom-secondary btn-lg">新規登録</a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>