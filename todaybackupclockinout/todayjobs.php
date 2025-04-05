<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("includes/config.php");
if (!isset($_SESSION['User'])) {
  header("Location:index.php");
}

$userId = $_SESSION['User']['TechID'];
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $action = $_POST['action']; 
//    $photo = $_POST['photo']; // Base64 image data
//    $longitude = $_POST['longitude'];
//    $latitude = $_POST['latitude'];
//    $jobId = intval($_POST['jobid']);
//
//    // Return JSON response
//    echo json_encode([
//        'status' => 'success',
//        'message' => "Action: $action, Photo: (Base64 Data), Longitude: $longitude, Latitude: $latitude, Job ID: $jobId, User ID: $userId"
//    ]);
//    exit;
//}
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

    // Check if record exists for the user and job
    $sql_check = "SELECT * FROM clock_status WHERE user_id = $userId AND JobID = $jobId";
    $result = $conn->query($sql_check);
    
    $response = []; // Initialize response array

    if ($result->num_rows > 0) {
        // Record exists; update the status
        if ($action == 0) {
            $sql_update = "UPDATE clock_status 
                           SET status = 1, clockin_image_path = '$photoPath', 
                               clockin_longitude = '$longitude', clockin_latitude = '$latitude'
                           WHERE user_id = $userId AND JobID = $jobId";
            if ($conn->query($sql_update) === TRUE) {
                $response = ["success" => true, "message" => "You have successfully clocked in. Image and location saved."];
            } else {
                $response = ["success" => false, "message" => "Error updating record: " . $conn->error];
            }
        } elseif ($action == 1) {
            $sql_update = "UPDATE clock_status 
                           SET status = 0, clockout_image_path = '$photoPath', 
                               clockout_longitude = '$longitude', clockout_latitude = '$latitude'
                           WHERE user_id = $userId AND JobID = $jobId";
            if ($conn->query($sql_update) === TRUE) {
                $response = ["success" => true, "message" => "You have successfully clocked out. Image and location saved."];
            } else {
                $response = ["success" => false, "message" => "Error updating record: " . $conn->error];
            }
        }
    } else {
        // User doesn't exist; insert a new record
        if ($action == 0) {
            $sql_insert = "INSERT INTO clock_status (user_id, JobID, status, clockin_image_path, clockin_longitude, clockin_latitude) 
                           VALUES ($userId, $jobId, 1, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                $response = ["success" => true, "message" => "New user clocked in and record created. Image and location saved."];
            } else {
                $response = ["success" => false, "message" => "Error inserting record: " . $conn->error];
            }
        } elseif ($action == 1) {
            $sql_insert = "INSERT INTO clock_status (user_id, JobID, status, clockout_image_path, clockout_longitude, clockout_latitude) 
                           VALUES ($userId, $jobId, 0, '$photoPath', '$longitude', '$latitude')";
            if ($conn->query($sql_insert) === TRUE) {
                $response = ["success" => true, "message" => "New user clocked out and record created. Image and location saved."];
            } else {
                $response = ["success" => false, "message" => "Error inserting record: " . $conn->error];
            }
        }
    }

    // Return the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


// Fetch current clock-in status for the specific user and job
//$sql = "SELECT status FROM clock_status WHERE user_id = $userId AND JobID = $job_id";
//$result = $conn->query($sql);
//
//if ($result->num_rows > 0) {
//    $row = $result->fetch_assoc();
//    $status = $row['status'];  
//} else {
//    // If no record is found, assume status is 0 (not clocked in)
//    $status = '0';  // Default to clock-out for new jobs or users
//}

$today_date = date('Y-m-d');
//$qry = "select * from JobTable where JobDate='" . $today_date . "' and TechID=" . $_SESSION['User']['TechID'] . " order by jOrder, JobDate";
$qry = "select * from JobTable where TechID=36 order by jOrder, JobDate";
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
    <div class="row"><div class="col-sm-12"><h2 style="float: left;">Today's Jobs (<?php echo $tot_jobs; ?>)</h2><!--<a style="float: right;" class="btn btn-primary" href="addjob.php" role="button">Add New Job</a></div>--></div>
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
                $clock_status_query = "SELECT status FROM clock_status WHERE "
                        . "user_id = $userId AND JobID = $job_id order by ID limit 1";
                $clock_status_result = $conn->query($clock_status_query);
                // Set default status
                 $status = '0';
                if ($clock_status_result->num_rows > 0) {
                  $clock_status_row = $clock_status_result->fetch_object();
                  $status = $clock_status_row->status;
                } 
                // Determine button text and class based on status
                $button_text = ($status == 1) ? 'Clock Out' : 'Clock IN';
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
                  <td>
                    <a href="<?php echo $link; ?>" <?php echo $target; ?>>Job</a><br>
                    <a href="measure.php?JobID=<?php echo $row->JobID; ?>">Measure</a> 
                  </td>
                  <td>
                    <button  class="toggle-button btn btn-primary" id="track-job-btn" 
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
              <div class="col-sm-3 col-md-6 col-lg-4" id="job">
                <div class="button-container">
                  <button id="take-photo" class="clock-in btn btn-success" data-jobid="" status="" >Take Photo</button>
                  
                </div>
                <video id="video" width="320" height="240" autoplay></video>
                <canvas id="canvas" style="display:none;"></canvas>
              </div>
              <div class="col-sm-3 col-md-6 col-lg-4" style="margin-top:auto;">
                <img id="photo" width="320" height="240" alt="Captured Photo" style="display:none;">
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
  $(document).on('click', '#track-job-btn', function () {
    const jobId = $(this).data('jobid');
    const status = $(this).data('status');
    
    // Pass data to take-photo button
    $("#take-photo").data('jobid', jobId).data('status', status);

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
    const photoData = capturePhoto($("#video")[0], $("#canvas")[0], $("#photo")[0]);
    const jobId = $(this).data('jobid'); 
    const status = $(this).data('status'); 

    getLocation(status, photoData, jobId);
  });

  // Stop camera stream when modal is closed
  $('.bd-example-modal-xl').on('hidden.bs.modal', function () {
    if (stream) {
      const tracks = stream.getTracks();
      tracks.forEach(track => track.stop());
    }
    $("#video")[0].srcObject = null;
  });

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

  // Function to get geolocation and send data
  function getLocation(action, photoData, jobId) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Send data to the server using AJAX POST
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

                    // Update button text and status dynamically
//                    let updatedText = (action == 0) ? 'Clock Out' : 'Clock IN'; //  switches to IN
//                    let updatedClass = (action == 0) ? 'btn btn-danger' : 'btn btn-success'; // Update button class 
//                    $("#track-job-btn")
//                        .text(updatedText)
//                        .removeClass('btn-success btn-danger') // Remove both classes
//                        .addClass(updatedClass); // Add the new class

                    // Close the modal after successful submission
                    $('.bd-example-modal-xl').modal('hide');
                } else {
                    // Show the error message in an alert
                    alert("Error: " + response.message);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
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
