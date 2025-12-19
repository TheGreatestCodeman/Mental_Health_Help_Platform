<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// TEMP DEV LOGIN
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    die("Invalid request");
}

$stmt = $pdo->prepare(
    "DELETE FROM posts WHERE id = ? AND user_id = ?"
);

$stmt->execute([$post_id, $_SESSION['user_id']]);

if ($stmt->rowCount() === 0) {
    die("Unauthorized or post not found");
}

echo "Post deleted successfully";
