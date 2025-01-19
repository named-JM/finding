<?php
header('Content-Type: application/json');
include '../db_connection.php';

$sender_id = $_POST['sender_id'];
$receiver_id = $_POST['receiver_id']; // This will be 'all' or the receiver's ID
$message = $_POST['message'];

// Check if an image file is uploaded
$image_url = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/messages/'; // Directory for image uploads
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = basename($_FILES['image']['name']);
    $target_file = $upload_dir . uniqid() . "_" . $file_name;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_url = str_replace('../', '', $target_file); // Adjust the URL path
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload image']);
        exit;
    }
}

// Prepare the SQL query
$sql = "INSERT INTO messages (sender_id, receiver_id, message, images, timestamp) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $sender_id, $receiver_id, $message, $image_url);

$response = array();
if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Message sent successfully';
    $response['images'] = $image_url; // Return the uploaded image URL
} else {
    $response['status'] = 'error';
    $response['message'] = $stmt->error;
}

echo json_encode($response);
?>
