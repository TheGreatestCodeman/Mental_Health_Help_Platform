<?php
require_once("database.php");
session_start();


$c_id = $_SESSION['c_id'] ?? 1;


$user_id = $_GET['user_id'] ?? 0;


$user_sql = "SELECT name, email FROM users WHERE user_id = $user_id";
$user_result = mysqli_query($mysqli, $user_sql);
$client = mysqli_fetch_assoc($user_result);


$sql = "
SELECT 
    a.appointment_id,
    a.appointment_date,
    a.appointment_time,
    a.status,
    sn.notes
FROM appointments a
LEFT JOIN session_notes sn ON a.appointment_id = sn.appointment_id
WHERE a.user_id = $user_id
AND a.counselor_id = $c_id
ORDER BY a.appointment_date DESC
";

$result = mysqli_query($mysqli, $sql);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Client History</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(120deg, #fdfbfb, #ebedee);
            padding: 40px;
        }

        h1 {
            color: #333;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .status {
            font-weight: bold;
            color: green;
        }

        .notes {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
            white-space: pre-line;
        }

        a {
            text-decoration: none;
            color: #3498db;
        }
    </style>
</head>

<body>

<h1>📘 <?= htmlspecialchars($client['name']) ?> — Appointment History</h1>
<p><?= htmlspecialchars($client['email']) ?></p>

<hr><br>

<?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card">
            <p>📅 <?= $row['appointment_date'] ?> | ⏰ <?= $row['appointment_time'] ?></p>
            <p class="status">Status: <?= ucfirst($row['status']) ?></p>

            <?php if (!empty($row['notes'])): ?>
                <div class="notes">
                    📝 <strong>Session Notes:</strong><br>
                    <?= htmlspecialchars($row['notes']) ?>
                </div>
            <?php else: ?>
                <p><em>No session notes written.</em></p>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No appointment history found.</p>
<?php endif; ?>

<br>
<a href="counselor_clients.php">← Back to My Clients</a>

</body>
</html>
