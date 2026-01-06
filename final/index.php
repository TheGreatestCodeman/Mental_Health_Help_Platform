<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

// https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=RDdQw4w9WgXcQ&start_radio=1
$user = null;
if (isset($_SESSION["user_id"])) {
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Get available contacts for messaging
$available_users = [];
$available_counselors = [];

if ($user) {
    // Get list of users
    $users_query = "SELECT user_id as id, name, email FROM users WHERE user_id != ? ORDER BY name";
    $stmt = $mysqli->prepare($users_query);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $available_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Get list of counselors
    $counselors_query = "SELECT c_id as id, name, email FROM counselor ORDER BY name";
    $stmt = $mysqli->prepare($counselors_query);
    $stmt->execute();
    $available_counselors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}


if ($user && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['post_from_past'])) {
    $post_text = $_POST['post_from_past'];
    $flair = $_POST['flair'] ?? null;
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    // push new posts
    $stmt = $mysqli->prepare("INSERT INTO post (post_from_past, flair, is_anonymous) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $post_text, $flair, $is_anonymous);
    $stmt->execute();
    $post_id = $stmt->insert_id;

    // put into can create table
    $stmt2 = $mysqli->prepare("INSERT INTO can_create (user_id, post_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $user['user_id'], $post_id);
    $stmt2->execute();

    header("Location: index.php");
    exit;
}

// comments
if ($user && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment_post_id'], $_POST['comment_text'])) {
    $post_id = (int)$_POST['comment_post_id'];
    $comment_text = trim($_POST['comment_text']);

    
    $stmt = $mysqli->prepare("UPDATE post SET comments = CONCAT(IFNULL(comments, ''), ?) WHERE post_id = ?");
    $full_comment = $user['name'] . ': ' . $comment_text . "\n";
    $stmt->bind_param("si", $full_comment, $post_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// get all posts of user
$posts = $mysqli->query("
    SELECT post.post_id, post.post_from_past, post.comments, 
           COALESCE(post.flair, NULL) as flair, 
           COALESCE(post.is_anonymous, 0) as is_anonymous, 
           COALESCE(post.created_at, NOW()) as created_at, 
           users.name
    FROM post
    JOIN can_create ON can_create.post_id = post.post_id
    JOIN users ON users.user_id = can_create.user_id
    ORDER BY post.post_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        .post-form {
            background: #9294e9ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .form-row {
            display: flex;
            gap: 10px;
            margin: 10px 0;
            flex-wrap: wrap;
        }
        .form-row select, .form-row input {
            flex: 1;
            min-width: 150px;
        }
        .flair {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            color: white;
            margin-right: 8px;
            margin-bottom: 5px;
        }
        .flair-question { background-color: #0066cc; }
        .flair-achievement { background-color: #28a745; }
        .flair-struggle { background-color: #dc3545; }
        .flair-gratitude { background-color: #fd7e14; }
        .flair-resource { background-color: #6f42c1; }
        .flair-updates { background-color: #17a2b8; }
        .post-meta {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 8px;
        }
        .anonymous-badge {
            display: inline-block;
            background-color: #999;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 8px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<h1>Home</h1>

<div style="background: #030000ff; border: 2px solid #dc3545; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
    <strong style="color: #dc3545; font-size: 1.1em;">🆘 In Crisis? Need Help?</strong><br>
    <p style="margin: 10px 0;">Call <strong>988</strong> or Text <strong>HOME to 741741</strong></p>
    <a href="crisis_helpline.php"><button style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">View Crisis Resources</button></a>
</div>

<?php if ($user): ?>
    <p>Hello <?= htmlspecialchars($user["name"]) ?></p>
    <p><a href="logout.php">Log out</a></p>

    
    <form method="POST" action="index.php" class="post-form">
        <textarea name="post_from_past" required placeholder="What's on your mind?"></textarea>
        <div class="form-row">
            <select name="flair">
                <option value="">-- Choose a flair (optional) --</option>
                <option value="Question">❓ Question</option>
                <option value="Achievement">🎉 Achievement</option>
                <option value="Struggle">💪 Struggle</option>
                <option value="Gratitude">🙏 Gratitude</option>
                <option value="Resource">📚 Resource</option>
                <option value="Updates">📰 Updates</option>
            </select>
        </div>
        <div class="checkbox-group">
            <label>
                <input type="checkbox" name="is_anonymous"> Post Anonymously
            </label>
        </div>
        <button type="submit">Post</button>
    </form>

    <!-- Quick Message Button -->
    <div style="margin: 15px 0;">
        <button onclick="openQuickMessage()" style="background: #667eea; color: white;">💬 Quick Message</button>
    </div>

    <!-- Quick Message Modal -->
    <div id="quickMessageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px; box-shadow: 0 5px 20px rgba(0,0,0,0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Send a Message</h2>
                <button onclick="closeQuickMessage()" style="background: none; border: none; font-size: 24px; cursor: pointer; padding: 0;">×</button>
            </div>
            
            <form method="POST" action="dm.php" style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label for="messageType" style="display: block; font-weight: bold; margin-bottom: 5px;">Message Type</label>
                    <select id="messageType" onchange="updateContacts()" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="user">👥 User</option>
                        <option value="counselor">💼 Counselor</option>
                    </select>
                </div>

                <div>
                    <label for="recipientSelect" style="display: block; font-weight: bold; margin-bottom: 5px;">Select Recipient</label>
                    <select id="recipientSelect" name="recipient" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">-- Choose a user --</option>
                        <?php foreach ($available_users as $contact): ?>
                            <option value="user_<?php echo $contact['id']; ?>"><?php echo htmlspecialchars($contact['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="messageText" style="display: block; font-weight: bold; margin-bottom: 5px;">Message</label>
                    <textarea id="messageText" name="message_text" required placeholder="Type your message..." style="width: 100%; height: 120px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-family: Arial, sans-serif;"></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeQuickMessage()" style="background: #ccc; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                    <button type="submit" style="background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Send Message</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let available_users = <?php echo json_encode($available_users); ?>;
        let available_counselors = <?php echo json_encode($available_counselors ?? []); ?>;

        function openQuickMessage() {
            document.getElementById('quickMessageModal').style.display = 'flex';
        }

        function closeQuickMessage() {
            document.getElementById('quickMessageModal').style.display = 'none';
        }

        function updateContacts() {
            const messageType = document.getElementById('messageType').value;
            const recipientSelect = document.getElementById('recipientSelect');
            
            recipientSelect.innerHTML = '';
            
            if (messageType === 'user') {
                recipientSelect.innerHTML += '<option value="">-- Choose a user --</option>';
                available_users.forEach(user => {
                    recipientSelect.innerHTML += '<option value="user_' + user.id + '">' + user.name + '</option>';
                });
            } else {
                recipientSelect.innerHTML += '<option value="">-- Choose a counselor --</option>';
                available_counselors.forEach(counselor => {
                    recipientSelect.innerHTML += '<option value="counselor_' + counselor.id + '">' + counselor.name + '</option>';
                });
            }
        }

        // Close modal when clicking outside
        document.getElementById('quickMessageModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeQuickMessage();
            }
        });
    </script>
<?php else: ?>
    <p><a href="login.php">Log in</a> or <a href="signup.html">sign up</a></p>
<?php endif; ?>

<div style="margin: 20px 0;">
    <a href="event.php">
        <button>Create New Event</button>
    </a>
    <a href="events_list.php">
        <button>View Upcoming Events</button>
    </a>
    <a href="user_dashboard.php">
        <button>My Dashboard</button>
    </a>
    <a href="dm.php">
        <button>💬 Messages</button>
    </a>
    <a href="crisis_helpline.php">
        <button style="background: #dc3545; color: white;">🆘 Crisis Help</button>
    </a>
</div>

<h2>All Posts</h2>
<?php if ($posts->num_rows > 0): ?>
    <?php while ($post = $posts->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
            <div class="post-meta">
                <strong><?= $post['is_anonymous'] ? 'Anonymous User' : htmlspecialchars($post['name']) ?></strong>
                <?php if ($post['is_anonymous']): ?>
                    <span class="anonymous-badge">🔒 Anonymous</span>
                <?php endif; ?>
                <br>
                <small>Posted: <?= date('M d, Y H:i', strtotime($post['created_at'] ?? 'now')) ?></small>
            </div>
            <?php if (!empty($post['flair'])): ?>
                <div>
                    <span class="flair flair-<?= strtolower($post['flair']) ?>">
                        <?= htmlspecialchars($post['flair']) ?>
                    </span>
                </div>
            <?php endif; ?>
            <p><?= htmlspecialchars($post['post_from_past']) ?></p>

            <?php if (!empty($post['comments'])): ?>
                <div style="margin-top:5px; padding:5px; border-top:1px solid #ccc; white-space:pre-line;">
                    <?= htmlspecialchars($post['comments']) ?>
                </div>
            <?php endif; ?>

            <?php if ($user && isset($user['user_id'])): ?>
                <!-- delete -->
                <form method="POST" action="delete_post.php" style="display:inline;">
                    <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                    <button type="submit">Delete</button>
                </form>

                <!--comments part -->
                <form method="POST" action="index.php" style="margin-top:5px;">
                    <input type="hidden" name="comment_post_id" value="<?= $post['post_id'] ?>">
                    <input type="text" name="comment_text" placeholder="Write a comment" required>
                    <button type="submit">Comment</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No posts yet!</p>
<?php endif; ?>

</body>
</html>





