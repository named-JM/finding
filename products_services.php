<?php
// Include database configuration and session handling
include 'db_connecton.php'; // Database connection file
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the user is logged in
    if (!isset($_SESSION['u_id']) || !isset($_SESSION['b_id'])) {
        echo json_encode(['success' => false, 'message' => 'User is not logged in']);
        exit;
    }

    // Get the logged-in user and business ID
    $u_id = $_SESSION['u_id']; // Assuming user ID is stored in session
    $b_id = $_SESSION['b_id']; // Assuming business ID is stored in session

    // Get product/service details from the POST request
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

    // Validate inputs
    if (empty($name) || empty($description) || !isset($_FILES['image'])) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields and select an image.']);
        exit;
    }

    // Process the uploaded image
    $image = $_FILES['image'];
    $targetDir = "uploads/";
    $imagePath = $targetDir . basename($image['name']);
    $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

    // Validate image file type
    $validTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $validTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image file type.']);
        exit;
    }

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($image['tmp_name'], $imagePath)) {
        // Insert product/service into the database
        $query = "INSERT INTO products_services (b_id, u_id, name, description, price, image) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("iissds", $b_id, $u_id, $name, $description, $price, $imagePath);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Product/Service added successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add product/service.']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare database query.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
