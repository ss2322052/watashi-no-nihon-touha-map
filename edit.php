<?php
session_start(); // セッションを開始

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db_config.php'; // データベース接続
$user_id = $_SESSION['user_id'];

// --- 都道府県とステータスの定義 ---

// 1. 全47都道府県のリスト (コード, 名前)
// (prefecture_code は 1から47)
$prefectures = [
    1 => '北海道', 2 => '青森', 3 => '岩手', 4 => '宮城', 5 => '秋田', 6 => '山形', 7 => '福島',
    8 => '茨城', 9 => '栃木', 10 => '群馬', 11 => '埼玉', 12 => '千葉', 13 => '東京', 14 => '神奈川',
    15 => '新潟', 16 => '富山', 17 => '石川', 18 => '福井', 19 => '山梨', 20 => '長野', 21 => '岐阜',
    22 => '静岡', 23 => '愛知', 24 => '三重', 25 => '滋賀', 26 => '京都', 27 => '大阪', 28 => '兵庫',
    29 => '奈良', 30 => '和歌山', 31 => '鳥取', 32 => '島根', 33 => '岡山', 34 => '広島', 35 => '山口',
    36 => '徳島', 37 => '香川', 38 => '愛媛', 39 => '高知', 40 => '福岡', 41 => '佐賀', 42 => '長崎',
    43 => '熊本', 44 => '大分', 45 => '宮崎', 46 => '鹿児島', 47 => '沖縄'
];

// 2. ステータス（点数）の定義
$status_levels = [
    5 => '住んだ',
    4 => '泊まった',
    3 => '歩いた',
    2 => '降り立った',
    1 => '通過した',
    0 => '行ってない'
];

// --- データベースから現在のステータスを読み込む ---

// ユーザーの現在のステータスをDBから取得
$stmt = $pdo->prepare("SELECT prefecture_code, status_point FROM map_status WHERE user_id = ?");
$stmt->execute([$user_id]);
$current_statuses_raw = $stmt->fetchAll();

// 扱いやすい形 [コード => 点数] に変換する
$current_statuses = [];
foreach ($current_statuses_raw as $row) {
    $current_statuses[$row['prefecture_code']] = $row['status_point'];
}

?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>マップ編集</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fdfaf6; }
        .form-wrapper { max-width: 800px; margin: 30px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .pref-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 15px; }
        .pref-item { padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; }
        .pref-name { font-weight: bold; margin-bottom: 10px; display: block; }
        .form-check-label { font-size: 0.9em; }
        .form-check-input:checked { background-color: #f4a261; border-color: #f4a261; }

        /* バッジの固定表示 */
        .badge-wrapper {
            position: sticky;
            top: 0;
            background-color: #fdfaf6;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-wrapper">

            <!-- ここがスクロールしても上部に固定されるバッジ -->
            <div class="badge-wrapper text-center mb-3">
                <span class="badge bg-danger">5: 住んだ</span>
                <span class="badge bg-warning text-dark">4: 泊まった</span>
                <span class="badge bg-success">3: 歩いた</span>
                <span class="badge bg-info">2: 降り立った</span>
                <span class="badge bg-primary">1:通過</span>
                <span class="badge bg-light text-dark">0:未踏</span>
            </div>

            <h2 class="text-center mb-4">マップ編集</h2>

            <form action="save_map.php" method="POST">

                <div class="pref-grid mb-4">
                    <?php foreach ($prefectures as $code => $name): ?>
                        <div class="pref-item">
                            <span class="pref-name"><?php echo htmlspecialchars($name); ?></span>
                            
                            <?php $current_point = $current_statuses[$code] ?? 0; ?>

                            <?php foreach ($status_levels as $point => $label): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" 
                                           name="statuses[<?php echo $code; ?>]" 
                                           id="pref-<?php echo $code; ?>-<?php echo $point; ?>" 
                                           value="<?php echo $point; ?>"
                                           <?php if ($current_point == $point) echo 'checked'; ?> >
                                    <label class="form-check-label" for="pref-<?php echo $code; ?>-<?php echo $point; ?>">
                                        <?php echo $point; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-grid gap-2 col-6 mx-auto">
                    <button type="submit" class="btn btn-lg" style="background-color: #f4a261; color: white;">保存する</button>
                    <a href="map.php" class="btn btn-secondary btn-lg">マップに戻る</a>
                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
