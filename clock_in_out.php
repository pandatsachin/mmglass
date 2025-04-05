<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "mmglass");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $job_id = $_POST['job_id'];
    $type = $_POST['type']; // clock_in or clock_out
    $gps = $_POST['gps'];
    $photo = $_FILES['photo'];

    // Upload the photo
    $target_dir = "uploads/";
    $photo_file = $target_dir . basename($photo["name"]);
    move_uploaded_file($photo["tmp_name"], $photo_file);

    if ($type === 'clock_in') {
        // Clock in: insert into database
        $sql = "INSERT INTO jobs (user_id, job_id, clock_in_time, clock_in_gps, clock_in_photo) 
                VALUES ('$user_id', '$job_id', NOW(), '$gps', '$photo_file')";
    } else if ($type === 'clock_out') {
        // Clock out: update the existing record
        $sql = "UPDATE jobs SET clock_out_time = NOW(), clock_out_gps = '$gps', clock_out_photo = '$photo_file' 
                WHERE user_id = '$user_id' AND job_id = '$job_id' AND clock_out_time IS NULL";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>
