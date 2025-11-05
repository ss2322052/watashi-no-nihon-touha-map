<?php
/*
 * テーブル作成用の特別なファイル（1回だけ実行します）
 */

// エラーを全部表示する（デバッグ用）
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>データベース設定中...</h1>";

// データベース接続ファイルを読み込む
require 'db_config.php';

// 実行するSQLコード（keikenchi.ddlの中身と同じ）
$sql_commands = "
    DROP TABLE IF EXISTS map_status;
    DROP TABLE IF EXISTS users CASCADE;

    CREATE TABLE users (
      user_id SERIAL PRIMARY KEY,
      email VARCHAR(255) NOT NULL UNIQUE,
      password_hash VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE map_status (
      status_id SERIAL PRIMARY KEY,
      user_id INT NOT NULL,
      prefecture_code INT NOT NULL,
      status_point INT NOT NULL DEFAULT 0,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
      UNIQUE (user_id, prefecture_code)
    );
";

// SQLを実行する
try {
    $pdo->exec($sql_commands);

    echo "<h2>成功！</h2>";
    echo "<p><code>users</code> テーブルと <code>map_status</code> テーブルを作成しました。</p>";
    echo "<p><strong><a href='register.php'>新規登録ページ</a></strong> に進んで、テストしてみてください。</p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>エラー...</h2>";
    echo "<p>テーブル作成に失敗しました: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>