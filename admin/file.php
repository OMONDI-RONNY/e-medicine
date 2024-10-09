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
        }

        .page-header h1 {
            font-size: 2rem;
            color: #007bff;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .prescription-filters {
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

        .prescriptions-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .prescriptions-table th,
        .prescriptions-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .prescriptions-table th {
            background-color: #007bff;
            color: white;
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
            .prescription-filters {
                flex-direction: column;
                gap: 10px;
            }

            .search-bar,
            .btn-primary {
                width: 100%;
            }
        }
    </style>