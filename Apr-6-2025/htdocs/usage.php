<?php

//ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require("includes/phpmailer/src/PHPMailer.php");
require("includes/phpmailer/src/SMTP.php");
require("includes/phpmailer/src/Exception.php");
include("tcpdf/tcpdf.php");

include("includes/config.php");
include("includes/smtp.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
if (isset($_GET['CustID']) && $_GET['CustID'] != '') {
    $CustID = trim($_GET['CustID']);
    $CustCode = trim($_GET['CustCode']);
}
$msg = '';
$months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
$next_month = date('m', strtotime("next month"));
for ($i = $next_month; $i <= 12; $i++) {
    $month_arr[] = $i;
}
for ($i = 1; $i < $next_month; $i++) {
    $month_arr[] = $i;
}
$condition = '';
$keyword = '';
if (isset($_POST['search'])) {
    $keyword = trim($_POST['keyword']);
    if ($keyword != '') {
        $condition = " AND Description LIKE '%" . $keyword . "%'";
    }
}
if (isset($_POST['send_result'])) {
    $email = trim($_POST['email']);
    $keyword = trim($_POST['keyword']);
    if ($keyword != '') {
        $condition = " AND Description LIKE '%" . $keyword . "%'";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-danger" role="alert">Invalid email!!!</div>';
    } else {
        $subject = "Usage Result for " . $CustCode;
        $qry = "select * from WUsageTable where custID=$CustID $condition ORDER BY Description";
        $result = $conn->query($qry);
        $pdftxt = '';
        if ($result->num_rows > 0) {
            $pdftxt = '<table cellspacing="0" cellpadding="3" border="1">';
            $pdftxt .= '<tr><td width="12%">Description</td>';
            $pdftxt .= '<td>' . $months[$month_arr[0]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[1]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[2]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[3]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[4]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[5]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[6]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[7]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[8]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[9]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[10]] . '</td>';
            $pdftxt .= '<td>' . $months[$month_arr[11]] . '</td>';
            $pdftxt .= '<td>Total</td>';
            $pdftxt .= '</tr>';
            while ($row = $result->fetch_assoc()) {
                $price = intval($row[1]) + intval($row[2]) + intval($row[3]) + intval($row[4]) + intval($row[5]) + intval($row[6]);
                $price += intval($row[7]) + intval($row[8]) + intval($row[9]) + intval($row[10]) + intval($row[11]) + intval($row[12]);
                $pdftxt .= '<tr><td>' . $row['Description'] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[0]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[1]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[2]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[3]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[4]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[5]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[6]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[7]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[8]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[9]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[10]] . '</td>';
                $pdftxt .= '<td>' . $row[$month_arr[11]] . '</td>';
                $pdftxt .= '<td>' . $price . '</td>';
                $pdftxt .= '</tr>';
            }
            $pdftxt .= '</table>';
        }
        //create price PDF        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Capri');
        $pdf->SetTitle($CustCode);
        $pdf->SetSubject($subject);
        $pdf->SetKeywords($CustCode);
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 048', PDF_HEADER_STRING);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetPrintHeader(false);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->AddPage();
        $pdf->Write(0, $subject, '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 10);
        $tbl = <<<EOD
            $pdftxt
            EOD;
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdfpath = "/home/emerestweb/caprisalesforce.com/$CustCode.pdf";
        $pdf->Output($pdfpath, 'F');
        /////////////////////////
        $mail = new PHPMailer(true);
        $From_Address = "From: " . FROM_EMAIL_ADDRESS;
        $body = 'Please find the attached PDF';
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = SMTP_USERNAME;                     //SMTP username
            $mail->Password = SMTP_PASSWORD;                               //SMTP password
            //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $mail->setFrom(FROM_EMAIL_ADDRESS, FROM_NAME);
            $mail->addAddress($email);     //Add a recipient            
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AddAttachment($pdfpath);
            $mail->send();
            $msg = '<div class="alert alert-success" role="alert">Result has been sent!!!</div>';
            @unlink($pdfpath);
        } catch (Exception $e) {
            $msg = '<div class="alert alert-danger" role="alert">Something is wrong!!!</div>';
        }
    }
}
?>
<?php
include("includes/header.php");
?>
<body style="align-items: baseline;">
    <?php
    include("includes/userlinks.php");
    ?>
    <main role="main" class="container" style="margin-top: 35px;">
        <div class="row"><?php echo $msg; ?></div>
        <div class="row">
            <div class="col-sm-12">
                <form name="sendresult" method="post" action="" class="form-inline">
                    <div class="row">
                        <div class="col">
                            <div class="form-group" style="width: 25rem;">
                                <label for="Keyword" class="sr-only">Keyword</label>
                                <input value="<?php echo $keyword; ?>" type="text" class="form-control" name="keyword" id="keyword" placeholder="Enter keyword to search style">
                                <button style="margin-left: 10px; margin-bottom: 0 !important;" type="submit" class="btn btn-primary mb-2" name="search">Search</button>
                            </div>                            
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="keyword" class="sr-only">Email</label>
                                <input value="" type="text" class="form-control" name="email" id="email" placeholder="Email to send result">
                                <button style="margin-left: 10px; margin-bottom: 0 !important;" type="submit" class="btn btn-primary mb-2" name="send_result">Send</button>
                            </div>                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row"><div class="col-sm-12"><h2><?php echo $CustCode; ?></h2></div></div>
        <div class="clearfix"></div>
        <div class="row"><div class="col-sm-12"><p class="text-info">(Description and Total columns are sortable. Click on them to sort)</p></div></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="tbl_usage">                
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="cursor: pointer;">Description</th>
                        <th scope="col"><?php echo $months[$month_arr[0]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[1]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[2]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[3]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[4]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[5]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[6]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[7]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[8]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[9]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[10]]; ?></th>
                        <th scope="col"><?php echo $months[$month_arr[11]]; ?></th>
                        <th scope="col" style="cursor: pointer;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select * from WUsageTable where custID=$CustID $condition ORDER BY Description";
                    $result = $conn->query($qry);
