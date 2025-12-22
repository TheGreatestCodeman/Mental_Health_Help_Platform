<?php
$totalScore = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $totalScore =
        ($_POST['q1'] ?? 0) +
        ($_POST['q2'] ?? 0) +
        ($_POST['q3'] ?? 0) +
        ($_POST['q4'] ?? 0) +
        ($_POST['q5'] ?? 0);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daily Wellness Quiz</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<div class="dashboard">

    <div class="sidebar">
        <h2>MentalCare</h2>
        <a href="user_dashboard.php" class="sidebar-btn">🏠 Dashboard</a>
    </div>

   
    <div class="main">
        <h2>🧠 Daily Wellness Check-in</h2>
        <p class="subtitle">Answer honestly. No right or wrong answers 💙</p>

        <div class="card quiz-card">

            <form method="POST">

                <div class="quiz-question">
                    <p>1️⃣ How stressed do you feel today?</p>
                    <label><input type="radio" name="q1" value="1" required> Very stressed</label>
                    <label><input type="radio" name="q1" value="2"> Somewhat stressed</label>
                    <label><input type="radio" name="q1" value="3"> Calm</label>
                    <label><input type="radio" name="q1" value="4"> Very calm</label>
                </div>

                <div class="quiz-question">
                    <p>2️⃣ How was your sleep last night?</p>
                    <label><input type="radio" name="q2" value="1" required> Very poor</label>
                    <label><input type="radio" name="q2" value="2"> Not great</label>
                    <label><input type="radio" name="q2" value="3"> Good</label>
                    <label><input type="radio" name="q2" value="4"> Very good</label>
                </div>

                <div class="quiz-question">
                    <p>3️⃣ How motivated do you feel today?</p>
                    <label><input type="radio" name="q3" value="1" required> Not motivated</label>
                    <label><input type="radio" name="q3" value="2"> Slightly motivated</label>
                    <label><input type="radio" name="q3" value="3"> Motivated</label>
                    <label><input type="radio" name="q3" value="4"> Very motivated</label>
                </div>

                <div class="quiz-question">
                    <p>4️⃣ How connected do you feel with others?</p>
                    <label><input type="radio" name="q4" value="1" required> Very isolated</label>
                    <label><input type="radio" name="q4" value="2"> Somewhat isolated</label>
                    <label><input type="radio" name="q4" value="3"> Connected</label>
                    <label><input type="radio" name="q4" value="4"> Very connected</label>
                </div>

                <div class="quiz-question">
                    <p>5️⃣ How much do you want to kill yourself?</p>
                    <label><input type="radio" name="q5" value="1" required> I'm already dead</label>
                    <label><input type="radio" name="q5" value="2"> Thinking of jumping from Bracu Rooftop</label>
                    <label><input type="radio" name="q5" value="3"> I am too coward to kill myself</label>
                    <label><input type="radio" name="q5" value="4"> Not me! Only losers kill themselves</label>
                </div>

                <button type="submit" class="btn primary-btn">
                    Submit Quiz
                </button>
            </form>

      
            <?php if ($totalScore !== null): ?>
                <div class="result-box">
                    <h3>Your Wellness Score: <?php echo $totalScore; ?>/20</h3>

                    <?php
                        if ($totalScore == 5) echo "<p>💀 Just Kill Yourself Already!</p>";
                        elseif ($totalScore <= 10) echo "<p>💙 Take it easy today. You're not alone.</p>";
                        elseif ($totalScore <= 16) echo "<p>🙂 You're doing okay. Small self-care helps.</p>";
                        else echo "<p>🌟 You're feeling great today! Keep it up.</p>";
                    ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
