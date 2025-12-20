<?php
session_start();
$mysqli = require __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$content = trim($_POST['content'] ?? '');

if ($content === '') {
    die("Post content cannot be empty");
}

$user_id = $_SESSION['user_id'];

/* STEP 1: Insert into post table */
$stmt = $mysqli->prepare(
    "INSERT INTO post (post_from_past) VALUES (?)"
);
$stmt->bind_param("s", $content);
$stmt->execute();

$post_id = $stmt->insert_id;
$stmt->close();

/* STEP 2: Map user to post */
$stmt = $mysqli->prepare(
    "INSERT INTO can_create (user_id, post_id) VALUES (?, ?)"
);
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$stmt->close();

echo "Post created successfully";
