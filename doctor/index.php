<?php
// Start the session
session_start();

// Include the database configuration
include '../access/config.php'; // Your database connection settings

// Check if the user is logged in
if (!isset($_SESSION['doctor_id'])) {
    echo "Doctor ID not set. Redirecting...";
    header("Location: login.php");
    exit();
}


// Initialize metrics
$totalPatients = 0;
$upcomingAppointments = 0;
$newPrescriptions = 0;
$id = $_SESSION['doctor_id'];

// Fetch total patients count
$patientResult = $conn->query("SELECT COUNT(*) as total FROM patients");
if ($patientResult && $patientResult->num_rows > 0) {
    $totalPatients = $patientResult->fetch_assoc()['total'];
} else {
    die("Database query failed: " . $conn->error);
}

// Fetch upcoming appointments count based on doctorID
$appointmentResult = $conn->prepare("SELECT COUNT(*) as total FROM appointments WHERE AppointmentDate >= CURDATE() AND DoctorID = ?");
$appointmentResult->bind_param("i", $id); // Bind the session doctorID
$appointmentResult->execute();
$upcomingAppointments = $appointmentResult->get_result()->fetch_assoc()['total'];

// Fetch new prescriptions count based on today's date and doctor's appointments
$prescriptionQuery = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM prescriptions p
    JOIN appointments a ON p.AppointmentID = a.AppointmentID
    WHERE DATE(p.CreatedAt) = CURDATE() AND a.DoctorID = ?
");
$prescriptionQuery->bind_param("i", $id); // Bind the session doctorID
$prescriptionQuery->execute();
$newPrescriptions = $prescriptionQuery->get_result()->fetch_assoc()['total'];

// Fetch patient data for management section
$patientsQuery = $conn->query("SELECT * FROM patients");
if (!$patientsQuery) {
    die("Database query failed: " . $conn->error);
}

// Fetch appointment data for management section with patient names
$appointmentsQuery = $conn->prepare("
    SELECT a.*, p.Name 
    FROM appointments a
    JOIN patients p ON a.PatientID = p.PatientID
    WHERE a.AppointmentDate >= CURDATE() AND a.DoctorID = ?
");
$appointmentsQuery->bind_param("i", $id); // Bind the session doctorID
$appointmentsQuery->execute();
$appointmentsResult = $appointmentsQuery->get_result();

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Dashboard - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CSS -->
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
        .navbar-brand, .nav-link {
            color: white !important;
        }

        /* Custom styling for hamburger icon */
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-toggler-icon {
            background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"%3E%3Cpath stroke="rgba%28255, 255, 255, 0.5%29" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"/%3E%3C/svg%3E');
        }

        /* Dashboard Styling */
        .dashboard {
            padding: 20px;
            display: flex; /* Use flexbox for layout */
        }
        .dashboard-content {
            flex: 1; /* Take remaining space */
            margin-right: 20px; /* Space between content and notifications */
        }
        .card {
            margin-bottom: 20px;
        }

        /* Metrics */
        .metric-card {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
        }
        .metric-card h2 {
            font-size: 36px;
        }
        .metric-card p {
            font-size: 18px;
        }
        .metric-card i {
            font-size: 48px; /* Increase icon size */
            margin-bottom: 10px; /* Spacing below icons */
        }

        /* Adjust spacing between cards on smaller screens */
        @media (max-width: 767.98px) {
            .metric-card {
                margin-bottom: 15px;
            }
        }

        /* Notifications */
        .notification {
            padding: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .notification h5 {
            margin: 0;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include '../resources/includes/d_header.php'; ?> <!-- Include the header file -->
<div class="dashboard"> <!-- Flex container for sidebar and content -->
    <?php include '../resources/includes/d_sidebar.php'; ?> <!-- Include the sidebar file -->

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <h1>Doctor's Dashboard</h1>

        <!-- Metrics Section -->
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="metric-card">
                    <i class="fas fa-users"></i> <!-- Total Patients Icon -->
                    <h2><?php echo $totalPatients; ?></h2>
                    
                    <p>Total Patients</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="metric-card">
                    <i class="fas fa-calendar-alt"></i> <!-- Upcoming Appointments Icon -->
                    <h2><?php echo $upcomingAppointments; ?></h2>
                    <p>Upcoming Appointments</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="metric-card">
                    <i class="fas fa-file-medical-alt"></i> <!-- New Prescriptions Icon -->
                    <h2><?php echo $newPrescriptions; ?></h2>
                    <p>New Prescriptions</p>
                </div>
            </div>
        </div>

        <!-- Patient Management Section -->
        <div class="card">
            <div class="card-header">
                <h5>Patient Management</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Last Visit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = $patientsQuery->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['Name']); ?></td>
                                <td><?php echo htmlspecialchars($patient['Age']); ?></td>
                                <td><?php echo htmlspecialchars($patient['CreatedAt']); ?></td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Appointment Management Section -->
        <div class="card">
            <div class="card-header">
                <h5>Appointment Management</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointmentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['Name']); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($appointment['AppointmentDate']))); ?></td>
                                <td><?php echo htmlspecialchars(date('H:i', strtotime($appointment['AppointmentDate']))); ?></td>
                                <td><span class="badge badge-warning"><?php echo htmlspecialchars($appointment['Status']); ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Prescription Management Section -->
        <div class="card">
            <div class="card-header">
                <h5>Prescription Management</h5>
            </div>
            <div class="card-body">
                <p>Write a new prescription for a patient.</p>
                <button class="btn btn-primary"><i class="fas fa-plus"></i> Create Prescription</button> <!-- Add icon to button -->
            </div>
        </div>
    </div>

    <!-- Notifications Section -->
    <div class="notifications">
        <?php include 'd_notification.php'; ?> <!-- Include the notifications file -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
