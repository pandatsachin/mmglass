<!--// fetch_status.php-->
<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
  echo json_encode(['error' => 'User not logged in']);
  exit;
}

$userId = $_SESSION['User']['TechID'];
$jobId = intval($_POST['jobid']);

// Fetch the status from the clock_status table
$sql = "SELECT status FROM clock_status WHERE user_id = $userId AND JobID = $jobId LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  echo json_encode(['status' => $row['status']]);
} else {
  echo json_encode(['status' => 0]);  // Default to 'clock out' if no record found
}
?>
