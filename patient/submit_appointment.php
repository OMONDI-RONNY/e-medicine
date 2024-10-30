<?php
// submit_appointment.php

// Include database configuration
include '../access/config.php'; 

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

// Get the patient's email from the session
$patientEmail = $_SESSION['user_id'];

// Prepare variables for the appointment
$doctorId = $_POST['doctor_id'];
$appointmentDate = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];

// Fetch PatientID from the database using the email
$query = "SELECT PatientID FROM patients WHERE Email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $patientEmail);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if ($patient) {
    $patientId = $patient['PatientID'];

    // Insert the appointment into the database
    $insertQuery = "INSERT INTO appointments (PatientID, DoctorID, AppointmentDate, Status) VALUES (?, ?, ?, 'Scheduled')";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iis", $patientId, $doctorId, $appointmentDate);

    if ($insertStmt->execute()) {
        // Redirect to appointments page with a success message
        $_SESSION['message'] = "Appointment scheduled successfully!";
        header("Location: appointment.php");
        exit();
    } else {
        // Handle insertion error
        $_SESSION['error'] = "Error scheduling appointment: " . $insertStmt->error;
        header("Location: appointment.php");
        exit();
    }
} else {
    // Handle patient not found error
    $_SESSION['error'] = "Error: Patient not found.";
    header("Location: appointment.php");
    exit();
}
?>
