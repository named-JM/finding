<?php
header('Content-Type: application/json');

include 'db_connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT category_name FROM business_categories";
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Only push the category_name to the array
        $categories[] = $row['category_name'];
    }
}

echo json_encode(['categories' => $categories]);

$conn->close();
?>
