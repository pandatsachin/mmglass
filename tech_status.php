<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
    include("includes/userlinks.php");


// Set default date or get from URL
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch technicians from techtable
$sql = "SELECT TechID, TechName FROM techtable WHERE TechName LIKE '%$search%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Check-In System</title>
       <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        

    <style>
        /* Tech Card Styling */
        .tech-card {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 5px;
            padding: 0;
            box-shadow: 0px 0px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #ddd;
            margin-bottom: 7px;
        }

        /* Left Section for Background Color */
        .status-left {
            width: 60px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        /* Background Colors for Different Statuses */
        .status-in { background-color: #28a745; }  /* Green */
        .status-out { background-color: #007bff; } /* Blue */
        .status-no-punch { background-color: gray; } /* Gray */

        /* Profile Image Container */
        .img-container {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Profile Image */
        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Right Section for Text */
        .tech-info {
            padding: 10px;
        }

        .tech-info h5 {
            margin: 0;
            font-size: 16px;
            color: #007bff;
        }

        .tech-info p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
      
    <div class="container mb-4" style="margin-top: 80px;         max-width: 1320px;">
  <?php
// Count statuses for the selected date
$inCount = $outCount = $noPunchCount = 0;
$statusCountQuery = "SELECT user_id, MAX(timestamp) as latest_time FROM clock_status 
                     WHERE DATE(timestamp) = '$date'
                     GROUP BY user_id";
$statusCountResult = $conn->query($statusCountQuery);

if ($statusCountResult->num_rows > 0) {
    while ($row = $statusCountResult->fetch_assoc()) {
        $uid = $row['user_id'];
        $latest = $row['latest_time'];
        $statusQ = "SELECT status FROM clock_status WHERE user_id = '$uid' AND timestamp = '$latest' LIMIT 1";
        $res = $conn->query($statusQ);
        if ($res && $res->num_rows > 0) {
            $status = $res->fetch_assoc()['status'];
            if ($status == 1) $inCount++;
            else $outCount++;
        }
    }
}

// Total techs - (In + Out) = No Punch
$totalTechs = $result->num_rows;
$noPunchCount = $totalTechs - ($inCount + $outCount);
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <!-- Left: Date Navigation -->
    <div class="d-flex mt-1 align-items-center  gap-2">
        <a href="?date=<?= date('Y-m-d', strtotime($date . ' -1 day')) ?>" class="btn btn-outline-primary">&lt;</a>
        <input style="margin-right: 5px; margin-left: 5px;" type="date" class="form-control" id="datePicker" value="<?= $date ?>">
        <a href="?date=<?= date('Y-m-d', strtotime($date . ' +1 day')) ?>" class="btn btn-outline-primary">&gt;</a>
    </div>

    <!-- Right: Status Counts -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="px-3 mt-1 py-2 bg-success text-white rounded d-flex align-items-center">
            <span class="me-2">Check-In </span> <strong><?= $inCount ?></strong>
        </div>
        <div class="px-3 mt-1 ml-1 mr-1 py-2 bg-primary text-white rounded d-flex align-items-center">
            <span class="me-2">Check-Out </span> <strong><?= $outCount ?></strong>
        </div>
        <div class="px-3 mt-1 py-2 bg-secondary text-white rounded d-flex align-items-center">
            <span class="me-2">No Punch</span> <strong><?= $noPunchCount ?></strong>
        </div>
    </div>
</div>



        <!-- Search Bar -->
<!--        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search Technician..." value="<?= $search ?>">
        </div>-->

        <div class="row g-3">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $techID = $row["TechID"];
                    $techName = htmlspecialchars($row["TechName"]);

                    // Fetch latest status, timestamp, and image for the selected date
                    $statusQuery = "SELECT status, timestamp, image_path FROM clock_status 
                                    WHERE user_id = '$techID' 
                                    AND DATE(timestamp) = '$date'
                                    ORDER BY timestamp DESC LIMIT 1";
                    $statusResult = $conn->query($statusQuery);

                    if ($statusResult->num_rows > 0) {
                        $statusRow = $statusResult->fetch_assoc();
                        $status = $statusRow["status"];
                        $timestamp = date("m/d/Y h:i A", strtotime($statusRow["timestamp"]));
                        $imagePath = !empty($statusRow["image_path"]) ? $statusRow["image_path"] : "no_photo.jfif";

                        if ($status == 1) {
                            $statusText = "In";
                            $statusClass = "status-in";
                        } else {
                            $statusText = "Out";
                            $statusClass = "status-out";
                        }
                    } else {
                        $statusText = "No Punch";
                        $statusClass = "status-no-punch";
                        $timestamp = "--"; // No timestamp if no punch
                        $imagePath = "no_photo.jfif"; // Use default image
                    }

                    // Output Technician Card
                    echo '
                    <div class="col-md-3" onclick="window.location.href=\'timecard.php?id=' . $techID . '\'">

                        <div class="tech-card">
                            <!-- Left Section with Background Color -->
                            <div class="status-left ' . $statusClass . '">
                                <div class="img-container">
                                    <img src="' . $imagePath . '" alt="Profile Photo">
                                </div>
                            </div>
                            
                            <!-- Right Section with Technician Info -->
                            <div class="tech-info">
                                <h5>' . $techName . '</h5>
                                <p>' . $statusText . ': ' . $timestamp . '</p>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo "<p>No technicians found.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        // Change date dynamically using input
        document.getElementById('datePicker').addEventListener('change', function() {
            window.location.href = '?date=' + this.value + '&search=<?= $search ?>';
        });

        // Search filter functionality
//        document.getElementById('searchInput').addEventListener('keypress', function(event) {
//            if (event.key === "Enter") {
//                let searchValue = this.value.trim();
//                window.location.href = '?date=<?= $date ?>&search=' + encodeURIComponent(searchValue);
//            }
//        });
    </script>
</body>
</html>
