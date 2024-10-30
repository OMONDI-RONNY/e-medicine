<?php
session_start();
// Include the database connection
include '../access/config.php'; // Adjust path if needed
if (!isset($_SESSION['username'])) {
    // Admin is not logged in, redirect to login page
    header("Location: login.php");
    exit; // Ensure no further code is executed
}

// Delete a laboratory test result
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];

    $query = "DELETE FROM laboratory WHERE LabID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: lab.php");
    exit();
}

// Fetch all laboratory records with patient information
$query = "
    SELECT laboratory.LabID, patients.firstname AS Patient, laboratory.TestName, laboratory.TestDate, laboratory.Result
    FROM laboratory
    JOIN patients ON laboratory.PatientID = patients.PatientID
";
$result = mysqli_query($conn, $query);
$records = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Management - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
         
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #007bff;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        /* Page Layout */
        .dashboard {
            display: flex;
        }

       
        .container {
    padding: 20px;
    flex-grow: 1;
    margin-left: -20px; /* Adjust this to move the container closer to the sidebar */
}
        /* Header */
        .page-header h1 {
            font-size: 2rem;
            color: #007bff;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

        /* Laboratory Filters */
        .laboratory-filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            padding: 8px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Laboratory Table */
        .laboratory-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .laboratory-table th,
        .laboratory-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .laboratory-table th {
            background-color: #007bff;
            color: white;
        }

        /* Action Buttons */
        .btn-edit,
        .btn-delete {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-edit {
            background-color: #ffc107;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .laboratory-filters {
                flex-direction: column;
                gap: 10px;
            }

            .search-bar,
            .btn-primary {
                width: 100%;
            }
        }
    
    </styl>
</head>

<body>

    <?php include 'header.php'; ?> <!-- Include the header file -->
    <div class="dashboard">
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar file -->

        <div class="container">
            <div class="page-header">
                <h1>Laboratory Management</h1>
                <p>Log patient samples and manage test results effectively.</p>
            </div>

            <!-- Laboratory Search & Filters -->
            <div class="laboratory-filters">
                <input type="text" class="search-bar" placeholder="Search tests...">
                <button class="btn-primary" data-toggle="modal" data-target="#addTestResultModal">Add Test Result</button>
            </div>

            <!-- Laboratory Table -->
            <div class="laboratory-table">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Test</th>
                            <th>Date</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $record) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['Patient']); ?></td>
                                <td><?php echo htmlspecialchars($record['TestName']); ?></td>
                                <td><?php echo htmlspecialchars($record['TestDate']); ?></td>
                                <td><?php echo htmlspecialchars($record['Result']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $record['LabID']; ?>">
                                        <button type="submit" class="btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal for adding a test result -->
            <div class="modal fade" id="addTestResultModal" tabindex="-1" role="dialog" aria-labelledby="addTestResultModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTestResultModalLabel">Add Test Result</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="action" value="add">
                                <div class="form-group">
                                    <label for="patient_id">Patient</label>
                                    <select name="patient_id" class="form-control" required>
                                        <?php
                                        $patientQuery = "SELECT PatientID, firstname FROM patients";
                                        $patientResult = mysqli_query($conn, $patientQuery);
                                        while ($patient = mysqli_fetch_assoc($patientResult)) {
                                            echo "<option value='{$patient['PatientID']}'>{$patient['firstname']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="test">Test</label>
                                    <input type="text" class="form-control" name="test" required>
                                </div>
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" name="date" required>
                                </div>
                                <div class="form-group">
                                    <label for="result">Result</label>
                                    <input type="text" class="form-control" name="result" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Result</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
