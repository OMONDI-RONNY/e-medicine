<?php
session_start();
include '../access/config.php'; // Include database configuration

// Redirect to login if the lab user is not logged in
if (!isset($_SESSION['lab_user'])) {
    header('Location: login.php');
    exit();
}

// Function to fetch patient test results
function getPatientTestResults($conn) {
    $results = [];
    $query = "SELECT p.firstname, p.lastname, l.TestName, l.TestDate, l.Result 
              FROM laboratory l 
              JOIN patients p ON l.PatientID = p.PatientID 
              ORDER BY l.TestDate DESC";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    
    return $results;
}

// Handle form submission to add a new test result
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patient_id'], $_POST['test_name'], $_POST['symptoms'], $_POST['result'])) {
    $patientId = (int)$_POST['patient_id'];
    $testName = $conn->real_escape_string($_POST['test_name']);
    $symptoms = $conn->real_escape_string($_POST['symptoms']);
    $result = $conn->real_escape_string($_POST['result']);

    // Insert new test result for the specified patient
    $query = "INSERT INTO laboratory (PatientID, TestName, Symptoms, Result, TestDate, CreatedAt)
              VALUES ('$patientId', '$testName', '$symptoms', '$result', NOW(), NOW())";
    
    if ($conn->query($query) === TRUE) {
        echo "<script>alert('New test result added successfully!'); window.location.href='test_results.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$testResults = getPatientTestResults($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .sidebar {
            height: 100vh;
            background-color: #007bff; 
            color: white;
            position: fixed;
            width: 220px;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            margin: 10px 0;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3;
            padding-left: 10px; 
        }
        .content {
            margin-left: 240px; 
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .card-header h5 {
            margin: 0;
        }
        .table {
            border-radius: 10px;
            overflow: hidden; 
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?> 

    <div class="content">
        <h1>Test Results</h1>
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-vial"></i> Patient Test Results</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Test</th>
                            <th>Date</th>
                            <th>Result</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testResults as $result): ?>
                            <tr>
                                <td><?php echo $result['firstname'] . ' ' . $result['lastname']; ?></td>
                                <td><?php echo $result['TestName']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($result['TestDate'])); ?></td>
                                <td><?php echo $result['Result'] ? $result['Result'] : 'Pending'; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-right mt-3">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addTestModal">
                        <i class="fas fa-plus"></i> Add Test Result
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Test Modal -->
    <div class="modal fade" id="addTestModal" tabindex="-1" role="dialog" aria-labelledby="addTestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTestModalLabel">Add Test Result</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="test_results.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="patientId">Patient ID</label>
                            <input type="number" class="form-control" id="patientId" name="patient_id" required>
                        </div>
                        <div class="form-group">
                            <label for="testName">Test Name</label>
                            <input type="text" class="form-control" id="testName" name="test_name" required>
                        </div>
                        <div class="form-group">
                            <label for="symptoms">Symptoms</label>
                            <input type="text" class="form-control" id="symptoms" name="symptoms" required>
                        </div>
                        <div class="form-group">
                            <label for="result">Result</label>
                            <input type="text" class="form-control" id="result" name="result" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
