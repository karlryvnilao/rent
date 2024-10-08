<?php
// Include your database connection
include '../connection/conn.php';
session_start(); // Start the session

// Check if the user is logged in and has the role 'owner'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: ../login.php"); // Redirect to login page if not logged in or not an owner
    exit();
}

$owner_id = $_SESSION['user_id'];

// Fetch approved rental requests for the properties owned by the current owner
$sql = "SELECT r.id, r.tenant_id, r.rent_start_date, p.description, p.price, p.location, u.username, u.email
        FROM rentals r
        JOIN properties p ON r.property_id = p.id
        JOIN users u ON r.tenant_id = u.id
        WHERE p.owner_id = ? AND r.status = 'approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
if (!$stmt->execute()) {
    echo "Error: " . $stmt->error;
    exit();
}
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Tenants</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .tenant-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .tenant-item p {
            margin: 0;
        }
        .btn-view {
            margin-top: 0; /* Remove top margin if needed */
            padding: 5px; /* Adjust padding if needed */
        }
        .collapse {
            margin-top: 10px; /* Add margin to separate collapsible content from button */
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="my-4 d-flex justify-content-between align-items-center">
            <h1>Approved Tenants</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </header>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="tenant-item">
                    <!-- Collapse Button with Username -->
                    <button class="btn btn-primary btn-lg btn-block btn-view" type="button" data-toggle="collapse" data-target="#collapse<?= $row['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['username']) ?>
                    </button>

                    <!-- Collapsible Content -->
                    <div id="collapse<?= $row['id'] ?>" class="collapse">
                        <p><strong>Name:</strong> <?= htmlspecialchars($row['username']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                        <p><strong>Property:</strong> <?= htmlspecialchars($row['description']) ?></p>
                        <p><strong>Price:</strong> $<?= number_format($row['price'], 2) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                        <p><strong>Rent Start Date:</strong> <?= htmlspecialchars($row['rent_start_date']) ?></p>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="<?= $row['id'] ?>">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No approved tenants at the moment.</p>
        <?php endif; ?>

    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-delete').forEach(function (button) {
            button.addEventListener('click', function () {
                var tenantId = this.getAttribute('data-id');
                if (confirm('Are you sure you want to delete this tenant?')) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'functions/delete.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                alert(response.message);
                                location.reload(); // Reload the page to reflect the changes
                            } else {
                                alert('Error: ' + response.message);
                            }
                        } else {
                            alert('Request failed. Please try again.');
                        }
                    };
                    xhr.send('id=' + encodeURIComponent(tenantId));
                }
            });
        });
    });
    </script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
