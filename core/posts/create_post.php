<?php
session_start();
require_once __DIR__ . '/../../config/db.php';


if (!isset($_SESSION['user_id'])) {
     $_SESSION['user_id'] = 1;
}

$content = trim($_POST['content']);

if ($content === '') {
    die("Post content cannot be empty");
}

$stmt = $pdo->prepare(
    "INSERT INTO posts (user_id, content) VALUES (?, ?)"
);
$stmt->execute([$_SESSION['user_id'], $content]);

echo "Post created successfully";
