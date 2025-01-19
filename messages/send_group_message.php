<?php
ob_start(); // Start output buffering
header('Content-Type: application/json');
include '../db_connection.php';

$group_id = $_POST['group_id'];
$sender_id = $_POST['sender_id'];
$message = $_POST['message'];

$query = "INSERT INTO group_messages (group_id, sender_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sis", $group_id, $sender_id, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message sent']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}

$conn->close();
?>
