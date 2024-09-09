<?php
include 'connection/conn.php';

// Initialize variables for error messages
$error_message = "";
$success_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = trim($_POST['name']);
    $birthday = trim($_POST['birthday']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $contactnumber = trim($_POST['contactnumber']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = trim($_POST['role']); // New field for role selection

    // Initialize variables for file upload
    $upload_path = "";

    // Handle file upload
    if (isset($_FILES['profile_pic'])) {
        $profile_pic = $_FILES['profile_pic'];
        $profile_pic_name = $profile_pic['name'];
        $profile_pic_tmp_name = $profile_pic['tmp_name'];
        $profile_pic_error = $profile_pic['error'];
        $profile_pic_size = $profile_pic['size'];
        
        // Validate file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($profile_pic_tmp_name);
        
        if ($profile_pic_error === UPLOAD_ERR_OK) {
            if (!in_array($file_type, $allowed_types)) {
                $error_message = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            } elseif ($profile_pic_size > 2000000) { // 2MB limit
                $error_message = "File size exceeds the 2MB limit.";
            } else {
                // Generate a unique file name to prevent overwriting
                $unique_name = uniqid('', true) . '.' . pathinfo($profile_pic_name, PATHINFO_EXTENSION);
                $upload_dir = 'uploads/profile_pics/';
                $upload_path = $upload_dir . $unique_name;
                
                if (!move_uploaded_file($profile_pic_tmp_name, $upload_path)) {
                    $error_message = "Failed to upload profile picture.";
                }
            }
        } else {
            $error_message = "Error uploading file.";
        }
    } else {
        $error_message = "No profile picture uploaded.";
    }
    
    // Validate input
    if (empty($error_message)) {
        if ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $error_message = "Username or email already exists.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user into the users table
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);
                
                if ($stmt->execute()) {
                    // Get the last inserted user ID
                    $user_id = $stmt->insert_id;
                    
                    // Insert user details into the user_details table
                    $stmt = $conn->prepare("INSERT INTO user_details (user_id, name, birthday, address, contactnumber, profile_pic) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssss", $user_id, $name, $birthday, $address, $contactnumber, $upload_path);
                    
                    if ($stmt->execute()) {
                        $success_message = "Registration successful. You can now log in.";
                    } else {
                        $error_message = "Failed to insert user details: " . $stmt->error;
                    }
                } else {
                    $error_message = "Registration failed: " . $stmt->error;
                }
            }
            
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="my-4  d-flex justify-content-between align-items-center">
        <h1>Register</h1>
        <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            </div>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="post" class="mt-3">
            <div class="mb-3">
                <label for="name" class="form-label">Fullname</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="birthday" class="form-label">Birthday</label>
                <input type="date" class="form-control" id="birthday" name="birthday" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="contactnumber" class="form-label">Contact Number</label>
                <input type="tel" class="form-control" id="contactnumber" name="contactnumber" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="role_owner" value="owner" checked>
                    <label class="form-check-label" for="role_owner">
                        Owner
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="role_tenant" value="tenant">
                    <label class="form-check-label" for="role_tenant">
                        Tenant
                    </label>
                </div>
            </div>
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Profile Picture</label>
                <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
