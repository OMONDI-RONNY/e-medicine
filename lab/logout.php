<?php
// lab_logout.php
session_start();
if (isset($_SESSION['lab_user'])) {
    unset($_SESSION['lab_user']); // Unset specific session variable
}
session_destroy(); // Destroy the session
header('Location: login.php'); // Redirect to login page
exit();
?>
