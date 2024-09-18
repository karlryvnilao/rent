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

// Fetch the property and owner details
$sql = "
    SELECT 
        properties.id AS property_id, 
        properties.description, 
        properties.price, 
        properties.location, 
        properties.file_path, 
        properties.type, 
        user_details.firstname AS owner_firstname, 
        user_details.lastname AS owner_lastname, 
        users.email AS owner_email, 
        user_details.contactnumber AS owner_phone, 
        user_details.profile_pic AS owner_profile_pic 
    FROM properties 
    JOIN users ON properties.owner_id = users.id 
    JOIN user_details ON users.id = user_details.user_id 
    WHERE properties.id = ?";

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
            header("Location: index.php?rent=success");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($stmt->error) . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error preparing the SQL statement: " . htmlspecialchars($conn->error) . "</div>";
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
            border-radius: 5px;
            background-color: #fff;
            margin-bottom: 70px; /* Space for sticky footer */
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .property-details img {
            width: 100%; /* Make image responsive */
            height: auto; /* Maintain aspect ratio */
            border-radius: 5px;
        }
        .details-container {
            display: flex;
            flex-direction: column;
            padding: 15px;
        }
        .rent-now-button {
            position: absolute;
            bottom: 15px;
            right: 15px;
        }

        /* Sticky footer adjustments */
        .sticky-footer {
            position: -webkit-sticky; /* For Safari */
            position: sticky;
            bottom: 0;
            background-color: #fff;
            padding: 10px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none; /* Hidden by default, shown on mobile */
        }

        /* Grid layout for two containers at the bottom */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1.4fr .6fr;
            gap: 20px;
            margin-top: 20px;
        }
        .right-container {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .owner-details {
            display: flex;
            align-items: center;
            background-color: #f9f9f9;
        }

        .owner-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }

        .owner-info {
            margin-left: 15px;
        }

        .owner-info p {
            margin: 0 0 10px 0;
        }
        /* Responsive design adjustments */
        @media (max-width: 767px) {
            .rent-now-button {
                display: none; /* Hide the button in the main content area on mobile */
            }
            .sticky-footer {
                display: block;
                position: fixed; /* Stick to the bottom of the viewport */
                width: 100%;
                bottom: 0;
            }
            /* Stack containers vertically on small screens */
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .property-details {
                padding: 20px;
            }
            .details-container {
                padding: 10px;
            }
        }

        @media (min-width: 992px) {
            .details-container {
                padding: 20px;
            }
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
        </div>

        <!-- Grid layout for two containers -->
        <div class="bottom-grid mb-5">
            <div class="left-container">
            <div class="property-details">
                <h3><strong><?= htmlspecialchars($property['location']) ?></strong></h3>
                <h5><?= htmlspecialchars($property['type']) ?></h5>
                <h6>₱<?= number_format($property['price'], 2) ?></h6>
                <div class="details-container">
                
                
                <p><?= htmlspecialchars($property['description']) ?></p>
            </div>
            <form action="property_details.php?id=<?= htmlspecialchars($property['property_id']) ?>" method="POST" class="rent-now-button">
                <input type="hidden" name="action" value="rent">
                <button type="submit" class="btn btn-primary">Rent Now</button>
            </form>

            </div>
            </div>
            <div class="right-container">
                <h4>Host Details</h4>
                <div class="owner-details d-flex align-items-center">
                    <!-- Owner profile picture -->
                    <img src="../uploads/<?= htmlspecialchars($property['owner_profile_pic']) ?>" alt="<?= htmlspecialchars($property['owner_firstname'] . ' ' . $property['owner_lastname']) ?>" class="img-thumbnail owner-image">

                    <!-- Owner details and button on the right -->
                    <div class="owner-info ml-3">
                        <p><strong>Name:</strong> <?= htmlspecialchars($property['owner_firstname'] . ' ' . $property['owner_lastname']) ?></p>
                        <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($property['owner_email']) ?>"><?= htmlspecialchars($property['owner_email']) ?></a></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($property['owner_phone']) ?></p>
                        <!-- Message Host Button -->
                        <button type="button" class="btn btn-primary">Message Host</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
  
    <!-- Sticky Footer for Rent Button -->
    <div class="sticky-footer">
        <form action="property_details.php?id=<?= htmlspecialchars($property['property_id']) ?>" method="POST">
            <input type="hidden" name="action" value="rent">
            <button type="submit" class="btn btn-primary">Rent Now</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
