<?php

$mysqli = require __DIR__ . "/database.php";

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $phone = $_POST["phone"] ?? "";
    $qualifications = $_POST["qualifications"] ?? "";
    $bio = $_POST["bio"] ?? "";
    $password = $_POST["password"] ?? "";
    $password_confirmation = $_POST["password_confirmation"] ?? "";
    
    if (!$name) {
        $errors[] = "Name is required";
    }
    if (!$email) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email must be a valid email address";
    }
    if (!$phone) {
        $errors[] = "Phone number is required";
    }
    if (!$qualifications) {
        $errors[] = "Qualifications are required";
    }
    if (!$password) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif ($password !== $password_confirmation) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT c_id FROM counselor WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "A counselor account with this email already exists";
        }
        $stmt->close();
    }
    
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $mysqli->prepare("INSERT INTO counselor (name, email, phone, qualifications, bio, password) VALUES (?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        
        $stmt->bind_param("ssssss", $name, $email, $phone, $qualifications, $bio, $password_hash);
        
        if ($stmt->execute()) {
            $success = true;
            header("Location: signup-success.html");
            exit;
        } else {
            $errors[] = "An error occurred. Please try again.";
        }
        
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Counselor Registration</title>
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
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        .container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .error-list {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        .error-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .error-list li {
            margin: 5px 0;
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
            box-sizing: border-box;
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
        }
        .form-group button:hover {
            transform: translateY(-2px);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>👨‍⚕️ Counselor Registration</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="error-list">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST["name"] ?? "") ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Professional Email *</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST["email"] ?? "") ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>" required>
        </div>
        
        <div class="form-group">
            <label for="qualifications">Qualifications *</label>
            <input type="text" id="qualifications" name="qualifications" placeholder="e.g., Licensed Professional Counselor, M.A. in Counseling" value="<?= htmlspecialchars($_POST["qualifications"] ?? "") ?>" required>
        </div>
        
        <div class="form-group">
            <label for="bio">Bio</label>
            <input type="text" id="bio" name="bio" placeholder="Brief description about yourself" value="<?= htmlspecialchars($_POST["bio"] ?? "") ?>">
        </div>
        
        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirm Password *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        
        <div class="form-group">
            <button type="submit">Complete Registration</button>
        </div>
    </form>
    
    <div class="login-link">
        Already registered? <a href="login_unified.php">Sign in here</a>
    </div>
</div>

</body>
</html>
