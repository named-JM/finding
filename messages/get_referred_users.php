<?php
header('Content-Type: application/json');
include '../db_connection.php';

// Get the logged-in user's ID from the POST data
$userId = $_POST['u_id'];

try {
    // Fetch the referral code of the logged-in user
    $referralCodeQuery = "SELECT referral_code FROM user_account WHERE u_id = ?";
    $stmt = $conn->prepare($referralCodeQuery);
    $stmt->bind_param('s', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(["success" => false, "message" => "User not found."]);
        exit;
    }

    $userReferralCode = $user['referral_code'];

    // Fetch all users referred by this user's referral code
    $connectionsQuery = "SELECT u_id, fname, lname, email 
                        FROM user_account
                        WHERE referred_by = ?";
    $stmt = $conn->prepare($connectionsQuery);
    $stmt->bind_param('s', $userReferralCode);
    $stmt->execute();
    $result = $stmt->get_result();

    $connections = [];
    while ($row = $result->fetch_assoc()) {
        $connections[] = [
            'u_id' => $row['u_id'],
            'fname' => $row['fname'],
            'lname' => $row['lname'],
            'email' => $row['email']
        ];
    }

    echo json_encode([
        "success" => true,
        "connections" => $connections
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching connections: " . $e->getMessage()
    ]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
