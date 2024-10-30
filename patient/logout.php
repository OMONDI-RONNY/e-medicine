<?php
session_name('patient_session'); // Set the session name here
session_start(); // Then start the session

// Check if the patient is logged in
$_SESSION = [];

// Destroy the session if it exists
if (session_id() != '') {
    session_destroy(); // Destroy the session
}
// Redirect to the login page or any other page
header("Location: ../index.php");
exit(); // Important to exit after header redirect
?>
