<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

// Get logged-in user
$user = null;
if (isset($_SESSION["user_id"])) {
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Handle join event
if ($user && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['join_event_id'])) {
    $event_id = (int)$_POST['join_event_id'];
    
    // Check if user already joined this event
    $check_stmt = $mysqli->prepare("SELECT * FROM can_create_between WHERE user_id = ? AND event_id = ?");
    $check_stmt->bind_param("ii", $user['user_id'], $event_id);
    $check_stmt->execute();
    $existing = $check_stmt->get_result()->fetch_assoc();
    
    if (!$existing) {
        // Get a group_id (using default group or user's group)
        $group_query = $mysqli->query("SELECT group_id FROM user_group WHERE user_id = " . $user['user_id'] . " LIMIT 1");
        $group = $group_query->fetch_assoc();
        $group_id = $group ? $group['group_id'] : 1; // Default to group 1 if no group
        
        // Add user to event
        $join_stmt = $mysqli->prepare("INSERT INTO can_create_between (user_id, event_id, group_id) VALUES (?, ?, ?)");
        $join_stmt->bind_param("iii", $user['user_id'], $event_id, $group_id);
        $join_stmt->execute();
        
        // Update participant count
        $update_stmt = $mysqli->prepare("UPDATE events SET participants = participants + 1 WHERE event_id = ?");
        $update_stmt->bind_param("i", $event_id);
        $update_stmt->execute();
    }
    
    header("Location: events_list.php");
    exit;
}

// Get ongoing and upcoming events
$today = date('Y-m-d');
$events = $mysqli->query("
    SELECT e.event_id, e.name, e.date, e.participants, u.name as creator_name
    FROM events e
    JOIN users u ON u.user_id = e.user_id
    WHERE e.date >= '$today'
    ORDER BY e.date ASC
");

// Get user's joined events
$user_events = [];
if ($user) {
    $stmt = $mysqli->prepare("SELECT event_id FROM can_create_between WHERE user_id = ?");
    $stmt->bind_param("i", $user['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $user_events[] = $row['event_id'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        .events-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .event-card {
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        
        .event-card h3 {
            margin-top: 0;
            color: #333;
        }
        
        .event-info {
            margin: 10px 0;
            font-size: 14px;
            color: #666;
        }
        
        .event-date {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .join-btn {
            background-color: #27ae60;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .join-btn:hover {
            background-color: #229954;
        }
        
        .joined-btn {
            background-color: #95a5a6;
            cursor: not-allowed;
        }
        
        .joined-btn:hover {
            background-color: #95a5a6;
        }
    </style>
</head>
<body>

<h1>Upcoming Events</h1>
<p><a href="index.php">← Back to Home</a></p>

<?php if (!$user): ?>
    <p style="color: #e74c3c;"><strong>Please <a href="login.php">log in</a> to join events.</strong></p>
<?php endif; ?>

<h2>Available Events</h2>

<?php if ($events->num_rows > 0): ?>
    <div class="events-container">
        <?php while ($event = $events->fetch_assoc()): ?>
            <div class="event-card">
                <h3><?= htmlspecialchars($event['name']) ?></h3>
                
                <div class="event-info">
                    <strong>Date:</strong> <span class="event-date"><?= date('M d, Y', strtotime($event['date'])) ?></span>
                </div>
                
                <div class="event-info">
                    <strong>Created by:</strong> <?= htmlspecialchars($event['creator_name']) ?>
                </div>
                
                <div class="event-info">
                    <strong>Participants:</strong> <?= $event['participants'] ?? 0 ?>
                </div>
                
                <?php if ($user): ?>
                    <form method="POST" action="events_list.php" style="margin: 0;">
                        <input type="hidden" name="join_event_id" value="<?= $event['event_id'] ?>">
                        <?php if (in_array($event['event_id'], $user_events)): ?>
                            <button type="button" class="join-btn joined-btn" disabled>Already Joined ✓</button>
                        <?php else: ?>
                            <button type="submit" class="join-btn">Join Event</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p>No upcoming events yet. <a href="event_create.php">Create one!</a></p>
<?php endif; ?>

</body>
</html>
