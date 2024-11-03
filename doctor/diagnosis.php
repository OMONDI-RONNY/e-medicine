<?php
session_start();
include '../access/config.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $appointment_id = $_POST['appointment_id'];
    $symptoms = $_POST['symptoms'];
    $medical_history = $_POST['medical_history'];
    $clinical_evaluation = $_POST['clinical_evaluation'];
    $diagnosis = $_POST['diagnosis'];

   
    $stmt = $conn->prepare("INSERT INTO diagnoses (appointment_id, symptoms, medical_history, clinical_evaluation, diagnosis) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $appointment_id, $symptoms, $medical_history, $clinical_evaluation, $diagnosis);

    if ($stmt->execute()) {
        echo "Diagnosis added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}


$doctor_id = $_SESSION['doctor_id'];
$result = $conn->prepare("SELECT appointmentID FROM appointments WHERE doctorID = ? AND status = 'Completed'");
$result->bind_param("i", $doctor_id);
$result->execute();
$appointments = $result->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Diagnosis</title>
</head>
<body>
    <h1>Add Diagnosis</h1>

    <form method="POST" action="">
        <label for="appointment_id">Select Appointment:</label>
        <select name="appointment_id" required>
            <option value="">Select Appointment</option>
            <?php while ($row = $appointments->fetch_assoc()): ?>
                <option value="<?php echo $row['appointment_id']; ?>">Appointment ID: <?php echo $row['appointment_id']; ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="symptoms">Symptoms:</label>
        <textarea name="symptoms" required placeholder="Enter patient symptoms"></textarea><br><br>

        <label for="medical_history">Medical History:</label>
        <textarea name="medical_history" required placeholder="Enter patient's medical history"></textarea><br><br>

        <label for="clinical_evaluation">Clinical Evaluation:</label>
        <textarea name="clinical_evaluation" required placeholder="Enter clinical evaluation details"></textarea><br><br>

        <label for="diagnosis">Diagnosis:</label>
        <textarea name="diagnosis" required placeholder="Enter diagnosis"></textarea><br><br>

        <button type="submit">Submit Diagnosis</button>
    </form>
</body>
</html>
