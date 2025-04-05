<?php
session_start();
error_reporting(0);
//ini_set('display_errors', 1);
include("includes/config.php");

$userId = 4; // Assume a user_id, you can dynamically set this based on logged-in users

// Fetch clock-in/clock-out status
$sql = "SELECT status FROM clock_status WHERE user_id = $userId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo $row['status'];  // Send the status (1 for clocked in, 0 for clocked out)
} else {
    echo '0'; // If no record exists for the user, initialize status as clocked out
}

// Handle POST requests for clock-in/clock-out
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // Clock in or out
    $photo = $_POST['photo']; // Base64 image data
    $longitude = $_POST['longitude']; // Longitude from Geolocation API
    $latitude = $_POST['latitude']; // Latitude from Geolocation API
//    $userId = 1; // Assume a user_id, dynamically set this based on logged-in users

    // Generate unique image name
    $imageFileName = "job_{$action}_" . time() . ".png";
    $photoPath = "photos/" . $imageFileName; // Path to store the image
    $imgData = str_replace('data:image/png;base64,', '', $photo);
    $imgData = str_replace(' ', '+', $imgData);

    // Save image to the server
    file_put_contents($photoPath, base64_decode($imgData));

    // Check if the user exists in the database
    $sql_check = "SELECT * FROM clock_status WHERE user_id = $userId";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // User exists, update the record
        if ($action === 'in') {
            // Update the clock-in status, image path, and geolocation data
            $sql_update = "UPDATE clock_status 
                           SET status = 1, clockin_image_path = '$photoPath', 
                               clockin_longitude = '$longitude', clockin_latitude = '$latitude'
                           WHERE user_id = $userId";
            if ($conn->query($sql_update) === TRUE) {
                echo "You have successfully clocked in. Image and location saved.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } elseif ($action === 'out') {
            // Update the clock-out status, image path, and geolocation data
            $sql_update = "UPDATE clock_status 
                           SET status = 0, clockout_image_path = '$photoPath', 
                               clockout_longitude = '$longitude', clockout_latitude = '$latitude'
                           WHERE user_id = $userId";
            if ($conn->query($sql_update) === TRUE) {
                echo "You have successfully clocked out. Image and location saved.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    } else {
        // User doesn't exist, insert a new record
        if ($action === 'in') {
            $sql_insert = "INSERT INTO clock_status (user_id, status, clockin_image_path, clockin_longitude, clockin_latitude) 
                           VALUES ($userId, 1, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "New user clocked in and record created. Image and location saved.";
            } else {
                echo "Error inserting record: " . $conn->error;
            }
        } elseif ($action === 'out') {
            $sql_insert = "INSERT INTO clock_status (user_id, status, clockout_image_path, clockout_longitude, clockout_latitude) 
                           VALUES ($userId, 0, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "New user clocked out and record created. Image and location saved.";
            } else {
                echo "Error inserting record: " . $conn->error;
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock In/Out System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        button { margin: 10px; }
        .inactive { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>
    <h1>Clock In/Out System</h1>

    <!-- Job Buttons -->
    <div id="job">
        <h3>Job</h3>
        <button id="clock-in" class="clock-in">Clock In</button>
        <button id="clock-out" class="clock-out inactive">Clock Out</button>
        <video id="video" width="320" height="240" autoplay></video>
        <canvas id="canvas" style="display:none;"></canvas>
        <img id="photo" alt="Captured Photo" style="display:none;">
    </div>

    <script>
        $(document).ready(function() {
            // Function to toggle buttons
            function toggleButtons(status) {
                if (status === 'in') {
                    $("#clock-in").addClass('inactive');
                    $("#clock-out").removeClass('inactive');
                } else {
                    $("#clock-out").addClass('inactive');
                    $("#clock-in").removeClass('inactive');
                }
            }

            // Check the current status from the server on page load
            $.get('code.php', function(response) {
                if (response === '1') {
                    toggleButtons('in');  // If status is 1, show Clock Out button
                } else {
                    toggleButtons('out'); // If status is 0, show Clock In button
                }
            });

            // Capture photo function
            function capturePhoto(video, canvas, img) {
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const data = canvas.toDataURL('image/png');
                img.src = data;
                img.style.display = 'block';
                return data;
            }

            // Set up camera
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    $("#video")[0].srcObject = stream;
                }).catch(function(err) {
                    console.error("Error accessing camera: " + err);
                });

            // Get location (clock-in or clock-out)
            function getLocation(action, photoData) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Send data to the server
                        $.post('code.php', {
                            action: action, 
                            photo: photoData, 
                            latitude: latitude, 
                            longitude: longitude
                        }, function(response) {
                            toggleButtons(action === 'in' ? 'in' : 'out');
                            alert(response);
                        });
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }

            // Handle Clock In
            $("#clock-in").click(function() {
                const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
                getLocation('in', photoData);  // Call getLocation with 'clock-in'
            });

            // Handle Clock Out
            $("#clock-out").click(function() {
                const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
                getLocation('out', photoData);  // Call getLocation with 'clock-out'
            });
        });
    </script>
</body>
</html>
