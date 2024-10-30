<?php
session_start();
include '../access/config.php'; // Include database configuration

// Redirect to login if the lab user is not logged in
if (!isset($_SESSION['lab_user'])) {
    header('Location: login.php');
    exit();
}

// Function to get dashboard statistics
function getDashboardStats($conn) {
    $stats = [];

    // Total patients
    $result = $conn->query("SELECT COUNT(*) as totalPatients FROM patients");
    $stats['totalPatients'] = $result ? $result->fetch_assoc()['totalPatients'] : 0;

    // Active patients
    $result = $conn->query("SELECT COUNT(*) as activePatients FROM patients WHERE status = 'Active'");
    $stats['activePatients'] = $result ? $result->fetch_assoc()['activePatients'] : 0;

    // Completed tests from the laboratory table
    $result = $conn->query("SELECT COUNT(*) as testsCompleted FROM laboratory WHERE Result IS NOT NULL");
    $stats['testsCompleted'] = $result ? $result->fetch_assoc()['testsCompleted'] : 0;

    return $stats;
}

// Function to get recent test results with PatientID and AppointmentID
function getRecentTests($conn) {
    $recentTests = [];
    $result = $conn->query("SELECT l.LabID, l.PatientID, l.AppointmentID, p.firstname AS patient_firstname, 
                                   p.lastname AS patient_lastname, l.TestName, l.Result, l.Symptoms 
                            FROM laboratory l
                            JOIN patients p ON l.PatientID = p.PatientID 
                            ORDER BY l.TestDate DESC LIMIT 5");

    while ($row = $result->fetch_assoc()) {
        $recentTests[] = $row;
    }

    return $recentTests;
}

// Function to get monthly statistics
function getMonthlyStats($conn) {
    $monthlyStats = [];
    $result = $conn->query("SELECT WEEK(TestDate) as week, COUNT(*) as testsCount 
                            FROM laboratory 
                            WHERE MONTH(TestDate) = MONTH(CURDATE())
                            GROUP BY WEEK(TestDate)");

    while ($row = $result->fetch_assoc()) {
        $monthlyStats[] = $row;
    }

    return $monthlyStats;
}

// Handle form submission to add or edit a test result
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lab_id'], $_POST['result'], $_POST['patient_id'], $_POST['appointment_id'])) {
    $labId = (int)$_POST['lab_id'];
    $result = $conn->real_escape_string($_POST['result']);
    $patientId = (int)$_POST['patient_id'];
    $appointmentId = (int)$_POST['appointment_id'];

    // Update existing test result
    $query = "UPDATE laboratory SET Result = '$result' WHERE LabID = '$labId'";
    $conn->query($query);

    // Update the LabID in the prescription table where PatientID and AppointmentID match
    $updatePrescriptionQuery = "UPDATE prescriptions SET LabID = '$labId' WHERE PatientID = '$patientId' AND AppointmentID = '$appointmentId'";
    $conn->query($updatePrescriptionQuery);

    echo "<script>alert('Test result and prescription updated successfully!');</script>";
}

// Fetch stats and recent data for initial page load
$stats = getDashboardStats($conn);
$recentTests = getRecentTests($conn);
$monthlyStats = getMonthlyStats($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body { background-color: #f4f7fa; font-family: 'Arial', sans-serif; margin: 0; }
        .sidebar { height: 100vh; background-color: #007bff; color: white; position: fixed; width: 220px; padding: 20px; }
        .sidebar a { color: white; margin: 10px 0; transition: background-color 0.3s; }
        .sidebar a:hover { background-color: #0056b3; padding-left: 10px; }
        .content { margin-left: 240px; padding: 20px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .card-header { background-color: #007bff; color: white; border-radius: 10px 10px 0 0; }
        .table { border-radius: 10px; overflow: hidden; }
        .stat-card { display: flex; justify-content: space-between; align-items: center; padding: 20px; border: 1px solid #007bff; border-radius: 10px; background-color: white; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <h1>Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <div>
                        <h4>Total Patients</h4>
                        <h2><?php echo $stats['totalPatients']; ?></h2>
                    </div>
                    <i class="fas fa-users fa-3x text-primary"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div>
                        <h4>Active Patients</h4>
                        <h2><?php echo $stats['activePatients']; ?></h2>
                    </div>
                    <i class="fas fa-user-check fa-3x text-primary"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div>
                        <h4>Tests Completed</h4>
                        <h2><?php echo $stats['testsCompleted']; ?></h2>
                    </div>
                    <i class="fas fa-vial fa-3x text-primary"></i>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-vial"></i> Recent Test Results</h5>
                    </div>
                    <div class="card-body">
                    <table class="table table-striped">
    <thead>
        <tr>
            <th>Patient First Name</th>
            <th>Patient Last Name</th>
            <th>Test Name</th>
            <th>Result</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentTests as $test): ?>
            <tr>
                <td><?php echo $test['patient_firstname']; ?></td>
                <td><?php echo $test['patient_lastname']; ?></td>
                <td><?php echo $test['TestName']; ?></td>
                <td><?php echo $test['Result'] ? $test['Result'] : 'Pending'; ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" 
                            onclick="editTest(<?php echo $test['LabID']; ?>, 
                                              '<?php echo addslashes($test['Result']); ?>', 
                                              <?php echo $test['PatientID']; ?>, 
                                              <?php echo $test['AppointmentID']; ?>)">
                        <i class="fas fa-edit"></i> Edit Result
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

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar"></i> Weekly Test Summary</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="weeklyStatsChart" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Test Modal -->
    <div class="modal fade" id="editTestModal" tabindex="-1" role="dialog" aria-labelledby="editTestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTestModalLabel">Edit Test Result</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editTestForm" method="POST" action="index.php">
                        <input type="hidden" name="lab_id" id="editLabId">
                        <input type="hidden" name="patient_id" id="editPatientId">
                        <input type="hidden" name="appointment_id" id="editAppointmentId">
                        <div class="form-group">
                            <label for="editResult">Result</label>
                            <input type="text" class="form-control" id="editResult" name="result" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Test Result</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include external libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Weekly Test Summary Chart
            var ctx = document.getElementById('weeklyStatsChart').getContext('2d');
            var weeklyStatsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($monthlyStats, 'week')); ?>,
                    datasets: [{
                        label: 'Tests Conducted',
                        data: <?php echo json_encode(array_column($monthlyStats, 'testsCount')); ?>,
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true }
                        }]
                    }
                }
            });
        });

        // Edit test result functionality
        function editTest(labId, result, patientId, appointmentId) {
            document.getElementById('editLabId').value = labId;
            document.getElementById('editResult').value = result;
            document.getElementById('editPatientId').value = patientId;
            document.getElementById('editAppointmentId').value = appointmentId;
            $('#editTestModal').modal('show');
        }
    </script>
</body>
</html>
