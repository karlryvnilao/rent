<?php
// Include your database connection
include '../../connection/conn.php';
session_start(); // Start the session

// Check if user is logged in and has the role 'owner'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: ../../index.php"); // Redirect to login page if not logged in or not an owner
    exit();
}

$owner_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rental_id = intval($_POST['rental_id']);
    $tenant_id = intval($_POST['tenant_id']);
    $property_id = intval($_POST['property_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $sql = "UPDATE rentals SET status = 'approved' WHERE id = ? AND property_id IN (SELECT id FROM properties WHERE owner_id = ?)";
    } elseif ($action === 'reject') {
        $sql = "UPDATE rentals SET status = 'rejected' WHERE id = ? AND property_id IN (SELECT id FROM properties WHERE owner_id = ?)";
    } else {
        echo "Invalid action.";
        exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $rental_id, $owner_id);

    if ($stmt->execute()) {
        // Redirect to the pending rentals page
        header("Location: ../pending_rentals.php?status=success");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
