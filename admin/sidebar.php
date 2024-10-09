<?php
// sidebar.php

// Start of the sidebar HTML structure
?>
<div class="sidebar" id="sidebar">
    <h2 class="sidebar-title">Admin Dashboard</h2>
    <ul class="list-group">
        <li class="list-group-item">
            <a href="#"><i class="fas fa-users"></i> Users</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-calendar-check"></i> Appointments</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-pills"></i> Pharmacy</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-vial"></i> Laboratory</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-dollar-sign"></i> Finance</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-chart-bar"></i> Reports</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-cogs"></i> Settings</a>
        </li>
        <li class="list-group-item">
            <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </li>
    </ul>
</div>

<!-- Toggle Button for Mobile -->
<button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

<!-- Add Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Sidebar Styling */
    .sidebar {
        background-color: #007bff;
        padding: 20px;
        margin-right: 20px;
        flex: 0 0 450px;
        height: auto;
        transition: transform 0.3s ease;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    /* Sidebar Title */
    .sidebar-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: white;
    }

    /* Remove background color from list items */
    .list-group-item {
        background-color: transparent;
        border: none;
        padding: 10px 0;
    }

    .list-group-item a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .list-group-item a i {
        margin-right: 10px;
    }

    /* Hover effect for links */
    .list-group-item a:hover {
        color: #cce7ff;
    }

    /* Sidebar Toggle Button */
    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        font-size: 1.5rem;
        cursor: pointer;
        border-radius: 5px;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            transform: translateX(-100%);
            z-index: 1000;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-toggle {
            display: none;
        }

        .container {
            padding-top: 70px;
        }
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
</script>
