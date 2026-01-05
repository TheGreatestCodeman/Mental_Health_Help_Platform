<?php
//require_once(__DIR__ . "/../../config/database.php");
require_once("database.php");
session_start();


//if (!isset($_SESSION['c_id'])) {
  //  header("Location: counselor_login.php");
    //exit;
//}



$c_id = $_SESSION['c_id'] ?? 1;


$sql = "SELECT * FROM counselor WHERE c_id = $c_id";
$result = mysqli_query($mysqli, $sql);
$counselor = mysqli_fetch_assoc($result);
?>



<!DOCTYPE html>
<html>
<head>
    <title>Counselor Dashboard</title>
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

        .logout:hover {
            transform: scale(1.05);
            box-shadow:
                0 0 15px rgba(255,65,108,0.8),
                0 0 30px rgba(255,75,43,0.7);
        }




        .container {
            padding: 40px;
            max-width: 1200px;
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

        .welcome h1 {
            margin: 0;
            font-size: 32px;
        }

        .welcome p {
            margin-top: 10px;
            opacity: 0.9;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 120px;
            height: 120px;
            background: rgba(255, 182, 193, 0.3);
            border-radius: 50%;
        }

        .card:hover {
            transform: translateY(-12px) scale(1.04);
            box-shadow:
            0 20px 40px rgba(0,0,0,0.15),
            0 0 30px rgba(102, 126, 234, 0.4);
        }


        .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .card h3 {
            margin: 0;
            color: #444;
        }

        .card p {
            color: #777;
            font-size: 14px;
            margin: 10px 0 20px;
        }

        .card a {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 22px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 10px 25px rgba(102,126,234,0.4);
        }

        .card a:hover {
            transform: scale(1.08);
            box-shadow:
                0 0 15px rgba(102,126,234,0.7),
                0 0 30px rgba(118,75,162,0.6);
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

        .s1 {
            width: 180px;
            height: 180px;
            background: #ff9a9e;
            top: 10%;
            left: 5%;
        }

        .s2 {
            width: 250px;
            height: 250px;
            background: #a18cd1;
            top: 60%;
            left: 10%;
            animation-duration: 22s;
        }

        .s3 {
            width: 200px;
            height: 200px;
            background: #84fab0;
            top: 30%;
            right: 10%;
            animation-duration: 20s;
        }

        .s4 {
            width: 300px;
            height: 300px;
            background: #fbc2eb;
            bottom: 10%;
            right: 5%;
            animation-duration: 26s;
        }

        .s5 {
            width: 150px;
            height: 150px;
            background: #ffdde1;
            bottom: 40%;
            left: 40%;
            animation-duration: 24s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-40px) translateX(20px);
            }
            100% {
                transform: translateY(0) translateX(0);
            }
        }

        .navbar,
        .container,
        footer {
            position: relative;
            z-index: 1;
        }

        a, button {
            transition: all 0.25s ease;
        }



    </style>
</head>

<body>

<div class="background-shapes">
    <span class="shape s1"></span>
    <span class="shape s2"></span>
    <span class="shape s3"></span>
    <span class="shape s4"></span>
    <span class="shape s5"></span>
</div>


<div class="navbar">
    <h2>🧠 Counselor Hub</h2>
    <a href="counselor_logout.php" class="logout">Logout</a>
</div>

<div class="container">

    <div class="welcome">
        <h1>Hey <?= htmlspecialchars($counselor['name']) ?>! 🌟</h1>
        <p>You’re making a difference today. Let’s help some minds heal 💙</p>
    </div>

    <div class="cards">

        <div class="card">
            <div class="icon">📅</div>
            <h3>Appointments</h3>
            <p>Check upcoming sessions and manage bookings.</p>
            <a href="counselor_appointments.php">Open</a>
        </div>

        <div class="card">
            <div class="icon">🧑‍🤝‍🧑</div>
            <h3>My Clients</h3>
            <p>View assigned users and session history.</p>
            <a href="counselor_clients.php">View</a>
        </div>

        <div class="card">
            <div class="icon">📝</div>
            <h3>Session Notes</h3>
            <p>Write private notes after counseling sessions.</p>
            <a href="counselor_session_notes.php">Write</a>
        </div>

        <div class="card">
            <div class="icon">⚙️</div>
            <h3>Profile & Availability</h3>
            <p>Update your profile and available time slots.</p>
            <a href="counselor_profile.php">Edit</a>
        </div>

    </div>
</div>

<footer>
    💖 Mental Health Support Platform · Counselor Dashboard
</footer>

</body>
</html>
