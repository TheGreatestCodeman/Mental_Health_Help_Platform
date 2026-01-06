<?php
require_once("database.php");
session_start();

$error = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM counselor WHERE email='$email'";
    $result = mysqli_query($mysqli, $sql);

    if (mysqli_num_rows($result) === 1) {

        $counselor = mysqli_fetch_assoc($result);

        
        if (password_verify($password, $counselor['password'])) {

            
            $_SESSION['c_id'] = $counselor['c_id'];

            header("Location: counselor_dashboard.php");
            exit;

        } else {
            $error = "Incorrect password ❌";
        }

    } else {
        $error = "Counselor not found ❌";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Counselor Login</title>
    <meta charset="UTF-8">

    <style>
        body{
            margin:0;
            height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background:linear-gradient(135deg,#89f7fe,#66a6ff);
            font-family:'Segoe UI',sans-serif;
        }

        .card{
            background:white;
            padding:35px;
            width:360px;
            border-radius:18px;
            box-shadow:0 20px 40px rgba(0,0,0,0.15);
        }

        h2{
            text-align:center;
            margin-bottom:20px;
            color:#1e293b;
        }

        input{
            width:100%;
            padding:12px;
            margin-bottom:14px;
            border-radius:8px;
            border:1px solid #ccc;
        }

        button{
            width:100%;
            padding:12px;
            background:#2563eb;
            color:white;
            border:none;
            border-radius:10px;
            font-size:16px;
            cursor:pointer;
        }

        button:hover{
            background:#1e40af;
        }

        .error{
            background:#fee2e2;
            color:#991b1b;
            padding:10px;
            border-radius:8px;
            margin-bottom:12px;
            text-align:center;
        }

        .link{
            text-align:center;
            margin-top:12px;
        }

        .link a{
            text-decoration:none;
            color:#2563eb;
        }
    </style>
</head>

<body>

<div class="card">
    <h2> Counselor Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button name="login">Login</button>
    </form>

    <div class="link">
        Don’t have an account?  
        <a href="counselor_register.php">Register</a>
    </div>
</div>

</body>
</html>
