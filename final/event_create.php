<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
$mysqli = require __DIR__ . "/database.php";

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$name = $_POST['name'] ?? '';
$date = $_POST['date'] ?? '';
$participants = $_POST['participants'] ?? 0;
$user_id = $_SESSION['user_id'];

// Basic validation
if ($name === '' || $date === '' || $participants <= 0) {
    header("Location: event.php");
    exit;
}

$stmt = $mysqli->prepare(
    "INSERT INTO events (name, date, participants, user_id)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("ssii", $name, $date, $participants, $user_id);
$stmt->execute();

header("Location: index.php");
exit;
