<?php
session_start(); // Start the session

// Include the database configuration
include '../access/config.php';

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

// Process form submission for updating the prescription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prescriptionID'], $_POST['dosage'], $_POST['instructions'], $_POST['refills'], $_POST['medication'])) {
    // Sanitize and retrieve form inputs
    $prescriptionID = (int) $_POST['prescriptionID'];
    $dosage = $conn->real_escape_string($_POST['dosage']);
    $instructions = $conn->real_escape_string($_POST['instructions']);
    $refills = (int) $_POST['refills'];
    $medication = $conn->real_escape_string($_POST['medication']); // Ensure medication is treated as a string

    // Prepare the SQL statement to update the prescription
    $stmt = $conn->prepare("
        UPDATE prescriptions 
        SET Dosage = ?, Instructions = ?, RefillsRemaining = ?, Medication = ?, Status = 'completed', CreatedAt = NOW()
        WHERE PrescriptionID = ?
    ");

    // Check if prepare was successful
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters and execute
    // The first four parameters are strings (s), and the last one is an integer (i)
    $stmt->bind_param("ssssi", $dosage, $instructions, $medication, $refills, $prescriptionID);

    if ($stmt->execute()) {
        echo "<script>alert('Prescription updated successfully!'); window.location.href = 'prescription.php';</script>";
    } else {
        echo "<script>alert('Error updating prescription. Please try again.'); window.location.href = 'prescription.php';</script>";
    }

    // Close the statement
    $stmt->close();
}

// Prepare the SQL statement for retrieving prescriptions with medication from the laboratory table
$stmt = $conn->prepare("
    SELECT p.firstname AS PatientName, p.lastname AS PatientLastName, pr.PrescriptionID, l.Result AS Medication, pr.Dosage, pr.CreatedAt
    FROM prescriptions pr
    JOIN appointments a ON pr.AppointmentID = a.AppointmentID
    JOIN patients p ON a.PatientID = p.PatientID
    JOIN laboratory l ON pr.LabID = l.LabID  -- Assuming prescriptions have a LabID to link to the laboratory table
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

<?php include '../resources/includes/d_header.php'; ?>
<div class="dashboard">
    <?php include '../resources/includes/d_sidebar.php'; ?>

    <div class="container">
        <h1>Prescription Management</h1>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prescriptions as $prescription): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prescription['PatientName']) . ' ' . htmlspecialchars($prescription['PatientLastName']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['Medication']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['Dosage']); ?></td>
                                <td><?php echo htmlspecialchars($prescription['CreatedAt']); ?></td>
                                <td>
                                    <button class="btn btn-warning" data-toggle="modal" 
                                            data-target="#prescribeModal" 
                                            data-prescriptionid="<?php echo $prescription['PrescriptionID']; ?>" 
                                            data-medication="<?php echo htmlspecialchars($prescription['Medication']); ?>">
                                        Prescribe
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="prescribeModal" tabindex="-1" role="dialog" aria-labelledby="prescribeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="prescribeModalLabel">Prescribe Medication</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="prescriptionID" id="prescriptionID" value="">
                        <div class="form-group">
                            <label for="medication">Medication:</label>
                            <input type="text" class="form-control" id="medication" name="medication" placeholder="Medication" readonly>
                        </div>
                        <div class="form-group">
                            <label for="dosage">Dosage:</label>
                            <input type="text" class="form-control" name="dosage" id="dosage" placeholder="Enter dosage" required>
                        </div>
                        <div class="form-group">
                            <label for="instructions">Instructions:</label>
                            <textarea class="form-control" name="instructions" id="instructions" rows="3" placeholder="Enter instructions" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="refills">Refills:</label>
                            <input type="number" class="form-control" name="refills" id="refills" placeholder="Number of refills" required min="0">
                        </div>
                        <button type="submit" class="btn btn-success">Submit Prescription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../resources/includes/d_notification.php'; ?>
</div>

<?php include '../resources/includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $('#prescribeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var prescriptionID = button.data('prescriptionid');
        var medication = button.data('medication');
        var modal = $(this);
        modal.find('#prescriptionID').val(prescriptionID);
        modal.find('#medication').val(medication); // This ensures medication is read-only and passed correctly
    });
</script>
</body>
</html>
