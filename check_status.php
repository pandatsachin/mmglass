<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mmglass";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = 2; // Assume a user_id, you can dynamically set this based on logged-in users

// Fetch the current clock-in/out status
$sql = "SELECT status FROM clock_status WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['status'];  // Send the status (1 for clocked in, 0 for clocked out)
} else {
    // If no record exists for the user, initialize status as clocked out
    echo '0';
}

$conn->close();
?>
