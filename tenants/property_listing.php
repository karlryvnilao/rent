<?php
// Include your database connection
include '../connection/conn.php';
session_start(); // Start the session

// Check if user is logged in and has the role 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tenant') {
    header("Location: ../login.php"); // Redirect to login page if not logged in or not a tenant
    exit();
}

// Handle the rent request
if (isset($_GET['action']) && $_GET['action'] === 'rent' && isset($_GET['property_id'])) {
    $tenant_id = $_SESSION['user_id'];
    $property_id = intval($_GET['property_id']); // Get the property ID from URL

    // Insert the rental record into the rentals table
    $sql = "INSERT INTO rentals (tenant_id, property_id, rent_start_date, status) 
            VALUES (?, ?, CURDATE(), 'pending')";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ii", $tenant_id, $property_id);
        
        if ($stmt->execute()) {
            // Redirect to tenant's dashboard or a success page
            header("Location: property_listing.php?rent=success");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing the SQL statement: " . $conn->error . "</div>";
    }
}

// Fetch properties available for rent
$sql = "SELECT id, description, price, location, file_path, type FROM properties WHERE available = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .property-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .property-item img {
            max-width: 100%;
            border-radius: 5px;
        }
        .property-details {
            margin-top: 10px;
        }
        .btn-rent {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="my-4">
            <h1>Available Properties for Rent</h1>
        </header>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="property-item">
                    <img src="../owner/<?= htmlspecialchars($row['file_path']) ?>" alt="<?= htmlspecialchars($row['description']) ?>">
                    <div class="property-details">
                        <p><strong>Type:</strong> <?= htmlspecialchars($row['type']) ?></p>
                        <p><strong>Price:</strong> $<?= number_format($row['price'], 2) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <a href="property_details.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-info">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No properties available for rent at the moment.</p>
        <?php endif; ?>

        <?php
        // Check if a rental request was successful
        if (isset($_GET['rent']) && $_GET['rent'] === 'success') {
            echo "<div class='alert alert-success'>Rental request has been submitted successfully.</div>";
        }
        ?>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
