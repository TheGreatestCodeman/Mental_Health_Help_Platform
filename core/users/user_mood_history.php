<?php
require_once("database.php");
session_start();


$user_id = $_SESSION['user_id'] ?? 8;

$query = "SELECT mood, checkin_date 
          FROM daily_mood 
          WHERE user_id = $user_id 
          ORDER BY checkin_date DESC";

$result = mysqli_query($mysqli, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Mood History</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #c3cfe2, #f5f7fa);
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 40px 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .mood-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.4s ease;
        }

        .mood {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .note {
            color: #555;
            margin-bottom: 8px;
        }

        .date {
            font-size: 13px;
            color: #888;
        }

        .empty {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 16px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .history-container {
    max-width: 600px;
    margin: auto;
}

.mood-card {
    display: flex;
    align-items: center;
    background: #ffffff;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.mood-emoji {
    font-size: 40px;
    margin-right: 20px;
}

.mood-info h3 {
    margin: 0;
}

.mood-info p {
    margin: 5px 0 0;
    color: gray;
}

    </style>
</head>

<body>

<h2>📅 Your Mood History</h2>
<a href="user_mood_checkin.php" class="btn">⬅ Back to Mood Checking</a>


<div class="history-container">
<?php
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $mood = $row['mood'];
        $date = date("d M Y", strtotime($row['checkin_date']));
?>
    <div class="mood-card">
        <span class="mood-emoji">
            <?php
                if ($mood == "Happy") echo "😊";
                elseif ($mood == "Sad") echo "😔";
                elseif ($mood == "Angry") echo "😠";
                elseif ($mood == "Stressed") echo "😫";
                else echo "😐";
            ?>
        </span>

        <div class="mood-info">
            <h3><?php echo $mood; ?></h3>
            <p><?php echo $date; ?></p>
        </div>
    </div>
<?php
    }
} else {
    echo "<p>No mood history yet.</p>";
}
?>
</div>


</body>
</html>
