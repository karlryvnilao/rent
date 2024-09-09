<?php
include '../connection/conn.php'; 
session_start();

// Check if tenant_id is received and user is logged in
if (isset($_POST['tenant_id']) && isset($_SESSION['user_id'])) {
    $tenant_id = $_POST['tenant_id'];
    $owner_id = $_SESSION['user_id'];

    // SQL query to fetch properties linked to the selected tenant and owner
    $stmt = $conn->prepare("
        SELECT p.id, p.location, p.type 
        FROM rentals r
        JOIN properties p ON r.property_id = p.id
        WHERE r.tenant_id = ? AND p.owner_id = ? AND r.status = 'approved'
    ");

    if ($stmt) {
        $stmt->bind_param("ii", $tenant_id, $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any properties are found and output them as options
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Output property options with location and type
                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['location']) . ' - ' . htmlspecialchars($row['type']) . '</option>';
            }
        } else {
            echo '<option disabled>No properties found for this tenant</option>';
        }
        $stmt->close();
    } else {
        // Handle errors if statement preparation fails
        echo '<option disabled>Error preparing SQL statement</option>';
    }
} else {
    // Handle invalid request
    echo '<option disabled>Invalid request</option>';
}

$conn->close();
?>
