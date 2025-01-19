<?php
header('Content-Type: application/json');
include '../db_connection.php';

$referralCode = $_POST['referral_code']; // Referral code of the logged-in user

$sql = "SELECT u_id, fname, lname, email FROM user_account WHERE referred_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $referralCode);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode(["success" => true, "users" => $users]);
} else {
    echo json_encode(["success" => false, "message" => "Error fetching referred users."]);
}

$stmt->close();
$conn->close();
?>
