<?php
// prescriptions.php

// Include the database configuration file
include '../access/config.php';

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the email from the session
$userEmail = $_SESSION['user_id']; // Assuming 'user_id' corresponds to the email

// Fetch the PatientID using the user's email
$query = "SELECT PatientID FROM patients WHERE email = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    error_log("Prepare failed: " . $conn->error); // Log error message
    die("MySQL Prepare Error: " . $conn->error); // Debugging: show error
}

$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($patientId);
$stmt->fetch();
$stmt->close();

// Debugging: check if PatientID is correctly retrieved
if (!$patientId) {
    die("Error: Patient ID not found for the logged-in user.");
}

// Helper function to fetch active prescriptions for the logged-in patient
function fetchActivePrescriptions($conn, $patientId) {
    $query = "SELECT l.Result AS Medication, p.Dosage, p.RefillsRemaining, d.firstname AS doctor_name 
              FROM prescriptions p 
              JOIN appointments a ON p.AppointmentID = a.AppointmentID 
              JOIN doctors d ON a.DoctorID = d.DoctorID 
              JOIN laboratory l ON a.PatientID = l.PatientID 
              WHERE a.PatientID = ? AND p.Status = 'Active'";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error); // Log error message
        die("MySQL Prepare Error: " . $conn->error); // Debugging: show error
    }
    
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


// Helper function to fetch prescription history for the logged-in patient
function fetchPrescriptionHistory($conn, $patientId) {
    $query = "SELECT l.Result AS Medication, p.Dosage, p.CreatedAt, p.Status 
              FROM prescriptions p 
              JOIN appointments a ON p.AppointmentID = a.AppointmentID 
              JOIN laboratory l ON a.PatientID = l.PatientID 
              WHERE a.PatientID = ? 
              ORDER BY p.CreatedAt DESC";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error); // Log error message
        die("MySQL Prepare Error: " . $conn->error); // Debugging: show error
    }
    
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


// Fetch active prescriptions and prescription history for the logged-in patient
$activePrescriptions = fetchActivePrescriptions($conn, $patientId);
$prescriptionHistory = fetchPrescriptionHistory($conn, $patientId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescriptions - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .dashboard-container {
            display: flex;
        }

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

<?php include '../resources/includes/p_header.php'; ?>

    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <div class="dashboard-content">
            <h1>My Prescriptions</h1>

            <div class="mb-3">
                <input type="text" id="medicationSearch" class="form-control" placeholder="Search by Medication..." onkeyup="filterPrescriptions()">
                <input type="date" id="dateFilter" class="form-control mt-2" onchange="filterPrescriptions()">
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Active Prescriptions</h5>
                </div>
                <div class="card-body">
                    <table class="table table-responsive" id="activePrescriptionsTable">
                        <thead>
                            <tr>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Refills Remaining</th>
                                <th>Doctor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($activePrescriptions)) : ?>
                                <?php foreach ($activePrescriptions as $prescription) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($prescription['Medication']); ?></td>
                                        <td><?= htmlspecialchars($prescription['Dosage']); ?></td>
                                        <td><?= htmlspecialchars($prescription['RefillsRemaining']); ?></td>
                                        <td><?= htmlspecialchars($prescription['doctor_name']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="4">No active prescriptions found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Prescription History</h5>
                </div>
                <div class="card-body">
                    <table class="table table-responsive" id="prescriptionHistoryTable">
                        <thead>
                            <tr>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Date Prescribed</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($prescriptionHistory)) : ?>
                                <?php foreach ($prescriptionHistory as $history) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($history['Medication']); ?></td>
                                        <td><?= htmlspecialchars($history['Dosage']); ?></td>
                                        <td><?= htmlspecialchars($history['CreatedAt']); ?></td>
                                        <td><span class="badge badge-<?= ($history['Status'] == 'completed') ? 'success' : 'danger'; ?>"><?= htmlspecialchars($history['Status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="4">No prescription history found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function filterPrescriptions() {
            const medicationInput = document.getElementById('medicationSearch').value.toLowerCase();
            const dateFilter = document.getElementById('dateFilter').value;

            // Filter active prescriptions
            const activeTable = document.getElementById('activePrescriptionsTable');
            const activeRows = activeTable.getElementsByTagName('tr');

            for (let i = 1; i < activeRows.length; i++) {
                const tdMedication = activeRows[i].getElementsByTagName('td')[0];

                let showRow = true;

                if (tdMedication) {
                    const medicationText = tdMedication.textContent || tdMedication.innerText;
                    if (medicationText.toLowerCase().indexOf(medicationInput) === -1) {
                        showRow = false;
                    }
                }

                activeRows[i].style.display = showRow ? '' : 'none';
            }

            // Filter prescription history
            const historyTable = document.getElementById('prescriptionHistoryTable');
            const historyRows = historyTable.getElementsByTagName('tr');

            for (let j = 1; j < historyRows.length; j++) {
                const tdHistoryMedication = historyRows[j].getElementsByTagName('td')[0];
                const tdDate = historyRows[j].getElementsByTagName('td')[2];

                let showHistoryRow = true;

                if (tdHistoryMedication) {
                    const historyMedicationText = tdHistoryMedication.textContent || tdHistoryMedication.innerText;
                    if (historyMedicationText.toLowerCase().indexOf(medicationInput) === -1) {
                        showHistoryRow = false;
                    }
                }

                if (dateFilter && tdDate) {
                    const dateText = tdDate.textContent || tdDate.innerText;
                    if (dateText !== dateFilter) {
                        showHistoryRow = false;
                    }
                }

                historyRows[j].style.display = showHistoryRow ? '' : 'none';
            }
        }
    </script>
</body>
</html>
