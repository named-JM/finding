<?php
// header('Content-Type: application/json');
// include '../db_connection.php';

// $u_id = $_POST['u_id'];

// $sql = "SELECT user_account.u_id, user_account.fname, user_account.lname 
//         FROM friends 
//         JOIN user_account ON friends.friend_id = user_account.u_id
//         WHERE friends.user_id = '$u_id' AND friends.status = 'accepted'";

// $result = $conn->query($sql);
// $friends = array();
// while ($row = $result->fetch_assoc()) {
//     $friends[] = $row;
// }

// echo json_encode($friends);

header('Content-Type: application/json');
include '../db_connection.php';

$u_id = $_POST['u_id']; // The user's ID

// Query to fetch the list of users (or friends) who are not the current user
$sql = "SELECT u_id, fname, lname, email FROM user_account WHERE u_id != '$u_id'";
$result = $conn->query($sql);
$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

?>
