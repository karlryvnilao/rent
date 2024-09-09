<?php
include '../connection/conn.php'; 
session_start(); 

// Check if user is logged in and has the role 'owner'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'owner') {
    header("Location: ../login.php");
    exit();
}

echo 'User ID: ' . $_SESSION['user_id'];
echo 'User Role: ' . $_SESSION['user_role'];

$owner_id = $_SESSION['user_id'];

// Handle payment logging
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenant_id = $_POST['tenant_id'];
    $amount = $_POST['amount'];
    $payment_date = date('Y-m-d');
    $due_date = $_POST['due_date'];

    // Insert payment details into the payments table
    $stmt = $conn->prepare("INSERT INTO payments (tenant_id, owner_id, amount, payment_date, due_date, status) VALUES (?, ?, ?, ?, ?, 'paid')");
    if ($stmt) {
        $stmt->bind_param("iidss", $tenant_id, $owner_id, $amount, $payment_date, $due_date);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?payment=success"); // Redirect or show success message
        exit();
    } else {
        echo "Error: " . $conn->error; // Error handling if prepare fails
    }
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
        
        <form method="post" action="">
            <div class="form-group">
                <label for="tenant_id">Tenant</label>
                <select class="form-control" id="tenant_id" name="tenant_id" required>
                    <!-- Fetch tenants associated with the owner's properties -->
                    <?php
                    $stmt = $conn->prepare("
                        SELECT DISTINCT u.id, u.username, p.type, p.location
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
                            echo '<option value="' . htmlspecialchars($row['id']) . '">';
                            echo htmlspecialchars($row['username']) . ' - ';
                            echo htmlspecialchars($row['type']) . ', ';
                            echo htmlspecialchars($row['location']);
                            echo '</option>';
                        }
                    } else {
                        echo '<option disabled>No tenants found</option>';
                    }
                    $stmt->close();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Pay</button>
        </form>
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


