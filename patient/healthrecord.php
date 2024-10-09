<?php
// health_records.php

// Include database configuration
include '../access/config.php'; // Assuming this file contains your mysqli connection code

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the patient's email from the session
$patientEmail = $_SESSION['user_id'];

// Helper function to fetch PatientID from patients table
function getPatientId($conn, $email) {
    $query = "SELECT PatientID FROM patients WHERE Email = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Output error message
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($patientId);
    $stmt->fetch();
    $stmt->close();
    
    return $patientId;
}

// Helper function to fetch health records
function fetchHealthRecords($conn, $patientId) {
    $query = "SELECT hr.CreatedAt, hr.Description, d.Name AS doctor_name
              FROM healthrecords hr
              JOIN appointments a ON hr.AppointmentID = a.AppointmentID
              JOIN doctors d ON a.DoctorID = d.DoctorID
              WHERE a.PatientID = ? 
              ORDER BY hr.CreatedAt DESC";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Output error message
    }
    
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get the PatientID from the patients table using the email
$patientId = getPatientId($conn, $patientEmail);

// Fetch health records
$healthRecords = fetchHealthRecords($conn, $patientId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Records - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Layout Styling */
        .dashboard-container {
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: #007bff;
            padding: 20px;
            flex: 0 0 250px;
            height: calc(100vh - 56px);
        }

        .dashboard-content {
            flex: 1;
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<?php include '../resources/includes/p_header.php'; ?> <!-- Include the header file -->

    <!-- Dashboard and Sidebar Container -->
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar file -->

        <!-- Health Records Content -->
        <div class="dashboard-content">
            <h1>My Health Records</h1>

            <!-- Medical History Section -->
            <div class="card">
                <div class="card-header">
                    <h5>Medical History</h5>
                </div>
                <div class="card-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Doctor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($healthRecords)) : ?>
                                <?php foreach ($healthRecords as $record) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($record['CreatedAt']); ?></td>
                                        <td><?= htmlspecialchars($record['Description']); ?></td>
                                        <td><?= htmlspecialchars($record['doctor_name']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="3">No health records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Share Records Section -->
            <div class="card">
                <div class="card-header">
                    <h5>Share Health Records</h5>
                </div>
                <div class="card-body">
                    <p>Share your health records with your doctor securely via email or directly through the system.</p>
                    <button class="btn btn-primary">Share Records</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
