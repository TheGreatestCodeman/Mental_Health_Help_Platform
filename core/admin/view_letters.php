



<!DOCTYPE html>
<html>
<head>
    <title>View Letters to Send</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="view_letters.php">View Letters to Send</a>
</div>

<div class="main">
    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            ✅ All letters have been released successfully.
        </div>
    <?php endif; ?>
    <div class="card">
        <h3>Letters Ready to Send</h3>

        <table width="100%" cellpadding="10" border="1">
            <tr>
                <th>Letter ID</th>
                <th>User ID</th>
                <th>Release Date</th>
                <th>Letter Text</th>
            </tr>

            <?php
            require_once('database.php');
            $today = date("Y-m-d");

            $sql = "SELECT letter_id, user_id, letter_text, release_date 
                    FROM future_letters 
                    WHERE release_date <= '$today' 
                    AND is_released = 0";

            $result = mysqli_query($mysqli,$sql);
            ?>

            <?php 
            if(mysqli_num_rows($result) > 0){
                     while($row = mysqli_fetch_array($result)){
                        ?>
            <tr>
                <td><?php echo $row['letter_id']; ?></td>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['release_date'] ?></td>
                <td><?php echo $row['letter_text'] ?></td>
            </tr>
            <?php } 
            }
            else{ ?>
            <tr>
                <td colspan="4" style="text-align:center;">No letters to display</td>
            </tr>
            <?php } ?>
        </table>

        <br>

        <form action="release_all.php" method="POST">
            <button class="btn" type="submit">Release All Letters</button>
        </form>

    </div>
</div>

</body>
</html>
