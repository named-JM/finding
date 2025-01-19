<?php
header('Content-Type: application/json');
error_reporting(0); // Suppress all errors/warnings
ini_set('display_errors', 0);

include 'db_connection.php';

// Ensure a clean start
ob_clean();

// Check database connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// Query the database
$query = "SELECT * FROM business";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $businesses = [];
    while ($row = $result->fetch_assoc()) {
        $businesses[] = [
            'bref' => $row['bref'],
            'bname' => $row['bname'],
            'bnumber' => $row['bnumber'],
            'baddress' => $row['baddress'],
            'bcategory' => $row['bcategory'],
            'business_line' => $row['business_line'],
        ];
    }
    echo json_encode(['status' => 'success', 'data' => $businesses]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found']);
}

$conn->close();
?>