<?php
header('Content-Type: application/json');
include '../db_connection.php';

// Retrieve user ID from the POST request
$u_id = $_POST['u_id'];

// Modify the query to filter only "all chat" messages
$sql = "SELECT 
            messages.*,
            user_account.fname AS sender_fname,
            user_account.lname AS sender_lname
        FROM messages
        LEFT JOIN user_account ON messages.sender_id = user_account.u_id
        WHERE (receiver_id = 'all')
        ORDER BY timestamp DESC";

$result = $conn->query($sql);

$messages = array();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Return messages as JSON
echo json_encode($messages);
?>
