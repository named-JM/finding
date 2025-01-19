<?php
header('Content-Type: application/json');
include 'db_connection.php';

// Retrieve POST data
$email = $_POST['email'] ?? '';
$bname = $_POST['bname'] ?? '';
$bnumber = $_POST['bnumber'] ?? '';
$baddress = $_POST['baddress'] ?? '';
$bcategory = $_POST['bcategory'] ?? '';
$bemail = $_POST['bemail'] ?? '';

// Validate email
if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit();
}

// Fetch user ID
$stmt = $conn->prepare("SELECT u_id FROM user_account WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $u_id = $user['u_id'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

// Handle logo upload if provided
$logoPath = null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $logoName = uniqid('logo_', true) . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $uploadDir = 'uploads/logos/';
    $uploadFile = $uploadDir . $logoName;

    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
        $logoPath = $uploadFile;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload logo']);
        exit();
    }
}

// Prepare update query
$query = "UPDATE business SET bname = ?, bnumber = ?, baddress = ?, bcategory = ?, bemail = ?";
$params = [$bname, $bnumber, $baddress, $bcategory, $bemail];

// Include logo path in query if uploaded
if ($logoPath) {
    $query .= ", logo = ?";
    $params[] = $logoPath;
}
$query .= " WHERE u_id = ?";

$stmt = $conn->prepare($query);
$params[] = $u_id;
$stmt->bind_param(str_repeat("s", count($params) - 1) . "i", ...$params);

// Execute query and send response
if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Business updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update business']);
}

// Close connections
$stmt->close();
$conn->close();
?>
