<?php
// save_location.php

// Database connection (adjust these values as per your setup)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mmglass";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if latitude and longitude are set
if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Assuming you have a user ID (replace 1 with actual user ID logic)
    $user_id = 1;

    // Insert into database
    $sql = "INSERT INTO user_locations (user_id, latitude, longitude) VALUES ('$user_id', '$latitude', '$longitude')";

    if ($conn->query($sql) === TRUE) {
        echo "Location saved successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Latitude and Longitude not provided.";
}

$conn->close();
?>
