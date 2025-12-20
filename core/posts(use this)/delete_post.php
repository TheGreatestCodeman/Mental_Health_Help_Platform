<?php
session_start();
$mysqli = require __DIR__ . "/database.php"; // adjust path if needed

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

// Validate post_id
if (!$post_id) {
    die("Invalid request");
}

// Check ownership via can_create table
$stmt = $mysqli->prepare("SELECT 1 FROM can_create WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    die("Unauthorized or post not found");
}
$stmt->close();

// Delete mapping first
$stmt = $mysqli->prepare("DELETE FROM can_create WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

// Delete the post
$stmt = $mysqli->prepare("DELETE FROM post WHERE post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

// Redirect back to home.php
header("Location: home.php");
exit;
