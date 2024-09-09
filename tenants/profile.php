<?php
// Include your database connection
include '../connection/conn.php';
session_start(); // Start the session

// Check if user is logged in and has the role 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tenant') {
    header("Location: ../index.php"); // Redirect to login page if not logged in or not a tenant
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the user_details table
$sql = "SELECT name, profile_pic FROM user_details WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>User not found.</div>";
        exit();
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Error preparing the SQL statement: " . $conn->error . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .profile-image {
            max-width: 150px;
            border-radius: 50%;
        }
        .profile-info {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container profile-container">
        <header class="text-center">
            <h1>Profile</h1>
        </header>

        <div class="text-center">
            <img src="../uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="profile-image">
            <h3><?= htmlspecialchars($user['name']) ?></h3>
        </div>

        <div class="profile-info">
            <a href="change_password.php" class="btn btn-warning btn-block">Change Password</a>
            <a href="payroll.php" class="btn btn-primary btn-block">My Payroll</a>
            <a href="messages.php" class="btn btn-secondary btn-block">Messages</a>
        </div>
    </div>
    <footer class="sticky-footer">
        <div class="container d-flex justify-content-end">
            <a href="logout.php" class="btn btn-secondary">logout</a> <!-- Back button in the sticky footer -->
        </div>
    </footer>

    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
