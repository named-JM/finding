<?php
// Start output buffering to prevent extra output
ob_start();

// Set the correct content type
header('Content-Type: application/json');

// Include the database connection and configuration
include '../db_connection.php';
include 'config.php'; // Include the configuration file

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
    $bcategory = 'Clothing 👗';  // Example category
    // Modify the SQL query to fetch additional fields
    $stmt = $conn->prepare("SELECT bref, bname, bnumber, bemail, baddress, logo, btagline, bdescription, bproducts_services FROM business WHERE bcategory = ?");
    $stmt->bind_param("s", $bcategory);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        // Use BASE_URL from the configuration file
        foreach ($data as &$business) {
            // Make sure the logo URL is absolute
            if (!empty($business['logo'])) {
                $business['logo'] = BASE_URL . $business['logo'];
            }
        }

        // Return the response with the additional fields
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
