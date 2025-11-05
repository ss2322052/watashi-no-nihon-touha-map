<?php
session_start(); // セッションを開始

// セッション変数をすべて解除する
$_SESSION = array();

// セッションクッキーを削除する
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 最終的にセッションを破棄する
session_destroy();

// トップページ（index.php）にリダイレクト
header("Location: index.php");
exit;
?>