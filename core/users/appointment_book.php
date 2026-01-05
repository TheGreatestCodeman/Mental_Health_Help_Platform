<?php
session_start();
require_once(__DIR__ . "/../../config/database.php");
//require_once("database.php");
//if (!isset($_SESSION['user_id'])) {
   // header("Location: ../../login.php");
  //  exit;
//}

$user_id = $_SESSION['user_id']??8;
$counselor_id = $_POST['counselor_id'] ?? 0;
$appointment_time = $_POST['appointment_time'] ?? '';
$appointment_date = $_POST['appointment_date'] ?? '';




$status = 'Pending';

$stmt = $mysqli->prepare(
    "INSERT INTO appointments 
     (user_id, counselor_id, appointment_date, appointment_time, status)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "iisss",
    $user_id,
    $counselor_id,
    $appointment_date,
    $appointment_time,
    $status
);

$stmt->execute();

header("Location: my_appointments.php");
exit;

