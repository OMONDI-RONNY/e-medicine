<?php
session_start(); // Start the session

// Include the database configuration
include '../access/config.php';

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

// Prepare the SQL statement with error handling
$stmt = $conn->prepare("
    SELECT a.AppointmentDate, a.CreatedAt, a.Status, p.Name 
    FROM appointments a 
    JOIN patients p ON a.patientID = p.patientID 
    WHERE a.doctorID = ?
");

// Check if prepare was successful
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Bind parameters and execute
$stmt->bind_param("s", $_SESSION['doctor_id']);

// Check for execution errors
if ($stmt->execute() === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

// Count total appointments
$totalAppointments = count($appointments);

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        .container {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .dashboard {
            display: flex;
        }
        .sidebar {
            background-color: #007bff;
            padding: 20px;
            margin-right: 20px;
            flex: 0 0 250px;
            height: calc(100vh - 40px);
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .notification-sidebar {
            width: 300px;
            padding: 20px;
            border-left: 1px solid #e9ecef;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            border-radius: 8px;
            flex: 0 0 300px;
        }
        h5 {
            margin-bottom: 20px;
            font-size: 20px;
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .manage-appointment-btn {
            position: relative;
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border-radius: 50px;
            border: none;
            font-weight: bold;
        }
        .manage-appointment-btn i {
            margin-right: 10px;
        }
        .manage-appointment-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            font-size: 12px;
            padding: 5px 8px;
            border-radius: 50%;
        }
        .manage-appointment-btn:hover {
            background-color: #218838;
        }
        .action-btn {
            margin-right: 5px;
        }
        .action-btn i {
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .notification-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <?php include '../resources/includes/d_header.php'; ?>

    <div class="dashboard">
        <?php include '../resources/includes/d_sidebar.php'; ?>

        <div class="container">
            <h1>Appointment Management</h1>

            <!-- Manage Appointment Button with Count Circle -->
            <div class="mb-3">
                <button class="manage-appointment-btn">
                    <i class="fas fa-calendar-check"></i> Manage Appointments
                    <span class="manage-appointment-count"><?php echo $totalAppointments; ?></span>
                </button>
            </div>

            <!-- Add New Appointment Button -->
            <div class="mb-3">
                <button class="btn btn-primary">Add New Appointment</button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Appointment List</h5>
                </div>
                <div class="card-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th> <!-- Action column for buttons -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['AppointmentDate']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['CreatedAt']); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($appointment['Status'] == 'Confirmed') ? 'badge-success' : (($appointment['Status'] == 'Pending') ? 'badge-warning' : 'badge-danger'); ?>">
                                            <?php echo htmlspecialchars($appointment['Status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Manage and Update buttons -->
                                        <button class="btn btn-info action-btn">
                                            <i class="fas fa-cog"></i> Manage
                                        </button>
                                        <button class="btn btn-warning action-btn">
                                            <i class="fas fa-edit"></i> Update
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include 'd_notification.php'; ?>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
