<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);
include("includes/config.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d', strtotime('-7 days'));
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

// Fetch technician name
$queryTech = "SELECT TechName FROM techtable WHERE TechID = ?";
$stmtTech = $conn->prepare($queryTech);
$stmtTech->bind_param("i", $id);
$stmtTech->execute();
$resultTech = $stmtTech->get_result();
$techName = $resultTech->fetch_assoc()['TechName'] ?? 'Unknown';

// Fetch technician clock data
$query = "SELECT status, timestamp FROM clock_status WHERE user_id = ? AND DATE(timestamp) BETWEEN ? AND ? ORDER BY timestamp ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $id, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$workTime = [];
while ($row = $result->fetch_assoc()) {
    $date = date('Y-m-d', strtotime($row['timestamp']));
    $time = date('h:i A', strtotime($row['timestamp']));
    $status = $row['status'] == 1 ? '<span style="color: green;">In</span>' : '<span style="color: blue;">Out</span>';
    $data[$date][] = ['status' => $status, 'time' => $time, 'timestamp' => strtotime($row['timestamp'])];
}

// Calculate work time per day
foreach ($data as $date => $entries) {
    $totalWorkTime = 0;
    $lastInTime = null;
    foreach ($entries as $entry) {
        if (strpos($entry['status'], 'green') !== false) {
            $lastInTime = $entry['timestamp'];
        } elseif (strpos($entry['status'], 'blue') !== false && $lastInTime) {
            $totalWorkTime += ($entry['timestamp'] - $lastInTime) / 3600;
            $lastInTime = null;
        }
    }
    $workTime[$date] = round($totalWorkTime, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Card</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .selected-date { background-color: #007bff; color: white; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h4>Time Card for <?php echo $techName; ?> (<?php echo date('m/d/Y', strtotime($startDate)); ?> - <?php echo date('m/d/Y', strtotime($endDate)); ?>)</h4>
        <form method="GET" class="mb-3">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label>Start Date:</label>
            <input type="date" name="startDate" value="<?php echo $startDate; ?>">
            <label>End Date:</label>
            <input type="date" name="endDate" value="<?php echo $endDate; ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Time</th>
                    <th>Work Time (hrs)</th>
                </tr>
            </thead>
            <tbody>
<?php foreach ($data as $date => $entries) { ?>
    <tr>
        <td rowspan="<?php echo count($entries) + 1; ?>"><?php echo date('D, M d', strtotime($date)); ?></td>
        <?php foreach ($entries as $index => $entry) { ?>
            <?php if ($index > 0) echo '<tr>'; ?>
            <td><?php echo $entry['status']; ?></td>
            <td><?php echo $entry['time']; ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="2"><strong>Work Time:</strong></td>
            <td><?php echo $workTime[$date] ?? 0; ?> hrs</td>
        </tr>
<?php } ?>

<!-- Add this after daily rows -->
<tr style="background-color:#f9f9f9; font-weight: bold;">
    <td colspan="3" style="text-align: right;">Total Work Time:</td>
    <td>
        <?php
            $total = array_sum($workTime);
            echo $total . " hrs";
        ?>
    </td>
</tr>

            </tbody>
        </table>
    </div>
</body>
</html>
