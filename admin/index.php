<?php
session_start();

include '../access/config.php';

// Check if the admin is logged in
if (!isset($_SESSION['username'])) {
    // Admin is not logged in, redirect to login page
    header("Location: login.php");
    exit; // Ensure no further code is executed
}


// Function to get total number of patients
function getTotalPatients($conn) {
    $sql = "SELECT COUNT(*) as total_patients FROM patients";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_patients'];
}

// Function to get total number of today's appointments
function getTodaysAppointments($conn) {
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as total_appointments FROM appointments WHERE DATE(AppointmentDate) = '$today'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_appointments'];
}

// Function to get active prescriptions
function getActivePrescriptions($conn) {
    $sql = "SELECT COUNT(*) as total_active_prescriptions FROM prescriptions WHERE status = 'Active'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_active_prescriptions'];
}

// Function to search for patients or get all patients
function getPatientData($conn, $start, $limit, $search = null) {
    if ($search) {
        $searchQuery = "%" . $conn->real_escape_string($search) . "%";
        $sql = "SELECT * FROM patients WHERE firstname LIKE '$searchQuery' OR lastname LIKE '$searchQuery' LIMIT $start, $limit";
    } else {
        $sql = "SELECT * FROM patients LIMIT $start, $limit";
    }
    $result = $conn->query($sql);
    return $result;
}

// Function to count total patients
function countTotalPatients($conn) {
    $sql = "SELECT COUNT(*) as total_patients FROM patients";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total_patients'];
}

// Add Patient Backend Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_patient') {
    $firstname = $_POST['firstName'];
    $lastname = $_POST['lastName'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    $stmt = $conn->prepare("INSERT INTO patients (firstname, lastname, Age, Gender) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $firstname, $lastname, $age, $gender);
    
    if ($stmt->execute()) {
        echo "<script>alert('Patient added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding patient.');</script>";
    }
    $stmt->close();
}

// Pagination variables
$limit = 5; 
$page = isset($_GET['page']) ? $_GET['page'] : 1; 
$start = ($page - 1) * $limit; 

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : null;

$total_patients = getTotalPatients($conn);
$appointments_today = getTodaysAppointments($conn);
$active_prescriptions = getActivePrescriptions($conn);
$patient_data = getPatientData($conn, $start, $limit, $search);
$total_patients_count = countTotalPatients($conn);
$total_pages = ceil($total_patients_count / $limit);
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

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .dashboard-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        .sidebar {
            background-color: #007bff;
            padding: 20px;
            width: 250px;
            color: white;
            height: 100vh;
        }

        .sidebar .list-group-item {
            background-color: transparent;
            color: white;
            border: none;
        }

        .dashboard {
            padding: 20px;
            flex-grow: 1;
            margin-left: 0;
            width: calc(100% - 250px);
        }

        .card {
            margin-bottom: 20px;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .overview-card {
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: auto;
            max-height: 150px;
        }

        .overview-card h2 {
            font-size: 28px;
        }

        .overview-card p {
            font-size: 16px;
        }

        .overview-card i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .card-yellow {
            background-color: #ffc107;
            color: #333;
        }

        .card-brown {
            background-color: #F06F40FF;
            color: white;
        }

        .card-green {
            background-color: #28a745;
            color: white;
        }

        .footer {
            text-align: center;
            margin: 20px 0;
        }

        .pagination {
            justify-content: center;
        }

        .table-responsive {
            width: 100%;
        }

        @media (max-width: 768px) {
            .overview-card,
            .card {
                margin-bottom: 30px;
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

    <?php include 'header.php'; ?> 

    <div class="dashboard-container"> 
        <?php include 'sidebar.php'; ?> 

        <div class="container-fluid dashboard">
            <h1>Administration Dashboard</h1>

            <!-- Overview Section -->
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="overview-card card-yellow">
                        <i class="fas fa-user-injured"></i>
                        <h2>Total Patients</h2>
                        <p><?php echo $total_patients; ?></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="overview-card card-brown">
                        <i class="fas fa-calendar-check"></i>
                        <h2>Appointments Today</h2>
                        <p><?php echo $appointments_today; ?></p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="overview-card card-green">
                        <i class="fas fa-pills"></i>
                        <h2>Active Prescriptions</h2>
                        <p><?php echo $active_prescriptions; ?></p>
                    </div>
                </div>
            </div>

            <!-- Patient Management Section -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i> Patient Management</h5>
                </div>
                <div class="card-body">
                    <p>Manage patient details and medical records.</p>
                    <form method="GET">
                    <input type="text" id="searchInput" placeholder="Search patients..." class="form-control mb-3">

                </form>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addPatientModal">Add Patient</button>
                    <div class="table-responsive">
                        <table class="table" id="patientTable">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $patient_data->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                                        <td><?php echo $row['Age']; ?></td>
                                        <td><?php echo $row['Gender']; ?></td>
                                        <td><button class="btn btn-warning"><i class="fas fa-stethoscope"></i> Patient Visit</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Patient pagination">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding a patient -->
    <div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPatientModalLabel">Add New Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addPatientForm" method="POST">
                        <input type="hidden" name="action" value="add_patient">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" id="age" name="age" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Patient</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('#patientTable tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let found = false;

            // Check each cell in the row for a match
            for (let i = 0; i < cells.length; i++) {
                const cellText = cells[i].textContent.toLowerCase();
                if (cellText.includes(query)) {
                    found = true; // Match found
                    break; // No need to check further cells
                }
            }

            // Show or hide the row based on the search match
            if (found) {
                row.style.display = ''; // Show the row
            } else {
                row.style.display = 'none'; // Hide the row
            }
        });
    });
</script>


    <!-- Include scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
