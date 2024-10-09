<?php
session_start(); // Start the session

// Include the database configuration
include '../access/config.php';

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

// Prepare the SQL statement for retrieving prescriptions
$stmt = $conn->prepare("
    SELECT p.Name AS PatientName, pr.Medication, pr.Dosage, pr.CreatedAt
    FROM prescriptions pr
    JOIN appointments a ON pr.AppointmentID = a.AppointmentID
    JOIN patients p ON a.PatientID = p.PatientID
    WHERE a.DoctorID = ?
");

// Check if prepare was successful
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters and execute
$stmt->bind_param("s", $_SESSION['doctor_id']);
$stmt->execute();
$result = $stmt->get_result();
$prescriptions = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Management - E-Medicine System</title>
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
        .navbar-brand, .nav-link {
            color: white !important;
        }

        .dashboard {
            display: flex;
        }

        .container {
            padding: 20px;
            flex: 1;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include '../resources/includes/d_header.php'; ?> <!-- Include the header file -->
<div class="dashboard"> <!-- Flex container for sidebar and content -->
    <?php include '../resources/includes/d_sidebar.php'; ?> <!-- Include the sidebar file -->

    <!-- Prescription Management Content -->
    <div class="container">
        <h1>Prescription Management</h1>

        <!-- Add New Prescription Button -->
        <div class="mb-3">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addPrescriptionModal">Add New Prescription</button>
        </div>

        <!-- Prescription List Table -->
        <div class="card">
            <div class="card-header">
                <h5>Prescription List</h5>
            </div>
            <div class="card-body">
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Medication</th>
                            <th>Dosage</th>
                            <th>Date Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prescriptions as $prescription): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prescription['PatientName']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['Medication']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['Dosage']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['CreatedAt']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Prescription Modal -->
    <div class="modal fade" id="addPrescriptionModal" tabindex="-1" role="dialog" aria-labelledby="addPrescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPrescriptionModalLabel">Add New Prescription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="form-group">
                            <label for="patientName">Patient Name:</label>
                            <input type="text" class="form-control" name="patientName" id="patientName" placeholder="Enter patient's name" required>
                        </div>
                        <div class="form-group">
                            <label for="medication">Medication:</label>
                            <input type="text" class="form-control" name="medication" id="medication" placeholder="Enter medication name" required>
                        </div>
                        <div class="form-group">
                            <label for="dosage">Dosage:</label>
                            <input type="text" class="form-control" name="dosage" id="dosage" placeholder="Enter dosage" required>
                        </div>
                        <button type="submit" name="addPrescription" class="btn btn-success">Add Prescription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the Notifications Sidebar -->
    <?php include 'd_notification.php'; ?> <!-- Include the notifications file -->
</div>

<?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
