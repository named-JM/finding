<?php

// Define upload directory (same as in the main script)
$uploadDir = '../photo/';

// Sanitize filename from the GET parameter to prevent directory traversal
$filename = basename($_GET['filename']);
$filePath = $uploadDir . $filename;

if (file_exists($filePath)) {
    // Serve the image with the appropriate content type
    header('Content-Type: image/jpeg');
    readfile($filePath);
    exit;
} else {
    // Return a 404 if the file is not found
    http_response_code(404);
    echo "Image not found.";
}
?>
