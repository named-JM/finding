<?php
header('Content-Type: application/json');
include '../db_connection.php';

$referralCode = $_POST['referral_code']; // Passed by the app

$sql = "SELECT id, fname, lname FROM user_account WHERE referred_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $referralCode);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

if (count($members) > 0) {
    echo json_encode(["success" => true, "members" => $members]);
} else {
    echo json_encode(["success" => false, "message" => "No members found."]);
}

$stmt->close();
$conn->close();
?>
