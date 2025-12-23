<?php
session_start();
$mysqli = require __DIR__ . "/database.php"; 

// login status
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

// check id
if (!$post_id) {
    die("Invalid request");
}


$stmt = $mysqli->prepare("SELECT 1 FROM can_create WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    die("Unauthorized or post not found");
}
$stmt->close();


$stmt = $mysqli->prepare("DELETE FROM can_create WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

// post delte
$stmt = $mysqli->prepare("DELETE FROM post WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();


header("Location: index.php");
exit;