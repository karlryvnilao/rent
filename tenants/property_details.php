<?php
// Include your database connection
include '../connection/conn.php';
session_start(); // Start the session

// Check if user is logged in and has the role 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tenant') {
    header("Location: ../login.php"); // Redirect to login page if not logged in or not a tenant
    exit();
}

// Check if the property ID is provided
if (!isset($_GET['id'])) {
    echo "Property ID is missing.";
    exit();
}

$property_id = intval($_GET['id']); // Get the property ID from URL

// Fetch the property details
$sql = "SELECT id, description, price, location, file_path, type FROM properties WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    echo "Property not found.";
    exit();
}

// Handle the rent request
if (isset($_POST['action']) && $_POST['action'] === 'rent') {
    $tenant_id = $_SESSION['user_id'];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .property-details {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .property-details img {
            max-width: 100%;
            border-radius: 5px;
        }
        .btn-rent {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="my-4 d-flex justify-content-between align-items-center">
            <h1>Property Details</h1>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </header>

        <div class="property-details">
            <img src="../owner/<?= htmlspecialchars($property['file_path']) ?>" alt="<?= htmlspecialchars($property['description']) ?>">
            <div>
                <p><strong>Type:</strong> <?= htmlspecialchars($property['type']) ?></p>
                <p><strong>Price:</strong> $<?= number_format($property['price'], 2) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
                <p><?= htmlspecialchars($property['description']) ?></p>
                
                <!-- Rent Now Button -->
                <form action="property_details.php?id=<?= htmlspecialchars($property['id']) ?>" method="POST">
                    <input type="hidden" name="action" value="rent">
                    <button type="submit" class="btn btn-primary btn-rent">Rent Now</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
