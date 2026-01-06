<?php 
require_once("database.php");
session_start();


$c_id = $_SESSION['c_id'] ?? 1;


if (isset($_POST['update_profile'])) {

    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $bio   = $_POST['bio'];

    
    if (!empty($_FILES['profile_pic']['name'])) {

        $file_name = time() . "_" . $_FILES['profile_pic']['name'];
        $tmp_name  = $_FILES['profile_pic']['tmp_name'];
        $upload_path = "uploads/" . $file_name;

        move_uploaded_file($tmp_name, $upload_path);

        $update = "
            UPDATE counselor SET
            name='$name',
            email='$email',
            phone='$phone',
            bio='$bio',
            profile_pic='$file_name'
            WHERE c_id=$c_id
        ";

    } else {

        $update = "
            UPDATE counselor SET
            name='$name',
            email='$email',
            phone='$phone',
            bio='$bio'
            WHERE c_id=$c_id
        ";
    }

    mysqli_query($mysqli, $update);
    $profile_msg = "Profile updated successfully ✅";
}


if (isset($_POST['add_availability'])) {
    $day  = $_POST['day'];
    $time = $_POST['time_slot'];

    $insert = "
        INSERT INTO counselor_availability (counselor_id, day, time_slot)
        VALUES ($c_id, '$day', '$time')
    ";
    mysqli_query($mysqli, $insert);
    $avail_msg = "Availability added successfully 🕒";
}


$c_sql = "SELECT * FROM counselor WHERE c_id=$c_id";
$c_res = mysqli_query($mysqli, $c_sql);
$counselor = mysqli_fetch_assoc($c_res);


$a_sql = "SELECT * FROM counselor_availability WHERE counselor_id=$c_id";
$a_res = mysqli_query($mysqli, $a_sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile & Availability</title>

<style>
body{
    margin:0;
    font-family: 'Segoe UI', sans-serif;
    background:#f4f6fb;
}
.dashboard{
    display:flex;
}
.sidebar{
    width:240px;
    background:#1e293b;
    height:100vh;
    color:white;
    padding:20px;
}
.sidebar h2{
    margin-bottom:30px;
}
.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:10px;
    margin-bottom:10px;
    border-radius:6px;
}
.sidebar a:hover{
    background:#334155;
}

.main{
    flex:1;
    padding:30px;
}

.header{
    font-size:26px;
    margin-bottom:20px;
}

.card{
    background:white;
    border-radius:14px;
    padding:25px;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

input, textarea{
    width:100%;
    padding:10px;
    margin-bottom:12px;
    border-radius:6px;
    border:1px solid #ccc;
}

button{
    padding:10px 18px;
    border:none;
    background:#2563eb;
    color:white;
    border-radius:6px;
    cursor:pointer;
    font-size:15px;
}
button:hover{
    background:#1e40af;
}

.success{
    background:#dcfce7;
    color:#166534;
    padding:10px;
    border-radius:6px;
    margin-bottom:10px;
}

.profile-pic{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    margin-bottom:15px;
    border:4px solid #2563eb;
}
        
.btn {
            
    display: inline-block;    
    text-decoration: none;
    background: linear-gradient(45deg, #56ab2f, #a8e063);
    color: white;
    padding: 10px 18px;
    border-radius: 18px;
    font-size: 14px;
    box-shadow: 0 8px 20px rgba(86,171,47,0.4);
        }
</style>
</head>

<body>

<div class="dashboard">

    
    <div class="sidebar">
        <h2>Counselor Profile</h2>
        <a href="counselor_dashboard.php">Dashboard</a>
        <a href="counselor_profile.php" style="background:#334155;">Profile & Availability</a>
        <a href="#" style="background:#334155;">Events</a>
    </div>

    
    <div class="main">

        <div class="header">Profile & Availability</div>

        
        <div class="card">
            <h3>👤 Update Profile</h3>

            <?php if (!empty($profile_msg)): ?>
                <div class="success"><?= $profile_msg ?></div>
            <?php endif; ?>

            <?php if (!empty($counselor['profile_pic'])): ?>
                <img src="uploads/<?= $counselor['profile_pic'] ?>" class="profile-pic">
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="name" value="<?= $counselor['name'] ?>" required>
                <input type="email" name="email" value="<?= $counselor['email'] ?>" required>
                <input type="text" name="phone" value="<?= $counselor['phone'] ?>" placeholder="Phone">

                <input type="file" name="profile_pic" accept="image/*">

                <textarea name="bio" rows="4" placeholder="Short bio"><?= $counselor['bio'] ?></textarea>

                <button name="update_profile">Update Profile</button>
            </form>
        </div>

        
        <div class="card">
            <h3>🕒 Add Availability</h3>
            <a href="counselor_availability.php" class="btn">Add slot</a>
        </div>

    </div>
</div>

</body>
</html>
