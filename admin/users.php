<?php
session_start();
// Include the database connection file
include '../access/config.php';
if (!isset($_SESSION['username'])) {
    // Admin is not logged in, redirect to login page
    header("Location: login.php");
    exit; // Ensure no further code is executed
}

// Function to fetch users from both patients and doctors tables
function getAllUsers($conn) {
    $users = [];

    // Query to get patients
    $patients_sql = "SELECT patientid AS id, firstname, email, status FROM patients";
    $patients_result = $conn->query($patients_sql);
    
    // Check if the query succeeded
    if ($patients_result && $patients_result->num_rows > 0) {
        while ($row = $patients_result->fetch_assoc()) {
            $row['type'] = 'Patient'; // Add a type field to identify the user type
            $users[] = $row; // Add each patient row to the users array
        }
    }

    // Query to get doctors
    $doctors_sql = "SELECT doctorid AS id, firstname AS firstname, email, status FROM doctors";
    $doctors_result = $conn->query($doctors_sql);
    
    // Check if the query succeeded
    if ($doctors_result && $doctors_result->num_rows > 0) {
        while ($row = $doctors_result->fetch_assoc()) {
            $row['type'] = 'Doctor'; // Add a type field to identify the user type
            $users[] = $row; // Add each doctor row to the users array
        }
    }

    return $users;
}

// Fetch users data
$users = getAllUsers($conn);

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['status'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    
    // Determine if we are adding or updating
    if (isset($_POST['id'])) { // If id is set, we are updating an existing user
        $id = $_POST['id'];
        $type = $_POST['type']; // Get the user type (Patient or Doctor)
        if ($type === 'Patient') {
            $update_sql = "UPDATE patients SET firstname=?, email=?, status=? WHERE patientid=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssi", $name, $email, $status, $id);
        } else {
            $update_sql = "UPDATE doctors SET firstname=?, email=?, status=? WHERE doctorid=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssi", $name, $email, $status, $id);
        }
        $stmt->execute();
    } else { // If no id, we are adding a new user
        // Assuming patients table, you can modify this based on your needs
        $insert_sql = "INSERT INTO patients (firstname, email, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $name, $email, $status);
        $stmt->execute();
    }
    // Redirect to avoid re-submitting the form
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $type = $_GET['type']; // Get the user type (Patient or Doctor)
    if ($type === 'Patient') {
        $delete_sql = "DELETE FROM patients WHERE patientid=?";
    } else {
        $delete_sql = "DELETE FROM doctors WHERE doctorid=?";
    }
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    // Redirect to avoid re-submitting the form
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - E-Medicine System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
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

        /* Page Layout */
        .dashboard {
            display: flex;
        }

        .container {
            padding: 20px;
            flex-grow: 1;
        }

        /* Header */
        .page-header h1 {
            font-size: 2rem;
            color: #007bff;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

        /* User Filters */
        .user-filters {
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

        .role-filter {
            padding: 8px;
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
        .container {
        padding: 20px;
        flex-grow: 1;
        margin-left: 20px; /* Shift content towards the sidebar */
    }

        /* Users Table */
        .users-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .users-table th,
        .users-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .users-table th {
            background-color: #007bff;
            color: white;
        }

        /* Status Indicators */
        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }

        /* Action Buttons */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-filters {
                flex-direction: column;
                gap: 10px;
            }

            .search-bar,
            .role-filter,
            .btn-primary {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?> <!-- Include the header file -->
    <div class="dashboard">
        <?php include 'sidebar.php'; ?> <!-- Include the sidebar file -->

        <div class="container">
            <div class="page-header">
                <h1>User Management</h1>
                <p>Manage registered users, assign roles, and set permissions.</p>
            </div>

            <!-- User Search & Filters -->
            <div class="user-filters">
                <input type="text" id="search-bar" class="search-bar" placeholder="Search users by name or email..." onkeyup="filterUsers()">
                <select id="role-filter" class="role-filter" onchange="filterUsers()">
                    <option value="">Filter by role</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Patient">Patient</option>
                </select>
                <button class="btn-primary" data-toggle="modal" data-target="#addUserModal">Add New User</button>
            </div>

            <!-- Users Table -->
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <?php foreach ($users as $user): ?>
                        <tr data-type="<?php echo $user['type']; ?>">
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="<?php echo $user['status'] == 'Active' ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo htmlspecialchars($user['status']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['type']); ?></td>
                            <td>
                                <button class="btn-edit" data-toggle="modal" data-target="#editUserModal" onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['firstname']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['status']; ?>', '<?php echo $user['type']; ?>')">Edit</button>
                                <a href="?delete=<?php echo $user['id']; ?>&type=<?php echo $user['type']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../resources/includes/footer.php'; ?> <!-- Include the footer file -->


    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" method="POST">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <input type="hidden" name="type" value="Patient"> <!-- Adjust according to your requirements -->
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="POST">
                        <input type="hidden" name="id" id="editUserId">
                        <input type="hidden" name="type" id="editUserType">
                        <div class="form-group">
                            <label for="editName">Name</label>
                            <input type="text" class="form-control" name="name" id="editUserName" required>
                        </div>
                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" class="form-control" name="email" id="editUserEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="editStatus">Status</label>
                            <select class="form-control" name="status" id="editUserStatus" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function editUser(id, name, email, status, type) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUserName').value = name;
            document.getElementById('editUserEmail').value = email;
            document.getElementById('editUserStatus').value = status;
            document.getElementById('editUserType').value = type;
        }

        function filterUsers() {
            const searchBar = document.getElementById('search-bar').value.toLowerCase();
            const roleFilter = document.getElementById('role-filter').value;
            const usersTableBody = document.getElementById('users-table-body');
            const rows = usersTableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const name = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
                const email = rows[i].getElementsByTagName('td')[2].textContent.toLowerCase();
                const type = rows[i].getAttribute('data-type');

                const matchesSearch = name.includes(searchBar) || email.includes(searchBar);
                const matchesRole = !roleFilter || type === roleFilter;

                rows[i].style.display = (matchesSearch && matchesRole) ? '' : 'none';
            }
        } 
    </script>
</body>

</html>
