<?php
session_start();

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h2>📅 Create Event</h2>

<form method="POST" action="event_create.php">
    <label>Event Name</label><br>
    <input type="text" name="name" required><br><br>

    <label>Event Date</label><br>
    <input type="date" name="date" required><br><br>

    <label>Participants</label><br>
    <input type="number" name="participants" min="1" required><br><br>

    <button type="submit">Create Event</button>
</form>

<p><a href="index.php">← Back to Home</a></p>

</body>
</html>

