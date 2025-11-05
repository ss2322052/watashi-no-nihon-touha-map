<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db_config.php';

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

$stmt_sum = $pdo->prepare("SELECT SUM(status_point) AS total_points FROM map_status WHERE user_id = ?");
$stmt_sum->execute([$user_id]);
$total_points = $stmt_sum->fetchColumn() ?? 0;

$stmt_map = $pdo->prepare("SELECT prefecture_code, status_point FROM map_status WHERE user_id = ?");
$stmt_map->execute([$user_id]);
$rows = $stmt_map->fetchAll();

$map_data = [];
foreach ($rows as $r) {
    $map_data[(int)$r['prefecture_code']] = (int)$r['status_point'];
}

for ($i = 1; $i <= 47; $i++) {
    if (!isset($map_data[$i])) $map_data[$i] = 0;
}

// JSON_HEX_TAG を追加してセキュリティを強化
$map_data_json = json_encode($map_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);

$prefectures_jp = [
    1=>'北海道', 2=>'青森', 3=>'岩手', 4=>'宮城', 5=>'秋田', 6=>'山形', 7=>'福島',
    8=>'茨城', 9=>'栃木', 10=>'群馬', 11=>'埼玉', 12=>'千葉', 13=>'東京', 14=>'神奈川',
    15=>'新潟', 16=>'富山', 17=>'石川', 18=>'福井', 19=>'山梨', 20=>'長野', 21=>'岐阜',
    22=>'静岡', 23=>'愛知', 24=>'三重', 25=>'滋賀', 26=>'京都', 27=>'大阪', 28=>'兵庫',
    29=>'奈良', 30=>'和歌山', 31=>'鳥取', 32=>'島根', 33=>'岡山', 34=>'広島', 35=>'山口',
    36=>'徳島', 37=>'香川', 38=>'愛媛', 39=>'高知', 40=>'福岡', 41=>'佐賀', 42=>'長崎',
    43=>'熊本', 44=>'大分', 45=>'宮崎', 46=>'鹿児島', 47=>'沖縄'
];
// JSON_HEX_TAG を追加してセキュリティを強化
$prefectures_jp_json = json_encode($prefectures_jp, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<title>わたしの日本踏破マップ</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://unpkg.com/japan-map-js@1.0.1/dist/jpmap.min.js"></script>

<style>
    body { background:#fdfaf6; font-family: "Hiragino Sans", sans-serif; }
    #japan-map {
        max-width: 800px;
        width: 100%;
        height: auto;
        margin: 0 auto;
        background: #fdfaf6 !important;
        border: none;
        padding: 0;
    }

    #japan-map svg {
        width: 100%;
        height: auto;
    }

    .legend {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }
    .legend span {
        margin: 0;
    }
</style>
</head>
<body>

<!-- ▼ ナビバー（右上にログアウトボタン付き） -->
<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="map.php">わたしの日本踏破マップ</a>
    <div class="ms-auto d-flex align-items-center">
      <span class="me-3 text-muted small">
        <?php echo htmlspecialchars($user_email, ENT_QUOTES, 'UTF-8'); ?>
      </span>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">ログアウト</a>
    </div>
  </div>
</nav>
<!-- ▲ ナビバーここまで -->

<div class="container mt-4 text-center">
    <h5>わたしの日本踏破マップ</h5>

    <div class="legend mt-3">
        <span style="background:#e76f51; padding:3px 7px; border-radius:3px;">5: 住んだ</span>
        <span style="background:#f4a261; padding:3px 7px; border-radius:3px;">4: 泊まった</span>
        <span style="background:#fde74c; padding:3px 7px; border-radius:3px;">3: 歩いた</span>
        <span style="background:#89b0ae; padding:3px 7px; border-radius:3px;">2: 降り立った</span>
        <span style="background:#a2d7dd; padding:3px 7px; border-radius:3px;">1: 通過した</span>
        <span style="background:#f0f0f0; padding:3px 7px; border-radius:3px;">0: 行ってない</span>
    </div>

    <p class="mt-3">合計点： <strong><?php echo htmlspecialchars($total_points, ENT_QUOTES); ?>点</strong></p>
    <div id="japan-map" class="mt-3"></div>
    
    <a href="edit.php" class="btn btn-primary mt-3">編集する</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const mapData = <?php echo $map_data_json; ?>;
    const prefNames = <?php echo $prefectures_jp_json; ?>;

    const colorRules = {
        0: '#f0f0f0',
        1: '#a2d7dd',
        2: '#89b0ae',
        3: '#fde74c',
        4: '#f4a261',
        5: '#e76f51'
    };

    const container = document.getElementById("japan-map");
    container.innerHTML = "";

    const areas = [];
    for(let i=1; i<=47; i++) {
        const pt = mapData[i];
        areas.push({
            code: i,
            name: prefNames[i],
            color: colorRules[pt],
            hoverColor: "#fff"
        });
    }

    new jpmap.japanMap(container, {
        width: 800,
        height: 600,
        areas: areas,
        showsPrefectureName: false,
        movesIslands: true,
        onSelect: function(data){
            window.location.href = "edit.php?pref=" + data.area.code;
        }
    });
});
</script>
</body>
</html>
