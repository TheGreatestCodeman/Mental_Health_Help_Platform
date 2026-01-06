<?php
session_start();

// Redirect to unified login that handles users, counselors, and admins
header("Location: login_unified.php");
exit;
?>