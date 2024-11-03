<?php



include '../access/config.php';


session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$patientEmail = $_SESSION['user_id']; 


function fetchPatientDetails($conn, $patientEmail) {
    $query = "SELECT * FROM patients WHERE Email = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); 
    }
    
    $stmt->bind_param("s", $patientEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


function fetchUpcomingAppointments($conn, $patientEmail) {
    $currentDate = date('Y-m-d H:i:s'); 
    $query = "SELECT d.firstname AS doctor_name, a.AppointmentDate, a.Status, a.CreatedAt 
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


function fetchActivePrescriptions($conn, $patientEmail) {
    $query = "SELECT p.Medication, p.Dosage 
              FROM prescriptions p 
              JOIN appointments a ON p.AppointmentID = a.AppointmentID 
              JOIN patients pat ON a.PatientID = pat.PatientID
              WHERE pat.Email = ? AND p.Status = 'active'";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $patientEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


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

       
        .dashboard {
            display: flex; 
            padding: 20px;
        }

        .dashboard-content {
            flex: 1; 
        }

       
        .overview-row {
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
        }

.overview-row {
    display: flex;
    flex-wrap: wrap;
    gap: 30px; 
}


.overview-card {
    background-color: #28a745; 
    color: white;
    text-align: center;
    padding: 8px; 
    border-radius: 10px;
    flex: 1 1 180px; 
    min-height: 120px; 
    display: flex;
    flex-direction: column;
    justify-content: center;
}


.icon {
    font-size: 35px;
    margin-bottom: 8px; 
}


.overview-card h2 {
    font-size: 20px;
    margin: 5px 0;
}

.overview-card p {
    font-size: 12px; 
}


.overview-card:first-child {
    background-color: #17a2b8;
}

.overview-card:nth-child(2) {
    background-color: #ffc107; 
}

.overview-card:nth-child(3) {
    background-color: #dc3545; 
}


.table {
    font-size: 14px; 
}




    
        .icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

       
        .card {
            margin-bottom: 20px;
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
    </style>
</head>

<body>
    <?php include '../resources/includes/p_header.php'; ?>

    <div class="dashboard">
        <?php include 'sidebar.php'; ?> 

        <div class="dashboard-content">
            <h1>Patient Dashboard</h1>

           
            <div class="overview-row">
                <div class="overview-card">
                    <i class="fas fa-user icon"></i>
                    <h2><?= strtoupper(htmlspecialchars($patientDetails['firstname'])) . ' ' . strtoupper(htmlspecialchars($patientDetails['lastname'])); ?></h2>
                    <p>ID: <?= strtoupper(htmlspecialchars($patientDetails['PatientID'])); ?></p>
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

            
        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function filterAppointments() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const dateFilter = document.getElementById('dateFilter').value;
            const table = document.getElementById('appointmentsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { 
                const tdDoctor = tr[i].getElementsByTagName('td')[0]; 
                const tdDate = tr[i].getElementsByTagName('td')[1]; 
                let found = true;

                if (tdDoctor) {
                    const doctorText = tdDoctor.textContent || tdDoctor.innerText;
                    if (doctorText.toLowerCase().indexOf(filter) === -1) {
                        found = false; 
                    }
                }

                if (tdDate) {
                    const appointmentDate = tdDate.textContent || tdDate.innerText;
                    
                    const dateOnly = appointmentDate.split(' ')[0]; 
                    if (dateFilter && dateOnly !== dateFilter) {
                        found = false; 
                    }
                }

                tr[i].style.display = found ? "" : "none"; 
            }
        }
    </script>
</body>

</html>
