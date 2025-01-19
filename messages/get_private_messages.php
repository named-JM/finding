<?php
header('Content-Type: application/json');
include '../db_connection.php';

$u_id = $_POST['u_id']; // sender's ID
$receiver_id = $_POST['receiver_id']; // receiver's ID for private chat

// SQL query for private messages between sender and receiver
$sql = "SELECT 
            messages.*,
            user_account.fname AS sender_fname,
            user_account.lname AS sender_lname
        FROM messages
        LEFT JOIN user_account ON messages.sender_id = user_account.u_id
        WHERE 
            (sender_id = '$u_id' AND receiver_id = '$receiver_id') 
            OR 
            (sender_id = '$receiver_id' AND receiver_id = '$u_id')
        ORDER BY timestamp DESC";

$result = $conn->query($sql);
$messages = array();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
