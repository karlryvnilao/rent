<?php
include '../../connection/conn.php';
session_start(); // Start the session

// Check if the user is logged in and has the role 'owner'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if (isset($_POST['id'])) {
    $rental_id = intval($_POST['id']);

    // Fetch property_id from the rentals table
    $sql = "SELECT property_id FROM rentals WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rental_id);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        exit();
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $property_id = $row['property_id'];

    // Delete the rental record
    $sql = "DELETE FROM rentals WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rental_id);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        exit();
    }

    // Update the properties table to set available status back to 1
    $sql = "UPDATE properties SET available = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        exit();
    }

    echo json_encode(['status' => 'success', 'message' => 'Tenant removed successfully']);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}
?>
