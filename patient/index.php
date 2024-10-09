<?php
// patient_dashboard.php

// Include database configuration
include '../access/config.php'; // Assuming this file contains your mysqli connection code

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the patient's email from the session
$patientEmail = $_SESSION['user_id']; // Assuming 'user_id' is the patient's email stored in the session

// Helper function to fetch patient details
function fetchPatientDetails($conn, $patientEmail) {
    $query = "SELECT * FROM patients WHERE Email = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Output error message
    }
    
    $stmt->bind_param("s", $patientEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Helper function to fetch upcoming appointments
function fetchUpcomingAppointments($conn, $patientEmail) {
    $currentDate = date('Y-m-d H:i:s'); // Current date and time
    $query = "SELECT d.Name AS doctor_name, a.AppointmentDate, a.Status, a.CreatedAt 
              FROM appointments a 
              JOIN doctors d ON a.DoctorID = d.DoctorID 
              JOIN patients p ON a.PatientID = p.PatientID
              WHERE p.Email = ? AND a.AppointmentDate >= ? 
              ORDER BY a.AppointmentDate ASC";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Output error message
    }
    
    $stmt->bind_param("ss", $patientEmail, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to fetch active prescriptions
function fetchActivePrescriptions($conn, $patientEmail) {
    $query = "SELECT p.Medication, p.Dosage 
              FROM prescriptions p 
              JOIN appointments a ON p.AppointmentID = a.AppointmentID 
              JOIN patients pat ON a.PatientID = pat.PatientID
              WHERE pat.Email = ? AND p.Status = 'active'";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Output error message
    }
    
    $stmt->bind_param("s", $patientEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch patient details, upcoming appointments, and active prescriptions
$patientDetails = fetchPatientDetails($conn, $patientEmail);
$upcomingAppointments = fetchUpcomingAppointments($conn, $patientEmail);
$activePrescriptions = fetchActivePrescriptions($conn, $patientEmail);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Dashboard Styling */
        .dashboard {
            display: flex; /* Use flexbox for layout */
            padding: 20px;
        }

        .dashboard-content {
            flex: 1; /* Take the remaining space */
        }

        /* Overview Section */
        .overview-row {
            display: flex; /* Use flexbox for the overview cards */
            flex-wrap: wrap; /* Allow cards to wrap */
            gap: 20px; /* Space between cards */
        }

        .overview-card {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            flex: 1 1 300px; /* Flex grow and shrink, with a basis of 300px */
            min-height: 200px; /* Set a minimum height for consistency */
            display: flex; /* Use flexbox for card content */
            flex-direction: column; /* Stack content vertically */
            justify-content: center; /* Center content vertically */
        }

        .overview-card h2 {
            font-size: 36px;
            margin: 10px 0; /* Add margin to separate from icon */
        }

        .overview-card p {
            font-size: 18px;
        }

        /* Icons */
        .icon {
            font-size: 50px; /* Icon size */
            margin-bottom: 15px; /* Space between icon and text */
        }

        /* Appointment Management Section */
        .card {
            margin-bottom: 20px;
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
    <?php include '../resources/includes/p_header.php'; ?>

    <div class="dashboard">
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar here -->

        <div class="dashboard-content">
            <h1>Patient Dashboard</h1>

            <!-- Overview Section -->
            <div class="overview-row">
                <div class="overview-card">
                    <i class="fas fa-user icon"></i>
                    <h2><?= htmlspecialchars($patientDetails['Name']); ?></h2>
                    <p>Age: <?= htmlspecialchars($patientDetails['Age']); ?></p>
                    <p>Status: <?= htmlspecialchars($patientDetails['status']); ?></p>
                </div>
                <div class="overview-card">
                    <i class="fas fa-calendar-alt icon"></i>
                    <h2>Upcoming Appointments</h2>
                    <p><?= count($upcomingAppointments); ?></p>
                </div>
                <div class="overview-card">
                    <i class="fas fa-pills icon"></i>
                    <h2>Active Prescriptions</h2>
                    <p><?= count($activePrescriptions); ?></p>
                </div>
            </div>

            <!-- Appointment Management Section -->
            <div class="card">
                <div class="card-header">
                    <h5>My Appointments</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by Doctor's Name..." onkeyup="filterAppointments()">
                        </div>
                        <div class="col-md-6">
                            <input type="date" id="dateFilter" class="form-control" onchange="filterAppointments()">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-responsive" id="appointmentsTable">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingAppointments as $appointment) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($appointment['doctor_name']); ?></td>
                                    <td><?= htmlspecialchars($appointment['AppointmentDate']); ?></td>
                                    <td><?= htmlspecialchars($appointment['CreatedAt']); ?></td>
                                    <td><span class="badge badge-warning"><?= htmlspecialchars($appointment['Status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Additional sections can be added here... -->
        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function filterAppointments() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const dateFilter = document.getElementById('dateFilter').value;
            const table = document.getElementById('appointmentsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { // Start from 1 to skip the header row
                const tdDoctor = tr[i].getElementsByTagName('td')[0]; // Doctor's name column
                const tdDate = tr[i].getElementsByTagName('td')[1]; // Appointment date column
                let found = true;

                if (tdDoctor) {
                    const doctorText = tdDoctor.textContent || tdDoctor.innerText;
                    if (doctorText.toLowerCase().indexOf(filter) === -1) {
                        found = false; // Doctor's name doesn't match search
                    }
                }

                if (tdDate) {
                    const appointmentDate = tdDate.textContent || tdDate.innerText;
                    // Extract only the date part (YYYY-MM-DD)
                    const dateOnly = appointmentDate.split(' ')[0]; // Ignore time
                    if (dateFilter && dateOnly !== dateFilter) {
                        found = false; // Date doesn't match filter
                    }
                }

                tr[i].style.display = found ? "" : "none"; // Show or hide the row
            }
        }
    </script>
</body>

</html>
