<?php
//error_reporting(0);
//ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("includes/config.php");

if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}

$userId = $_SESSION['User']['TechID'];
$job_id = '375';

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
                exit;
            } else {
                echo "Error updating record: " . $conn->error;
                exit;
            }
        } elseif ($action === 'out') {
            $sql_update = "UPDATE clock_status 
                           SET status = 0, clockout_image_path = '$photoPath', 
                               clockout_longitude = '$longitude', clockout_latitude = '$latitude'
                           WHERE user_id = $userId AND JobID = $jobId";
            if ($conn->query($sql_update) === TRUE) {
                echo "You have successfully clocked out. Image and location saved.";
                exit;
            } else {
                echo "Error updating record: " . $conn->error;
                exit;
            }
        }
    } else {
        // User doesn't exist; insert a new record
        if ($action === 'in') {
            $sql_insert = "INSERT INTO clock_status (user_id, JobID, status, clockin_image_path, clockin_longitude, clockin_latitude) 
                           VALUES ($userId, $jobId, 1, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "New user clocked in and record created. Image and location saved.";
                exit;
            } else {
                echo "Error inserting record: " . $conn->error;
                exit;
            }
        } elseif ($action === 'out') {
            $sql_insert = "INSERT INTO clock_status (user_id, JobID, status, clockout_image_path, clockout_longitude, clockout_latitude) 
                           VALUES ($userId, $jobId, 0, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                echo "New user clocked out and record created. Image and location saved.";
                exit;
            } else {
                echo "Error inserting record: " . $conn->error;
                exit;
            }
        }
    }
}

// Fetch current clock-in status for the specific user and job
$sql = "SELECT status FROM clock_status WHERE user_id = $userId AND JobID = $job_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row['status'];  
} else {
    // If no record is found, assume status is 0 (not clocked in)
    $status = '0';  // Default to clock-out for new jobs or users
}

//include("includes/header.php");
//include("includes/userlinks.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>jobid track</title>
        <!-- Bootstrap CSS -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
         <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    </head>
    <body>

        <div class="container mt-5">
            <!-- Button to Open the Extra Large Modal -->
           <button type="button" class="btn btn-primary" id="track-job-btn" data-toggle="modal" data-target=".bd-example-modal-xl">
    Track Job
</button>

        </div>

        <!-- Extra Large Modal -->
        <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">Track Job</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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
                        </div>                </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery, Popper.js, and Bootstrap JS -->
        <!--<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>-->
       
    <script>
    $(document).ready(function () {
        let stream;

        // Add click event listener to the "track job" button
        $("#track-job-btn").click(function () {
            console.log("Track job button clicked!");

            // Function to toggle buttons (Clock In/Out)
            function toggleButtons(status) {
                if (status === '1') {
                    $("#clock-in").attr('disabled', true);
                    $("#clock-out").attr('disabled', false);
                } else {
                    $("#clock-in").attr('disabled', false);
                    $("#clock-out").attr('disabled', true);
                }
            }

            // Set initial button state based on PHP status
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

            // Access the camera and display video
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (videoStream) {
                    stream = videoStream; // Store the stream globally so it can be stopped later
                    $("#video")[0].srcObject = stream;
                })
                .catch(function (err) {
                    console.error("Error accessing camera: " + err);
                });

            // Function to get geolocation and send data
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
                            jobid: jobId // Use JS variable here
                        }, function (response) {
                            toggleButtons(action === 'in' ? '1' : '0');
                            alert(response);
                        });
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }

            // Remove previous click event listeners to prevent double execution
            $("#clock-in").off("click").on("click", function () {
                const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
                const jobId = $(this).data('jobid');
                getLocation('in', photoData, jobId);
            });

            // Remove previous click event listeners to prevent double execution
            $("#clock-out").off("click").on("click", function () {
                const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
                const jobId = $(this).data('jobid');
                getLocation('out', photoData, jobId);
            });
        });

        // Event listener for when the modal is hidden
        $('.bd-example-modal-xl').on('hidden.bs.modal', function () {
            console.log("Modal closed. Stopping camera stream.");

            // Stop the camera stream when the modal is closed
            if (stream) {
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop()); // Stop each track (audio, video)
            }

            // Optionally reset the video source
            $("#video")[0].srcObject = null;
        });
    });
</script>




    </body>
</html>