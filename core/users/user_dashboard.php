<?php
require_once('database.php');
session_start();
// I will use the below code line to determine user id after connecting this page to login page.
$user_id = $_SESSION['user_id']??8;
//$user_id = 8;

$releasedQuery = "SELECT COUNT(*) AS total FROM future_letters 
                  WHERE user_id = $user_id AND is_released = 1";
$releasedResult = mysqli_query($mysqli, $releasedQuery);
$released = mysqli_fetch_assoc($releasedResult)['total'];


$pendingQuery = "SELECT COUNT(*) AS total FROM future_letters 
                 WHERE user_id = $user_id AND is_released = 0";
$pendingResult = mysqli_query($mysqli, $pendingQuery);
$pending = mysqli_fetch_assoc($pendingResult)['total'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<div class="dashboard">

    <div class="header">
        <h1>🌱 Welcome Back!</h1>
        <p>Your mental wellness space</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h2>📩</h2>
            <p>Letters Received</p>
            <span><?php echo $released; ?></span>

        </div>

        <div class="stat-card">
            <h2>🕒</h2>
            <p>Letters Pending</p>
            <span><?php echo $pending; ?></span>

        </div>

        <div class="stat-card">
            <h2>⭐</h2>
            <p>Wellness Points</p>
            <span>120</span>
        </div>
    </div>

   
    <div class="card">
        <h2>💌 Letters From Your Past Self</h2>

        <div class="letter">
            <?php
                $letterQuery = "SELECT letter_text, created_at 
                                FROM future_letters 
                                WHERE user_id = $user_id AND is_released = 1
                                ORDER BY created_at DESC limit 3";

                $result = mysqli_query($mysqli, $letterQuery);

                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <div class="letter">
                        <h4>📅 Written on: <?php echo $row['created_at']; ?></h4>
                        <p><?php echo nl2br($row['letter_text']); ?></p>
                    </div>
            <?php
                }
            ?>

        </div>

    </div>

    <div class="actions">
        <a href="write_letter.php" class="btn">✍️ Write a New Letter</a>
        <a href="wellness_quiz.php" class="btn secondary">📜 Take Daily Wellness Quiz</a>
        <a href="#" class="btn">👨‍👨‍👦‍👦 View Groups</a>
        <a href="#" class="btn secondary">📌 Book Appointments </a>
    </div>

</div>

</body>
</html>
