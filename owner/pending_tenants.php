<?php
// Include your database connection
include '../connection/conn.php';
session_start(); // Start the session

// Check if user is logged in and has the role 'owner'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: ../login.php"); // Redirect to login page if not logged in or not an owner
    exit();
}

echo 'User ID: ' . $_SESSION['user_id'];
echo 'User Role: ' . $_SESSION['user_role'];


$owner_id = $_SESSION['user_id'];

// Fetch pending rental requests for the properties owned by the current owner
$sql = "SELECT r.id, r.tenant_id, r.property_id, r.rent_start_date, p.description, p.price, p.location 
        FROM rentals r
        JOIN properties p ON r.property_id = p.id
        WHERE p.owner_id = ? AND r.status = 'pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Rentals</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .rental-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .rental-item p {
            margin: 0;
        }
        .btn-approve, .btn-reject {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="my-4 d-flex justify-content-between align-items-center">
            <h1>Pending Tenants</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </header>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="rental-item">
                    <p><strong>Property:</strong> <?= htmlspecialchars($row['description']) ?></p>
                    <p><strong>Price:</strong> $<?= number_format($row['price'], 2) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                    <p><strong>Requested Start Date:</strong> <?= htmlspecialchars($row['rent_start_date']) ?></p>
                    
                    <form action="functions/process_rental.php" method="POST">
                        <input type="hidden" name="rental_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <input type="hidden" name="tenant_id" value="<?= htmlspecialchars($row['tenant_id']) ?>">
                        <input type="hidden" name="property_id" value="<?= htmlspecialchars($row['property_id']) ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-approve">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-reject">Reject</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No pending rental requests at the moment.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
