<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);
include("includes/config.php");

$techID = isset($_GET['id']) ? $_GET['id'] : 0;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-7 days'));
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch technician details
$techQuery = "SELECT * FROM techtable WHERE TechID = $techID";
$techResult = mysqli_query($conn, $techQuery);
$tech = mysqli_fetch_assoc($techResult);
$techName = $tech['TechName'];
$imagePath = $tech['image_path'] ? $tech['image_path'] : 'default.png';

// Fetch timecard data
$logQuery = "SELECT * FROM clock_status WHERE user_id = $techID AND DATE(timestamp) BETWEEN '$startDate' AND '$endDate' ORDER BY timestamp";
$logResult = mysqli_query($conn, $logQuery);

// Count total hours
$totalHours = 0;
$previousTimestamp = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Card - <?php echo $techName; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .header { display: flex; align-items: center; margin-bottom: 20px; }
        .header img { width: 50px; height: 50px; border-radius: 50%; margin-right: 10px; }
        .in-status { color: green; }
        .out-status { color: blue; }
        .no-punch { color: gray; }
        .date-picker { display: flex; gap: 10px; margin-bottom: 15px; }
        .time-table { width: 100%; border-collapse: collapse; }
        .time-table th, .time-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .time-table th { background-color: #f4f4f4; }
        .total-hours { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="header">
        <img src="/mmglass/<?php echo $imagePath; ?>" alt="Profile Photo">
        <h4>Time Card for <?php echo $techName; ?></h4>
    </div>

    <!-- Date Range Picker -->
    <form method="GET" class="date-picker">
        <input type="hidden" name="id" value="<?php echo $techID; ?>">
        <input type="date" name="start_date" value="<?php echo $startDate; ?>" class="form-control">
        <input type="date" name="end_date" value="<?php echo $endDate; ?>" class="form-control">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <!-- Time Log Table -->
    <table class="time-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($log = mysqli_fetch_assoc($logResult)) { 
                $status = ($log['status'] == 1) ? "In" : "Out";
                $statusClass = ($log['status'] == 1) ? "in-status" : "out-status";
                $timestamp = strtotime($log['timestamp']);

                // Calculate total hours
                if ($previousTimestamp && $log['status'] == 0) {
                    $totalHours += ($timestamp - $previousTimestamp) / 3600; // Convert seconds to hours
                }
                if ($log['status'] == 1) {
                    $previousTimestamp = $timestamp;
                }
            ?>
            <tr>
                <td><?php echo date('M d, Y', $timestamp); ?></td>
                <td class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
                <td><?php echo date('h:i A', $timestamp); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Total Hours -->
    <div class="total-hours">Total Work Time: <?php echo round($totalHours, 2); ?> hrs</div>
</div>
</body>
</html>
