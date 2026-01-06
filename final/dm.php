\                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   <?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$mysqli = require __DIR__ . '/database.php';
$user_id = $_SESSION['user_id'];

// Get list of all users (excluding current user) for conversation selection
$users_query = "SELECT user_id as id, name, email, 'user' as type FROM users WHERE user_id != ? ORDER BY name";
$stmt = $mysqli->prepare($users_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$users_result = $stmt->get_result();
$available_users = $users_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get list of all counselors for conversation selection
$counselors_query = "SELECT c_id as id, name, email, 'counselor' as type FROM counselor ORDER BY name";
$stmt = $mysqli->prepare($counselors_query);
$stmt->execute();
$counselors_result = $stmt->get_result();
$available_counselors = $counselors_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Combine both lists
$all_contacts = array_merge($available_users, $available_counselors);

// Get current conversation (if selected)
$conversation_with_id = null;
$conversation_with_name = null;
$conversation_type = null;
$messages = [];

// Handle form submission from quick message modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recipient'])) {
    $recipient = $_POST['recipient'];
    $message_text = trim($_POST['message_text']);
    
    // Parse recipient format: "user_id" or "counselor_id"
    if (strpos($recipient, 'user_') === 0) {
        $receiver_id = intval(str_replace('user_', '', $recipient));
    } elseif (strpos($recipient, 'counselor_') === 0) {
        $receiver_id = intval(str_replace('counselor_', '', $recipient));
    } else {
        $receiver_id = 0;
    }
    
    if (!empty($message_text) && $receiver_id > 0) {
        // Insert message into dm table
        $insert_query = "INSERT INTO dm (sender_id, receiver_id) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param("ii", $user_id, $receiver_id);
        
        if ($stmt->execute()) {
            $message_id = $mysqli->insert_id;
            
            // Store message content
            $content_insert = "INSERT INTO message_content (message_id, content, created_at) VALUES (?, ?, NOW())";
            $content_stmt = $mysqli->prepare($content_insert);
            $content_stmt->bind_param("is", $message_id, $message_text);
            $content_stmt->execute();
            $content_stmt->close();
            
            $_SESSION['message_sent'] = "Message sent successfully!";
            
            // Redirect to conversation
            if (strpos($recipient, 'user_') === 0) {
                header("Location: dm.php?user_id=" . $receiver_id);
            } else {
                header("Location: dm.php?counselor_id=" . $receiver_id);
            }
            exit;
        }
        $stmt->close();
    }
}

// Handle message sending from the chat interface
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'send_message') {
        // Handle sending a new message
        $receiver_id = intval($_POST['receiver_id']);
        $message_text = trim($_POST['message_text']);
        
        if (!empty($message_text) && $receiver_id > 0) {
            // Insert message into dm table
            $insert_query = "INSERT INTO dm (sender_id, receiver_id) VALUES (?, ?)";
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("ii", $user_id, $receiver_id);
            
            if ($stmt->execute()) {
                $message_id = $mysqli->insert_id;
                
                // Store message content
                $content_insert = "INSERT INTO message_content (message_id, content, created_at) VALUES (?, ?, NOW())";
                $content_stmt = $mysqli->prepare($content_insert);
                $content_stmt->bind_param("is", $message_id, $message_text);
                $content_stmt->execute();
                $content_stmt->close();
                
                $_SESSION['message_sent'] = "Message sent successfully!";
            }
            $stmt->close();
        }
    }
}

// Get conversation if a contact is selected (user or counselor)
$conversation_type = null; // 'user' or 'counselor'

