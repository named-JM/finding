<?php
header('Content-Type: application/json');
include 'db_connection.php';

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$birthDate = $_POST['birthDate'];
$email = $_POST['email'];
$phoneNum = $_POST['phoneNum'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$verification = $_POST['verification'];
$country = $_POST['countries'];
$address = $_POST['address'];
$idPhotoBase64 = $_POST['idPhoto'];
$selfiePhotoBase64 = $_POST['selfiePhoto'];
$receiptPhotoBase64 = $_POST['receiptPhoto'];
$referredBy = $_POST['referred_by'] ?? null; // Optional referral code from the form

// Define upload directory
$uploadDir = '../photo/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filenames for the images
$idPhotoFilename = uniqid('id_', true) . '.jpg';
$selfiePhotoFilename = uniqid('selfie_', true) . '.jpg';
$receiptPhotoFilename = uniqid('receipt_', true) . '.jpg';

// Decode Base64 data and save the files
$idPhotoPath = $uploadDir . $idPhotoFilename;
$selfiePhotoPath = $uploadDir . $selfiePhotoFilename;
$receiptPhotoPath = $uploadDir . $receiptPhotoFilename;

if (
    file_put_contents($idPhotoPath, base64_decode($idPhotoBase64)) === false ||
    file_put_contents($selfiePhotoPath, base64_decode($selfiePhotoBase64)) === false ||
    file_put_contents($receiptPhotoPath, base64_decode($receiptPhotoBase64)) === false
) {
    echo json_encode(["success" => false, "message" => "Error saving images."]);
    exit;
}

// Generate a unique referral code
$referralCode = substr(md5(uniqid($email, true)), 0, 8); // Create an 8-character unique code

// Save data to the database
$sql = "INSERT INTO user_account (fname, lname, bdate, email, phone_num, password, gov_id, country, address, id_photo, selfie_photo, receipt_photo, referral_code, referred_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'ssssssssssssss',
    $firstName,
    $lastName,
    $birthDate,
    $email,
    $phoneNum,
    $password,
    $verification,
    $country,
    $address,
    $idPhotoFilename,
    $selfiePhotoFilename,
    $receiptPhotoFilename,
    $referralCode,
    $referredBy
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User data saved successfully.", "referralCode" => $referralCode]);
} else {
    echo json_encode(["success" => false, "message" => "Error saving user data to the database."]);
}

// Close connections
$stmt->close();
$conn->close();
?>
