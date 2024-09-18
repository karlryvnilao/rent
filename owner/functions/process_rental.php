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
    // Validate and sanitize inputs
    $rental_id = isset($_POST['rental_id']) ? intval($_POST['rental_id']) : 0;
    $tenant_id = isset($_POST['tenant_id']) ? intval($_POST['tenant_id']) : 0;
    $property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Validate action
    if ($action !== 'approve' && $action !== 'reject') {
        echo "Invalid action.";
        exit();
    }

    // Prepare SQL based on action
    if ($action === 'approve') {
        $sql = "UPDATE rentals SET status = 'approved' WHERE id = ? AND property_id IN (SELECT id FROM properties WHERE owner_id = ?)";
        
        // Update property availability
        $updatePropertySql = "UPDATE properties SET available = 2 WHERE id = ? AND owner_id = ?";
    } elseif ($action === 'reject') {
        $sql = "UPDATE rentals SET status = 'rejected' WHERE id = ? AND property_id IN (SELECT id FROM properties WHERE owner_id = ?)";
        
        // No need to update property availability when rejecting
        $updatePropertySql = null;
    }

    // Execute rental status update
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $rental_id, $owner_id);
        if ($stmt->execute()) {
            if ($updatePropertySql) {
                // Execute property availability update
                if ($propertyStmt = $conn->prepare($updatePropertySql)) {
                    $propertyStmt->bind_param("ii", $property_id, $owner_id);
                    $propertyStmt->execute();
                    $propertyStmt->close();
                } else {
                    echo "<div class='alert alert-danger'>Error preparing property update statement: " . htmlspecialchars($conn->error) . "</div>";
                }
            }
            // Redirect to the pending rentals page
            header("Location: ../pending_tenants.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error executing rental update query: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing rental update statement: " . htmlspecialchars($conn->error) . "</div>";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
