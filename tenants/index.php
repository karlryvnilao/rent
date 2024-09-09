<?php
// Include your database connection
include '../connection/conn.php';
session_start(); // Start the session

// Check if user is logged in and has the role 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tenant') {
    header("Location: ../index.php"); // Redirect to login page if not logged in or not a tenant
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
            header("Location: index.php?rent=success");
            exit();
        } else {
            echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
        // Optionally, log $conn->error for debugging
    }
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch properties available for rent
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Debugging: Check if the search term is captured correctly
// echo "<pre>Search Term: $search</pre>";

// Adjust the SQL query to filter properties based on the search term
$sql = "SELECT id, description, price, location, file_path, type FROM properties WHERE available = 1";

if (!empty($search)) {
    // Sanitize input to prevent SQL injection
    $search = $conn->real_escape_string($search);
    $sql .= " AND (description LIKE '%$search%' OR location LIKE '%$search%' OR type LIKE '%$search%')";
}

// Debugging: Check if the SQL query is constructed correctly
// echo "<pre>SQL Query: $sql</pre>";

$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    echo "<div class='alert alert-danger'>Error fetching properties: " . $conn->error . "</div>";
}
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
            display: flex;
            flex-direction: row; /* Image on the left */
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .property-item img {
            max-width: 300px; /* Limit the width of the image */
            border-radius: 5px;
            margin-right: 20px; /* Space between image and details */
        }
        .property-details {
            flex: 1; /* Allow details to take remaining space */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .property-item {
                flex-direction: column; /* Stack on small screens */
            }
            .property-item img {
                margin-right: 0; /* Remove right margin when stacked */
                margin-bottom: 10px; /* Add bottom margin instead */
                max-width: 100%; /* Full width on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="my-4 d-flex justify-content-between">
            <h1>Available Properties for Rent</h1>
            <a href="profile.php" class="btn btn-secondary">Profile</a>
        </header>

        <!-- Search Form -->
        <form method="GET" action="index.php" class="my-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search for properties..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="property-item">
                    <img src="../owner/<?= htmlspecialchars($row['file_path']) ?>" alt="<?= htmlspecialchars($row['description']) ?>" loading="lazy">
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
