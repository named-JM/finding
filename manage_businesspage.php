<?php
header('Content-Type: application/json');
include 'db_connection.php';

// Ensure the user's email is passed in the POST request
$email = $_POST['email'] ?? ''; // Use the email passed from the Flutter app

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit();
}

// Fetch user ID based on the email
$stmt = $conn->prepare("SELECT u_id FROM user_account WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $u_id = $user['u_id']; // Get the user ID from the query result
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gather the new business information
    $btagline = $_POST['btagline'] ?? '';
    $bdescription = $_POST['bdescription'] ?? '';
    $bproducts_services = $_POST['bproducts_services'] ?? '';
    
    // Validation for required fields
    if (empty($btagline) || empty($bdescription) || empty($bproducts_services)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit();
    }

    // Add logic for the logo upload (optional)
    $logoPath = '';

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logoTempPath = $_FILES['logo']['tmp_name'];
        $logoName = uniqid() . '_' . basename($_FILES['logo']['name']);
        $logoDestination = 'uploads/' . $logoName;

        // Ensure the upload directory exists
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0777, true);
        }

        // Validate the file type and size before moving the file
        $allowedFileTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($logoTempPath);
        $fileSize = $_FILES['logo']['size'];

        if (!in_array($fileType, $allowedFileTypes) || $fileSize > 500000) { // 500KB limit
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type or size']);
            exit();
        }

        // Try moving the uploaded file
        if (move_uploaded_file($logoTempPath, $logoDestination)) {
            $logoPath = $logoDestination; // Save the file path to update the database
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload logo']);
            exit();
        }
    }

    // Check if a business entry already exists for the user
    $stmt = $conn->prepare("SELECT b_id FROM business WHERE u_id = ?");
    $stmt->bind_param("i", $u_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If a business entry exists, update it, else insert a new one
    if ($result->num_rows > 0) {
        // Update business data
        $b_id = $result->fetch_assoc()['b_id']; // Get the existing b_id

        if (!empty($logoPath)) {
            // Update business data with the logo
            $stmt = $conn->prepare("UPDATE business SET btagline = ?, bdescription = ?, bproducts_services = ?, logo = ? WHERE b_id = ?");
            $stmt->bind_param("ssssi", $btagline, $bdescription, $bproducts_services, $logoPath, $b_id);
        } else {
            // Update business data without logo
            $stmt = $conn->prepare("UPDATE business SET btagline = ?, bdescription = ?, bproducts_services = ? WHERE b_id = ?");
            $stmt->bind_param("sssi", $btagline, $bdescription, $bproducts_services, $b_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Business updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update business', 'error' => $stmt->error]);
        }
    } else {
        // Insert new business data if no entry exists
        if (!empty($logoPath)) {
            // Insert business data with logo
            $stmt = $conn->prepare("INSERT INTO business (btagline, bdescription, bproducts_services, logo, u_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $btagline, $bdescription, $bproducts_services, $logoPath, $u_id);
        } else {
            // Insert business data without logo
            $stmt = $conn->prepare("INSERT INTO business (btagline, bdescription, bproducts_services, u_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $btagline, $bdescription, $bproducts_services, $u_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Business added successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add business', 'error' => $stmt->error]);
        }
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
