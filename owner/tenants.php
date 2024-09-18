<?php
include '../connection/conn.php'; // Ensure the path is correct and the connection file is properly set up.

session_start(); // Start session to manage user state or other session-related data.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenants Status</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h2>Tenants Status</h2>
                <div class="btn-group mt-4" role="group">
                    <!-- Approved Tenants Button -->
                    <button type="button" class="btn btn-success" id="approvedTenants" onclick="window.location.href='approved_tenants.php'">
                        Approved Tenants
                    </button>
                    <!-- Pending Tenants Button -->
                    <button type="button" class="btn btn-warning" id="pendingTenants" onclick="window.location.href='pending_tenants.php'">
                        Pending Tenants
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
