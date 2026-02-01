<?php
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');
session_start();

if (empty($_SESSION['login_user_id']) || empty($_POST['target_user_id'])) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

// 自分のIDと、フォローしたい相手のID
$follower_id = $_SESSION['login_user_id'];
$followee_id = $_POST['target_user_id'];

// 自分自身はフォローできないようにする
if ($follower_id == $followee_id) {
    echo json_encode(['status' => 'error', 'message' => '自分はフォローできません']);
    exit;
}

// DBに保存（INSERT IGNOREを使うと、既にフォロー済みでもエラーにならず無視してくれる）
$sql = "INSERT IGNORE INTO user_relationships (follower_user_id, followee_user_id) VALUES (:me, :target)";
$stmt = $dbh->prepare($sql);
$stmt->execute([
    ':me' => $follower_id,
    ':target' => $followee_id
]);

echo json_encode(['status' => 'success']);
