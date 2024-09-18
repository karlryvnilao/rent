<?php
include '../../connection/conn.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'tenant') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['payment_id'];
    $amount = $_POST['amount'];
    
    // Fetch the payment record
    $stmt = $conn->prepare("SELECT amount_due, amount_paid FROM payments WHERE id = ? AND tenant_id = ?");
    $stmt->bind_param("ii", $payment_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
        $new_amount_paid = $payment['amount_paid'] + $amount;

        // Check if full payment is made
        if ($new_amount_paid >= $payment['amount_due']) {
            $status = 'paid';
            $new_amount_paid = $payment['amount_due']; // Cap the paid amount to the due amount
        } else {
            $status = 'pending';
        }

        // Update the payment record
        $update_stmt = $conn->prepare("UPDATE payments SET amount_paid = ?, status = ?, payment_date = NOW() WHERE id = ?");
        $update_stmt->bind_param("dsi", $new_amount_paid, $status, $payment_id);
        $update_stmt->execute();

        $_SESSION['message'] = 'Payment processed successfully.';
    } else {
        $_SESSION['error'] = 'Payment not found or you are not authorized.';
    }
}

header("Location: tenant_dashboard.php");
exit();
?>
