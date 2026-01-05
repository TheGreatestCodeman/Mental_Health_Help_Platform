<?php
require_once("database.php");
session_start();

$appointment_id = $_GET['appointment_id'] ?? 0;

$sql = "
SELECT 
    a.appointment_date,
    a.appointment_time,
    u.name AS user_name,
    sn.notes
FROM appointments a
JOIN users u ON a.user_id = u.user_id
JOIN session_notes sn ON a.appointment_id = sn.appointment_id
WHERE a.appointment_id = $appointment_id
";

$result = mysqli_query($mysqli, $sql);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Session Notes</title>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            padding: 40px;
        }

        .box {
            background: white;
            padding: 30px;
            max-width: 700px;
            margin: auto;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        h2 {
            margin-top: 0;
        }

        .meta {
            color: #666;
            margin-bottom: 20px;
        }

        .notes {
            white-space: pre-line;
            line-height: 1.6;
            background: #f7f9fc;
            padding: 20px;
            border-radius: 10px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background: #667eea;
            color: white;
            padding: 10px 18px;
            border-radius: 15px;
        }
    </style>
</head>

<body>

<div class="box">
    <h2>📖 Session Notes</h2>

    <div class="meta">
        Client: <strong><?= htmlspecialchars($data['user_name']) ?></strong><br>
        Date: <?= $data['appointment_date'] ?> |
        Time: <?= $data['appointment_time'] ?>
    </div>

    <div class="notes">
        <?= htmlspecialchars($data['notes']) ?>
    </div>

    <a href="counselor_session_notes.php">← Back</a>
</div>

</body>
</html>
