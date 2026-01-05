<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user = null;
$user_id = $_SESSION["user_id"];

// Get current user info
$stmt = $mysqli->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$current_group = null;
$group_members = [];
$group_posts = [];
$group_events = [];

// Handle Create Group
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create_group') {
    $group_name = trim($_POST['group_name']);
    
    if (!empty($group_name)) {
        $one = 1;
        $stmt = $mysqli->prepare("INSERT INTO user_group (name, members) VALUES (?, ?)");
        $stmt->bind_param("si", $group_name, $one);
        $stmt->execute();
        $group_id = $mysqli->insert_id;
        $stmt->close();
        
        // Add creator to group
        $stmt = $mysqli->prepare("INSERT INTO users_in_group (user_id, group_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $group_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['group_created'] = "Group created successfully!";
        header("Location: group.php");
        exit;
    }
}

// Handle Join Group
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'join_group') {
    $group_id = (int)$_POST['group_id'];
    
    // Check if already a member
    $check = $mysqli->prepare("SELECT * FROM users_in_group WHERE user_id = ? AND group_id = ?");
    $check->bind_param("ii", $user_id, $group_id);
    $check->execute();
    
    if ($check->get_result()->num_rows === 0) {
        $stmt = $mysqli->prepare("INSERT INTO users_in_group (user_id, group_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $group_id);
        $stmt->execute();
        $stmt->close();
        
        // Update member count
        $mysqli->query("UPDATE user_group SET members = members + 1 WHERE group_id = " . $group_id);
        
        $_SESSION['joined_group'] = "You joined the group!";
    }
    $check->close();
    header("Location: group.php");
    exit;
}

// Handle Leave Group
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'leave_group') {
    $group_id = (int)$_POST['group_id'];
    
    $stmt = $mysqli->prepare("DELETE FROM users_in_group WHERE user_id = ? AND group_id = ?");
    $stmt->bind_param("ii", $user_id, $group_id);
    $stmt->execute();
    $stmt->close();
    
    // Update member count
    $mysqli->query("UPDATE user_group SET members = members - 1 WHERE group_id = " . $group_id);
    
    $_SESSION['left_group'] = "You left the group!";
    header("Location: group.php");
    exit;
}

// Handle Group Post Creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create_group_post') {
    $group_id = (int)$_POST['group_id'];
    $post_text = trim($_POST['post_text']);
    
    // Verify user is in group
    $verify = $mysqli->prepare("SELECT * FROM users_in_group WHERE user_id = ? AND group_id = ?");
    $verify->bind_param("ii", $user_id, $group_id);
    $verify->execute();
    
    if ($verify->get_result()->num_rows > 0 && !empty($post_text)) {
        // Create post
        $stmt = $mysqli->prepare("INSERT INTO post (post_from_past, group_id) VALUES (?, ?)");
        $stmt->bind_param("si", $post_text, $group_id);
        $stmt->execute();
        $post_id = $mysqli->insert_id;
        $stmt->close();
        
        // Link user to post
        $stmt = $mysqli->prepare("INSERT INTO can_create (user_id, post_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $post_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['post_created'] = "Post created!";
    }
    $verify->close();
    header("Location: group.php?view=" . $group_id);
    exit;
}

