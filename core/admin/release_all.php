<?php
session_start();
require_once('database.php');

$today = date("Y-m-d");

$sql = "UPDATE future_letters
        SET is_released = 1
        WHERE release_date <= '$today'
        AND is_released = 0";

$result = mysqli_query($mysqli, $sql);

if (mysqli_affected_rows($mysqli)) {
    header("Location: view_letters.php?success=1");
} 
else {
    header("Location: view_letters.php");
    echo "Error releasing letters.";
}
?>
