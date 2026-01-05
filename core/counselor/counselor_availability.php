<?php
require_once("database.php");
session_start();


$c_id = $_SESSION['c_id'] ?? 1;


if (isset($_POST['add_availability'])) {

    $date = $_POST['available_date'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];

    if (!empty($date) && !empty($start) && !empty($end)) {
        $sql = "INSERT INTO counselor_availability 
                (counselor_id, available_date, start_time, end_time)
                VALUES ($c_id, '$date', '$start', '$end')";
        mysqli_query($mysqli, $sql);
    }
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($mysqli, "DELETE FROM counselor_availability WHERE availability_id = $id");
}


$result = mysqli_query(
    $mysqli,
    "SELECT * FROM counselor_availability 
     WHERE counselor_id = $c_id 
     ORDER BY available_date, start_time"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Availability</title>
    <style>
        body {
            font-family: Fredoka, sans-serif;
            background: linear-gradient(135deg, #fdfbfb, #ebedee);
            padding: 40px;
        }

        .box {
            background: white;
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        h2 {
            color: #667eea;
        }

        input, button {
            padding: 10px;
            margin: 5px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        button {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            cursor: pointer;
        }

        table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background: #667eea;
            color: white;
        }

        .delete {
            background: #ff4b5c;
            padding: 8px 14px;
            color: white;
            border-radius: 10px;
            text-decoration: none;
        }

.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    background: linear-gradient(45deg, #ff9a9e, #fad0c4);
    color: #333;
    padding: 10px 18px;
    border-radius: 18px;
    text-decoration: none;
    font-size: 14px;
    box-shadow: 0 8px 20px rgba(255,154,158,0.4);
}

.back-btn:hover {
    transform: scale(1.05);
    box-shadow:
        0 0 15px rgba(255,154,158,0.8),
        0 0 25px rgba(250,208,196,0.7);
}


    </style>
</head>

<body>

<div class="box">
    <a href="counselor_profile.php" class="back-btn">⬅ Back to Profile</a>

    <h2>🕒 Manage Availability</h2>

    <form method="post">
        <input type="date" name="available_date" required>
        <input type="time" name="start_time" required>
        <input type="time" name="end_time" required>
        <button name="add_availability">Add Slot</button>
    </form>

    <table>
        <tr>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['available_date'] ?></td>
            <td><?= $row['start_time'] ?></td>
            <td><?= $row['end_time'] ?></td>
            <td>
                <a class="delete" href="?delete=<?= $row['availability_id'] ?>">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
