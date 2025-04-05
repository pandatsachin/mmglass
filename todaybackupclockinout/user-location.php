<?php
$ip = $_SERVER['REMOTE_ADDR'];

// Check for local IPs and replace with a public IP for testing
if ($ip == '::1' || $ip == '127.0.0.1') {
    // Use a known public IP for testing
    $ip = '8.8.8.8'; // Google's public DNS IP, used as an example
}

$ch = curl_init();

// Set the URL with the IP
curl_setopt($ch, CURLOPT_URL, "https://api.ipgeolocation.io/ipgeo?apiKey=0f3b6d0f2d974a3799b2c1128270bbcc&ip=".$ip);

// Return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Execute cURL and get the result
$output = curl_exec($ch);

// Print the output
echo '<pre>'; print_r($output);


// Close curl resource
curl_close($ch);
?>
