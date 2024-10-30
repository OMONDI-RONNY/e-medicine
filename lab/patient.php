<?php
session_start();
include '../access/config.php'; // Include database configuration

// Redirect to login if the user is not logged in
if (!isset($_SESSION['lab_user'])) {
    header('Location: login.php');
    exit();
}

// Function to fetch patients
function getPatients($conn) {
    $patients = [];
    $query = "SELECT * FROM patients ORDER BY lastname ASC";
    
    $result = $conn->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    
    return $patients;
}

// Handle form submission to add a new patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['age'], $_POST['gender'], $_POST['contact'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $age = (int)$_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $contact = $conn->real_escape_string($_POST['contact']);

    // Insert new patient
    $query = "INSERT INTO patients (fullname, age, gender, contact, created_at)
              VALUES ('$name', '$age', '$gender', '$contact', NOW())";
    
    if ($conn->query($query) === TRUE) {
        echo "<script>alert('New patient added successfully!'); window.location.href='patient_management.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$patients = getPatients($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .sidebar {
            height: 100vh;
            background-color: #007bff; 
            color: white;
            position: fixed;
            width: 220px;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            margin: 10px 0;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #0056b3;
            padding-left: 10px; 
        }
        .content {
            margin-left: 240px; 
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .card-header h5 {
            margin: 0;
        }
        .table {
            border-radius: 10px;
            overflow: hidden; 
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?> 

    <div class="content">
        <h1>Patient Management</h1>
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Patient List</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?php echo $patient['firstname']; ?></td>
                                <td><?php echo $patient['Age']; ?></td>
                                <td><?php echo $patient['Gender']; ?></td>
                                <td><?php echo $patient['Phone']; ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-right mt-3">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addPatientModal">
                        <i class="fas fa-plus"></i> Add Patient
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Patient Modal -->
    <div class="modal fade" id="addPatientModal" tabindex="-1" role="dialog" aria-labelledby="addPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPatientModalLabel">Add Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="patient_management.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact</label>
                            <input type="text" class="form-control" id="contact" name="contact" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
