<?php
include '../connection/conn.php';

// Start session to retrieve owner_id
session_start();
$owner_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if the form is submitted and if the user is logged in
if ($_SERVER["REQUEST_METHOD"] == "POST" && $owner_id !== null) {
    // Retrieve form data
    $type = $_POST['property_type']; // 'House' or 'Room'
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $file = $_FILES['file']; // Uploaded file

    // File upload handling
    $target_dir = "img/"; // Directory to save the uploaded files
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;

    // Check if file is a valid image
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");

    // Check file type
    if (in_array($imageFileType, $allowedExtensions)) {
        // Attempt to move the uploaded file to the target directory
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($file["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
            $uploadOk = 0;
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // If file upload is successful, insert data into the database
    if ($uploadOk) {
        // Prepare and execute the database insert statement
        $stmt = $conn->prepare("INSERT INTO properties (type, description, location, price, file_path, owner_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdsi", $type, $description, $location, $price, $target_file, $owner_id);

        if ($stmt->execute()) {
            echo "New property added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch properties if owner_id is set
$houses = [];
$rooms = [];

if ($owner_id !== null) {
    // Fetch houses
    // Fetch houses
    $stmt = $conn->prepare("SELECT * FROM properties WHERE owner_id = ? AND type = 'House'");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $houses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch rooms
    $stmt = $conn->prepare("SELECT * FROM properties WHERE owner_id = ? AND type = 'Room'");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rooms = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
    <link rel="stylesheet" href="../assets/css/property.css">
    <link rel="stylesheet" href="../assets/css/apartment.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .property-item {
    display: flex;
    flex-direction: row; /* Default for larger screens */
    align-items: center;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    padding: 10px;
}

.property-details {
    flex: 1;
    padding-right: 10px;
}

.property-image {
    width: 200px; /* Adjust the width as needed */
    flex-shrink: 0;
}

.property-image img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .property-item {
        flex-direction: column; /* Stack details and image vertically on smaller screens */
        text-align: center;
    }

    .property-details {
        padding-right: 0;
        margin-bottom: 10px; /* Add space between details and image */
    }

    .property-image {
        width: 100%; /* Make image full width on smaller screens */
    }
}

    </style>
</head>
<body>
    

    <!-- Property Container -->
    <div class="container">
    <!-- House Column -->
    <div class="property-column" id="houses">
        <h3>Houses</h3>
        <?php if (count($houses) > 0): ?>
            <?php foreach ($houses as $index => $house): ?>
                <div class="property-item" id="house-<?php echo $index; ?>">
                    <div class="property-image">
                        <img src="<?php echo $house['file_path']; ?>" alt="House Image">
                    </div>
                    <div class="property-details">
                        <p>Type: <?php echo $house['type']; ?></p>
                        <p>Description: <?php echo $house['description']; ?></p>
                        <p>Location: <?php echo $house['location']; ?></p>
                        <p>Price: <?php echo $house['price']; ?></p>
                        <p>Status: <?php echo $house['available'] == 1 ? 'Available' : 'Occupied'; ?></p>
                        <a href="property_details.php?id=<?php echo $house['id']; ?>" class="btn btn-link">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No houses available.</p>
        <?php endif; ?>
    </div>

    <!-- Room Column -->
    <div class="property-column" id="rooms">
        <h3>Rooms</h3>
        <?php if (count($rooms) > 0): ?>
            <?php foreach ($rooms as $index => $room): ?>
                <div class="property-item" id="room-<?php echo $index; ?>">
                    <div class="property-image">
                        <img src="<?php echo $room['file_path']; ?>" alt="Room Image">
                    </div>
                    <div class="property-details">
                        <p>Type: <?php echo $room['type']; ?></p>
                        <p>Description: <?php echo $room['description']; ?></p>
                        <p>Location: <?php echo $room['location']; ?></p>
                        <p>Price: <?php echo $room['price']; ?></p>
                        <p>Status: <?php echo $room['available'] == 1 ? 'Available' : 'Occupied'; ?></p>
                        <a href="property_details.php?id=<?php echo $room['id']; ?>" class="btn btn-link">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No rooms available.</p>
        <?php endif; ?>
    </div>


    </div>

    <div class="d-flex justify-content-start mb-3">
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-primary" id="openModalBtn" data-toggle="modal" data-target="#propertyModal">Upload Property</button>
        <!-- Back Button -->
        <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="propertyModal" tabindex="-1" role="dialog" aria-labelledby="propertyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="propertyModalLabel">Upload Property</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form for uploading property -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="property_type">Property Type</label>
                            <select class="form-control" id="property_type" name="property_type" required>
                                <option value="House">House</option>
                                <option value="Room">Room</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="file">Upload Image</label>
                            <input type="file" class="form-control-file" id="file" name="file" accept=".jpg,.jpeg,.png,.gif" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../assets/js/property.js"></script>
</body>
</html>
