<?php
include '../connection/conn.php'; 
session_start(); 

// Check if user is logged in and has the role 'owner'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$message = '';

// Handle payment logging
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'log_payment') {
    // Check if required POST parameters are set
    if (isset($_POST['tenant_id'], $_POST['amount'])) {
        $tenant_id = $_POST['tenant_id'];
        $amount = $_POST['amount'];
        $payment_date = date('Y-m-d');

        // Insert payment details into the payments table
        $stmt = $conn->prepare("INSERT INTO payments (tenant_id, owner_id, amount, payment_date, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iids", $tenant_id, $owner_id, $amount, $payment_date, $status);
            $status = 'paid'; // Set status here
            if ($stmt->execute()) {
                $message = "Payment recorded successfully.";
            } else {
                $message = "Error recording payment: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Error preparing statement: " . $conn->error;
        }
    } else {
        $message = "Required form data missing.";
    }
}

// Fetch tenants associated with the owner's properties
$tenants = [];
$stmt = $conn->prepare("
    SELECT DISTINCT u.id, u.username
    FROM users u 
    JOIN rentals r ON u.id = r.tenant_id
    JOIN properties p ON r.property_id = p.id
    WHERE u.role = 'tenant' AND p.owner_id = ? AND r.status = 'approved'
");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tenants[] = $row;
    }
}
$stmt->close();

// Handle tenant selection
$selected_tenant_id = $_POST['tenant_id'] ?? null;
$payments = [];

if ($selected_tenant_id) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE tenant_id = ?");
    $stmt->bind_param("i", $selected_tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
        .sticky-footer {
            position: sticky;
            bottom: 0;
            background-color: #f8f9fa;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-5 content">
        <div class="text-center"><h1>PAYROLL OF TENANTS</h1></div>

        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="action" value="log_payment">
            <div class="form-group">
                <label for="tenant_id">Tenant</label>
                <select class="form-control" id="tenant_id" name="tenant_id" required onchange="this.form.submit()">
                    <option value="">Select a tenant</option>
                    <?php foreach ($tenants as $tenant): ?>
                        <option value="<?php echo htmlspecialchars($tenant['id']); ?>" <?php echo ($selected_tenant_id == $tenant['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tenant['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($selected_tenant_id): ?>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-primary">Pay</button>
            <?php endif; ?>
        </form>

        <?php if ($selected_tenant_id): ?>
            <h2 class="mt-5">Payment History for Tenant</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                <td><?php echo htmlspecialchars($payment['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No payments found for this tenant.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Sticky Footer Section with Back Button -->
    <footer class="sticky-footer">
        <div class="container d-flex justify-content-end">
            <a href="index.php" class="btn btn-secondary">Back</a> <!-- Back button in the sticky footer -->
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
