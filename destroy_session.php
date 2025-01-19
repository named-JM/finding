<?php
include 'db_connection.php';
session_start();
session_destroy();
echo json_encode(['status' => 'success', 'message' => 'Logged out']);
?>
