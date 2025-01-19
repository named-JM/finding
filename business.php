<?php
// header('Content-Type: application/json');
// include 'db_connection.php';

// // Ensure the user's email is passed in the POST request
// $email = $_POST['email'] ?? ''; // Use the email passed from the Flutter app

// if (empty($email)) {
//     echo json_encode(['status' => 'error', 'message' => 'Email is required']);
//     exit();
// }

// // Fetch user ID based on the email
// $stmt = $conn->prepare("SELECT u_id FROM user_account WHERE email = ?");
// $stmt->bind_param("s", $email);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     $user = $result->fetch_assoc();
//     $u_id = $user['u_id']; // Get the user ID from the query result
// } else {
//     echo json_encode(['status' => 'error', 'message' => 'User not found']);
//     exit();
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $bname = $_POST['bname'] ?? '';
//     $bnumber = $_POST['bnumber'] ?? '';
//     $baddress = $_POST['baddress'] ?? '';
//     $bcategory = $_POST['bcategory'] ?? '';
//     $bemail = $_POST['bemail'] ?? ''; // New business email field

//     if (empty($bname) || empty($bnumber) || empty($baddress) || empty($bcategory) || empty($bemail)) {
//         echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
//         exit();
//     }

//     if (!filter_var($bemail, FILTER_VALIDATE_EMAIL)) {
//         echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
//         exit();
//     }

//     $bref = uniqid();
//     $logoPath = '';

//     // Handle logo file upload
//     if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
//         $logoTempPath = $_FILES['logo']['tmp_name'];
//         $logoName = uniqid() . '_' . basename($_FILES['logo']['name']);
//         $logoDestination = 'uploads/' . $logoName;

//         // Ensure the upload directory exists
//         if (!is_dir('uploads/')) {
//             mkdir('uploads/', 0777, true);
//         }

//         // Validate the file type and size before moving the file
//         $allowedFileTypes = ['image/jpeg', 'image/png'];
//         $fileType = mime_content_type($logoTempPath);
//         $fileSize = $_FILES['logo']['size'];

//         if (!in_array($fileType, $allowedFileTypes) || $fileSize > 500000) { // 500KB limit
//             echo json_encode(['status' => 'error', 'message' => 'Invalid file type or size']);
//             exit();
//         }

//         // Try moving the uploaded file
//         if (move_uploaded_file($logoTempPath, $logoDestination)) {
//             $logoPath = $logoDestination; // Save the file path to the database
//         } else {
//             echo json_encode(['status' => 'error', 'message' => 'Failed to upload logo']);
//             exit();
//         }
//     } else {
//         echo json_encode(['status' => 'error', 'message' => 'No logo uploaded or upload error']);
//         exit();
//     }

//     // Insert query includes u_id, logo, and bemail
//     $stmt = $conn->prepare("INSERT INTO business (bref, bname, bnumber, baddress, bcategory, bemail, logo, u_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
//     $stmt->bind_param("sssssssi", $bref, $bname, $bnumber, $baddress, $bcategory, $bemail, $logoPath, $u_id);

//     if ($stmt->execute()) {
//         echo json_encode(['status' => 'success', 'bref' => $bref]);
//     } else {
//         echo json_encode(['status' => 'error', 'message' => 'Failed to save data', 'error' => $stmt->error]);
//     }

//     $stmt->close();
// } else {
//     echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
// }

// $conn->close();

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bname = $_POST['bname'] ?? '';
    $bnumber = $_POST['bnumber'] ?? '';
    $baddress = $_POST['baddress'] ?? '';
    $bcategory = $_POST['bcategory'] ?? '';
    $bemail = $_POST['bemail'] ?? ''; // New business email field

    if (empty($bname) || empty($bnumber) || empty($baddress) || empty($bcategory) || empty($bemail)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        exit();
    }

    if (!filter_var($bemail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    }

    $bref = uniqid();
    $logoPath = '';
    $documentPath = '';
    $permitPath = '';

    // Function to handle file upload
    function uploadFile($fileKey, $allowedFileTypes, $maxSize, $uploadDir) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $tempPath = $_FILES[$fileKey]['tmp_name'];
            $fileName = uniqid() . '_' . basename($_FILES[$fileKey]['name']);
            $destination = $uploadDir . $fileName;

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileType = mime_content_type($tempPath);
            $fileSize = $_FILES[$fileKey]['size'];

            if (!in_array($fileType, $allowedFileTypes) || $fileSize > $maxSize) {
                return ['error' => 'Invalid file type or size'];
            }

            if (move_uploaded_file($tempPath, $destination)) {
                return ['path' => $destination];
            } else {
                return ['error' => 'Failed to upload file'];
            }
        }
        return ['error' => 'No file uploaded or upload error'];
    }

    // Allowed file types and size
    $allowedFileTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    $maxSize = 500000; // 500KB
    $uploadDir = 'business_documents/';

    // Upload logo
    $logoResult = uploadFile('logo', $allowedFileTypes, $maxSize, $uploadDir);
    if (isset($logoResult['error'])) {
        echo json_encode(['status' => 'error', 'message' => $logoResult['error']]);
        exit();
    }
    $logoPath = $logoResult['path'];

    // Upload document
    $documentResult = uploadFile('document', $allowedFileTypes, $maxSize, $uploadDir);
    if (isset($documentResult['error'])) {
        echo json_encode(['status' => 'error', 'message' => $documentResult['error']]);
        exit();
    }
    $documentPath = $documentResult['path'];

    // Upload permit
    $permitResult = uploadFile('permit', $allowedFileTypes, $maxSize, $uploadDir);
    if (isset($permitResult['error'])) {
        echo json_encode(['status' => 'error', 'message' => $permitResult['error']]);
        exit();
    }
    $permitPath = $permitResult['path'];

    // Insert query includes u_id, logo, document, and permit
    $stmt = $conn->prepare("INSERT INTO business (bref, bname, bnumber, baddress, bcategory, bemail, logo, document, permit, u_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssi", $bref, $bname, $bnumber, $baddress, $bcategory, $bemail, $logoPath, $documentPath, $permitPath, $u_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'bref' => $bref]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save data', 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();

?>
