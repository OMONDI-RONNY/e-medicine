<?php



include '../access/config.php'; 


session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}


$patientEmail = $_SESSION['user_id'];


$doctorId = $_POST['doctor_id'];
$appointmentDate = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];


$query = "SELECT PatientID FROM patients WHERE Email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $patientEmail);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if ($patient) {
    $patientId = $patient['PatientID'];

   
    $insertQuery = "INSERT INTO appointments (PatientID, DoctorID, AppointmentDate, Status) VALUES (?, ?, ?, 'Scheduled')";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iis", $patientId, $doctorId, $appointmentDate);

    if ($insertStmt->execute()) {
        
        $_SESSION['message'] = "Appointment scheduled successfully!";
        header("Location: appointment.php");
        exit();
    } else {
        
        $_SESSION['error'] = "Error scheduling appointment: " . $insertStmt->error;
        header("Location: appointment.php");
        exit();
    }
} else {
    
    $_SESSION['error'] = "Error: Patient not found.";
    header("Location: appointment.php");
    exit();
}
?>
