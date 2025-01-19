<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

// Check if userId is passed in the POST request
$userId = $_POST['userId'] ?? '';

if (empty($userId)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit();
}

try {
    // Query to check if a business profile exists for the given user ID
    $stmt = $conn->prepare("SELECT * FROM business WHERE u_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Business profile exists']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No business profile found']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred', 'error' => $e->getMessage()]);
}

$conn->close();
?>
