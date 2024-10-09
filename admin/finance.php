<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Management - E-Medicine System</title>
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

        /* Finance Table */
        .finance-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .finance-table th,
        .finance-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .finance-table th {
            background-color: #007bff;
            color: white;
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
            .finance-table table {
                font-size: 0.9rem;
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
                <h1>Finance Management</h1>
                <p>Handle billing and invoicing processes efficiently.</p>
            </div>

            <!-- Finance Table -->
            <div class="finance-table">
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>$100</td>
                            <td><span class="badge badge-success">Paid</span></td>
                            <td>
                                <button class="btn-edit">Edit</button>
                                <button class="btn-delete">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>$150</td>
                            <td><span class="badge badge-warning">Pending</span></td>
                            <td>
                                <button class="btn-edit">Edit</button>
                                <button class="btn-delete">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Emily Johnson</td>
                            <td>$200</td>
                            <td><span class="badge badge-danger">Failed</span></td>
                            <td>
                                <button class="btn-edit">Edit</button>
                                <button class="btn-delete">Delete</button>
                            </td>
                        </tr>
                        <!-- Additional financial records can be added here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?> <!-- Include the footer file -->

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
