<?php
session_start(); // セッションを開始

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// データベース接続 (db_config.php はエラーを出す設定済み)
require 'db_config.php';
$user_id = $_SESSION['user_id'];

// POSTリクエストかどうかをチェック
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // フォームから送信されたステータスの配列を取得
    $statuses = $_POST['statuses'] ?? [];

    if (empty($statuses)) {
        // もしデータが空なら、何もせずマップに戻る
        header("Location: map.php");
        exit;
    }

    // データベースへの保存処理 (トランザクションを使用)
    try {
        // トランザクション開始
        $pdo->beginTransaction();

        // 1. このユーザーの古いデータを「すべて削除」する
        $stmt_delete = $pdo->prepare("DELETE FROM map_status WHERE user_id = ?");
        $stmt_delete->execute([$user_id]);

        // 2. 新しいデータを「すべて挿入」する準備
        $sql_insert = "INSERT INTO map_status (user_id, prefecture_code, status_point) VALUES (?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert);

        // 3. 47都道府県のデータ（$statuses 配列）をループで挿入
        foreach ($statuses as $prefecture_code => $status_point) {
            
            // 点数が0（未踏）のデータは、DBに入れる必要がないのでスキップ
            // (お好みで: もし0点も保存したい場合は、この if を消してください)
            if ($status_point == 0) {
                continue;
            }

            // $code が 1〜47 の範囲内か、 $point が 1〜5 の範囲内かチェック
            if (is_numeric($prefecture_code) && $prefecture_code >= 1 && $prefecture_code <= 47 &&
                is_numeric($status_point) && $status_point >= 1 && $status_point <= 5) {
                
                // SQLを実行
                $stmt_insert->execute([$user_id, $prefecture_code, $status_point]);
            }
        }

        // 4. すべての処理が成功したら、変更を確定（コミット）
        $pdo->commit();

    } catch (Exception $e) {
        // 5. エラーが発生したら、すべての変更を元に戻す（ロールバック）
        $pdo->rollBack();
        
        // エラーメッセージを表示
        die("データベースの保存に失敗しました: " . htmlspecialchars($e->getMessage()));
    }
}

// 保存処理が終わったら、マップページにリダイレクトして戻る
header("Location: map.php");
exit;
?>