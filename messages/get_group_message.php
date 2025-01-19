<?php
header('Content-Type: application/json');
include '../db_connection.php';

$groupId = $_POST['group_id'];

try {
    $query = "SELECT gm.message, gm.timestamp, u.fname AS sender_fname, u.lname AS sender_lname
              FROM group_message gm
              JOIN user_account u ON gm.sender_id = u.u_id
              WHERE gm.group_id = ?
              ORDER BY gm.timestamp DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $groupId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode(["success" => true, "messages" => $messages]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
