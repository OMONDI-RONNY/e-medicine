<?php
session_name('patient_session'); // Set the session name here
session_start(); // Then start the session

// Check if the patient is logged in
if (isset($_SESSION['patientID'])) {
    // Unset specific session variables for the patient
    unset($_SESSION['patientID']);
    unset($_SESSION['patient_name']); // Assuming you store the patient's name in session
}

// Redirect to the login page or any other page
header("Location: login.php");
exit(); // Important to exit after header redirect
?>
