<?php
header('Content-Type: application/json');
include 'db_connection.php';

$searchTerm = $_POST['query'] ?? ''; // Search query from frontend
$userId = $_POST['u_id']; // Exclude the current user from search results

// Query to search users
$sql = "SELECT u_id, fname, lname, email FROM user_account 
        WHERE (fname LIKE ? OR lname LIKE ? OR email LIKE ?) 
        AND u_id != ?";
$stmt = $conn->prepare($sql);
$searchLike = '%' . $searchTerm . '%';
$stmt->bind_param('ssss', $searchLike, $searchLike, $searchLike, $userId);

$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(["success" => true, "users" => $users]);

$stmt->close();
$conn->close();
?>
