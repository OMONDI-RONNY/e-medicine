<head>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>


<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="#">E-Medicine Admin</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item hidden-max"><a class="nav-link" href="index.php">Dashboard</a></li>
            <li class="nav-item hidden-max"><a class="nav-link" href="users.php">Users</a></li>
            <li class="nav-item hidden-max"><a class="nav-link" href="appoints.php">Appointments</a></li>
            <li class="nav-item hidden-max"><a class="nav-link" href="prescription.php">Prescriptions</a></li>
            <li class="nav-item hidden-max"><a class="nav-link" href="pharmacy.php">Pharmacy</a></li>
            <li class="nav-item hidden-max"><a class="nav-link" href="lab.php">Laboratory</a></li>
            <li class="nav-item hidden-max"><a class="nav-link" href="finance.php">Finance</a></li>
            <li class="nav-item hidden-max"><a class="nav-link" href="reports.php">Reports</a></li>

           
            <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<style>
    .navbar {
        background-color: #007bff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
    }

    .navbar-toggler {
        border: none;
    }

    .navbar-toggler-icon {
        background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"%3E%3Cpath stroke="rgba%28255, 255, 255, 0.5%29" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"/%3E%3C/svg%3E');
    }

    .profile-bar {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .profile-info {
        font-size: 16px;
        color: #333;
    }

    .profile-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    body {
        padding-top: 20px;
    }

 
    @media (min-width: 992px) {
        .hidden-max {
            display: none !important;
        }
    }

    
    @media (max-width: 991.98px) {
        .navbar-nav .nav-item {
            display: block;
        }
    }
</style>
