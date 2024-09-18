<?php

include '../conn/config.php';
session_start();


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    // Validate and sanitize the form data
    $businessName = mysqli_real_escape_string($conn, $_POST['business_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $landmark = mysqli_real_escape_string($conn, $_POST['landmark']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $gmapLink = mysqli_real_escape_string($conn, $_POST['gmap_link']);

    $licenseFilePath = NULL; 
    $bhImgFilePath = NULL; 

    if (isset($_FILES['license']) && $_FILES['license']['error'] === UPLOAD_ERR_OK) {
        // Process the license image upload
        $uploadDir = "License/";
        $licenseFileName = basename($_FILES['license']['name']);
        $licenseFilePath = $uploadDir . $licenseFileName;
        move_uploaded_file($_FILES['license']['tmp_name'], $licenseFilePath);
    }

    if (isset($_FILES['bh_img']) && $_FILES['bh_img']['error'] === UPLOAD_ERR_OK) {
        // Process the boarding house image upload
        $uploadDir = "../Landlord/Houses/"; 
        $bhImgFileName = basename($_FILES['bh_img']['name']);
        $bhImgFilePath = $uploadDir . $bhImgFileName;
        move_uploaded_file($_FILES['bh_img']['tmp_name'], $bhImgFilePath);
    }

    // Insert the data into bh_info table
    $insertQuery = "INSERT INTO bh_info (landlord_id, business_name, location, landmark, description, gmap_link, license, bh_img)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "isssssss", $userId, $businessName, $location, $landmark, $description, $gmapLink, $licenseFilePath, $bhImgFilePath);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Set a success message to display 
    $_SESSION['success-message'] = "Boarding House information added successfully.";
    
    // Redirect to the same page to prevent form resubmission
    header("Location: ../Landlord/pg_bh.php");
    exit();
}
?>