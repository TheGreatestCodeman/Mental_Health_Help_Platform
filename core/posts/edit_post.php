<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// TEMP DEV LOGIN
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$post_id = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if (!$post_id || $content === '') {
    die("Invalid request");
}

// Ensure user owns the post
$stmt = $pdo->prepare(
    "UPDATE posts 
     SET content = ?
     WHERE id = ? AND user_id = ?"
);

$stmt->execute([$content, $post_id, $_SESSION['user_id']]);

if ($stmt->rowCount() === 0) {
    die("Unauthorized or no changes made");
}

echo "Post updated successfully";
