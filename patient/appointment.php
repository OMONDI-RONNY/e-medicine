<?php



include '../access/config.php'; 


session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}


$patientEmail = $_SESSION['user_id'];


function fetchUpcomingAppointments($conn, $patientEmail) {
    $currentDate = date('Y-m-d H:i:s');
    $query = "SELECT d.firstname AS doctor_name, a.AppointmentDate, a.Status 
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

function fetchPastAppointments($conn, $patientEmail) {
    $currentDate = date('Y-m-d H:i:s');
    $query = "SELECT d.firstname AS doctor_name, a.AppointmentDate, a.Status 
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

$upcomingAppointments = fetchUpcomingAppointments($conn, $patientEmail);
$pastAppointments = fetchPastAppointments($conn, $patientEmail);

// Fetch all doctors for the dropdown
$doctorsQuery = "SELECT DoctorID, firstname, specialty FROM doctors";
$doctorsResult = $conn->query($doctorsQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

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

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .modal-footer .btn-primary {
            background-color: #007bff;
        }

        
        .btn-appointment {
            margin-bottom: 20px;
            background-color: #28a745 !important; 
            color: white !important;
            border-radius: 50px;
            padding: 15px 30px;
            font-size: 18px;
            transition: 0.3s ease-in-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            font-weight: bold;
            border: none;
        }

        .btn-appointment i {
            margin-right: 10px;
        }

        .btn-appointment:hover {
            background-color: #218838 !important; 
            transform: scale(1.05); 
        }

        .modal-body {
            padding: 30px;
        }

        .modal-header, .modal-footer {
            border: none;
        }

        .form-control {
            padding: 10px;
            font-size: 16px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
    </style>
</head>

<body>

<?php include '../resources/includes/p_header.php'; ?>

    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <div class="dashboard-content">
            <h1>My Appointments</h1>

            
            <button class="btn btn-appointment" data-toggle="modal" data-target="#appointmentModal">
                <i class="fas fa-calendar-plus"></i> Make an Appointment
            </button>

            
            <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="appointmentModalLabel">Make an Appointment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="appointmentForm" action="submit_appointment.php" method="POST">
                                <div class="form-group">
                                <label for="doctorSelect">Select Doctor</label>
                                <select class="form-control" id="doctorSelect" name="doctor_id" required onchange="updateSpecialty()">
                                    <option value="" selected disabled>Choose a doctor...</option>
                                    <?php while ($doctor = $doctorsResult->fetch_assoc()): ?>
                                        <option value="<?= $doctor['DoctorID']; ?>" data-specialty="<?= htmlspecialchars($doctor['specialty']); ?>">
                                            <?= htmlspecialchars($doctor['firstname']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="specialtySelect">Specialty</label>
                                <select class="form-control" id="specialtySelect" name="specialty" disabled>
                                    <option value="" selected disabled>Select a specialty...</option>
                                </select>
                            </div>

                                <div class="form-group">
                                    <label for="appointmentDate">Appointment Date</label>
                                    <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="appointmentTime">Appointment Time</label>
                                    <input type="time" class="form-control" id="appointmentTime" name="appointment_time" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" onclick="document.getElementById('appointmentForm').submit();">Save Appointment</button>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Doctor's Name..." onkeyup="filterAppointments()">
                <input type="date" id="dateFilter" class="form-control mt-2" onchange="filterAppointments()">
            </div>

           
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
                                        <td><span class="badge badge-secondary"><?= htmlspecialchars($appointment['Status']); ?></span></td>
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
    <?php include '../resources/includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        
        function filterAppointments() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const dateFilter = document.getElementById('dateFilter').value;
            const appointmentsTable = document.getElementById('appointmentsTable');
            const rows = appointmentsTable.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const doctorName = cells[0].textContent.toLowerCase();
                const appointmentDate = cells[1].textContent;

                const matchesSearch = doctorName.includes(searchInput);
                const matchesDate = dateFilter ? appointmentDate.startsWith(dateFilter) : true;

                if (matchesSearch && matchesDate) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
                
        function updateSpecialty() {
            const doctorSelect = document.getElementById('doctorSelect');
            const specialtySelect = document.getElementById('specialtySelect');
            
            
            const selectedDoctor = doctorSelect.options[doctorSelect.selectedIndex];
            const specialty = selectedDoctor.getAttribute('data-specialty');

            
            if (specialty) {
                specialtySelect.innerHTML = `<option value="${specialty}" selected>${specialty}</option>`;
                specialtySelect.disabled = false;
            } else {
                specialtySelect.innerHTML = '<option value="" selected disabled>No specialty available</option>';
                specialtySelect.disabled = true;
            }
        }

    </script>
</body>

</html>
