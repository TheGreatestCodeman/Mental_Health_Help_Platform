<?php
require_once("database.php");
session_start();


$user_id = $_SESSION['user_id'] ?? 8;

$message = "";

if (isset($_POST['save_mood'])) {

    $mood = $_POST['mood'];
    $today = date('Y-m-d');

    $check = "
        SELECT * FROM daily_mood 
        WHERE user_id = $user_id 
        AND checkin_date = '$today'
    ";
    $result = mysqli_query($mysqli, $check);

    if (mysqli_num_rows($result) > 0) {
        $message = "You already checked in today 🌼";
    } else {
        $insert = "
            INSERT INTO daily_mood (user_id, mood, checkin_date)
            VALUES ($user_id, '$mood', '$today')
        ";
        mysqli_query($mysqli, $insert);
        $message = "Mood saved successfully 🎉";
    }
}

$query1 = "SELECT checkin_date 
          FROM daily_mood 
          WHERE user_id = $user_id 
          ORDER BY checkin_date DESC";

$result1 = mysqli_query($mysqli, $query1);

$streak = 0;
$expected_date = date("Y-m-d"); 

while ($row = mysqli_fetch_assoc($result1)) {
    if ($row['checkin_date'] == $expected_date) {
        $streak++;
        $expected_date = date("Y-m-d", strtotime(datetime: $expected_date . " -1 day"));
    } else {
        break;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Daily Mood Check-in</title>

<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Fredoka', sans-serif;
    background: linear-gradient(135deg,#89f7fe,#66a6ff);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

.card{
    background:white;
    width:420px;
    padding:30px;
    border-radius:25px;
    box-shadow:0 20px 40px rgba(0,0,0,0.15);
    text-align:center;
}

.card h1{
    margin:0;
    font-size:28px;
    color:#333;
}

.card p{
    margin-top:10px;
    color:#666;
}

.moods{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:15px;
    margin:25px 0;
}

.mood-btn{
    font-size:30px;
    padding:15px;
    border:none;
    border-radius:15px;
    cursor:pointer;
    background:#f1f5f9;
    transition:0.3s;
}

.mood-btn:hover{
    transform:scale(1.15);
    background:#e0f2fe;
}

button.submit{
    margin-top:15px;
    padding:12px 25px;
    border:none;
    border-radius:20px;
    background:linear-gradient(45deg,#667eea,#764ba2);
    color:white;
    font-size:16px;
    cursor:pointer;
    box-shadow:0 10px 25px rgba(102,126,234,0.4);
}

button.submit:hover{
    transform:scale(1.05);
}

.message{
    margin-top:15px;
    background:#dcfce7;
    color:#166534;
    padding:10px;
    border-radius:10px;
}

input[type="radio"]{
    display:none;
}

label{
    cursor:pointer;
}

input[type="radio"]:checked + label .mood-btn{
    background:#a5f3fc;
    box-shadow:0 0 0 3px #38bdf8;
}
.history-btn {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 12px 20px;
    border-radius: 20px;
    font-size: 14px;
    box-shadow: 0 8px 20px rgba(102,126,234,0.4);
}

.history-btn:hover {
    transform: scale(1.05);
}
.streak-box {
    background: linear-gradient(135deg, #ff9800, #ff5722);
    color: white;
    padding: 25px;
    border-radius: 18px;
    text-align: center;
    max-width: 300px;
    margin: 30px auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.streak-box span {
    font-size: 42px;
    font-weight: bold;
}


</style>
</head>

<body>





<div class="card">
    <a href="user_dashboard.php" class="history-btn">⬅ Back to Dashboard</a>
    <h1>How are you feeling today? 🌈</h1>
    <p>Tap one emoji to check in</p>

    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>


    <form method="POST">

        <div class="moods">

            <input type="radio" name="mood" value="Very Happy" id="m1" required>
            <label for="m1"><div class="mood-btn">😄</div></label>

            <input type="radio" name="mood" value="Happy" id="m2">
            <label for="m2"><div class="mood-btn">🙂</div></label>

            <input type="radio" name="mood" value="Neutral" id="m3">
            <label for="m3"><div class="mood-btn">😐</div></label>

            <input type="radio" name="mood" value="Sad" id="m4">
            <label for="m4"><div class="mood-btn">😔</div></label>

            <input type="radio" name="mood" value="Stressed" id="m5">
            <label for="m5"><div class="mood-btn">😣</div></label>

            <input type="radio" name="mood" value="Angry" id="m6">
            <label for="m6"><div class="mood-btn">😡</div></label>

        </div>

        <button class="submit" name="save_mood">Save My Mood</button>
        <a href="user_mood_history.php" class="history-btn">
            📖 View Mood History
        </a>


    </form>
    <div class="streak-box">
        <h2>🔥 Mood Streak</h2>
        <p><span><?php echo $streak; ?></span> Days in a Row</p>
    </div>
</div>

</body>
</html>
