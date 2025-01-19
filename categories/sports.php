<?php
// Start output buffering to prevent extra output
ob_start();

// Set the correct content type
header('Content-Type: application/json');

// Include the database connection
include '../db_connection.php';
include 'config.php';

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
    $bcategory = 'Sports ⚽';
    $stmt = $conn->prepare("SELECT bref, bname, bnumber, bemail, baddress, logo FROM business WHERE bcategory = ?");
    $stmt->bind_param("s", $bcategory);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        foreach ($data as &$business) {
            if (!empty($business['logo'])) {
                $business['logo'] = BASE_URL . $business['logo'];
            }
        }


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
