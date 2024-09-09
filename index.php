<?php
include 'connection/conn.php';

// Start session
session_start();

// Initialize variables
$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $input_username = $conn->real_escape_string($_POST['username']);
    $input_password = $_POST['password'];

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $role);
    $stmt->fetch();

    // Verify password
    if ($stmt->num_rows > 0 && password_verify($input_password, $hashed_password)) {
        // Set session variables
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = $role;

        // Redirect based on role
        switch ($role) {
            case 'admin':
                header("Location: admin_dashboard.php");
                break;
            case 'owner':
                header("Location: owner/index.php");
                break;
            case 'tenant':
                header("Location: tenants/index.php");
                break;
            default:
                $error_message = "Invalid role.";
        }
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-center py-3 my-5 fs-1">
        <h1 class="mt-5">Login</h1>
        </div>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form method="post" class="mt-3">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary mx-3 my-2">Login</button>
            <a href="register.php">Not yet registered?</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>