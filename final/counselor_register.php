<?php
require_once("database.php");
session_start();

$message = "";

if (isset($_POST['register'])) {

    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $bio   = $_POST['bio'];
    $qualifications = $_POST['qualifications'];
    $password = $_POST['password'];

    if (!empty($name) && !empty($email) && !empty($password)) {

        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        
        $check = "SELECT * FROM counselor WHERE email='$email'";
        $res = mysqli_query($mysqli, $check);

        if (mysqli_num_rows($res) > 0) {
            $message = "Email already registered ❌";
        } else {

            $sql = "
                INSERT INTO counselor 
                (name, email, phone, bio, qualifications, password)
                VALUES
                ('$name', '$email', '$phone', '$bio', '$qualifications', '$hashed_password')
            ";

            if (mysqli_query($mysqli, $sql)) {
                $message = "Counselor registered successfully ✅";
            } else {
                $message = "Something went wrong ❌";
            }
        }
    } else {
        $message = "Please fill all required fields ❗";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Counselor Registration</title>
    <meta charset="UTF-8">

    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(135deg, #89f7fe, #66a6ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 25px;
            width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #2563eb;
            margin-bottom: 20px;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        textarea {
            resize: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(102,126,234,0.5);
        }

        .msg {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 10px;
            background: #f1f5f9;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="card">
    <h2> Counselor Registration</h2>

    <?php if (!empty($message)): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number">
        <textarea name="bio" rows="3" placeholder="Short bio"></textarea>
        <input type="text" name="qualifications" placeholder="Qualifications (e.g. MSc Psychology)">
        <input type="password" name="password" placeholder="Password" required>

        <button name="register">Register</button>
    </form>

    <div class="login-link">
        Already registered? <a href="counselor_login.php">Login</a>
    </div>
</div>

</body>
</html>
