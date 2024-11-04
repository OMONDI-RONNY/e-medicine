<?php
session_start();

include '../access/config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

function getAllUsers($conn) {
    $users = [];
    $patients_sql = "SELECT patientid AS id, firstname, email, status FROM patients";
    $patients_result = $conn->query($patients_sql);

    if ($patients_result && $patients_result->num_rows > 0) {
        while ($row = $patients_result->fetch_assoc()) {
            $row['type'] = 'Patient';
            $users[] = $row;
        }
    }

    $doctors_sql = "SELECT doctorid AS id, firstname AS firstname, email, status FROM doctors";
    $doctors_result = $conn->query($doctors_sql);

    if ($doctors_result && $doctors_result->num_rows > 0) {
        while ($row = $doctors_result->fetch_assoc()) {
            $row['type'] = 'Doctor';
            $users[] = $row;
        }
    }

    return $users;
}

function generateRandomPassword($length = 8) {
    return bin2hex(random_bytes($length / 2));
}

function sendSMS($endpoint, $apiKey, $to, $from, $message) {
    $request = [
        'to' => $to,
        'from' => $from,
        'message' => $message
    ];
    $requestBody = json_encode($request);

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

    curl_exec($curl);
    curl_close($curl);
}

$users = getAllUsers($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'] ?? null;
    $status = $_POST['status'];
    $role = $_POST['role'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Editing existing user
        $id = $_POST['id'];
        $type = $_POST['type']; // Get the user type here

        if ($type === 'Patient') {
            $update_sql = "UPDATE patients SET firstname=?, email=?, phone=?, status=? WHERE PatientID=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssi", $name, $email, $phone, $status, $id);
        } else {
            $update_sql = "UPDATE doctors SET firstname=?, email=?, status=? WHERE DoctorID=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssi", $name, $email, $status, $id);
        }

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $errorMsg = "Error updating user details: " . $conn->error;
        }
        $stmt->close();
    } else {
        // Adding new user
        if ($role === 'Patient') {
            $insert_sql = "INSERT INTO patients (firstname, email, phone, status, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssss", $name, $email, $phone, $status, $password);
        } else {
            $insert_sql = "INSERT INTO doctors (firstname, email, status, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $name, $email, $status, $password);
        }

        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            $endpoint = 'https://api.tiaraconnect.io/api/messaging/sendsms';
            $apiKey = 'eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIzNTEiLCJvaWQiOjM1MSwidWlkIjoiN2Y5ZGQ1ZmMtM2QwMi00ZGZiLTg1YjItY2FjMDBlYjU0NDhkIiwiYXBpZCI6MjQxLCJpYXQiOjE3MTExOTQyMTAsImV4cCI6MjA1MTE5NDIxMH0._BW3-yd5JJmAnRsL_trguFXmTLKFmz_a4EAJVmoIk7H66Lpccj3uKiwuTJjgYoxKLU6ZH0EhAC3pkDU2wQcPXQ';
            $from = 'TIARACONECT';
            $message = 'Welcome to E-Medicine, ' . $name . '! You have been successfully registered as a ' . $role . '. Your login ID is: ' . $userId . ' and your password is: ' . $password;

            sendSMS($endpoint, $apiKey, $phone, $from, $message);
            header("Location: " . $_SERVER['PHP_SELF'] . "?sms_sent=1");
            exit();
        } else {
            $errorMsg = "Error submitting details: " . $conn->error;
        }

        $stmt->close();
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $type = $_GET['type'];
    if ($type === 'Patient') {
        $delete_sql = "DELETE FROM patients WHERE patientid=?";
    } else {
        $delete_sql = "DELETE FROM doctors WHERE doctorid=?";
    }
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

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
            flex-grow: 1;
            width: 100%;
        }

     
        .page-header h1 {
            font-size: 2rem;
            color: #007bff;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

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
            margin: 0;
            max-width: 100%;
            flex-grow: 1;
        }

       
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

       
        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
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

    <?php include 'header.php'; ?>
    <div class="dashboard">
        <?php include 'sidebar.php'; ?> 

        <div class="container">
            <div class="page-header">
                <h1>User Management</h1>
                <p>Manage registered users, assign roles, and set permissions.</p>
            </div>

        
            <div class="user-filters">
                <input type="text" id="search-bar" class="search-bar" placeholder="Search users by name or email..." onkeyup="filterUsers()">
                <select id="role-filter" class="role-filter" onchange="filterUsers()">
                    <option value="">Filter by role</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Patient">Patient</option>
                </select>
                <button class="btn-primary" data-toggle="modal" data-target="#addUserModal">Add New User</button>
            </div>

           
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
    <?php include '../resources/includes/footer.php'; ?> 


   
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
                        <label for="email">Phone</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" name="role" required>
                            <option value="Patient">Patient</option>
                            <option value="Doctor">Doctor</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>

   
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
                        <input type="hidden" name="type" id="editUser Type">
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

   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
          function editUser(id, name, email, status, type) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUserName').value = name;
            document.getElementById('editUserEmail').value = email;
            document.getElementById('editUserStatus').value = status;
            document.getElementById('editUser Type').value = type;
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
      
   
    if (new URLSearchParams(window.location.search).has('sms_sent')) {
        alert("SMS sent successfully to the user with login credentials.");
    }


    </script>
</body>

</html>
