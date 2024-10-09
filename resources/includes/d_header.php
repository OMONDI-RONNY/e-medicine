<?php
// header.php
?>
<nav class="navbar navbar-expand-lg" style="background-color: #007bff;"> <!-- Keep the background color -->
    <a class="navbar-brand" href="#" style="color: white;">E-Medicine</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php" style="color: white;">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="patientdata.php" style="color: white;">Patients</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="appointment.php" style="color: white;">Appointments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="prescription.php" style="color: white;">Prescriptions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php" style="color: white;">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<style>
    /* Custom Hamburger Icon Styling */
    .navbar-toggler {
        border: none;
        outline: none;
    }

    .navbar-toggler-icon {
        background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"%3E%3Cpath stroke="rgba%28255, 255, 255, 0.5%29" stroke-width="2" stroke-linecap="round" stroke-miterlimit="10" d="M4 7h22M4 15h22M4 23h22"/%3E%3C/svg%3E');
    }

    /* Ensure the navbar is responsive */
    @media (max-width: 767.98px) {
        .navbar-collapse {
            background-color: #007bff; /* Keep the collapse background blue */
        }
    }
</style>
