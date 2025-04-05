<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
    include("includes/userlinks.php");


$techID = isset($_GET['id']) ? $_GET['id'] : 0;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch technician details
$techQuery = "SELECT * FROM TechTable WHERE TechID = $techID";
$techResult = mysqli_query($conn, $techQuery);
$tech = mysqli_fetch_assoc($techResult);

$techName = $tech['TechName'];

// Fetch timecard data
$logQuery = "SELECT * FROM clock_status WHERE user_id = $techID AND DATE(timestamp) BETWEEN '$startDate' AND '$endDate' ORDER BY timestamp";
$logResult = mysqli_query($conn, $logQuery);


// Organize time logs by date
$timeLogs = [];
//$imagePath = 'no_photo.jfif'; // default
while ($log = mysqli_fetch_assoc($logResult)) {
//   if (!empty($log['image_path']) && file_exists("mmglass/photos/" . $log['image_path'])) {
//        $imagePath = $log['image_path'];
//    }
    $logDate = date('D, M j', strtotime($log['timestamp']));
    $logTime = date('h:i A', strtotime($log['timestamp']));
    if (!isset($timeLogs[$logDate])) {
        $timeLogs[$logDate] = [];
    }
    $timeLogs[$logDate][] = [
        'time' => $logTime,
        'status' => $log['status']
    ];
}

// Format time logs to ensure "In" and "Out" are paired
$formattedLogs = [];
foreach ($timeLogs as $date => $logs) {
    $pairs = [];
    $inTime = null;

    foreach ($logs as $log) {
        if ($log['status'] == 1) { // "In" time
            $inTime = $log['time'];
        } elseif ($log['status'] == 0 && $inTime) { // "Out" time (pair it)
            $pairs[] = ['in' => $inTime, 'out' => $log['time']];
            $inTime = null; // Reset inTime for the next pair
        }
    }
    
    // If an "In" is left without an "Out", store it separately
    if ($inTime) {
        $pairs[] = ['in' => $inTime, 'out' => '']; 
    }

    $formattedLogs[$date] = $pairs;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Card for <?php echo $techName; ?></title>
         <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<style>
        .time-table { width: 100%; border-collapse: collapse; }
        .time-table th, .time-table td { border: 1px solid #ddd; padding: 8px; text-align: center; vertical-align: middle; }
        .time-table th { background-color: grey; color: white; }
        .in-status { color: green; font-weight: bold; }
        .out-status { color: blue; font-weight: bold; }
        .work-time-row { background: #f8f9fa; font-weight: bold; text-align: center; color: red; }
        .table-container {
    width: 100%;
    overflow-x: auto; /* Enable horizontal scroll */
    white-space: nowrap; /* Prevent text from wrapping */
}
@media (max-width: 768px) {
.table-container {
width: 100%;
overflow-x: auto;
white-space: inherit;
}
}

    </style>
</head>
<body>
<div class="container mb-4" style="margin-top: 80px;">
       <?php
 if (!$tech) {
    echo "<div class='bg-danger text-white p-4 rounded'>
            <h5>No data found for technician ID: $techID</h5>
          </div>";
    exit;
}

?>
    <div class="header d-flex align-items-center mb-3 p-3 bg-secondary text-white rounded">
        <!--<img src="/mmglass/<?php echo $imagePath; ?>" alt="Profile Photo" width="50" height="50" class="rounded-circle me-2">-->
        <h4 class="mb-0">Time Card for <?php echo $techName; ?></h4>
    </div>

<form method="GET" class="row g-2 mb-3">
    <input type="hidden" name="id" value="<?php echo $techID; ?>">

    <div class="col-12 col-sm-auto">
        <input type="date" name="start_date" value="<?php echo $startDate; ?>" class="form-control" />
    </div>

    <div class="col-12 col-sm-auto">
        <input type="date" name="end_date" value="<?php echo $endDate; ?>" class="form-control" />
    </div>

    <div class="col-12 col-sm-auto">
        <button type="submit" class="btn btn-primary w-100">Find</button>
    </div>
</form>

<div class="table-container">
    <?php
 
  if (empty($formattedLogs)) {
    echo "<div class='bg-danger text-white p-4 rounded'>
            <h5>No timecard data found for the selected date range.</h5>
          </div>";
    exit;
}

?>

    <table class="time-table">
        <thead>
            <tr>
                <th>Status</th>
                <?php foreach ($formattedLogs as $date => $entries): ?>
                    <th><?php echo $date; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $maxRows = max(array_map('count', $formattedLogs)); // Find max rows needed
            for ($i = 0; $i < $maxRows; $i++): ?>
                <tr>
                     <td class="in-status">In</td>
                    <?php foreach ($formattedLogs as $date => $entries): ?>
                        <td class="in-status">
                            <?php echo isset($entries[$i]['in']) ? $entries[$i]['in'] : '-'; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td class="out-status">Out</td>
                    <?php foreach ($formattedLogs as $date => $entries): ?>
                        <td class="out-status">
                            <?php echo isset($entries[$i]['out']) ? $entries[$i]['out'] : '-'; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endfor; ?>
            
           <!-- Work Time Row -->
<tr class="work-time-row">
    <td>Work Time</td>
    <?php
    $grandTotal = 0;
    foreach ($formattedLogs as $date => $entries):
        $totalWorkTime = 0;
        foreach ($entries as $pair) {
            if (!empty($pair['in']) && !empty($pair['out'])) {
                $inTime = strtotime($pair['in']);
                $outTime = strtotime($pair['out']);
                $totalWorkTime += round(($outTime - $inTime) / 3600, 2);
            }
        }
        $grandTotal += $totalWorkTime;
    ?>
        <td><?php echo number_format($totalWorkTime, 2); ?> hrs</td>
    <?php endforeach; ?>
</tr>

<!-- Grand Total Time Row -->
<tr class="work-time-row" style="background-color: #d1e7dd; color: #0f5132;">
    <td colspan="<?php echo count($formattedLogs) + 1; ?>">
        Total Time (All Days): <strong><?php echo number_format($grandTotal, 2); ?> hrs</strong>
    </td>
</tr>

        </tbody>
    </table>
</div>
</div>
</body>
</html>
