
<?php
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['u_id'])) {
    echo json_encode([
        'status' => 'success',
        'u_id' => $_SESSION['u_id'],
        'email' => $_SESSION['email']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No active session']);
}
?>
