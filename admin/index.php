<?php
// Include the database connection file
include '../access/config.php';

// Function to fetch total patients
function getTotalPatients($conn) {
    $sql = "SELECT COUNT(*) as total_patients FROM patients";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_patients'];
}

// Function to fetch today's appointments
function getTodaysAppointments($conn) {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as total_appointments FROM appointments WHERE DATE(AppointmentDate) = '$today'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_appointments'];
}

// Function to fetch active prescriptions
function getActivePrescriptions($conn) {
    $sql = "SELECT COUNT(*) as total_active_prescriptions FROM prescriptions WHERE status = 'Active'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_active_prescriptions'];
}

// Fetch total patients
$total_patients = getTotalPatients($conn);

// Fetch today's appointments
$appointments_today = getTodaysAppointments($conn);

// Fetch active prescriptions
$active_prescriptions = getActivePrescriptions($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration | E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        /* Flex container for sidebar and content */
        .dashboard-container {
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: #007bff;
            padding: 20px;
            flex: 0 0 250px;
            /* Sidebar width */
            color: white;
        }

        .sidebar .list-group-item {
            background-color: transparent;
            color: white;
            border: none;
        }

        /* Dashboard Styling */
        .dashboard {
            padding: 20px;
            flex: 1;
        }

        .card {
            margin-bottom: 20px;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Notification Styling */
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

        /* Overview Section */
        .overview-card {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        .overview-card h2 {
            font-size: 36px;
        }

        .overview-card p {
            font-size: 18px;
        }

        .overview-card i {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .footer {
            text-align: center;
            margin: 20px 0;
        }

        /* Responsive behavior */
        @media (max-width: 768px) {

            .overview-card,
            .card {
                margin-bottom: 30px;
                /* Increased vertical space between cards */
            }

            .row {
                display: flex;
                flex-direction: column;
            }

            .col-md-4 {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?> <!-- Include the header file -->

    <div class="dashboard-container"> <!-- Flex container for sidebar and content -->
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar file -->

        <!-- Main Dashboard Content -->
        <div class="container dashboard">
            <h1>Administration Dashboard</h1>

            <!-- Overview Section -->
            <div class="row">
                <div class="col-md-4">
                    <div class="overview-card">
                        <i class="fas fa-user-injured"></i> <!-- Total Patients Icon -->
                        <h2>Total Patients</h2>
                        <p><?php echo $total_patients; ?></p> <!-- Dynamic data -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="overview-card">
                        <i class="fas fa-calendar-check"></i> <!-- Appointments Icon -->
                        <h2>Appointments Today</h2>
                        <p><?php echo $appointments_today; ?></p> <!-- Dynamic data -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="overview-card">
                        <i class="fas fa-pills"></i> <!-- Active Prescriptions Icon -->
                        <h2>Active Prescriptions</h2>
                        <p><?php echo $active_prescriptions; ?></p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>

            <!-- Pharmacy Management Section -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-prescription-bottle-alt"></i> Pharmacy Management</h5> <!-- Pharmacy Icon -->
                </div>
                <div class="card-body">
                    <p>Manage prescription orders and inventory.</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addPrescriptionModal">Add
                        Prescription</button>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Medication</th>
                                <th>Dosage</th>
                                <th>Stock Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Medication A</td>
                                <td>500mg</td>
                                <td>50</td>
                                <td><button class="btn btn-warning btn-sm">Edit</button> <button
                                        class="btn btn-danger btn-sm">Delete</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Laboratory Management Section -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-flask"></i> Laboratory Management</h5> <!-- Laboratory Icon -->
                </div>
                <div class="card-body">
                    <p>Log patient samples and manage test results.</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addTestResultModal">Add Test
                        Result</button>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Test</th>
                                <th>Date</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>John Doe</td>
                                <td>Blood Test</td>
                                <td>2024-10-01</td>
                                <td>Normal</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Finance Management Section -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-money-check-alt"></i> Finance Management</h5> <!-- Finance Icon -->
                </div>
                <div class="card-body">
                    <p>Handle billing and invoicing processes.</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addInvoiceModal">Add Invoice</button>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jane Smith</td>
                                <td>$100</td>
                                <td><span class="badge badge-success">Paid</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- End of Dashboard Content -->
    </div> <!-- End of Dashboard Container -->

    <footer class="footer">
        <p>&copy; 2024 E-Medicine. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
