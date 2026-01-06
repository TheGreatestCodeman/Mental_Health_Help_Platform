<?php
require_once("database.php");
session_start();


$c_id = $_SESSION['c_id'] ?? 1;


$sql = "
SELECT DISTINCT 
    users.user_id,
    users.name,
    users.email
FROM users
JOIN appointments ON appointments.user_id = users.user_id
WHERE appointments.counselor_id = $c_id
";

$result = mysqli_query($mysqli, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Clients</title>
    <meta charset="UTF-8">

  
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Fredoka', sans-serif;
            background: radial-gradient(circle at top, #d4fc79, #96e6a1);
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
            color: #2e7d32;
        }

        .back {
            text-decoration: none;
            background: #2ecc71;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(46,204,113,0.4);
        }

        .container {
            padding: 40px;
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            margin-bottom: 25px;
            color: #1b5e20;
        }

        .clients {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 45px rgba(0,0,0,0.2);
        }

        .icon {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .name {
            font-size: 18px;
            font-weight: 600;
        }

        .email {
            font-size: 14px;
            color: #666;
            margin: 8px 0 15px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            background: linear-gradient(45deg, #56ab2f, #a8e063);
            color: white;
            padding: 10px 18px;
            border-radius: 18px;
            font-size: 14px;
            box-shadow: 0 8px 20px rgba(86,171,47,0.4);
        }

        .empty {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
    </style>
</head>

<body>

<div class="navbar">
    <h2>🧑‍🤝‍🧑 My Clients</h2>
    <a href="counselor_dashboard.php" class="back">← Dashboard</a>
</div>

<div class="container">
    <h1>Your Clients</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="clients">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="card">
                    <div class="icon">🙂</div>
                    <div class="name"><?= htmlspecialchars($row['name']) ?></div>
                    <div class="email"><?= htmlspecialchars($row['email']) ?></div>

               
                    <a href="counselor_client_history.php?user_id=<?= $row['user_id'] ?>" class="btn">View History</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <h3>No clients yet</h3>
            <p>Your client list will appear once you complete sessions.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