//                    $subtotal = 0;
//                    $oct = 0;
                    $flag = false;
                    if ($result->num_rows > 0) {
                        $flag = true;
                        $tot_arr[1] = 0;
                        $tot_arr[2] = 0;
                        $tot_arr[3] = 0;
                        $tot_arr[4] = 0;
                        $tot_arr[5] = 0;
                        $tot_arr[6] = 0;
                        $tot_arr[7] = 0;
                        $tot_arr[8] = 0;
                        $tot_arr[9] = 0;
                        $tot_arr[10] = 0;
                        $tot_arr[11] = 0;
                        $tot_arr[12] = 0;
                        while ($row = $result->fetch_assoc()) {
                            $price = intval($row[1]) + intval($row[2]) + intval($row[3]) + intval($row[4]) + intval($row[5]) + intval($row[6]);
                            $price += intval($row[7]) + intval($row[8]) + intval($row[9]) + intval($row[10]) + intval($row[11]) + intval($row[12]);
                            $subtotal += $price;
                            $tot_arr[1] += $row[$month_arr[0]];
                            $tot_arr[2] += $row[$month_arr[1]];
                            $tot_arr[3] += $row[$month_arr[2]];
                            $tot_arr[4] += $row[$month_arr[3]];
                            $tot_arr[5] += $row[$month_arr[4]];
                            $tot_arr[6] += $row[$month_arr[5]];
                            $tot_arr[7] += $row[$month_arr[6]];
                            $tot_arr[8] += $row[$month_arr[7]];
                            $tot_arr[9] += $row[$month_arr[8]];
                            $tot_arr[10] += $row[$month_arr[9]];
                            $tot_arr[11] += $row[$month_arr[10]];
                            $tot_arr[12] += $row[$month_arr[11]];
                            ?>
                            <tr>                                
                                <td><a href="usage_desc.php?CollectionID=<?php echo $row['CollectionID']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row['Description']; ?></a></td>                            
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[0]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[0]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[1]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[1]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[2]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[2]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[3]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[3]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[4]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[4]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[5]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[5]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[6]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[6]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[7]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[7]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[8]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[8]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[9]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[9]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[10]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[10]]; ?></a></td>
                                <td><a href="usage_details.php?Month=<?php echo $month_arr[11]; ?>&Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $row[$month_arr[11]]; ?></a></td>
                                <td><a href="usage_tot_details.php?Description=<?php echo $row['Description']; ?>&CustID=<?php echo $row['custid']; ?>"><?php echo $price; ?></a></td>
                            </tr>

                            <?php
                        }
                    } else {
                        ?>
                        <tr><td colspan="14">No data!!!</td></tr>
                        <?php
                    }
                    ?>
                </tbody>
                <?php
                if ($flag) {
                    ?>
                    <tr id="datatable-row" class="odd font-weight-bold">
                        <td colspan="1">Total:</td>
                        <td colspan="1"> <?php echo $tot_arr[1]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[2]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[3]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[4]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[5]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[6]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[7]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[8]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[9]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[10]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[11]; ?></td>
                        <td colspan="1"> <?php echo $tot_arr[12]; ?></td>
                        <td colspan="1"> <?php echo $subtotal; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </main>
</body>
<?php
include("includes/footer.php");
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#tbl_usage').DataTable({
            paging: false,
            searching: false,
            bInfo: false,
            "columns": [
                {
                    "sortable": true
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": true
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": false
                },
                {
                    "sortable": true
                }
            ]
        });
    });
</script>