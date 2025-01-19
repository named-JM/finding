<?php
header('Content-Type: application/json');
include '../db_connection.php';

$u_id = $_POST['u_id']; // User ID of the current user

$sql = "SELECT u_id, fname, lname, email
        FROM user_account
        WHERE u_id IN (SELECT DISTINCT referred_by FROM user_account WHERE referred_by IS NOT NULL)";
$stmt = $conn->prepare($sql);
$stmt->execute();

$result = $stmt->get_result();
$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['success' => true, 'users' => $users]);
?>
