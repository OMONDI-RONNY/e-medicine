<?php
session_start();
include '../access/config.php'; // Include database configuration

// Redirect to login if the lab user is not logged in
if (!isset($_SESSION['lab_user'])) {
    header('Location: login.php');
    exit();
}

// Check if the form was submitted for updating
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_test_result'])) {
    $labId = (int)$_POST['lab_id']; // Get LabID from the POST request
    $testName = $conn->real_escape_string($_POST['test_name']);
    $symptoms = $conn->real_escape_string($_POST['symptoms']);
    $result = $conn->real_escape_string($_POST['result']);

    // Update query to modify the existing test result using LabID
    $updateQuery = "UPDATE laboratory SET TestName = '$testName', Symptoms = '$symptoms', Result = '$result', UpdatedAt = NOW() 
                    WHERE LabID = '$labId'";
    
    try {
        if ($conn->query($updateQuery) === TRUE) {
            echo "<script>alert('Test result updated successfully!'); window.location.href='test.php';</script>";
        } else {
            throw new Exception("Error updating record: " . $conn->error);
        }
    } catch (Exception $e) {
        // Redirect with error message
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: test.php');
        exit();
    }
}

// Function to fetch patient test results
function getPatientTestResults($conn) {
    $results = [];
    $query = "SELECT LabID, PatientID, TestName, TestDate, Result, Symptoms 
              FROM laboratory 
              ORDER BY TestDate DESC";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    
    return $results;
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
                            <th>Lab ID</th>
                            <th>Patient ID</th>
                            <th>Test</th>
                            <th>Date</th>
                            <th>Result</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testResults as $result): ?>
                            <tr>
                                <td><?php echo $result['LabID']; ?></td>
                                <td><?php echo $result['PatientID']; ?></td>
                                <td><?php echo $result['TestName']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($result['TestDate'])); ?></td>
                                <td><?php echo $result['Result'] ? $result['Result'] : 'Pending'; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="openViewModal('<?php echo $result['TestName']; ?>', '<?php echo $result['TestDate']; ?>', '<?php echo $result['Result']; ?>', '<?php echo $result['Symptoms']; ?>')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Test Result</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Test Name:</strong> <span id="viewTestName"></span></p>
                    <p><strong>Test Date:</strong> <span id="viewTestDate"></span></p>
                    <p><strong>Symptoms:</strong> <span id="viewSymptoms"></span></p>
                    <p><strong>Result:</strong> <span id="viewResult"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Test Result</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="test.php" method="POST">
                        <input type="hidden" id="editLabId" name="lab_id"> <!-- Include LabID in the form -->
                        <input type="hidden" name="update_test_result" value="1">
                        <div class="form-group">
                            <label for="editTestName">Test Name</label>
                            <input type="text" class="form-control" id="editTestName" name="test_name" required>
                        </div>
                        <div class="form-group">
                            <label for="editSymptoms">Symptoms</label>
                            <input type="text" class="form-control" id="editSymptoms" name="symptoms" required>
                        </div>
                        <div class="form-group">
                            <label for="editResult">Result</label>
                            <input type="text" class="form-control" id="editResult" name="result" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Result</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function openViewModal(testName, testDate, result, symptoms) {
            document.getElementById('viewTestName').textContent = testName;
            document.getElementById('viewTestDate').textContent = testDate;
            document.getElementById('viewSymptoms').textContent = symptoms;
            document.getElementById('viewResult').textContent = result;
            $('#viewModal').modal('show');
        }

        function openEditModal(labId, testName, symptoms, result) {
            document.getElementById('editLabId').value = labId; // Set LabID in edit modal
            document.getElementById('editTestName').value = testName;
            document.getElementById('editSymptoms').value = symptoms;
            document.getElementById('editResult').value = result;
            $('#editModal').modal('show');
        }
    </script>
</body>
</html>
