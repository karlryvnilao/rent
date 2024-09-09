<?php

include '../conn/config.php';
session_start(); // Start session to use session variables

// Editing and Updating Boarding House info
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'edit') {
    // Validate and sanitize inputs
    $businessName = mysqli_real_escape_string($conn, $_POST['business_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $landmark = mysqli_real_escape_string($conn, $_POST['landmark']);
    $noRooms = isset($_POST['no_rooms']) ? intval($_POST['no_rooms']) : 0; // Ensure no_rooms is set and valid
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $gmapLink = mysqli_real_escape_string($conn, $_POST['gmap_link']);
    
    // Assuming $userId is obtained from session or previous logic
    $userId = $_SESSION['user_id']; // Example, adjust based on your logic

    // Debugging: Print values to check
    echo "<pre>";
    print_r($_POST);
    echo "User ID: $userId\n";
    echo "Business Name: $businessName\n";
    echo "Location: $location\n";
    echo "Landmark: $landmark\n";
    echo "Description: $description\n";
    echo "GMap Link: $gmapLink\n";
    echo "</pre>";

    // Update the data in bh_info table
    $updateQuery = "UPDATE bh_info SET business_name = ?, location = ?, landmark = ?, description = ?, gmap_link = ? WHERE landlord_id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssi", $businessName, $location, $landmark, $description, $gmapLink, $userId);
        $result = mysqli_stmt_execute($stmt);

        if ($result === false) {
            $_SESSION['error-message'] = "Failed to update boarding house information. Error: " . mysqli_error($conn);
        } else {
            $_SESSION['success-message'] = "Boarding House information updated successfully.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error-message'] = "Failed to prepare the update query. Error: " . mysqli_error($conn);
    }

    // Redirect to the same page to prevent form resubmission
    header("Location: ../Landlord/pg_bh.php");
    exit();
}
?>
