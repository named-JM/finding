<?php
// Start output buffering to prevent extra output
ob_start();

// Set the correct content type
header('Content-Type: application/json');

// Include the database connection
include 'db_connection.php';

// Check for database connection errors
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    ob_end_flush();
    exit();
}

// Ensure no debug output is sent before JSON
ob_clean(); // Clear any output before this point

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $bcategory = 'Food & Beverage 🍏';
    $stmt = $conn->prepare("SELECT bref, bname, bnumber, baddress FROM business WHERE bcategory = ?");
    $stmt->bind_param("s", $bcategory);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();


ob_end_flush();
?>