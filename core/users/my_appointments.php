<?php
session_start();

//if (!isset($_SESSION['user_id'])) {
  //  header("Location: login.php");
    //exit;
//}

$user_id = $_SESSION['user_id']??8;

require_once __DIR__ . "/../../config/database.php";
//require_once("database.php");

$sql = "
    SELECT 
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.status,
        c.name AS counselor_name,
        c.profile_pic
    FROM appointments a
    JOIN counselor c ON a.counselor_id = c.c_id
    WHERE a.user_id = ?
      AND (
          a.appointment_date > CURDATE()
          OR (a.appointment_date = CURDATE() AND a.appointment_time > CURTIME())
      )
    ORDER BY a.appointment_date ASC, a.appointment_time ASC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:#f5f7fb;
            padding:30px;
        }
        .container {
            max-width:900px;
            margin:auto;
        }
        .card {
            background:white;
            border-radius:14px;
            padding:20px;
            display:flex;
            gap:20px;
            margin-bottom:20px;
            box-shadow:0 8px 20px rgba(0,0,0,0.08);
        }
        .card img {
            width:80px;
            height:80px;
            border-radius:50%;
            object-fit:cover;
        }
        .info h3 {
            margin:0;
            color:#1f2937;
        }
        .info p {
            margin:6px 0;
            color:#555;
        }
        .status {
            margin-left:auto;
            font-weight:600;
            color:#2563eb;
        }
        .empty {
            text-align:center;
            color:#777;
            padding:60px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📅 My Upcoming Appointments</h2>

    <?php if ($result->num_rows === 0): ?>
        <div class="empty">
            No upcoming appointments scheduled.
        </div>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">

            <img src="../../uploads/<?= htmlspecialchars($row['profile_pic']) ?>" alt="Counselor">
            <div class="info">
                <h3><?= htmlspecialchars($row['counselor_name']) ?></h3>
                <p>📆 <?= date("d M Y", strtotime($row['appointment_date'])) ?></p>
                <p>⏰ <?= date("h:i A", strtotime($row['appointment_time'])) ?> </p>
            </div>
            <div class="status">
                <?= ucfirst($row['status']) ?>
            </div>
        </div>
    <?php endwhile; ?>
    <p><a href="user_dashboard.php" class="btn">← Back to Dashboard</a></p>
</div>

</body>
</html>