if (isset($_GET['user_id'])) {
    $conversation_with_id = intval($_GET['user_id']);
    $conversation_type = 'user';
    
    // Get the other user's details
    $user_query = "SELECT name FROM users WHERE user_id = ?";
    $stmt = $mysqli->prepare($user_query);
    $stmt->bind_param("i", $conversation_with_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_data = $user_result->fetch_assoc();
    $conversation_with_name = $user_data['name'] ?? 'Unknown';
    $stmt->close();
    
    // Get all messages between the two users
    $messages_query = "
        SELECT dm.message_id, dm.sender_id, dm.receiver_id, mc.content, mc.created_at 
        FROM dm
        LEFT JOIN message_content mc ON dm.message_id = mc.message_id
        WHERE (dm.sender_id = ? AND dm.receiver_id = ?) 
           OR (dm.sender_id = ? AND dm.receiver_id = ?)
        ORDER BY mc.created_at ASC
    ";
    $stmt = $mysqli->prepare($messages_query);
    $stmt->bind_param("iiii", $user_id, $conversation_with_id, $conversation_with_id, $user_id);
    $stmt->execute();
    $messages_result = $stmt->get_result();
    $messages = $messages_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} elseif (isset($_GET['counselor_id'])) {
    $conversation_with_id = intval($_GET['counselor_id']);
    $conversation_type = 'counselor';
    
    // Get the counselor's details
    $counselor_query = "SELECT name FROM counselor WHERE c_id = ?";
    $stmt = $mysqli->prepare($counselor_query);
    $stmt->bind_param("i", $conversation_with_id);
    $stmt->execute();
    $counselor_result = $stmt->get_result();
    $counselor_data = $counselor_result->fetch_assoc();
    $conversation_with_name = $counselor_data['name'] ?? 'Unknown';
    $stmt->close();
    
    // Get all messages between the two users
    $messages_query = "
        SELECT dm.message_id, dm.sender_id, dm.receiver_id, mc.content, mc.created_at 
        FROM dm
        LEFT JOIN message_content mc ON dm.message_id = mc.message_id
        WHERE (dm.sender_id = ? AND dm.receiver_id = ?) 
           OR (dm.sender_id = ? AND dm.receiver_id = ?)
        ORDER BY mc.created_at ASC
    ";
    $stmt = $mysqli->prepare($messages_query);
    $stmt->bind_param("iiii", $user_id, $conversation_with_id, $conversation_with_id, $user_id);
    $stmt->execute();
    $messages_result = $stmt->get_result();
    $messages = $messages_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Get list of recent conversations
$conversations_query = "
    SELECT DISTINCT 
        CASE 
            WHEN dm.sender_id = ? THEN dm.receiver_id 
            ELSE dm.sender_id 
        END as other_user_id,
        u.name,
        MAX(mc.created_at) as last_message_time
    FROM dm
    LEFT JOIN message_content mc ON dm.message_id = mc.message_id
    LEFT JOIN users u ON u.user_id = CASE 
        WHEN dm.sender_id = ? THEN dm.receiver_id 
        ELSE dm.sender_id 
    END
    WHERE dm.sender_id = ? OR dm.receiver_id = ?
    GROUP BY other_user_id, u.name
    ORDER BY last_message_time DESC
";
$stmt = $mysqli->prepare($conversations_query);
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$conversations_result = $stmt->get_result();
$conversations = $conversations_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Messages</title>
    <link rel="stylesheet" href="dm.css">
</head>
<body>
    <div class="dm-container">
        <!-- Sidebar with conversations -->
        <div class="dm-sidebar">
            <div class="sidebar-header">
                <h2>Messages</h2>
                <a href="user_dashboard.php" class="back-btn">← Back</a>
            </div>

            <div class="conversations-section">
                <h3>Recent Conversations</h3>
                <div class="conversations-list">
                    <?php if (!empty($conversations)): ?>
                        <?php foreach ($conversations as $conv): ?>
                            <a href="dm.php?user_id=<?php echo $conv['other_user_id']; ?>" 
                               class="conversation-item <?php echo ($conversation_with_id === $conv['other_user_id']) ? 'active' : ''; ?>">
                                <div class="conv-name"><?php echo htmlspecialchars($conv['name']); ?></div>
                                <div class="conv-time"><?php echo $conv['last_message_time']; ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-conversations">No conversations yet</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="users-section">
                <h3>Start New Chat</h3>
                <div class="users-list">
                    <div class="section-label">👥 Users</div>
                    <?php if (!empty($available_users)): ?>
                        <?php foreach ($available_users as $user): ?>
                            <a href="dm.php?user_id=<?php echo $user['id']; ?>" 
                               class="user-item <?php echo ($conversation_with_id === $user['id'] && $conversation_type === 'user') ? 'active' : ''; ?>">
                                <div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-users">No other users available</p>
                    <?php endif; ?>
                    
                    <div class="section-label">💼 Counselors</div>
                    <?php if (!empty($available_counselors)): ?>
                        <?php foreach ($available_counselors as $counselor): ?>
                            <a href="dm.php?counselor_id=<?php echo $counselor['id']; ?>" 
                               class="user-item <?php echo ($conversation_with_id === $counselor['id'] && $conversation_type === 'counselor') ? 'active' : ''; ?>">
                                <div class="user-name"><?php echo htmlspecialchars($counselor['name']); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($counselor['email']); ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-users">No counselors available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main chat area -->
        <div class="dm-main">
            <?php if ($conversation_with_id): ?>
                <div class="chat-header">
                    <h2><?php echo htmlspecialchars($conversation_with_name); ?></h2>
                </div>

                <div class="messages-container">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php if (!empty($msg['content'])): ?>
                            <div class="message <?php echo ($msg['sender_id'] === $user_id) ? 'sent' : 'received'; ?>">
                                <div class="message-content">
                                    <?php echo htmlspecialchars($msg['content']); ?>
                                </div>
                                <div class="message-time">
                                    <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-messages">
                            No messages yet. Start the conversation!
                        </div>
                    <?php endif; ?>
                </div>

                <form method="POST" class="message-form">
                    <input type="hidden" name="action" value="send_message">
                    <input type="hidden" name="receiver_id" value="<?php echo $conversation_with_id; ?>">
                    
                    <div class="input-group">
                        <textarea name="message_text" placeholder="Type your message..." required></textarea>
                        <button type="submit" class="send-btn">Send</button>
                    </div>
                </form>

                <?php if (isset($_SESSION['message_sent'])): ?>
                    <div class="success-message">
                        <?php 
                        echo $_SESSION['message_sent'];
                        unset($_SESSION['message_sent']);
                        ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="welcome-section">
                    <h2>Welcome to Direct Messages</h2>
                    <p>Select a conversation from the left or start a new chat with someone</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
