<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

// Get logged-in user info
$user = null;
if (isset($_SESSION["user_id"])) {
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// Handle form submission (Create post)
if ($user && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['post_from_past'])) {
    $post_text = $_POST['post_from_past'];

    // Insert new post
    $stmt = $mysqli->prepare("INSERT INTO post (post_from_past) VALUES (?)");
    $stmt->bind_param("s", $post_text);
    $stmt->execute();
    $post_id = $stmt->insert_id;

    // Link post to user
    $stmt2 = $mysqli->prepare("INSERT INTO can_create (user_id, post_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $user['user_id'], $post_id);
    $stmt2->execute();

    header("Location: home.php");
    exit;
}

// Handle comment submission
if ($user && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment_post_id'], $_POST['comment_text'])) {
    $post_id = (int)$_POST['comment_post_id'];
    $comment_text = trim($_POST['comment_text']);

    // Append comment to post's comments column
    $stmt = $mysqli->prepare("UPDATE post SET comments = CONCAT(IFNULL(comments, ''), ?) WHERE post_id = ?");
    $full_comment = $user['name'] . ': ' . $comment_text . "\n";
    $stmt->bind_param("si", $full_comment, $post_id);
    $stmt->execute();
    $stmt->close();

    header("Location: home.php");
    exit;
}

// Fetch all posts with user names
$posts = $mysqli->query("
    SELECT post.post_id, post.post_from_past, post.comments, users.name
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
</head>
<body>

<h1>Home</h1>

<?php if ($user): ?>
    <p>Hello <?= htmlspecialchars($user["name"]) ?></p>
    <p><a href="logout.php">Log out</a></p>

    <!-- Create Post Form -->
    <form method="POST" action="home.php">
        <textarea name="post_from_past" required placeholder="What's on your mind?"></textarea>
        <button type="submit">Post</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Log in</a> or <a href="signup.html">sign up</a></p>
<?php endif; ?>

<h2>All Posts</h2>
<?php if ($posts->num_rows > 0): ?>
    <?php while ($post = $posts->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
            <p><strong><?= htmlspecialchars($post['name']) ?>:</strong> <?= htmlspecialchars($post['post_from_past']) ?></p>

            <?php if (!empty($post['comments'])): ?>
                <div style="margin-top:5px; padding:5px; border-top:1px solid #ccc; white-space:pre-line;">
                    <?= htmlspecialchars($post['comments']) ?>
                </div>
            <?php endif; ?>

            <?php if ($user && isset($user['user_id'])): ?>
                <!-- Delete button -->
                <form method="POST" action="delete_post.php" style="display:inline;">
                    <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                    <button type="submit">Delete</button>
                </form>

                <!-- Comment form -->
                <form method="POST" action="home.php" style="margin-top:5px;">
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
