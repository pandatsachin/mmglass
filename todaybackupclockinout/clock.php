<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // Clock in or out
    $photo = $_POST['photo']; // Base64 image data

    // Simulate storing clock-in/out status in a session or file (this could be a database in real-world)
    if (!isset($_SESSION['clock_status'])) {
        $_SESSION['clock_status'] = [];
    }

    // Store photo (as Base64 string or write to file)
    $photoPath = "photos/job_{$action}_" . time() . ".png";
    $imgData = str_replace('data:image/png;base64,', '', $photo);
    $imgData = str_replace(' ', '+', $imgData);
    file_put_contents($photoPath, base64_decode($imgData));

    // Update the session with the clock-in/out status
    if ($action === 'in') {
        $_SESSION['clock_status'] = 'in';
        echo "You have successfully clocked in.";
    } elseif ($action === 'out') {
        $_SESSION['clock_status'] = 'out';
        echo "You have successfully clocked out.";
    }
}
?>
