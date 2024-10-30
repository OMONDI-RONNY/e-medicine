<?php
session_start(); // Start the session

// Include the database configuration
include '../access/config.php';

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

// Prepare the SQL statement for retrieving patients associated with the logged-in doctor
$stmt = $conn->prepare("
    SELECT p.PatientID, p.firstname, p.Age, p.Gender, p.CreatedAt, p.Status
    FROM patients p
    JOIN appointments a ON p.PatientID = a.PatientID
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
$patients = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement
$stmt->close();

// Handle adding a new patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPatient'])) {
    $patientName = $_POST['patientName'];
    $patientAge = $_POST['patientAge'];
    $patientGender = $_POST['patientGender'];
    $patientStatus = $_POST['patientStatus'];

    // Prepare the SQL statement for inserting a new patient
    $insertStmt = $conn->prepare("
        INSERT INTO patients (firstname, Age, Gender, Status) VALUES (?, ?, ?, ?)
    ");

    if ($insertStmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters and execute
    $insertStmt->bind_param("siss", $patientName, $patientAge, $patientGender, $patientStatus);
    
    if ($insertStmt->execute()) {
        // Redirect to the same page to refresh the list after adding
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . htmlspecialchars($insertStmt->error);
    }

    // Close the insert statement
    $insertStmt->close();
}

// Handle updating an existing patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePatient'])) {
    $patientID = $_POST['patientID'];
    $patientName = $_POST['patientName'];
    $patientAge = $_POST['patientAge'];
    $patientGender = $_POST['patientGender'];
    $patientStatus = $_POST['patientStatus'];

    // Prepare the SQL statement for updating a patient
    $updateStmt = $conn->prepare("
        UPDATE patients 
        SET firstname = ?, Age = ?, Gender = ?, Status = ?
        WHERE PatientID = ?
    ");

    if ($updateStmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters and execute
    $updateStmt->bind_param("sisss", $patientName, $patientAge, $patientGender, $patientStatus, $patientID);
    
    if ($updateStmt->execute()) {
        // Redirect to the same page to refresh the list after updating
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . htmlspecialchars($updateStmt->error);
    }

    // Close the update statement
    $updateStmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management - E-Medicine System</title>
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

        .content {
            flex: 1;
            padding: 20px;
        }

        .patient-table {
            margin-top: 20px;
        }

        .form-section {
            margin-top: 40px;
        }
    </style>
</head>
<body>

<?php include '../resources/includes/d_header.php'; ?> <!-- Include the header file -->
    
<div class="dashboard"> <!-- Flex container for sidebar and content -->
    <?php include '../resources/includes/d_sidebar.php'; ?> <!-- Include the sidebar file -->

    <!-- Page Content -->
    <div class="content">
        <h1>Patient Management</h1>

        <!-- Patient Table -->
        <div class="patient-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($patient['Age']); ?></td>
                            <td><?php echo htmlspecialchars($patient['Gender']); ?></td>
                            <td><span class="badge <?php echo $patient['Status'] == 'Active' ? 'badge-success' : 'badge-danger'; ?>"><?php echo htmlspecialchars($patient['Status']); ?></span></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editPatientModal<?php echo $patient['PatientID']; ?>">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>

                        <!-- Edit Patient Modal -->
                        <div class="modal fade" id="editPatientModal<?php echo $patient['PatientID']; ?>" tabindex="-1" aria-labelledby="editPatientModalLabel<?php echo $patient['PatientID']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPatientModalLabel<?php echo $patient['PatientID']; ?>">Edit Patient</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                        <div class="modal-body">
                                            <input type="hidden" name="patientID" value="<?php echo $patient['PatientID']; ?>">
                                            <div class="form-group">
                                                <label for="patientName">Name:</label>
                                                <input type="text" class="form-control" name="patientName" id="patientName" value="<?php echo htmlspecialchars($patient['firstname']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="patientAge">Age:</label>
                                                <input type="number" class="form-control" name="patientAge" id="patientAge" value="<?php echo htmlspecialchars($patient['Age']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="patientGender">Gender:</label>
                                                <select class="form-control" name="patientGender" id="patientGender" required>
                                                    <option <?php echo ($patient['Gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                    <option <?php echo ($patient['Gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="patientStatus">Status:</label>
                                                <select class="form-control" name="patientStatus" id="patientStatus" required>
                                                    <option <?php echo ($patient['Status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                                    <option <?php echo ($patient['Status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" name="updatePatient" class="btn btn-success">Update Patient</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Button to trigger modal for adding new patient -->
        <button type="button" class="btn btn-success mt-3" data-toggle="modal" data-target="#addPatientModal">
            Add New Patient
        </button>

        <!-- Modal for Adding New Patient -->
        <div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPatientModalLabel">Add New Patient</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="patientName">Name:</label>
                                <input type="text" class="form-control" name="patientName" id="patientName" placeholder="Enter patient's name" required>
                            </div>
                            <div class="form-group">
                                <label for="patientAge">Age:</label>
                                <input type="number" class="form-control" name="patientAge" id="patientAge" placeholder="Enter patient's age" required>
                            </div>
                            <div class="form-group">
                                <label for="patientGender">Gender:</label>
                                <select class="form-control" name="patientGender" id="patientGender" required>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="patientStatus">Status:</label>
                                <select class="form-control" name="patientStatus" id="patientStatus" required>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="addPatient" class="btn btn-success">Add Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <?php include '../resources/includes/d_notification.php'; ?> <!-- Include the notifications file -->
</div>
    
<?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
