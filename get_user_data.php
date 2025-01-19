<?php
// Fetch user data from the database
header('Access-Control-Allow-Origin: *');

header('Content-Type: application/json');
include 'db_connection.php';

$u_id = $_GET['u_id']; // Get user ID from the URL query parameter

$sql = "SELECT fname, lname, email, referral_code FROM user_account WHERE u_id = ?";
//$sql = "SELECT groupId FROM group_message WHERE u_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $u_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if ($userData) {
  echo json_encode([
    'status' => 'success',
    'data' => $userData
  ]);
} else {
  echo json_encode([
    'status' => 'error',
    'message' => 'User not found'
  ]);
}

$stmt->close();
$conn->close();
?>
