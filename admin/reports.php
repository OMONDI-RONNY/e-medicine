<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - E-Medicine System</title>
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

        /* Reports Table */
        .reports-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .reports-table th,
        .reports-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .reports-table th {
            background-color: #007bff;
            color: white;
        }

        /* Action Buttons */
        .btn-generate {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-generate {
            background-color: #28a745;
            color: white;
        }

        .btn-generate:hover {
            background-color: #218838;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .reports-table table {
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
                <h1>Reports</h1>
                <p>Generate and view various reports related to the administration module.</p>
            </div>

            <!-- Reports Table -->
            <div class="reports-table">
                <button class="btn-generate" data-toggle="modal" data-target="#generateReportModal">Generate Report</button>
                <table>
                    <thead>
                        <tr>
                            <th>Report Type</th>
                            <th>Date Generated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Patient Summary Report</td>
                            <td>2024-10-01</td>
                            <td>
                                <button class="btn-generate">Download</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Appointment Report</td>
                            <td>2024-10-02</td>
                            <td>
                                <button class="btn-generate">Download</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Prescription Report</td>
                            <td>2024-10-03</td>
                            <td>
                                <button class="btn-generate">Download</button>
                            </td>
                        </tr>
                        <!-- Additional report records can be added here -->
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
