<?php


// header('Content-Type: application/json');

// // Database connection
// include '../db_connection.php';

// $referred_by = $_POST['referred_by'];



// // Step 1: Check if a group exists for the referred_by
// $query = "SELECT group_id FROM referral_groups WHERE referred_by = ?";
// $stmt = $conn->prepare($query);
// $stmt->bind_param("s", $referred_by);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     // Group exists, return the group_id
//     $row = $result->fetch_assoc();
//     echo json_encode(['success' => true, 'group_id' => $row['group_id']]);
// } else {
//     // Step 2: Check if the current user is the origin of the referral_code
//     $queryOrigin = "SELECT referral_code FROM user_account WHERE referral_code = ?";
//     $stmtOrigin = $conn->prepare($queryOrigin);
//     $stmtOrigin->bind_param("s", $referred_by);
//     $stmtOrigin->execute();
//     $resultOrigin = $stmtOrigin->get_result();

//     if ($resultOrigin->num_rows > 0) {
//         // User is the origin, create or fetch group for their referral_code
//         $group_id = uniqid('group_');
//         $insertQuery = "INSERT INTO referral_groups (group_id, referred_by) VALUES (?, ?)";
//         $stmtInsert = $conn->prepare($insertQuery);
//         $stmtInsert->bind_param("ss", $group_id, $referred_by);

//         if ($stmtInsert->execute()) {
//             echo json_encode(['success' => true, 'group_id' => $group_id]);
//         } else {
//             echo json_encode(['success' => false, 'message' => 'Failed to create group']);
//         }
//     } else {
//         // Step 3: Fetch the origin's group (if exists) using referred_by chain
//         $queryReferralChain = "
//             SELECT g.group_id 
//             FROM referral_groups g
//             JOIN user_account u ON g.referred_by = u.referral_code
//             WHERE u.referral_code = ?
//         ";
//         $stmtChain = $conn->prepare($queryReferralChain);
//         $stmtChain->bind_param("s", $referred_by);
//         $stmtChain->execute();
//         $resultChain = $stmtChain->get_result();

//         if ($resultChain->num_rows > 0) {
//             $row = $resultChain->fetch_assoc();
//             echo json_encode(['success' => true, 'group_id' => $row['group_id']]);
//         } else {
//             // Create a new group as a last resort
//             $group_id = uniqid('group_');
//             $insertQuery = "INSERT INTO referral_groups (group_id, referred_by) VALUES (?, ?)";
//             $stmtInsertNew = $conn->prepare($insertQuery);
//             $stmtInsertNew->bind_param("ss", $group_id, $referred_by);

//             if ($stmtInsertNew->execute()) {
//                 echo json_encode(['success' => true, 'group_id' => $group_id]);
//             } else {
//                 echo json_encode(['success' => false, 'message' => 'Failed to create group']);
//             }
//         }
//     }
// }




// $conn->close();




header('Content-Type: application/json');

// Database connection
include '../db_connection.php';

$referred_by = $_POST['referred_by'];

$query = "SELECT group_id FROM referral_groups WHERE referred_by = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $referred_by);
$stmt->execute();
$result = $stmt->get_result();


// this is to check if he/she has group na exists and else it will create new group auto
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'group_id' => $row['group_id']]);
} else {
    $group_id = uniqid('group_');
    $insertQuery = "INSERT INTO referral_groups (group_id, referred_by) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ss", $group_id, $referred_by);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'group_id' => $group_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create group']);
    }
}

$conn->close();
?>
