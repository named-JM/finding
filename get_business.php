<?php
header('Content-Type: application/json');
include 'db_connection.php';

// Ensure the user's email is passed in the POST request
$email = $_POST['email'] ?? ''; // Use the email passed from the Flutter app

if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit();
}

// Fetch user ID based on the email
$stmt = $conn->prepare("SELECT u_id FROM user_account WHERE email = ?");
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $u_id = $user['u_id']; // Get the user ID from the query result
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

// Fetch business details for the logged-in user, including btagline, bdescription, bproducts_services, and other fields
$stmt = $conn->prepare("SELECT bname, bnumber, baddress, bcategory, bemail, logo, btagline, bdescription, bproducts_services 
                        FROM business WHERE u_id = ?");
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Error preparing statement: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $u_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'business' => $business]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Business not found']);
}

$stmt->close();
$conn->close();
?>
