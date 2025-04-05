<?php
 if (!empty($additional_repairs)) {
                $addrepair_key = $i == 0 ? "additional_repairs" : "additional_repairs" . $i;
                $RedTip_c = $row->RedTip_c;
                $UltraLift_c = $row->UltraLift_c;
                $BlackTip_c = $row->BlackTip_c;
                $BlockTackle_c = $row->BlockTackle_c;
                $Locks = $row->Locks[$i];
                $Shoes = $row->Shoes[$i];
                $TiltLatch = $row->TiltLatch[$i];
                $Caps = $row->Caps[$i];
            } else {
                $repair_key = $i == 0 ? "repairs" : "repairs" . $i;
                 $RedTip_c = $row->RedTip_c;
                $UltraLift_c = $row->UltraLift_c;
                $BlackTip_c = $row->BlackTip_c;
                $BlockTackle_c = $row->BlockTackle_c;
                $Locks = $row->Locks[$i];
                $Shoes = $row->Shoes[$i];
                $TiltLatch = $row->TiltLatch[$i];
                $Caps = $row->Caps[$i];
            }
//////////add_repairs end

            $RegRepairs = $row->RegRepairs;
            $UltraLift = $row->UltraLift;
            $BlackTip = $row->BlackTip;
            $BlockTackle = $row->BlockTackle;
           if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle))) {
            $BlackTip = $row->BlackTip;
            }

                $WebID = $jobID;
$qry = "select * from JobTable where WebID=" . $WebID;
$result = $conn->query($qry);
$row = $result->fetch_object();
echo $JobID = $row->JobID;
echo $Address = $row->Address;
echo $Apt = $row->Apt;
echo $City = $row->City;
            
            
            
            
            
include("includes/config.php");
//if (!isset($_SESSION['User'])) {
//    header("Location:index.php");
//}
$msg = ""; 
 $id = $row->ID;
        $jobID = $row->JobID;
        $room = $row->Room;
        $floor = $row->Floor;
        $top = $row->Top;
        $bottom = $row->Bottom;
        $regGlass = $row->RegGlass;
        $doublePaneRegular = $row->DoublePaneRegular;
        $largeGlass = $row->LargeGlass;
        $doublePaneLarge = $row->DoublePaneLarge;
        $redTip = $row->RedTip;
        $redTip_c = $row->RedTip_c;
        $ultraLift = $row->UltraLift;
        $ultraLift_c = $row->UltraLift_c;
        $blackTip = $row->BlackTip;
        $blackTip_c = $row->BlackTip_c;
        $blockTackle = $row->BlockTackle;
        $blockTackle_c = $row->BlockTackle_c;
        $lami = $row->LAMI;
        $plexi = $row->Plexi;
        $roughWire = $row->RoughWire;
        $roughWireClear = $row->RoughWireClear;
        $polyWire = $row->PolyWire;
        $polyWireClear = $row->PolyWireClear;
        $height = $row->Height;
        $width = $row->Width;
        $insulatedUnit = $row->InsulatedUnit;
        $newScreen = $row->NewScreen;
        $screenRepair = $row->ScreenRepair;
        $moldings = $row->Moldings;
        $windowGuards = $row->WindowGuards;
        $capping = $row->Capping;
        $locks = $row->Locks;
        $shoes = $row->Shoes;
        $tiltLatch = $row->TiltLatch;
        $pivot = $row->Pivot;
        $caps = $row->Caps;
        $notes = $row->Notes;
        $systemID = $row->systemID;
        $additional = $row->additional;
        $gtQty = $row->GTQty;
        $status = $row->Status;
if (isset($_POST['submit'])) {
    $jobID = $conn->real_escape_string($_POST['jobid']);
    $sql = "SELECT JobID FROM JobDetailsTable WHERE JobID = '$jobID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $directory = "D:/xampp/htdocs/mmglass/JobDetailsPDFs/";
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
