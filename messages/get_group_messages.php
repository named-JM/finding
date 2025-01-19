<?php
header('Content-Type: application/json');

include '../db_connection.php';

$group_id = $_POST['group_id'];

$query = "SELECT m.message, m.created_at, u.fname, u.lname, m.sender_id
        FROM group_messages m
        JOIN user_account u ON m.sender_id = u.u_id
        WHERE m.group_id = ?
        ORDER BY m.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $group_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode(['success' => true, 'messages' => $messages]);

$conn->close();
?>
