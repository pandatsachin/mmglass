<?php
error_reporting(0);
//ini_set('display_errors', 1);

include("includes/config.php");

if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}

$userId = $_SESSION['User']['TechID'];
$job_id = '372';

// Handle POST request for clocking in/out
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // Clock in or out
    $photo = $_POST['photo']; // Base64 image data
    $longitude = $_POST['longitude']; 
    $latitude = $_POST['latitude']; 
    $jobId = $_POST['jobid']; // Get job ID from POST request

    // Generate unique image name
    $imageFileName = "job_{$action}_" . time() . ".png";
    $photoPath = "photos/" . $imageFileName; // Path to store the image
    $imgData = str_replace('data:image/png;base64,', '', $photo);
    $imgData = str_replace(' ', '+', $imgData);
    file_put_contents($photoPath, base64_decode($imgData));

    // Check if record exists for the user and job
    $sql_check = "SELECT * FROM clock_status WHERE user_id = $userId AND JobID = $jobId";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // Record exists; update the status
        if ($action === 'in') {
            $sql_update = "UPDATE clock_status 
                           SET status = 1, clockin_image_path = '$photoPath', 
                               clockin_longitude = '$longitude', clockin_latitude = '$latitude'
                           WHERE user_id = $userId AND JobID = $jobId";
            if ($conn->query($sql_update) === TRUE) {
                echo "You have successfully clocked in. Image and location saved.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } elseif ($action === 'out') {
            $sql_update = "UPDATE clock_status 
                           SET status = 0, clockout_image_path = '$photoPath', 
                               clockout_longitude = '$longitude', clockout_latitude = '$latitude'
                           WHERE user_id = $userId AND JobID = $jobId";
            if ($conn->query($sql_update) === TRUE) {
                echo "You have successfully clocked out. Image and location saved.";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    } else {
        // User doesn't exist; insert a new record
        if ($action === 'in') {
            $sql_insert = "INSERT INTO clock_status (user_id, JobID, status, clockin_image_path, clockin_longitude, clockin_latitude) 
                           VALUES ($userId, $jobId, 1, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "New user clocked in and record created. Image and location saved.";
            } else {
                echo "Error inserting record: " . $conn->error;
            }
        } elseif ($action === 'out') {
            $sql_insert = "INSERT INTO clock_status (user_id, JobID, status, clockout_image_path, clockout_longitude, clockout_latitude) 
                           VALUES ($userId, $jobId, 0, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "New user clocked out and record created. Image and location saved.";
            } else {
                echo "Error inserting record: " . $conn->error;
            }
        }
    }
    $conn->close();
}


// Fetch current clock-in status
$sql = "SELECT status FROM clock_status WHERE user_id = $userId AND JobID = '$job_id'" ;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['status'];  
} else {
    $status = '0';
}
include("includes/header.php");
include("includes/userlinks.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock In/Out System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        button {
            margin: 10px;
        }
        .inactive {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Track Jobs</h1>
    <div class="row">
        <div class="col-sm-3 col-md-6 col-lg-4" id="job">
          <div class="button-container">
                                <?php if ($status === '1') { ?>
                                    <button id="clock-in" class="clock-in btn btn-success" data-jobid="<?php echo $job_id; ?>" disabled>Clock In</button>
                                    <button id="clock-out" class="clock-out btn btn-danger" data-jobid="<?php echo $job_id; ?>" >Clock Out</button>
                                <?php } else { ?>
                                    <button id="clock-in" class="clock-in btn btn-success" data-jobid="<?php echo $job_id; ?>" >Clock In</button>
                                    <button id="clock-out" class="clock-out btn btn-danger" data-jobid="<?php echo $job_id; ?>" disabled>Clock Out</button>
                                <?php } ?>
                            </div>
            <video id="video" width="320" height="240" autoplay></video>
            <canvas id="canvas" style="display:none;"></canvas>
        </div>
        <div class="col-sm-3 col-md-6 col-lg-4" style="margin-top:auto;">
            <img id="photo" width="320" height="240" alt="Captured Photo" style="display:none;">
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Function to toggle buttons
    function toggleButtons(status) {
        if (status === '1') {
            $("#clock-in").attr('disabled', true);  // Disable Clock In
            $("#clock-out").attr('disabled', false); // Enable Clock Out
        } else {
            $("#clock-in").attr('disabled', false);  // Enable Clock In
            $("#clock-out").attr('disabled', true); // Disable Clock Out
        }
    }

    // On page load, use PHP to check the initial status and call toggleButtons
    toggleButtons("<?php echo $status; ?>");

    // Function to capture photo
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
    navigator.mediaDevices.getUserMedia({video: true})
        .then(function (stream) {
            $("#video")[0].srcObject = stream;
        }).catch(function (err) {
            console.error("Error accessing camera: " + err);
    });

      // Get location (clock-in or clock-out)
function getLocation(action, photoData, jobId) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Send data to the server
            $.post('clock.php', {
                action: action,
                photo: photoData,
                latitude: latitude,
                longitude: longitude,
                jobid: jobId  // Add job ID here
            }, function (response) {
                toggleButtons(action === 'in' ? '1' : '0');
                alert(response);
            });
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

// Handle Clock In
$("#clock-in").click(function () {
    const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
    const jobId = $(this).data('jobid');  // Get job ID from the button
    getLocation('in', photoData, jobId);  // Call getLocation with 'clock-in'
});

// Handle Clock Out
$("#clock-out").click(function () {
    const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
    const jobId = $(this).data('jobid');  // Get job ID from the button
    getLocation('out', photoData, jobId);  // Call getLocation with 'clock-out'
});
});
</script>
</body>
</html>
