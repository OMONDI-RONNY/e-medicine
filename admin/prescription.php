<?php
session_start();
include '../access/config.php'; 
if (!isset($_SESSION['username'])) {
    
    header("Location: login.php");
    exit; 
}


$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query = "SELECT prescriptions.PrescriptionID, patients.firstname AS PatientName, prescriptions.Medication, 
          prescriptions.Dosage, prescriptions.CreatedAt, prescriptions.Status 
          FROM prescriptions 
          JOIN appointments ON prescriptions.AppointmentID = appointments.AppointmentID
          JOIN patients ON appointments.PatientID = patients.PatientID 
          LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$prescriptions = [];
while ($row = $result->fetch_assoc()) {
    $prescriptions[] = $row;
}


$totalQuery = "SELECT COUNT(*) AS total FROM prescriptions";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalPrescriptions = $totalRow['total'];
$totalPages = ceil($totalPrescriptions / $limit);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        
        $prescriptionID = $_POST['prescription_id'];
        $status = $_POST['status']; 
        $updateQuery = "UPDATE prescriptions SET Status = '$status' WHERE PrescriptionID = $prescriptionID";
        if ($conn->query($updateQuery) === TRUE) {
            
        } else {
            die("Update failed: " . $conn->error);
        }
    }

    if (isset($_POST['delete'])) {
        
        $prescriptionID = $_POST['prescription_id'];
        $deleteQuery = "DELETE FROM prescriptions WHERE PrescriptionID = $prescriptionID";
        if ($conn->query($deleteQuery) === TRUE) {
            
        } else {
            die("Delete failed: " . $conn->error);
        }
    }

    
    header("Location: prescription.php?page=$page");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Management - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
          body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
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

        .page-header h1 {
            font-size: 2rem;
            color: #007bff;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .prescription-filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-bar {
            padding: 8px;
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        
        .prescriptions-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .prescriptions-table th,
        .prescriptions-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .prescriptions-table th {
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
            transition: background 0.3s;
        }

        .btn-edit {
            background-color: #ffc107;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        @media (max-width: 768px) {
            .prescription-filters {
                flex-direction: column;
                gap: 10px;
            }

            .search-bar,
            .btn-primary {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?> 
    <div class="dashboard">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <div class="page-header">
                <h1>Prescription Management</h1>
                <p>Manage patient prescriptions effectively.</p>
            </div>

            <div class="prescription-filters">
                <input type="text" class="search-bar" placeholder="Search prescriptions..." id="searchInput">
                
            </div>

            <div class="prescriptions-table">
                <?php if (count($prescriptions) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Medication</th>
                            <th>Dosage</th>
                            <th>Date Issued</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="prescriptionTableBody">
                        <?php foreach ($prescriptions as $prescription): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prescription['PatientName']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['Medication']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['Dosage']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($prescription['CreatedAt'])); ?></td>
                            <td><span class="badge badge-<?php echo strtolower($prescription['Status']); ?>"><?php echo htmlspecialchars($prescription['Status']); ?></span></td>
                            <td>
                                <button class="btn-edit" data-toggle="modal" data-target="#editPrescriptionModal" data-id="<?php echo $prescription['PrescriptionID']; ?>" data-status="<?php echo $prescription['Status']; ?>">Edit</button>
                                <button class="btn-delete" data-toggle="modal" data-target="#deletePrescriptionModal" data-id="<?php echo $prescription['PrescriptionID']; ?>">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No prescriptions found.</p>
                <?php endif; ?>
            </div>

          
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../resources/includes/footer.php'; ?> 

    
    <div class="modal fade" id="editPrescriptionModal" tabindex="-1" role="dialog" aria-labelledby="editPrescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPrescriptionModalLabel">Edit Prescription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <input type="hidden" name="prescription_id" id="editPrescriptionID">
                        <div class="form-group">
                            <label for="editStatus">Status</label>
                            <select name="status" id="editStatus" class="form-control">
                                <option value="Active">Active</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" name="edit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

  
    <div class="modal fade" id="deletePrescriptionModal" tabindex="-1" role="dialog" aria-labelledby="deletePrescriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePrescriptionModalLabel">Delete Prescription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <input type="hidden" name="prescription_id" id="deletePrescriptionID">
                        <p>Are you sure you want to delete this prescription?</p>
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            var rows = document.querySelectorAll('#prescriptionTableBody tr');
            
            rows.forEach(function(row) {
                var cells = row.getElementsByTagName('td');
                var found = false;
                for (var i = 0; i < cells.length; i++) {
                    if (cells[i].textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
                row.style.display = found ? '' : 'none'; 
        });

        
        $('#editPrescriptionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var prescriptionID = button.data('id');
            var status = button.data('status');
            var modal = $(this);
            modal.find('#editPrescriptionID').val(prescriptionID);
            modal.find('#editStatus').val(status);
        });

        
        $('#deletePrescriptionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var prescriptionID = button.data('id');
            var modal = $(this);
            modal.find('#deletePrescriptionID').val(prescriptionID);
        });
    </script>
</body>

</html>
