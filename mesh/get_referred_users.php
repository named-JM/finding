<?php
include('../db_connection.php'); // Include your DB connection

// Fetch the referred users for the logged-in user
$user_referral_code = $_GET['referral_code']; // Get the referral code from the query parameter

$sql = "SELECT u_id, fname, lname, referral_code 
        FROM user_account 
        WHERE referred_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_referral_code);
$stmt->execute();
$result = $stmt->get_result();

$referred_users = [];
while ($row = $result->fetch_assoc()) {
    $referred_users[] = $row;
}
// Before inserting a new referral, check if the user has already referred 5 people
$sql = "SELECT COUNT(*) AS referred_count FROM user_account WHERE referred_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_referral_code);
$stmt->execute();
$stmt->bind_result($referred_count);
$stmt->fetch();

if ($referred_count >= 5) {
    echo json_encode(['error' => 'You can refer a maximum of 5 people']);
} else {
    // Insert the new user as a referred user
}


echo json_encode($referred_users); // Return the referred users as JSON
?>
