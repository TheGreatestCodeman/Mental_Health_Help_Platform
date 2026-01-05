<?php
require_once("database.php");
session_start();


$c_id = $_SESSION['c_id'] ?? 1;
$today = date("Y-m-d");

$sql = "
    SELECT
        appointments.appointment_id, 
        appointments.appointment_date,
        appointments.appointment_time,
        appointments.status,
        users.name,
        users.email
    FROM appointments
    JOIN users ON users.user_id = appointments.user_id
    WHERE appointments.counselor_id = $c_id
    AND appointments.appointment_date >= '$today'
    ORDER BY appointments.appointment_date ASC
";

$result = mysqli_query($mysqli, $sql);


if (isset($_POST['appointment_id'], $_POST['action'])) {

    $appointment_id = $_POST['appointment_id'];

    if ($_POST['action'] == 'accept') {
        $status = "Accepted";
    } else {
        $status = "Rejected";
    }

    $update_sql = "
        UPDATE appointments 
        SET status = '$status'
        WHERE appointment_id = $appointment_id
        AND counselor_id = $c_id
    ";

    mysqli_query($mysqli, $update_sql);

    header("Location: counselor_appointments.php");
    exit;
}

$today_sql = "
    SELECT COUNT(*) AS total
    FROM appointments
    WHERE counselor_id = $c_id
    AND appointment_date = '$today'
";

$today_result = mysqli_query($mysqli, $today_sql);
$today_row = mysqli_fetch_assoc($today_result);
$today_count = $today_row['total'];


$count_sql = "
    SELECT status, COUNT(*) as total
    FROM appointments
    WHERE counselor_id = $c_id
    GROUP BY status
";

$count_result = mysqli_query($mysqli, $count_sql);


$pending = 0;

while ($row = mysqli_fetch_assoc($count_result)) {
    if ($row['status'] == 'Pending') {
        $pending = $row['total'];

    }
}

if (isset($_POST['mark_completed'])) {
    $appointment_id = $_POST['appointment_id'];

    $update = "
        UPDATE appointments 
        SET status = 'completed' 
        WHERE appointment_id = $appointment_id
    ";

    mysqli_query($mysqli, $update);

    header("Location: counselor_appointments.php");
    exit();
}



?>

<!DOCTYPE html>
<html>
<head>
    <title>Counselor Appointments</title>
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

        .logout {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 8px 20px rgba(255,65,108,0.4);
        }

        .container {
            padding: 40px;
            max-width: 1000px;
            margin: auto;
        }

        .welcome {
            background: linear-gradient(135deg, #89f7fe, #66a6ff);
            padding: 30px;
            border-radius: 25px;
            color: #003566;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            margin-bottom: 40px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .card h3 {
            margin-top: 0;
        }

        .card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 18px;
            border-radius: 20px;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .accept {
            background: #4CAF50;
        }

        .reject {
            background: #ff6f61;
        }

        .completed {
            background: #4CAF50;
        }


        footer {
            text-align: center;
            padding: 30px;
            color: #555;
            font-size: 14px;
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

        .s1 { width:180px;height:180px;background:#ff9a9e;top:10%;left:5%; }
        .s2 { width:250px;height:250px;background:#a18cd1;top:60%;left:10%; }
        .s3 { width:200px;height:200px;background:#84fab0;top:30%;right:10%; }
        .s4 { width:300px;height:300px;background:#fbc2eb;bottom:10%;right:5%; }

        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-40px); }
            100% { transform: translateY(0); }
        }

        .navbar, .container, footer {
            position: relative;
            z-index: 1;
        }

        .status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .status.Pending {
            background: #f4b400; 
            }

        .status.Accepted {
            background: #4CAF50; 
            }

        .status.Rejected {
            background: #ff4b5c; 
            }

        .write-notes-btn {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            border-radius: 12px;
            padding: 14px 26px;
            font-weight: 600;
            transition: 0.3s;
            }

        .write-notes-btn:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
            transform: scale(1.05);
            color: #fff;
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
    <a href="logout.php" class="logout">Logout</a>
    <a href="counselor_dashboard.php" class="logout">← Back to Dashboard</a>
</div>

<div class="container">

    <div class="welcome">
        <h1>📅 My Appointments</h1>
        
        <p>View and manage upcoming counseling sessions</p>
    </div>
    
    <div class="card">
        <h3>📆 Today’s Appointments</h3>
            <p style="font-size:15px; font-weight:bold;">
                <?= $today_count ?>
            </p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; margin-bottom:40px;">

    <div class="card">
        <h3>🟡 Pending</h3>
            <p style="font-size:15px; font-weight:bold;"><?= $pending ?></p>
    </div>


</div>



    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card">
                <h3>👤 <?= htmlspecialchars($row['name']) ?></h3>
                <p>Email: <?= htmlspecialchars($row['email']) ?></p>
                <p>Date: <?= $row['appointment_date'] ?></p>
                <p>Time: <?= $row['appointment_time'] ?></p>
                <p>
                    Status:
                    <span class="status <?= $row['status'] ?>">
                    <?= $row['status'] ?>
                    </span>
                </p>

                <?php if ($row['status'] != 'completed') { ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                    <input type="hidden" name="action" value="accept">
                    <button class="accept">Accept</button>
                </form>
                <?php } ?>

                <?php if ($row['status'] != 'completed') { ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                    <input type="hidden" name="action" value="reject">
                    <button class="reject">Reject</button>
                </form>
                <?php } ?>

                <?php if ($row['status'] != 'completed') { ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="appointment_id" value="<?= $row['appointment_id']; ?>">
                    <button type="submit" name="mark_completed" class="completed">
                        Completed
                    </button>
                </form>
                <?php } ?>
                <?php if ($row['status'] == 'completed') { ?>
                    <a href="write_notes.php?appointment_id=<?= $row['appointment_id']; ?>"
                    class="btn btn-lg write-notes-btn">
                    Write Session Notes
                    </a>
                <?php } ?>





            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No appointments yet 🌱</p>
    <?php endif; ?>

</div>

<footer>
    💖 Mental Health Support Platform · Counselor Dashboard
</footer>

</body>
</html>
