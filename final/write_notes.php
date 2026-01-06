<?php
require_once("database.php");
session_start();


$c_id = $_SESSION['c_id'] ?? 1;

$appointment_id = $_GET['appointment_id'] ?? null;
$success = "";


$sql = "
SELECT a.*, u.name AS user_name, u.email
FROM appointments a
JOIN users u ON a.user_id = u.user_id
WHERE a.appointment_id = $appointment_id
AND a.counselor_id = $c_id
AND a.status = 'completed'
";

$result = mysqli_query($mysqli, $sql);
$appointment = mysqli_fetch_assoc($result);


if (isset($_POST['save_notes'])) {
    $notes = mysqli_real_escape_string($mysqli, $_POST['notes']);

    if (!empty($notes)) {
        $insert = "
        INSERT INTO session_notes (appointment_id, counselor_id, notes)
        VALUES ($appointment_id, $c_id, '$notes')
        ";
        mysqli_query($mysqli, $insert);

        $success = "Session notes saved successfully 📝";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Write Session Notes</title>
    <meta charset="UTF-8">

    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Fredoka', sans-serif;
            background: radial-gradient(circle at top, #ffecd2, #fcb69f);
            min-height: 100vh;
        }

        .navbar {
            background: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
        }

        .navbar h2 {
            margin: 0;
            color: #ff6f61;
        }

        .container {
            padding: 40px;
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 35px;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .card h3 {
            margin-top: 0;
            color: #444;
        }

        .info {
            background: #f7f9ff;
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 25px;
        }

        textarea {
            width: 100%;
            border-radius: 20px;
            border: 2px solid #ddd;
            padding: 18px;
            font-size: 15px;
            outline: none;
            resize: vertical;
        }

        textarea:focus {
            border-color: #667eea;
        }

        .btn {
            margin-top: 20px;
            background: linear-gradient(45deg, #43cea2, #185a9d);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 25px;
            font-size: 15px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(67,206,162,0.4);
        }

        .btn:hover {
            transform: scale(1.07);
            box-shadow:
                0 0 15px rgba(67,206,162,0.8),
                0 0 30px rgba(24,90,157,0.6);
        }

        .success {
            background: #d4f8e8;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
            color: #065f46;
        }

        .background-shapes {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.25;
            filter: blur(2px);
            animation: float 18s infinite ease-in-out;
        }

        .s1 { width: 180px; height: 180px; background: #ff9a9e; top: 10%; left: 5%; }
        .s2 { width: 250px; height: 250px; background: #a18cd1; top: 60%; left: 10%; }
        .s3 { width: 200px; height: 200px; background: #84fab0; top: 30%; right: 10%; }
        .s4 { width: 300px; height: 300px; background: #fbc2eb; bottom: 10%; right: 5%; }

        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-40px); }
            100% { transform: translateY(0); }
        }

        .navbar, .container {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body>

<div class="background-shapes">
    <span class="shape s1"></span>
    <span class="shape s2"></span>
    <span class="shape s3"></span>
    <span class="shape s4"></span>
</div>

<div class="navbar">
    <h2>🧠 Counselor Hub</h2>
    <a href="counselor_appointments.php" class="btn">← Back to Appointments</a>
    <a href="counselor_session_notes.php" class="btn">← Back to Session Notes</a>
</div>

<div class="container">

    <div class="card">

        <h3>📝 Session Notes</h3>

        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <div class="info">
            <p><strong>Client:</strong> <?= htmlspecialchars($appointment['user_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($appointment['email']) ?></p>
            <p><strong>Appointment Date:</strong> <?= $appointment['appointment_date'] ?></p>
        </div>

        <form method="POST">
            <textarea name="notes" rows="7" placeholder="Write session notes here..." required></textarea>
            <button class="btn" name="save_notes">Save Session Notes</button>
        </form>

    </div>

</div>

</body>
</html>
