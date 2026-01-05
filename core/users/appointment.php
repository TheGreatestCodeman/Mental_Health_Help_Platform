<?php
session_start();
$mysqli = require __DIR__ . "/../../database.php";
//require_once("database.php");
//if (!isset($_SESSION['user_id'])) {
  //  header("Location: ../../login.php");
    //exit;
//}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Counselor Slots</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="user.css">
</head>
<body>

<h2>🧑‍⚕️ Counselor Availability</h2>

<?php
$query = "
    SELECT ca.availability_id, ca.counselor_id,ca.available_date, ca.start_time, ca.end_time, c.name
    FROM counselor_availability ca
    JOIN counselor c ON ca.counselor_id = c.c_id
    ORDER BY ca.start_time
";
$result = $mysqli->query($query);
?>

<?php if ($result->num_rows > 0): ?>
    <?php while ($slot = $result->fetch_assoc()): ?>
        <div class="appointment-card">
            <p><strong>Counselor:</strong> <?= htmlspecialchars($slot['name']) ?></p>
            <p><strong>Time:</strong> <?= $slot['start_time'] ?> - <?= $slot['end_time'] ?></p>

            <form method="POST" action="appointment_book.php">
                <input type="hidden" name="counselor_id" value="<?= $slot['counselor_id'] ?>">
                <input type="hidden" name="appointment_time" value="<?= $slot['start_time'] ?>">
                <input type="hidden" name="appointment_date" value="<?= $slot['available_date'] ?>">
                <button type="submit" class="btn-link">Book Slot</button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No available slots.</p>
<?php endif; ?>

<p><a href="user_dashboard.php">← Back to Dashboard</a></p>
<p><a href="my_appointments.php">👨 My Appointments</a></p>

</body>
</html>

