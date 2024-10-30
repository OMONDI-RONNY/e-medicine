<?php
session_start(); // Start the session

// Include the database configuration
include '../access/config.php';

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $patientID = $_POST['patientID'];
    $appointmentID = $_POST['appointmentID'];
    $symptoms = $_POST['symptoms'];
    $recommendation = $_POST['recommendation'];
    $fee = $_POST['fee']; // Retrieve the consultation fee from the form

    // Begin transaction to ensure all inserts happen together
    $conn->begin_transaction();

    try {
        // Insert into laboratory table, including AppointmentID
        $stmt1 = $conn->prepare("INSERT INTO laboratory (PatientID, AppointmentID, TestName, Symptoms, TestDate) VALUES (?, ?, ?, ?, NOW())");
        $stmt1->bind_param('iiss', $patientID, $appointmentID, $recommendation, $symptoms);
        $stmt1->execute();

        // Insert into finance table with PaymentStatus defaulting to 'Unpaid'
        $stmt2 = $conn->prepare("INSERT INTO finance (PatientID, Amount, PaymentStatus, Description) VALUES (?, ?, 'Unpaid', 'Consultation Fee')");
        $stmt2->bind_param('id', $patientID, $fee);
        $stmt2->execute();

        // Insert into prescriptions table with only patientID and appointmentID
        $stmt3 = $conn->prepare("INSERT INTO prescriptions (patientID, AppointmentID) VALUES (?, ?)");
        $stmt3->bind_param('ii', $patientID, $appointmentID);
        $stmt3->execute();

        // Commit transaction
        $conn->commit();

        // Redirect to appointment.php upon successful submission
        header("Location: appointment.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if any insert fails
        $conn->rollback();
        $errorMsg = "Error submitting consultation details: " . $e->getMessage();
        // Handle error message as needed (e.g., display in the modal or log)
    }

    // Close the statements
    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
}

// Close the connection
$conn->close();
?>
