<?php
// Include the database connection file
include '../access/config.php';

// Function to fetch users from both patients and doctors tables
function getAllUsers($conn) {
    $users = [];

    // Query to get patients
    $patients_sql = "SELECT patientid AS id, name, email, status FROM patients";
    $patients_result = $conn->query($patients_sql);
    
    // Check if the query succeeded
    if ($patients_result && $patients_result->num_rows > 0) {
        while ($row = $patients_result->fetch_assoc()) {
            $row['type'] = 'Patient'; // Add a type field to identify the user type
            $users[] = $row; // Add each patient row to the users array
        }
    }

    // Query to get doctors
    $doctors_sql = "SELECT doctorid AS id, name, email, status FROM doctors";
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
                <button class="btn-primary">Add New User</button>
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
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['status']; ?></td>
                            <td><?php echo $user['type']; ?></td> <!-- Display the user type -->
                            <td>
                                <button class="btn-edit">Edit</button>
                                <button class="btn-delete">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?> <!-- Include the footer file -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script>
        function filterUsers() {
            const searchInput = document.getElementById('search-bar').value.toLowerCase();
            const roleInput = document.getElementById('role-filter').value;
            const tableRows = document.querySelectorAll('#users-table-body tr');

            tableRows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const type = row.getAttribute('data-type');

                const matchesSearch = name.includes(searchInput) || email.includes(searchInput);
                const matchesRole = roleInput === '' || type === roleInput;

                if (matchesSearch && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>
