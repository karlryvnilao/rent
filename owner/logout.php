<?php
session_start(); // Start the session

// Check if a session is active
if (isset($_SESSION['user_id'])) {
    // Destroy the session
    session_unset();
    session_destroy();
}

// Redirect to the login page or homepage
header("Location: ../index.php");
exit();
?>
