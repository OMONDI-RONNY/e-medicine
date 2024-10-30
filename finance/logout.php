<?php
session_start(); // Start the session

// Unset all session variables
unset($_SESSION['finance_id']);
unset($_SESSION['username']);

// Destroy the session
session_destroy();

// Redirect to the finance login page
header("Location: login.php");
exit();
?>
