<?php
session_name('doctor_session'); // Ensure the session name matches the one used for doctors
session_start(); // Start the session

// Check if the doctor is logged in
if (isset($_SESSION['doctorID'])) {
    // Unset specific session variables for the doctor
    unset($_SESSION['doctorID']);
   
}

// Optionally, destroy the session if you want to clear all data
// session_destroy(); // Uncomment if you want to destroy the session completely

// Redirect to the login page or any other page
header("Location: login.php");
exit(); // Important to exit after header redirect
?>
