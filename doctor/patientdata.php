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
    SELECT p.PatientID, p.Name, p.Age, p.Gender, p.CreatedAt, p.Status
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
    $lastVisit = $_POST['lastVisit'];
    $patientStatus = $_POST['patientStatus'];

    // Prepare the SQL statement for inserting a new patient
    $insertStmt = $conn->prepare("
        INSERT INTO patients (Name, Age, Gender, CreatedAt, Status) VALUES (?, ?, ?, ?, ?)
    ");

    if ($insertStmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters and execute
    $insertStmt->bind_param("sisss", $patientName, $patientAge, $patientGender, $lastVisit, $patientStatus);
    
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
                        <th>Last Visit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['Name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['Age']); ?></td>
                            <td><?php echo htmlspecialchars($patient['Gender']); ?></td>
                            <td><?php echo htmlspecialchars($patient['CreatedAt']); ?></td>
                            <td><span class="badge <?php echo $patient['Status'] == 'Active' ? 'badge-success' : 'badge-danger'; ?>"><?php echo htmlspecialchars($patient['Status']); ?></span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">View</button>
                                <button class="btn btn-warning btn-sm">Edit</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Form Section to Add New Patient -->
        <div class="form-section">
            <h2>Add New Patient</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
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
                    <label for="lastVisit">Last Visit:</label>
                    <input type="date" class="form-control" name="lastVisit" id="lastVisit" required>
                </div>
                <div class="form-group">
                    <label for="patientStatus">Status:</label>
                    <select class="form-control" name="patientStatus" id="patientStatus" required>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
                <button type="submit" name="addPatient" class="btn btn-success">Add Patient</button>
            </form>
        </div>
    </div>

    <?php include 'd_notification.php'; ?> <!-- Include the notifications file -->
</div>
    
<?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
