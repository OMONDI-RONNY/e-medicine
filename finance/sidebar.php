

<div class="sidebar">
    <h2 class="text-center text-white">Finance Dashboard</h2>
    <ul class="list-unstyled">
        <li>
            <a href="index.php" class="sidebar-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="billing.php" class="sidebar-link">
                <i class="fas fa-file-invoice-dollar"></i> Billing Management
            </a>
        </li>
        
        
        <li>
            <a href="reports.php" class="sidebar-link">
                <i class="fas fa-file-alt"></i> Financial Reports
            </a>
        </li>
        <li>
            <a href="balance.php" class="sidebar-link">
                <i class="fas fa-user-tag"></i> Patient Balances
            </a>
        </li>
        <li>
            <a href="logout.php" class="sidebar-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<style>
    .sidebar {
        height: 100vh;
        background-color: #007bff; 
        color: white;
        position: fixed;
        width: 250px; 
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }
    .sidebar h2 {
        margin-bottom: 30px;
        font-size: 1.5em;
    }
    .sidebar-link {
        display: block;
        padding: 10px 15px;
        color: white;
        text-decoration: none;
        border-radius: 5px; 
        transition: background-color 0.3s, padding-left 0.3s;
    }
    .sidebar-link:hover {
        background-color: white; 
        padding-left: 20px; 
    }
    .sidebar-link i {
        margin-right: 10px; 
    }
</style>
