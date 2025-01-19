<?php
header('Content-Type: application/json');
include '../db_connection.php';

// Disable error reporting in production
ini_set('display_errors', 0);
error_reporting(0);

// Collect input data
$senderId = $_POST['sender_id'];
$message = $_POST['message'];
$groupId = $_POST['group_id'];
// Insert message into the database with the group_id as referral code
//$groupId = $referralCode; // Fetch referral code for the sender ID


if (empty($senderId) || empty($message) || empty($groupId)) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

try {
    // Insert the group message into the new table
    $insertQuery = "INSERT INTO group_messages (group_id, sender_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('sss', $groupId, $senderId, $message);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Message sent successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to send message."]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error sending message: " . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
