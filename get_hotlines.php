<?php
// include 'db_connection.php';

// $filename = 'police_mobile_numbers.csv'; // Path to your CSV file
// $file = fopen($filename, "r");

// if ($file !== false) {
//     // Skip the first row (headers)
//     fgetcsv($file);
    
//     while (($row = fgetcsv($file)) !== false) {
//         // Check if the row has the expected number of columns (3)
//         if (count($row) == 3) {
//             // Prepare and bind the SQL statement
//             $stmt = $conn->prepare("INSERT INTO hotlines (service_name, city, contact_number) VALUES (?, ?, ?)");
//             $stmt->bind_param("sss", $service_name, $city, $contact_number);
            
//             // Assign the values from the CSV to the parameters
//             $service_name = $row[0];
//             $city = $row[1];
//             $contact_number = $row[2];
            
//             // Execute the statement
//             if ($stmt->execute()) {
//                 echo "Data successfully imported: " . $service_name . " - " . $city . " - " . $contact_number . "\n";
//             } else {
//                 echo "Error importing data: " . $stmt->error . "\n";
//             }
            
//             // Close the statement
//             $stmt->close();
//         } else {
//             // Log the skipped row for debugging
//             echo "Skipping row due to insufficient data: " . implode(", ", $row) . "\n";
//         }
//     }
//     fclose($file);
// } else {
//     echo "Error opening file.";
// }

// $conn->close();


include 'db_connection.php';

// Get search term from request
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modify SQL to query based on your CSV-imported columns
$sql = "SELECT service_name, city, contact_number FROM hotlines WHERE service_name LIKE '%$searchTerm%' OR city LIKE '%$searchTerm%'";
$result = $conn->query($sql);

$hotlines = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hotlines[] = $row;
    }
}

// Return the result as a JSON response for Flutter
echo json_encode($hotlines);

$conn->close();



?>
