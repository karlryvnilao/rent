<?php
include '../connection/conn.php'; 
session_start(); 

// Check if user is logged in and has the role 'tenant'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tenant') {
    header("Location: ../login.php");
    exit();
}

$tenant_id = $_SESSION['user_id'];
echo 'Tenant ID: ' . htmlspecialchars($tenant_id); // Debug output

try {
    // Fetch payment details and associated property information for the logged-in tenant
    $stmt = $conn->prepare("
        SELECT p.amount, p.payment_date, p.status, pr.type 
        FROM payments p
        JOIN rentals r ON p.tenant_id = r.tenant_id
        JOIN properties pr ON r.property_id = pr.id
        WHERE p.tenant_id = ?
    ");
    $stmt->bind_param("i", $tenant_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $payments = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo 'Error executing query: ' . $stmt->error;
        $payments = [];
    }

    $stmt->close();
} catch (Exception $e) {
    // Handle database connection error
    echo 'Database error: ' . $e->getMessage();
    exit();
}

$conn->close();

// Check if any payments are due
$due_payments = array_filter($payments, function($payment) {
    return $payment['status'] == 'pending' && isset($payment['due_date']) && $payment['due_date'] > date('Y-m-d');
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="my-4 d-flex justify-content-between align-items-center">
            <h1>Payment Details</h1>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($payments) > 0): ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('F j, Y', strtotime($payment['payment_date']))); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($payment['status'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($payment['type'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No payments found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Toastr Notifications -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            <?php if (count($due_payments) > 0): ?>
                toastr.warning('You have upcoming due payments. Please check your payment details.');
            <?php endif; ?>
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
