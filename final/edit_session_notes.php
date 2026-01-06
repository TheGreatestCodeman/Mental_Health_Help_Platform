<?php
require_once("database.php");
session_start();

$appointment_id = $_GET['appointment_id'] ?? 0;


if (isset($_POST['save_notes'])) {
    $notes = mysqli_real_escape_string($mysqli, $_POST['notes']);

    $update = "
    UPDATE session_notes 
    SET notes = '$notes'
    WHERE appointment_id = $appointment_id
    ";

    mysqli_query($mysqli, $update);

    header("Location: counselor_session_notes.php");
    exit;
}


$sql = "
SELECT 
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
    <title>Edit Session Notes</title>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff3e0;
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

        textarea {
            width: 100%;
            height: 200px;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        button {
            background: #f39c12;
            border: none;
            color: white;
            padding: 12px 22px;
            border-radius: 18px;
            margin-top: 15px;
            cursor: pointer;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>

<body>

<div class="box">
    <h2>✏️ Edit Notes for <?= htmlspecialchars($data['user_name']) ?></h2>

    <form method="POST">
        <textarea name="notes" required><?= htmlspecialchars($data['notes']) ?></textarea>
        <br>
        <button type="submit" name="save_notes">Save Changes</button>
    </form>

    <a href="counselor_session_notes.php" class="button">← Cancel</a>
</div>

</body>
</html>
