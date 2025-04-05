<?php
ini_set('display_errors', 0);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}

$userId = $_SESSION['User']['TechID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $photo = $_POST['photo']; // Base64 image data
    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];
    $jobId = $_POST['jobid'];

    // Generate unique image name
    $imageFileName = "job_{$action}_" . time() . ".png";
    $photoPath = "photos/" . $imageFileName; // Path to store the image
    $imgData = str_replace('data:image/png;base64,', '', $photo);
    $imgData = str_replace(' ', '+', $imgData);
    file_put_contents($photoPath, base64_decode($imgData));

    $response = []; // Initialize response array
    // User doesn't exist; insert a new record
    $sql_insert = "INSERT INTO clock_status (user_id, JobID, status, image_path, longitude, latitude) 
                           VALUES ($userId, $jobId, $action, '$photoPath', '$longitude', '$latitude')";
    if ($conn->query($sql_insert) === TRUE) {
        $response = ["success" => true, "message" => "Your information has been successfully saved."];
    } else {
        $response = ["success" => false, "message" => "Error inserting record: " . $conn->error];
    }

    // Return the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


$qry = "select * from JobTable where TechID=" . $_SESSION['User']['TechID'] . " order by jOrder, JobDate";
//$qry = "select * from JobTable where TechID=36 order by jOrder, JobDate";
$result = $conn->query($qry);
$tot_jobs = $result->num_rows;
?>
<?php
include("includes/header.php");
?>
<body style="align-items: baseline;">
    <?php
    include("includes/userlinks.php");
    ?>
    <main role="main" class="container jobs_page" style="margin-top: 35px; max-width: 100%;">    
        <div class="row"><div class="col-sm-12"><h2 style="float: left;">All Jobs (<?php echo $tot_jobs; ?>)</h2><!--<a style="float: right;" class="btn btn-primary" href="addjob.php" role="button">Add New Job</a></div>--></div>
            <div class="clearfix"></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="tbl_alljobs">                
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Address</th>
                            <th scope="col">Apt</th>
                            <th scope="col">City</th>              
                            <th scope="col">Schedule</th>
                            <th scope="col">Job Order</th>                              
                            <th scope="col">Job Description</th>                          
                            <th scope="col">Measurement</th>
                            <th scope="col">Job Date</th>
                            <th scope="col">Actions</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($tot_jobs > 0) {
                            // Loop through each job row
                            while ($row = $result->fetch_object()) {
                                $row_color = '';
                                if ($row->Priority == 'True') {
                                    $row_color = 'style="color:red;"';
                                }
                                $link = "jobdone.php?JobID=" . $row->WebID;
                                $target = "";
                                $measurement = 'No';
                                if ($row->Measurement == 1) {
                                    $measurement = 'Yes';
                                    $link = "https://www.mmglassnyc.com/wm.php?JobID=" . $row->WebID . "&user=" . $_SESSION['User']['TechName'];
                                    $target = "target='_blank'";
                                }
                                // Get the clock status for the current job
                                $job_id = $row->WebID;
                                $clock_status_query = "SELECT status FROM clock_status WHERE user_id = $userId AND JobID = $job_id ORDER BY ID DESC LIMIT 1";

//                                $clock_status_query = "SELECT status FROM clock_status WHERE "
//                                        . "user_id = $userId AND JobID = $job_id order by ID limit 1";
                                $clock_status_result = $conn->query($clock_status_query);
                                // Set default status
                                $status = 0;
                                if ($clock_status_result->num_rows > 0) {
                                    $clock_status_row = $clock_status_result->fetch_object();
                                    $status = $clock_status_row->status;
                                }
                                $button_text = ($status == 1) ? 'Clock OUT' : 'Clock IN';
//                $button_class = ($status == 1) ? 'btn btn-danger' : 'btn btn-success';
                                ?>

                                <tr <?php echo $row_color; ?>>
                                    <td><?php echo $row->Address; ?></td>
                                    <td><?php echo $row->Apt; ?></td>
                                    <td><?php echo $row->City; ?></td>
                                    <td><?php echo $row->Schedule; ?></td>
                                    <td><?php echo $row->jOrder; ?></td>                                 
                                    <td><?php echo $row->JobDescription; ?></td>                  
                                    <td><?php echo $measurement; ?></td>
                                                      <td><?php echo $row->JobDate; ?></td> 

                                    <td>
                                        <a href="<?php echo $link; ?>" <?php echo $target; ?>>Job</a><br>
                                        <a href="measure.php?JobID=<?php echo $row->JobID; ?>">Measure</a> 
                                    </td>
                                    <td>
                                        <button  class="toggle-button btn btn-primary track-job-btn" id="btnjobid-<?php echo $job_id; ?>"
                                                 data-jobid="<?php echo $job_id; ?>"
                                                 data-status="<?php echo $status; ?>"
                                                 data-toggle="modal"
                                                 data-target=".bd-example-modal-xl">
                                                     <?php echo $button_text; ?>
                                        </button>

                                    </td>
                                </tr>

                                <?php
                            } // End of while loop
                        } else {
                            ?>
                            <tr><td colspan="11">No data!!!</td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
    </main>
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
                            <div class="col-12 button-container">
                                <button id="take-photo" class="clock-in btn btn-success mb-3" data-jobid="0" status="0" >Take Photo</button>

                            </div>
                            <div class="col-sm-3 col-md-6" id="job">

                                <video id="video" width="200" height="auto" autoplay></video>
                                <canvas id="canvas" style="display:none;"></canvas>
                            </div>
                            <div class="col-sm-3 col-md-6 col-lg-4" >
                                <img id="photo" class="photo" width="200" height="auto" alt="Captured Photo" style="display:none;">
                            </div>
                        </div>
                    </div>                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            let stream;

            // Click event for track job button
            $(document).on('click', '.track-job-btn', function () {
                var jobId = $(this).attr('data-jobid');
                var status = $(this).attr('data-status');
                if (status == 1) {
                    var in_status = 0;
                } else {
                    var in_status = 1;
                }

                // Pass data to take-photo button
                $("#take-photo").attr('data-jobid', jobId);
                $("#take-photo").attr('status', in_status);

                // Start camera stream
                navigator.mediaDevices.getUserMedia({video: true})
                        .then(function (videoStream) {
                            stream = videoStream;
                            $("#video")[0].srcObject = stream;
                        })
                        .catch(function (err) {
                            console.error("Error accessing camera: " + err);
                        });
            });

            // Take photo button click event
            $("#take-photo").off("click").on("click", function () {
                var photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
                var jobId = $(this).attr('data-jobid');
                var status = $(this).attr('status');
                getLocation(status, photoData, jobId);
            });

            // Stop camera stream when modal is closed
            $('.bd-example-modal-xl').on('hidden.bs.modal', function () {
                if (stream) {
                    var tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                }
                $("#video")[0].srcObject = null;
            });

            // Function to capture photo
            function capturePhoto(video, canvas, img) {
                var context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                var data = canvas.toDataURL('image/png');
                img.src = data;
                img.style.display = 'block';
                return data;
            }

            // Function to get geolocation and send data
            function getLocation(action, photoData, jobId) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;
                        // Send data 
                        $.post('', {
                            action: action,
                            photo: photoData,
                            latitude: latitude,
                            longitude: longitude,
                            jobid: jobId
                        }, function (response) {
                            // Check for success status
                            if (response.success) {
                                alert(response.message);
                                var btnid = 'btnjobid-' + jobId;
                                var btntext = (action == 1) ? 'Clock OUT' : 'Clock IN';
                                $('#' + btnid).text(btntext);
                                //$('#' + btnid).data('status', action);
                                $('#' + btnid).attr('data-status', action);
                                // Close the modal after successful submission
                                $('.bd-example-modal-xl').modal('hide');
                                $('#photo').css('display', 'none');
                            } else {
                                // Show the error message in an alert
                                alert("Error: " + response.message);
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            alert("AJAX Error: " + textStatus + " : " + errorThrown);
                        });
                    });
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            }

        });


    </script>

</body>
<?php
include("includes/footer.php");
?>
