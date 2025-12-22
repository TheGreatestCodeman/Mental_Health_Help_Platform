<?php
include "database.php";
session_start();

// I will use the below code line to determine user id after connecting this page to login page.
//$user_id = $_SESSION['user_id'];
$user_id = 8;

$success = "";

if (isset($_POST['save_letter'])) {

    $letter_text = mysqli_real_escape_string($mysqli, $_POST['letter_text']);
    $release_date = $_POST['release_date'];

    if (!empty($letter_text) && !empty($release_date)) {

        $query = "INSERT INTO future_letters 
                  (user_id, letter_text, release_date, is_released)
                  VALUES ($user_id, '$letter_text', '$release_date', 0)";

        mysqli_query($mysqli, $query);

        $success = "Your letter has been saved and scheduled 💌";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Write Letter</title>
    <link rel="stylesheet" href="user.css"> 
</head>
<body>

<div class="dashboard">

    <div class="sidebar">
        <h2>MentalCare</h2>

        <a href="user_dashboard.php" class="sidebar-btn active">
            🏠 Dashboard
        </a>

    </div>



    <div class="main">
        <h2>✍️ Write a Letter to Your Future Self</h2>

        <?php if ($success): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">

                <h3>📝 Your Letter</h3><br>
                <textarea name="letter_text" rows="8" required
                    placeholder="Write your thoughts here..."
                    style="width:100%; padding:15px; border-radius:8px;"></textarea>

                <br><br>

                <label>Release Date</label><br>
                <input type="date" name="release_date" required
                       style="padding:10px; border-radius:6px;">

                <br><br>

                <button type="submit" name="save_letter"
                        style="padding:10px 20px; background:#4CAF50;
                               color:white; border:none; border-radius:6px;">
                    Save Letter
                </button>
            </form>
        </div>

    </div>
</div>

</body>
</html>

