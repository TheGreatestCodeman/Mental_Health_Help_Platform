<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $role = $_SESSION["role"] ?? "user";
    if ($role === "admin") {
        header("Location: admin_dashboard.php");
    } elseif ($role === "counselor") {
        header("Location: counselor_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

$mysqli = require __DIR__ . "/database.php";
$error = "";
$user_type = $_GET["type"] ?? "user"; // user, counselor, or admin

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $login_type = $_POST["login_type"] ?? "user";
    
    if (!$email || !$password) {
        $error = "Email and password are required";
    } else {
        if ($login_type === "counselor") {
            // Counselor login
            $stmt = $mysqli->prepare("SELECT c_id, name, email, password FROM counselor WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $counselor = $result->fetch_assoc();
            
            if ($counselor && password_verify($password, $counselor["password"])) {
                $_SESSION["user_id"] = $counselor["c_id"];
                $_SESSION["name"] = $counselor["name"];
                $_SESSION["role"] = "counselor";
                header("Location: counselor_dashboard.php");
                exit;
            } else {
                $error = "Invalid email or password for counselor account";
            }
        } else {
            // User or Admin login (both use users table)
            $stmt = $mysqli->prepare("SELECT user_id, name, email, password_hash, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user && password_verify($password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role"] ?? "user";
                
                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit;
            } else {
                $error = "Invalid email or password";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Mental Health Help Platform</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        .login-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .login-container p {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
        }
        .tab-button {
            flex: 1;
            padding: 12px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            color: #999;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab-button.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .form-group button:hover {
            transform: translateY(-2px);
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .info-box {
            background: #f0f7ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
            font-size: 0.9em;
            color: #333;
        }
        .back-link {
            text-align: center;
            margin-bottom: 20px;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="back-link">
        <a href="index.php">← Back to Home</a>
    </div>
    
    <h1>🧠 Mental Health Platform</h1>
    <p>Sign in to your account</p>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="tabs">
        <button class="tab-button active" onclick="showTab('user-tab')">👤 User</button>
        <button class="tab-button" onclick="showTab('counselor-tab')">👨‍⚕️ Counselor</button>
        <button class="tab-button" onclick="showTab('admin-tab')">🔑 Admin</button>
    </div>
    
    <!-- USER LOGIN -->
    <div id="user-tab" class="tab-content active">
        <div class="info-box">
            Sign in as a user to access your profile, book appointments, and connect with the community.
        </div>
        <form method="POST">
            <input type="hidden" name="login_type" value="user">
            <div class="form-group">
                <label for="user-email">Email:</label>
                <input type="email" id="user-email" name="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="user-password">Password:</label>
                <input type="password" id="user-password" name="password" required placeholder="Enter your password">
            </div>
            <div class="form-group">
                <button type="submit">Sign In as User</button>
            </div>
            <div class="signup-link">
                Don't have an account? <a href="signup.html">Sign up here</a>
            </div>
        </form>
    </div>
    
    <!-- COUNSELOR LOGIN -->
    <div id="counselor-tab" class="tab-content">
        <div class="info-box">
            Sign in as a counselor to manage appointments, view client history, and provide support.
        </div>
        <form method="POST">
            <input type="hidden" name="login_type" value="counselor">
            <div class="form-group">
                <label for="counselor-email">Email:</label>
                <input type="email" id="counselor-email" name="email" required placeholder="Enter your counselor email">
            </div>
            <div class="form-group">
                <label for="counselor-password">Password:</label>
                <input type="password" id="counselor-password" name="password" required placeholder="Enter your password">
            </div>
            <div class="form-group">
                <button type="submit">Sign In as Counselor</button>
            </div>
            <div class="signup-link">
                New counselor? <a href="counselor_register.php">Register here</a>
            </div>
        </form>
    </div>
    
    <!-- ADMIN LOGIN -->
    <div id="admin-tab" class="tab-content">
        <div class="info-box">
            Sign in as an admin to manage users, content, and platform settings.
        </div>
        <form method="POST">
            <input type="hidden" name="login_type" value="user">
            <div class="form-group">
                <label for="admin-email">Email:</label>
                <input type="email" id="admin-email" name="email" required placeholder="Enter your admin email">
            </div>
            <div class="form-group">
                <label for="admin-password">Password:</label>
                <input type="password" id="admin-password" name="password" required placeholder="Enter your admin password">
            </div>
            <div class="form-group">
                <button type="submit">Sign In as Admin</button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tabId) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Remove active class from all buttons
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab
    document.getElementById(tabId).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}
</script>

</body>
</html>
