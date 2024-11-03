<?php
session_start();


include '../access/config.php';


if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $patientID = $_POST['patientID'];
    $appointmentID = $_POST['appointmentID'];
    $symptoms = $_POST['symptoms'];
    $recommendation = $_POST['recommendation'];
    $fee = $_POST['fee']; 

    
    $conn->begin_transaction();

    try {
        
        $stmt1 = $conn->prepare("INSERT INTO laboratory (PatientID, AppointmentID, TestName, Symptoms, TestDate) VALUES (?, ?, ?, ?, NOW())");
        $stmt1->bind_param('iiss', $patientID, $appointmentID, $recommendation, $symptoms);
        $stmt1->execute();

        
        $stmt2 = $conn->prepare("INSERT INTO finance (PatientID, Amount, PaymentStatus, Description) VALUES (?, ?, 'Unpaid', 'Consultation Fee')");
        $stmt2->bind_param('id', $patientID, $fee);
        $stmt2->execute();

       
        $stmt3 = $conn->prepare("INSERT INTO prescriptions (patientID, AppointmentID) VALUES (?, ?)");
        $stmt3->bind_param('ii', $patientID, $appointmentID);
        $stmt3->execute();

        
        $conn->commit();

        
        header("Location: appointment.php");
        exit();
    } catch (Exception $e) {
       
        $conn->rollback();
        $errorMsg = "Error submitting consultation details: " . $e->getMessage();
       
    }

   
    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
}


$conn->close();
?>
