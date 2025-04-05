<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
$msg = ""; 

if (isset($_POST['submit'])) {
    $jobID = $conn->real_escape_string($_POST['jobid']);
    $sql = "SELECT JobID FROM JobDetailsTable WHERE JobID = '$jobID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $directory = "/home/bitnami/htdocs/mmglass/JobDetailsPDFs/";
        $foundPDF = false;

        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match("/^$jobID-\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}\.pdf$/", $file)) {
                    $foundPDF = true;
                    $filePath = '/mmglass/JobDetailsPDFs/' . $file;
                    $msg = "<div class='alert alert-success mt-4'>
                                <p class='h3 text-center'>Found PDF: <a href='$filePath' target='_blank'>$file</a></p>
                            </div>";
                    break;
                }
            }
            closedir($handle);
        }

        if (!$foundPDF) {
            $msg = "<div class='alert alert-warning mt-4'>
                        <p class='h3 text-center'>No PDF found for Job ID $jobID.</p>
                    </div>";
        }
    } else {
        $msg = "<div class='alert alert-danger mt-4'>
                    <p class='h3 text-center'>Job ID $jobID not found in the database.</p>
                </div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Job PDF</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
     <?php
  include("includes/userlinks.php");
  ?>
    <div class="container mt-5">
        <h1 class="mb-4" style="margin-top: 60px;">Search Job PDF</h1>
        <form action="#" method="POST" class="form-inline">
            <div class="form-group mr-3">
                <label for="jobid" class="mr-2">Enter Job ID:</label>
                <input type="text" id="jobid" name="jobid" class="form-control" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Search</button>
        </form>
        <!-- show message -->
        <?php if (!empty($msg)) echo $msg; ?>
    </div>
</body>
</html>
