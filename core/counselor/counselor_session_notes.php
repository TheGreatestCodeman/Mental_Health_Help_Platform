<?php
require_once("database.php");
session_start();


$c_id = $_SESSION['c_id'] ?? 1;

$sql = "
SELECT 
    a.appointment_id,
    a.appointment_date,
    a.appointment_time,
    u.name AS user_name,
    sn.note_id,
    sn.notes
FROM appointments a
JOIN users u ON a.user_id = u.user_id
LEFT JOIN session_notes sn 
    ON a.appointment_id = sn.appointment_id
WHERE a.counselor_id = $c_id
AND a.status = 'completed'
ORDER BY a.appointment_date DESC
";

$result = mysqli_query($mysqli, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Notes</title>
    <meta charset="UTF-8">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #cfd9df, #e2ebf0);
            min-height: 100vh;
        }

        .topbar {
            background: #ffffffcc;
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .topbar h2 {
            margin: 0;
            color: #34495e;
        }

        .back {
            text-decoration: none;
            background: #667eea;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            font-size: 14px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 40px;
        }

        .page-title {
            font-size: 32px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .subtitle {
            color: #555;
            margin-bottom: 40px;
        }

        .note-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .note-card {
            background: rgba(255,255,255,0.85);
            border-radius: 25px;
            padding: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
            position: relative;
        }

        .note-card::before {
            content: "📝";
            position: absolute;
            top: -15px;
            right: -15px;
            background: #74ebd5;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .client-name {
            font-size: 20px;
            font-weight: bold;
            color: #34495e;
        }

        .date {
            font-size: 14px;
            color: #777;
            margin: 8px 0 15px;
        }

        .preview {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
            max-height: 60px;
            overflow: hidden;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 18px;
            font-size: 13px;
            color: white;
        }

        .write {
            background: #1abc9c;
        }

        .edit {
            background: #f39c12;
        }

        .view {
            background: #3498db;
        }

        .empty {
            background: #fff;
            padding: 50px;
            border-radius: 25px;
            text-align: center;
            color: #555;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

    </style>
</head>

<body>

<div class="topbar">
    <h2>📚 Session Notes Archive</h2>
    <a href="counselor_dashboard.php" class="back">← Dashboard</a>
</div>

<div class="container">
    <div class="page-title">Completed Sessions</div>
    <div class="subtitle">Review, write, or edit notes for your completed counseling sessions</div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="note-grid">

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="note-card">
                    <div class="client-name"><?= htmlspecialchars($row['user_name']) ?></div>
                    <div class="date">
                        📅 <?= $row['appointment_date'] ?> |
                        ⏰ <?= $row['appointment_time'] ?>
                    </div>

                    <div class="preview">
                        <?= $row['notes'] ? htmlspecialchars(substr($row['notes'], 0, 100)) . "..." : "No notes written yet." ?>
                    </div>

                    <div class="actions">
                        <?php if ($row['note_id']): ?>
                            <a class="btn edit" href="edit_session_notes.php?appointment_id=<?= $row['appointment_id'] ?>">✏️ Edit</a>
                            <a class="btn view" href="view_session_notes.php?appointment_id=<?= $row['appointment_id'] ?>">👀 View</a>
                        <?php else: ?>
                            <a class="btn write" href="write_notes.php?appointment_id=<?= $row['appointment_id'] ?>">📝 Write Notes</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>

        </div>
    <?php else: ?>
        <div class="empty">
            🌱 No completed sessions yet.<br>
            Once sessions are completed, notes will appear here.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
