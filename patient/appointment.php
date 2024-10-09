<?php
// appointments.php

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

// Helper function to fetch upcoming appointments
function fetchUpcomingAppointments($conn, $patientEmail) {
    $currentDate = date('Y-m-d H:i:s');
    $query = "SELECT d.Name AS doctor_name, a.AppointmentDate, a.Status 
              FROM appointments a 
              JOIN doctors d ON a.DoctorID = d.DoctorID 
              JOIN patients p ON a.PatientID = p.PatientID
              WHERE p.Email = ? AND a.AppointmentDate >= ? 
              ORDER BY a.AppointmentDate ASC";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $patientEmail, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to fetch past appointments
function fetchPastAppointments($conn, $patientEmail) {
    $currentDate = date('Y-m-d H:i:s');
    $query = "SELECT d.Name AS doctor_name, a.AppointmentDate, a.Status 
              FROM appointments a 
              JOIN doctors d ON a.DoctorID = d.DoctorID 
              JOIN patients p ON a.PatientID = p.PatientID
              WHERE p.Email = ? AND a.AppointmentDate < ? 
              ORDER BY a.AppointmentDate DESC";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $patientEmail, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch upcoming and past appointments
$upcomingAppointments = fetchUpcomingAppointments($conn, $patientEmail);
$pastAppointments = fetchPastAppointments($conn, $patientEmail);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Layout Styling */
        .dashboard-container {
            display: flex;
        }

        .sidebar {
            background-color: #007bff;
            padding: 20px;
            flex: 0 0 250px;
            height: calc(100vh - 56px);
        }

        .dashboard-content {
            flex: 1;
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<?php include '../resources/includes/p_header.php'; ?>

    <!-- Dashboard and Sidebar Container -->
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar file -->

        <!-- Appointments Content -->
        <div class="dashboard-content">
            <h1>My Appointments</h1>

            <!-- Search and Filter Section -->
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Doctor's Name..." onkeyup="filterAppointments()">
                <input type="date" id="dateFilter" class="form-control mt-2" onchange="filterAppointments()">
            </div>

            <!-- Upcoming Appointments -->
            <div class="card">
                <div class="card-header">
                    <h5>Upcoming Appointments</h5>
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
                            <?php if (!empty($upcomingAppointments)) : ?>
                                <?php foreach ($upcomingAppointments as $appointment) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appointment['doctor_name']); ?></td>
                                        <td><?= htmlspecialchars($appointment['AppointmentDate']); ?></td>
                                        <td><?= date('h:i A', strtotime($appointment['AppointmentDate'])); ?></td>
                                        <td><span class="badge badge-warning"><?= htmlspecialchars($appointment['Status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="4">No upcoming appointments found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Past Appointments -->
            <div class="card">
                <div class="card-header">
                    <h5>Past Appointments</h5>
                </div>
                <div class="card-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pastAppointments)) : ?>
                                <?php foreach ($pastAppointments as $appointment) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appointment['doctor_name']); ?></td>
                                        <td><?= htmlspecialchars($appointment['AppointmentDate']); ?></td>
                                        <td><?= date('h:i A', strtotime($appointment['AppointmentDate'])); ?></td>
                                        <td><span class="badge badge-success"><?= htmlspecialchars($appointment['Status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr><td colspan="4">No past appointments found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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
