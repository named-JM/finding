<?php
$servername = "localhost";
$username = "aguabaras";
$password = "09204353341_account";
$dbname = "aguabaras_finding_app_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>