// Handle Group Event Creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'create_group_event') {
    $group_id = (int)$_POST['group_id'];
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    
    // Verify user is in group
    $verify = $mysqli->prepare("SELECT * FROM users_in_group WHERE user_id = ? AND group_id = ?");
    $verify->bind_param("ii", $user_id, $group_id);
    $verify->execute();
    
    if ($verify->get_result()->num_rows > 0 && !empty($event_name) && !empty($event_date)) {
        // Create event
        $stmt = $mysqli->prepare("INSERT INTO events (name, date, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $event_name, $event_date, $user_id);
        $stmt->execute();
        $event_id = $mysqli->insert_id;
        $stmt->close();
        
        // Link event to group
        $stmt = $mysqli->prepare("INSERT INTO can_create_between (user_id, event_id, group_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $event_id, $group_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['event_created'] = "Event created!";
    }
    $verify->close();
    header("Location: group.php?view=" . $group_id);
    exit;
}

// Handle Comment on Group Post
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'comment_group_post') {
    $group_id = (int)$_POST['group_id'];
    $post_id = (int)$_POST['post_id'];
    $comment_text = trim($_POST['comment_text']);
    
    if (!empty($comment_text)) {
        $full_comment = $user['name'] . ': ' . $comment_text . "\n";
        $stmt = $mysqli->prepare("UPDATE post SET comments = CONCAT(IFNULL(comments, ''), ?) WHERE post_id = ?");
        $stmt->bind_param("si", $full_comment, $post_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['comment_added'] = "Comment added!";
    }
    header("Location: group.php?view=" . $group_id);
    exit;
}

// Get user's groups
$user_groups_result = $mysqli->query("
    SELECT DISTINCT ug.group_id, ug.name, ug.members
    FROM user_group ug
    INNER JOIN users_in_group uig ON ug.group_id = uig.group_id
    WHERE uig.user_id = $user_id
    ORDER BY ug.name
");
$user_groups = $user_groups_result->fetch_all(MYSQLI_ASSOC);

// Get all groups (for joining)
$all_groups_result = $mysqli->query("
    SELECT group_id, name, members FROM user_group
    ORDER BY name
");
$all_groups = $all_groups_result->fetch_all(MYSQLI_ASSOC);

// Check if viewing specific group
if (isset($_GET['view'])) {
    $view_group_id = (int)$_GET['view'];
    
    // Verify user is in group
    $verify = $mysqli->prepare("SELECT * FROM users_in_group WHERE user_id = ? AND group_id = ?");
    $verify->bind_param("ii", $user_id, $view_group_id);
    $verify->execute();
    
    if ($verify->get_result()->num_rows > 0) {
        // Get group info
        $stmt = $mysqli->prepare("SELECT * FROM user_group WHERE group_id = ?");
        $stmt->bind_param("i", $view_group_id);
        $stmt->execute();
        $current_group = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        // Get group members
        $stmt = $mysqli->prepare("
            SELECT u.user_id, u.name, u.email
            FROM users u
            INNER JOIN users_in_group uig ON u.user_id = uig.user_id
            WHERE uig.group_id = ?
            ORDER BY u.name
        ");
        $stmt->bind_param("i", $view_group_id);
        $stmt->execute();
        $group_members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Get group posts
        $stmt = $mysqli->prepare("
            SELECT p.post_id, p.post_from_past, p.comments, u.name
            FROM post p
            JOIN can_create cc ON cc.post_id = p.post_id
            JOIN users u ON u.user_id = cc.user_id
            WHERE p.group_id = ?
            ORDER BY p.post_id DESC
        ");
        $stmt->bind_param("i", $view_group_id);
        $stmt->execute();
        $group_posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Get group events
        $stmt = $mysqli->prepare("
            SELECT e.event_id, e.name as event_name, e.date, u.name as creator_name
            FROM events e
            JOIN can_create_between ccb ON e.event_id = ccb.event_id
            JOIN users u ON u.user_id = e.user_id
            WHERE ccb.group_id = ?
            ORDER BY e.date DESC
        ");
        $stmt->bind_param("i", $view_group_id);
        $stmt->execute();
        $group_events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
    $verify->close();
}

// Helper function to check if user is member of group
function is_group_member($user_id, $group_id, $mysqli) {
    $stmt = $mysqli->prepare("SELECT * FROM users_in_group WHERE user_id = ? AND group_id = ?");
    $stmt->bind_param("ii", $user_id, $group_id);
    $stmt->execute();
    $result = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $result;
}

// Helper function to check if user created post
function is_post_creator($user_id, $post_id, $mysqli) {
    $stmt = $mysqli->prepare("SELECT * FROM can_create WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
    $result = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups</title>
    <link rel="stylesheet" href="group.css">
</head>
<body>
    <div class="groups-container">
        <!-- Sidebar -->
        <aside class="groups-sidebar">
            <div class="sidebar-header">
                <h2>Groups</h2>
                <a href="user_dashboard.php" class="back-link">← Back</a>
            </div>

            <!-- My Groups -->
            <div class="my-groups-section">
                <h3>My Groups</h3>
                <?php if (!empty($user_groups)): ?>
                    <div class="groups-list">
                        <?php foreach ($user_groups as $group): ?>
                            <a href="group.php?view=<?php echo $group['group_id']; ?>" 
                               class="group-item <?php echo (isset($current_group) && $current_group['group_id'] === $group['group_id']) ? 'active' : ''; ?>">
                                <div class="group-name"><?php echo htmlspecialchars($group['name']); ?></div>
                                <div class="group-members"><?php echo $group['members']; ?> members</div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-groups">No groups yet. Join one to get started!</p>
                <?php endif; ?>
            </div>

            <!-- Create Group Form -->
            <div class="create-group-section">
                <h3>Create New Group</h3>
                <form method="POST" class="create-group-form">
                    <input type="hidden" name="action" value="create_group">
                    <input type="text" name="group_name" placeholder="Group name" required>
                    <button type="submit" class="btn-create">Create</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="groups-main">
            <?php if ($current_group): ?>
                <!-- Group View -->
                <div class="group-header">
                    <h1><?php echo htmlspecialchars($current_group['name']); ?></h1>
                    <div class="group-info">
                        <span class="members-count"><?php echo $current_group['members']; ?> members</span>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="leave_group">
                            <input type="hidden" name="group_id" value="<?php echo $current_group['group_id']; ?>">
                            <button type="submit" class="btn-leave">Leave Group</button>
                        </form>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="group-tabs">
                    <button class="tab-btn active" data-tab="posts">Posts</button>
                    <button class="tab-btn" data-tab="events">Events</button>
                    <button class="tab-btn" data-tab="members">Members</button>
                </div>

                <!-- Posts Tab -->
                <div id="posts" class="tab-content active">
                    <h2>Group Posts</h2>
                    
                    <!-- Create Post Form -->
                    <form method="POST" class="post-form">
                        <input type="hidden" name="action" value="create_group_post">
                        <input type="hidden" name="group_id" value="<?php echo $current_group['group_id']; ?>">
                        <textarea name="post_text" placeholder="What's on your mind?" required></textarea>
                        <button type="submit" class="btn-post">Post</button>
                    </form>

                    <!-- Posts List -->
                    <div class="posts-list">
                        <?php if (!empty($group_posts)): ?>
                            <?php foreach ($group_posts as $post): ?>
                                <div class="post-card">
                                    <div class="post-header">
                                        <strong><?php echo htmlspecialchars($post['name']); ?></strong>
                                    </div>
                                    <div class="post-content">
                                        <?php echo htmlspecialchars($post['post_from_past']); ?>
                                    </div>

                                    <?php if (!empty($post['comments'])): ?>
                                        <div class="post-comments">
                                            <div class="comments-header">Comments:</div>
                                            <div class="comments-text"><?php echo htmlspecialchars($post['comments']); ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="post-actions">
                                        <?php if (is_post_creator($user_id, $post['post_id'], $mysqli)): ?>
                                            <form method="POST" action="delete_post.php" style="display:inline;">
                                                <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                                <button type="submit" class="btn-delete">Delete</button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="POST" class="comment-form" style="display:inline;">
                                            <input type="hidden" name="action" value="comment_group_post">
                                            <input type="hidden" name="group_id" value="<?php echo $current_group['group_id']; ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                            <input type="text" name="comment_text" placeholder="Comment..." required>
                                            <button type="submit" class="btn-comment">Comment</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-content">No posts yet. Be the first to post!</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Events Tab -->
                <div id="events" class="tab-content">
                    <h2>Group Events</h2>
                    
                    <!-- Create Event Form -->
                    <form method="POST" class="event-form">
                        <input type="hidden" name="action" value="create_group_event">
                        <input type="hidden" name="group_id" value="<?php echo $current_group['group_id']; ?>">
                        <div class="form-group">
                            <input type="text" name="event_name" placeholder="Event name" required>
                            <input type="date" name="event_date" required>
                            <button type="submit" class="btn-event">Create Event</button>
                        </div>
                    </form>

                    <!-- Events List -->
                    <div class="events-list">
                        <?php if (!empty($group_events)): ?>
                            <?php foreach ($group_events as $event): ?>
                                <div class="event-card">
                                    <div class="event-name"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                    <div class="event-date">📅 <?php echo date('M d, Y', strtotime($event['date'])); ?></div>
                                    <div class="event-creator">Created by <?php echo htmlspecialchars($event['creator_name']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-content">No events scheduled yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Members Tab -->
                <div id="members" class="tab-content">
                    <h2>Group Members</h2>
                    <div class="members-list">
                        <?php if (!empty($group_members)): ?>
                            <?php foreach ($group_members as $member): ?>
                                <div class="member-card">
                                    <div class="member-name"><?php echo htmlspecialchars($member['name']); ?></div>
                                    <div class="member-email"><?php echo htmlspecialchars($member['email']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-content">No members in this group.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['post_created'])): ?>
                    <div class="success-message"><?php echo $_SESSION['post_created']; unset($_SESSION['post_created']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['event_created'])): ?>
                    <div class="success-message"><?php echo $_SESSION['event_created']; unset($_SESSION['event_created']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['comment_added'])): ?>
                    <div class="success-message"><?php echo $_SESSION['comment_added']; unset($_SESSION['comment_added']); ?></div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Browse Groups -->
                <div class="explore-groups">
                    <h2>Explore Groups</h2>
                    
                    <?php if (isset($_SESSION['group_created'])): ?>
                        <div class="success-message"><?php echo $_SESSION['group_created']; unset($_SESSION['group_created']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['joined_group'])): ?>
                        <div class="success-message"><?php echo $_SESSION['joined_group']; unset($_SESSION['joined_group']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['left_group'])): ?>
                        <div class="info-message"><?php echo $_SESSION['left_group']; unset($_SESSION['left_group']); ?></div>
                    <?php endif; ?>

                    <div class="available-groups">
                        <?php if (!empty($all_groups)): ?>
                            <?php foreach ($all_groups as $group): ?>
                                <div class="available-group-card">
                                    <h3><?php echo htmlspecialchars($group['name']); ?></h3>
                                    <p class="members-info"><?php echo $group['members']; ?> members</p>
                                    
                                    <?php if (is_group_member($user_id, $group['group_id'], $mysqli)): ?>
                                        <a href="group.php?view=<?php echo $group['group_id']; ?>" class="btn-view">View Group</a>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="join_group">
                                            <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                            <button type="submit" class="btn-join">Join Group</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-groups">No groups available. Create the first one!</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Tab switching
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabName = button.getAttribute('data-tab');
                
                // Remove active class
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class
                button.classList.add('active');
                document.getElementById(tabName).classList.add('active');
            });
        });
    </script>
</body>
</html>
