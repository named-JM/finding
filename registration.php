<?php

header('Content-Type: application/json');
include 'db_connection.php';

if (isset($_POST['fname'], $_POST['lname'], $_POST['bdate'], $_POST['email'], $_POST['password'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $bdate = $_POST['bdate'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $bdate)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid date format']);
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM user_account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO user_account (fname, lname, bdate, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fname, $lname, $bdate, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error occurred during registration']);
        }
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}

$conn->close();
?>
