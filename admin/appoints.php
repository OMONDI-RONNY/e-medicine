<?php
session_start();
include '../access/config.php'; 
if (!isset($_SESSION['username'])) {

    header("Location: login.php");
    exit;
}


$records_per_page = 10;


$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;


$query = "SELECT appointments.AppointmentID, 
                 patients.firstname AS PatientName, 
                 doctors.firstname AS DoctorName, 
                 appointments.AppointmentDate, 
                 appointments.Status 
          FROM appointments 
          JOIN patients ON appointments.PatientID = patients.PatientID
          JOIN doctors ON appointments.DoctorID = doctors.DoctorID
          LIMIT $offset, $records_per_page";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}


$total_query = "SELECT COUNT(*) AS total FROM appointments";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
      
        $appointmentID = $_POST['appointment_id'];
        $status = $_POST['status'];
        $updateQuery = "UPDATE appointments SET Status = '$status' WHERE AppointmentID = $appointmentID";
        $conn->query($updateQuery);
    }

    if (isset($_POST['delete'])) {
      
        $appointmentID = $_POST['appointment_id'];
        $deleteQuery = "DELETE FROM appointments WHERE AppointmentID = $appointmentID";
        $conn->query($deleteQuery);
    }

    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        
        .dashboard {
            display: flex;
        }

        .container {
            padding: 20px;
            margin: 0;
            max-width: 100%;
            flex-grow: 1;
        }

        
        .appointments-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .appointments-table th,
        .appointments-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .appointments-table th {
            background-color: #007bff;
            color: white;
        }

        
        .btn-edit,
        .btn-delete {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 5px;
            cursor: pointer;
        }

        
        .modal-header {
            background-color: #007bff;
            color: white;
        }

        
        .pagination {
            justify-content: center;
        }
        
    </style>
</head>

<body>

    <?php include 'header.php'; ?> 
    <div class="dashboard">
        <?php include 'sidebar.php'; ?> 

        <div class="container">
            <div class="page-header">
                <h1>Appointment Management</h1>
            </div>

            
            <div class="form-group">
                <input type="text" id="searchBar" class="form-control" placeholder="Search appointments...">
            </div>

            
            <div class="appointments-table">
                <table id="appointmentsTable">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No appointments found.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo $appointment['PatientName']; ?></td>
                            <td><?php echo $appointment['DoctorName']; ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($appointment['AppointmentDate'])); ?></td>
                            <td><span class="status-<?php echo strtolower($appointment['Status']); ?>">
                                <?php echo $appointment['Status']; ?>
                            </span></td>
                            <td>
                                
                                <button type="button" class="btn btn-warning btn-edit" data-toggle="modal" 
                                        data-target="#editModal" 
                                        data-id="<?php echo $appointment['AppointmentID']; ?>" 
                                        data-status="<?php echo $appointment['Status']; ?>">
                                    Edit
                                </button>
                                
                                
                                <button type="button" class="btn btn-danger btn-delete" data-toggle="modal" 
                                        data-target="#deleteModal" 
                                        data-id="<?php echo $appointment['AppointmentID']; ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
    <?php include '../resources/includes/footer.php'; ?> 

    
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="appointment_id" id="editAppointmentId">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="editStatus" class="form-control">
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="appointment_id" id="deleteAppointmentId">
                        <p>Are you sure you want to delete this appointment?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var appointmentId = button.data('id');
            var status = button.data('status');

            var modal = $(this);
            modal.find('#editAppointmentId').val(appointmentId);
            modal.find('#editStatus').val(status);
        });

        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var appointmentId = button.data('id');

            var modal = $(this);
            modal.find('#deleteAppointmentId').val(appointmentId);
        });


        $(document).ready(function() {
            $('#searchBar').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#appointmentsTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>

</html>
