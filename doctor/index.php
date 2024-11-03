<?php


include '../access/config.php'; 

session_start();
if (!isset($_SESSION['doctor_id'])) {
    
    header("Location: login.php");
    exit();
}


$totalPatients = 0;
$upcomingAppointments = 0;
$newPrescriptions = 0;
$id = $_SESSION['doctor_id'];


$patientResult = $conn->query("SELECT COUNT(*) as total FROM patients");
if ($patientResult && $patientResult->num_rows > 0) {
    $totalPatients = $patientResult->fetch_assoc()['total'];
} else {
    die("Database query failed: " . $conn->error);
}


$appointmentResult = $conn->prepare("SELECT COUNT(*) as total FROM appointments WHERE AppointmentDate >= CURDATE() AND DoctorID = ?");
$appointmentResult->bind_param("i", $id); 
$appointmentResult->execute();
$upcomingAppointments = $appointmentResult->get_result()->fetch_assoc()['total'];


$prescriptionQuery = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM prescriptions p
    JOIN appointments a ON p.AppointmentID = a.AppointmentID
    WHERE DATE(p.CreatedAt) = CURDATE() AND a.DoctorID = ?
");
$prescriptionQuery->bind_param("i", $id); 
$prescriptionQuery->execute();
$newPrescriptions = $prescriptionQuery->get_result()->fetch_assoc()['total'];


$recordsPerPage = 5; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $recordsPerPage;


$patientsQuery = $conn->prepare("SELECT * FROM patients LIMIT ?, ?");
$patientsQuery->bind_param("ii", $offset, $recordsPerPage);
$patientsQuery->execute();
$patientsResult = $patientsQuery->get_result();


$totalPatientsQuery = $conn->query("SELECT COUNT(*) as total FROM patients");
$totalPatients = $totalPatientsQuery->fetch_assoc()['total'];
$totalPages = ceil($totalPatients / $recordsPerPage); 


$appointmentsQuery = $conn->prepare("
    SELECT a.*, p.firstname 
    FROM appointments a
    JOIN patients p ON a.PatientID = p.PatientID
    WHERE a.AppointmentDate >= CURDATE() AND a.DoctorID = ?
");
$appointmentsQuery->bind_param("i", $id);
$appointmentsQuery->execute();
$appointmentsResult = $appointmentsQuery->get_result();


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
        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .navbar-toggler-icon {
            background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"%3E%3Cpath stroke="rgba%28255, 255, 255, 0.5%29" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"/%3E%3C/svg%3E');
        }

        .dashboard {
            padding: 20px;
            display: flex;
        }
        .dashboard-content {
            flex: 1;
            margin-right: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
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
            font-size: 48px;
            margin-bottom: 10px;
        }

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

        .pagination {
            justify-content: center;
        }

        @media (max-width: 767.98px) {
            .metric-card {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>

<?php include '../resources/includes/d_header.php'; ?> 
<div class="dashboard"> 
    <?php include '../resources/includes/d_sidebar.php'; ?> 

    
    <div class="dashboard-content">
        <h1>Doctor's Dashboard</h1>

        
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="metric-card">
                    <i class="fas fa-users"></i>
                    <h2><?php echo $totalPatients; ?></h2>
                    <p>Total Patients</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="metric-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h2><?php echo $upcomingAppointments; ?></h2>
                    <p>Upcoming Appointments</p>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="metric-card">
                    <i class="fas fa-file-medical-alt"></i>
                    <h2><?php echo $newPrescriptions; ?></h2>
                    <p>New Prescriptions</p>
                </div>
            </div>
        </div>

        
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
                        <?php while ($patient = $patientsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($patient['Age']); ?></td>
                                <td><?php echo htmlspecialchars($patient['CreatedAt']); ?></td>
                                <td><span class="badge badge-success">Active</span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                
                <nav>
                    <ul class="pagination">
                        <?php if($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if($page < $totalPages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header">
                <h5>Appointment Management</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointmentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['AppointmentDate']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['AppointmentDate']); ?></td>
                                <td><span class="badge badge-warning"><?php echo htmlspecialchars($appointment['Status']); ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
