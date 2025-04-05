<?php
//error_reporting(0);

ini_set('display_errors', 1);
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
?>
<?php
include("includes/header.php");
?>
<!--<body style="align-items: baseline;">-->
<?php
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
        <div class="container-fluid">
            <h1>track jobs</h1>

            <div class="row" >
                <div class="col-sm-3 col-md-6 col-lg-4" id="job" style="">
                    <button id="clock-in" class="clock-in">Clock In</button>
                    <button id="clock-out" class="clock-out inactive">Clock Out</button>
                    <video id="video" width="320" height="240" autoplay></video>
                    <canvas id="canvas" style="display:none;"></canvas>  
                </div>
                    
                <div class="col-sm-9 col-md-6 col-lg-8" style="">
                    <img id="photo" width="320" height="240" alt="Captured Photo" style="display:none;">
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
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
                navigator.mediaDevices.getUserMedia({video: true})
                        .then(function (stream) {
                            $("#video")[0].srcObject = stream;
                        }).catch(function (err) {
                    console.error("Error accessing camera: " + err);
                });

                // Handle Clock In
                $("#clock-in").click(function () {
                    const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
                    $.post('clock.php', {action: 'in', photo: photoData}, function (response) {
                        toggleButtons('in');
                        alert(response);
                    });
                });

                // Handle Clock Out
                $("#clock-out").click(function () {
                    const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
                    $.post('clock.php', {action: 'out', photo: photoData}, function (response) {
                        toggleButtons('out');
                        alert(response);
                    });
                });
            });
        </script>
    </body>
</html>
