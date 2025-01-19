<?php
header('Content-Type: application/json');
include '../db_connection.php';

$groupId = $_POST['group_id']; // Fetch group_id from request
$userId = $_POST['user_id']; // Fetch user_id from request

if (empty($groupId) || empty($userId)) {
    echo json_encode(["success" => false, "message" => "Missing group_id or user_id."]);
    exit;
}

try {
    // Use the group_id as provided without alteration
    $fetchQuery = "SELECT gm.sender_id, gm.message, gm.created_at, ua.fname, ua.lname 
                FROM group_messages gm 
                JOIN user_account ua ON gm.sender_id = ua.u_id 
                WHERE gm.group_id = ?";

    $stmt = $conn->prepare($fetchQuery);
    $stmt->bind_param('s', $groupId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'sender_id' => $row['sender_id'],
            'message' => $row['message'],
            'created_at' => $row['created_at'],
            'sender_fname' => $row['fname'],
            'sender_lname' => $row['lname'],
        ];
    }

    echo json_encode(["success" => true, "messages" => $messages]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
