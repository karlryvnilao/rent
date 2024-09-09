<?php
include '../connection/conn.php'; // Include your database connection
session_start(); // Start session

// Check if user is logged in and has the role 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tenant') {
    header("Location: ../index.php"); // Redirect to login page if not logged in as a tenant
    exit();
}

// Check if POST data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['property_id'], $_POST['tenant_id']) &&
        is_numeric($_POST['property_id']) &&
        is_numeric($_POST['tenant_id'])) {

        $property_id = intval($_POST['property_id']);
        $tenant_id = intval($_POST['tenant_id']);
        $start_date = date('Y-m-d'); // Current date
        $end_date = date('Y-m-d', strtotime('+1 month')); // Example end date, 1 month from today

        // Fetch the property price (assuming it is passed from the property details page)
        $sql = "SELECT price FROM properties WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            echo "<p>Property not found. Please try again.</p>";
            exit();
        }
        $property = $result->fetch_assoc();
        $rent_amount = $property['price'];

        // Insert rental agreement into the database
        $sql = "INSERT INTO rental_agreements (tenant_id, property_id, start_date, end_date, rent_amount) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissd", $tenant_id, $property_id, $start_date, $end_date, $rent_amount);

        if ($stmt->execute()) {
            // Update property status to rented (2) after successful agreement insertion
            $sql = "UPDATE properties SET available = 2 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $property_id);
            if ($stmt->execute()) {
                echo "<p>Rental agreement created successfully. <a href='index.php'>Back to Properties</a></p>";
            } else {
                echo "<p>Failed to update property status. Please try again later.</p>";
            }
        } else {
            echo "<p>Error: Could not create rental agreement. Please try again later.</p>";
        }
    } else {
        echo "<p>Invalid request. Please try again.</p>";
    }
} else {
    header("Location: index.php"); // Redirect if accessed without POST request
    exit();
}
