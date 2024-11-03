<?php
session_start(); 

include '../access/config.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateAppointment'])) {
   
    $appointmentID = $_POST['appointmentID'];
    $appointmentDate = $_POST['appointmentDate'];
    $appointmentStatus = $_POST['appointmentStatus'];

   
    $stmt = $conn->prepare("UPDATE appointments SET AppointmentDate = ?, Status = ? WHERE AppointmentID = ?");
    $stmt->bind_param('ssi', $appointmentDate, $appointmentStatus, $appointmentID);

    if ($stmt->execute()) {
        $successMsg = "Appointment updated successfully!";
        
        
        $patientStmt = $conn->prepare("SELECT p.phone FROM appointments a JOIN patients p ON a.patientID = p.PatientID WHERE a.AppointmentID = ?");
        $patientStmt->bind_param('i', $appointmentID);
        $patientStmt->execute();
        $result = $patientStmt->get_result();
        
        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();
            $phone = $patient['phone'];

            
            $endpoint = 'https://api.tiaraconnect.io/api/messaging/sendsms';
            $apiKey = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIzNTEiLCJvaWQiOjM1MSwidWlkIjoiN2Y5ZGQ1ZmMtM2QwMi00ZGZiLTg1YjItY2FjMDBlYjU0NDhkIiwiYXBpZCI6MjQxLCJpYXQiOjE3MTExOTQyMTAsImV4cCI6MjA1MTE5NDIxMH0._BW3-yd5JJmAnRsL_trguFXmTLKFmz_a4EAJVmoIk7H66Lpccj3uKiwuTJjgYoxKLU6ZH0EhAC3pkDU2wQcPXQ'; // Replace with your actual API key
            $from = 'TIARACONECT';
            $message = 'Your appointment has been ' . $appointmentStatus .'. Thanks for being part of our services';

          
            sendSMS($endpoint, $apiKey, $phone, $from, $message);
        }

    } else {
        $errorMsg = "Error updating appointment: " . $conn->error;
    }

    $stmt->close();
    $patientStmt->close();
}
function sendSMS($endpoint, $apiKey, $to, $from, $message) {
    
    $request = [
        'to' => $to,
        'from' => $from,
        'message' => $message
    ];
    $requestBody = json_encode($request);

    
    error_log("Sending SMS to $to with message: $message");

    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $requestBody,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
    ]);

    
    $response_body = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    
    if ($response_body === false) {
        error_log('cURL Error: ' . curl_error($curl));
        return false; 
    } 
    
    
    if ($http_status !== 200) {
        error_log('HTTP Error ' . $http_status . ': ' . $response_body);
        return false; 
    } 
    
    
    error_log("SMS sent successfully to $to: " . $response_body);
    return true; 
}
// Prepare the SQL statement to get appointments
$stmt = $conn->prepare("
    SELECT a.AppointmentID, a.AppointmentDate, a.CreatedAt, a.Status, p.firstname, p.PatientID
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
            margin: 0;
            max-width: 100%;
            flex-grow: 1;
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

            <!-- Success/Error message display -->
            <?php if (isset($successMsg)): ?>
                <div class="alert alert-success"><?php echo $successMsg; ?></div>
            <?php elseif (isset($errorMsg)): ?>
                <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
            <?php endif; ?>

            <!-- Manage Appointment Button with Count Circle -->
            <div class="mb-3">
                <button class="manage-appointment-btn">
                    <i class="fas fa-calendar-check"></i> Manage Appointments
                    <span class="manage-appointment-count"><?php echo $totalAppointments; ?></span>
                </button>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Appointment List</h5>
                    <macquee>conslutation</macquee>
                </div>
                <div class="card-body">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Patient ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Actions</th> <!-- Action column for buttons -->
                            </tr>
                        </thead>
                        <tbody>

<?php
$today = date('Y-m-d'); 
foreach ($appointments as $appointment): ?>
    <tr>
        <td><?php echo htmlspecialchars($appointment['firstname']); ?></td>
        <td><?php echo htmlspecialchars($appointment['PatientID']); ?></td>
        <td><?php echo htmlspecialchars($appointment['AppointmentDate']); ?></td>
        <td><?php echo htmlspecialchars($appointment['CreatedAt']); ?></td>
        <td>
            <span class="badge <?php echo ($appointment['Status'] == 'Confirmed') ? 'badge-success' : (($appointment['Status'] == 'Pending') ? 'badge-warning' : 'badge-danger'); ?>">
                <?php echo htmlspecialchars($appointment['Status']); ?>
            </span>
        </td>
        <td>
            <!-- Manage Button with data attributes to pass values to the modal -->
            <button class="btn btn-info action-btn manage-btn" 
                data-appointment-id="<?php echo htmlspecialchars($appointment['AppointmentID']); ?>" 
                data-appointment-date="<?php echo htmlspecialchars($appointment['AppointmentDate']); ?>" 
                data-appointment-status="<?php echo htmlspecialchars($appointment['Status']); ?>" 
                data-toggle="modal" data-target="#manageAppointmentModal">
                <i class="fas fa-cog"></i> Manage
            </button>

            <!-- Start Consultation Button, active only for today's appointments -->
            <?php if (date('Y-m-d', strtotime($appointment['AppointmentDate'])) == $today): ?>
                <button class="btn btn-success action-btn start-consultation-btn" 
                    data-appointment-id="<?php echo htmlspecialchars($appointment['AppointmentID']); ?>" 
                    data-patient-id="<?php echo htmlspecialchars($appointment['PatientID']); ?>" 
                    data-toggle="modal" data-target="#startConsultationModal">
                    <i class="fas fa-user-md"></i> Start Consultation(Active)
                </button>
            <?php else: ?>
                <button class="btn btn-secondary action-btn" disabled>
                    <i class="fas fa-user-md"></i> Start Consultation(Inactive)
                </button>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include '../resources/includes/d_notification.php'; ?>
        
    </div>

    <!-- Manage Appointment Modal -->
    <div class="modal fade" id="manageAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="manageAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageAppointmentModalLabel">Manage Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" id="appointmentID" name="appointmentID">

                        <div class="form-group">
                            <label for="appointmentDate">Appointment Date</label>
                            <input type="datetime-local" class="form-control" id="appointmentDate" name="appointmentDate" required>
                        </div>

                        <div class="form-group">
                            <label for="appointmentStatus">Status</label>
                            <select class="form-control" id="appointmentStatus" name="appointmentStatus" required>
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="updateAppointment" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
   <!-- Start Consultation Modal -->
<!-- Start Consultation Modal -->
<div class="modal fade" id="startConsultationModal" tabindex="-1" role="dialog" aria-labelledby="startConsultationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="startConsultationModalLabel">Start Consultation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="sendlab.php">
                <div class="modal-body">
                    <input type="hidden" id="consultationPatientID" name="patientID"> <!-- Hidden PatientID field -->
                    <input type="hidden" id="consultationAppointmentID" name="appointmentID"> <!-- Hidden AppointmentID field -->
                    <p>Patient ID: <span id="patientIDDisplay"></span></p> <!-- Display Patient ID -->

                    <div class="form-group">
                        <label for="symptoms">Symptoms</label>
                        <textarea class="form-control" id="symptoms" name="symptoms" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="recommendation">Doctor's Recommendation for Lab Tests</label>
                        <textarea class="form-control" id="recommendation" name="recommendation" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="recommendation">Consultation Fee</label>
                        <input type="number" name="fee" id="fee" class="form-control" value="500">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Start Consultation</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
       $(document).ready(function() {
    // When the manage button is clicked, populate the modal with appointment data
    $('.manage-btn').on('click', function() {
        var appointmentID = $(this).data('appointment-id');
        var appointmentDate = $(this).data('appointment-date');
        var appointmentStatus = $(this).data('appointment-status');

        $('#appointmentID').val(appointmentID);
        $('#appointmentDate').val(appointmentDate);
        $('#appointmentStatus').val(appointmentStatus);
    });

    // When the start consultation button is clicked, populate the patient ID and appointment ID
    $('.start-consultation-btn').on('click', function() {
        var patientID = $(this).data('patient-id'); // Get the patient ID
        var appointmentID = $(this).data('appointment-id'); // Get the appointment ID

        $('#consultationPatientID').val(patientID); // Set patient ID in the modal
        $('#consultationAppointmentID').val(appointmentID); // Set appointment ID in the modal

        $('#patientIDDisplay').text(patientID); // Display Patient ID on the modal
    });
});

    </script>

</body>
</html>
