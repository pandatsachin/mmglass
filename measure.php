<?php
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include("includes/config.php");
require("phpmailer/src/PHPMailer.php");
require("includes/smtp.php");
require("phpmailer/src/SMTP.php");
require("phpmailer/src/Exception.php");

if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
$msg = '';
$JobID = $_GET['JobID'];
if (isset($_POST['save'])) {
    $total_box = $_POST['total_box'];
    for ($i = 0; $i < $total_box; $i++) {
        $ScreenUnit = $_POST['ScreenUnit'][$i];
        if (empty($ScreenUnit)) {
            $ScreenUnit = trim($_POST['Other'][$i]);
        }
        $Color = trim($_POST['Color'][$i]);
        $Qty = trim($_POST['Qty'][$i]);
        $Width = trim($_POST['Width'][$i]);
        $Height = trim($_POST['Height'][$i]);
        $Thickness = trim($_POST['Thickness'][$i]);
        $GlassType = trim($_POST['GlassType'][$i]);
        $Room = trim($_POST['Room'][$i]);
        $Grid = isset($_POST['Grid'][$i]) ? 1 : 0;
        $GridColor = trim($_POST['GridColor'][$i]);
        $GridSize = trim($_POST['GridSize'][$i]);
        $User = $_SESSION['User']['TechName'];
        $Notes = trim($_POST['Notes'][$i]);
        $query = "insert into IUMeasurements(JobID,Qty,Width,Height,GlassType,Room,Grid,GridColor,GridSize,Thickness,ScreenUnit,Notes,Color,User)"
            . " values ('" . $JobID . "','" . $Qty . "','" . $Width . "','" . $Height . "','" . $GlassType . "',"
            . "'" . $Room . "','" . $Grid . "','" . $GridColor . "','" . $GridSize . "','" . $Thickness . "',"
            . "'" . $ScreenUnit . "','" . $Notes . "','" . $Color . "','" . $User . "')";
        $conn->query($query);
    }
    $mail = new PHPMailer(true);
    //Send email to admin  
    $to_email = 'server@mazelrealty.com';
    $Subject = 'ImportMeasureJob-' . $JobID;
    $Body = $JobID . ' has been done measured!';
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = SMTP_HOST;                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = SMTP_USERNAME;                     //SMTP username
        $mail->Password = SMTP_PASSWORD;                               //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom(FROM_EMAIL_ADDRESS, FROM_NAME);
        $mail->addAddress($to_email);     //Add a recipient    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $Subject;
        $mail->Body = $Body;
        $mail->send();
    } catch (Exception $e) {
        
    }
    $msg = '<div class="row"><div class="alert alert-success" role="alert">Data has been saved successfully!</div></div>';
}
?>
<?php
include("includes/header.php");
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> 
<link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet"> 
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<body style="align-items: baseline;">
    <?php
    include("includes/userlinks.php");
    ?>
    <main role="main" class="container jobs_page" style="margin-top: 35px;">
        <h1 style="text-align: center;">Measurement Form</h1>
        <?php echo $msg; ?>
        <form name="job_form" method="post" action="" class="job_form measure_job_form" id="job_form">      
            <div id="main_div">
                <div id="form_div">
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Select Screen/Unit</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="ScreenUnit[]">
                                <option value="">---Select---</option>
                                <option value="Screen">Screen</option>
                                <option value="Unit">Unit</option>
                            </select>
                        </div>
                    </div> 
                    <div class="form-group row">            
                        <label for="Done" class="col-sm-3 col-form-label">Other (if no Screen/Unit)</label>
                        <div class="col-sm-4">
                            <input name="Other[]" type="text"  class="form-control width">
                        </div>            
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Color</label>
                        <div class="col-sm-4">
                            <input name="Color[]" type="text"  class="form-control width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Quantity</label>
                        <div class="col-sm-4">
                            <input name="Qty[]" type="text"  class="form-control width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Width</label>
                        <div class="col-sm-4">
                            <input name="Width[]" type="text"  class="form-control width">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Height</label>
                        <div class="col-sm-4">
                            <input name="Height[]" type="text"  class="form-control width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Thickness</label>
                        <div class="col-sm-4">
                            <input name="Thickness[]" type="text"  class="form-control width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Glass Type</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="GlassType[]">
                                <option value="Lami">Lami</option>
                                <option value="Clear">Clear</option>
                                <option value="Frosted">Frosted</option>
                                <option value="Rough Wire">Rough Wire</option>
                                <option value="Clear Wire">Clear Wire</option>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Room</label>
                        <div class="col-sm-4">
                            <input name="Room[]" type="text"  class="form-control width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Grid</label>
                        <div class="col-sm-4">
                            <input type="checkbox" name="Grid[]" value="1"> Grid
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Grid Color</label>
                        <div class="col-sm-4">
                            <input name="GridColor[]" type="text"  class="form-control width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Grid Size</label>
                        <div class="col-sm-4">
                            <input name="GridSize[]" type="text"  class="form-control width">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="Done" class="col-sm-3 col-form-label">Notes</label>
                        <div class="col-sm-4">
                            <textarea class="form-control" name="Notes[]" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <span class="btn btn-success" id="addElementField"> Add +</span>
                    <input type="hidden" name="total_box" id="total_box" value="1">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3" name="save">Save</button>
        </form>
    </main>
</body>
<?php
include("includes/footer.php");
?>
<script type="text/javascript">
    $(document).on('click', '#addElementField', function () {
        var cur_val = parseInt($('#total_box').val());
        var form_html = $('#form_div').html();
        $('#main_div').append('<div><hr>' + form_html + '<span class="btn btn-danger remove_box"> Remove -</span></div>');
        $('#total_box').val(cur_val + 1);
    });
    $(document).on('click', '.remove_box', function () {
        $(this).parent('div').remove();
        var cur_val = parseInt($('#total_box').val());
        $('#total_box').val(cur_val - 1);
    });
</script>