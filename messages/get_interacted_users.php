<?php
header('Content-Type: application/json');
include '../db_connection.php';

$u_id = $_POST['u_id']; // Current user's ID

// SQL query to fetch users the current user has conversed with
$sql = "SELECT 
            user_account.u_id,
            user_account.fname,
            user_account.lname,
            user_account.email
        FROM messages
        INNER JOIN user_account 
        ON (messages.sender_id = user_account.u_id AND messages.receiver_id = '$u_id')
        OR (messages.receiver_id = user_account.u_id AND messages.sender_id = '$u_id')
        GROUP BY user_account.u_id
        ORDER BY MAX(messages.timestamp) DESC";

$result = $conn->query($sql);
$users = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
error_log("u_id received: " . $u_id);
if (!$result) {
    error_log("SQL Error: " . $conn->error);
} else {
    error_log("Query executed successfully, rows: " . $result->num_rows);
}

echo json_encode(array("success" => true, "users" => $users));
error_log(json_encode(array("success" => true, "users" => $users)));

?>
