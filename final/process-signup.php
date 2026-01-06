<?php

if (empty($_POST["name"])) {
    die("Name is required");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["password"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php";

// Determine signup type (user or admin)
$signup_type = isset($_POST["signup_type"]) ? $_POST["signup_type"] : "user";
$role = "user";

// If admin signup, validate invite code
if ($signup_type === "admin") {
    $valid_invite_codes = ["admin123", "healthhelp2024", "mentalwellness"];
    
    if (empty($_POST["invite_code"])) {
        die("Admin invite code is required");
    }
    
    if (!in_array($_POST["invite_code"], $valid_invite_codes)) {
        die("Invalid admin invite code");
    }
    
    $role = "admin";
}

$sql = "INSERT INTO users (name, email, password_hash, role)
        VALUES (?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("ssss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash,
                  $role);

if ($stmt->execute()) {
    header("Location: signup-success.html");
    exit;
} else {
    if ($mysqli->errno === 1062) {
        die("Email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
