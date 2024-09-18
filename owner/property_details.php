<?php
include '../connection/conn.php';

// Get the property ID from the query string
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($property_id > 0) {
    // Fetch the property details from the database
    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();
    $stmt->close();

    // If property not found, redirect or show an error message
    if (!$property) {
        echo "Property not found.";
        exit();
    }
} else {
    echo "Invalid property ID.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details</title>
    <link rel="stylesheet" href="../assets/css/property.css">
</head>
<body>
    <h1>Property Details</h1>
    <div class="property-details">
        <h2><?php echo htmlspecialchars($property['type']); ?></h2>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($property['description']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
        <p><strong>Price:</strong> $<?php echo htmlspecialchars($property['price']); ?></p>
        <p><strong>Image:</strong></p>
        <img src="../<?php echo htmlspecialchars($property['file_path']); ?>" alt="Property Image" style="max-width: 100%; height: auto;">
    </div>
    <a href="index.php" class="btn btn-secondary">Back to List</a>
</body>
</html>
