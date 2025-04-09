<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require("includes/phpmailer/src/PHPMailer.php");
require("includes/phpmailer/src/SMTP.php");
require("includes/phpmailer/src/Exception.php");
include("tcpdf/tcpdf.php");

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = 'includes/header.png';
        //$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->Image($image_file);
        // Set font
        //$this->SetFont('helvetica', 'B', 20);
        // Title
        //$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

}

include("includes/config.php");
include("includes/smtp.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
if (isset($_GET['CustID']) && $_GET['CustID'] != '') {
    $CustID = trim($_GET['CustID']);
    $CustCode = trim($_GET['CustCode']);
}
$condition = '';
$keyword = '';
if (isset($_POST['search'])) {
    $keyword = trim($_POST['keyword']);
    if ($keyword != '') {
        $condition = " AND Description LIKE '%" . $keyword . "%'";
    }
}
$msg = '';
if (isset($_POST['send_result'])) {
    $email = trim($_POST['email']);
    $keyword = trim($_POST['keyword']);
    if ($keyword != '') {
        $condition = " AND Description LIKE '%" . $keyword . "%'";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-danger" role="alert">Invalid email!!!</div>';
    } else {
        $qry1 = "select * from WCustomerTable where CustID=" . $CustID;
        $result1 = $conn->query($qry1);
        $row1 = $result1->fetch_object();
        $subject = "Price Result for " . $row1->CustName . " for " . date('F Y');
        $qry = "select * from WCustPriceTable where CustID=$CustID $condition ORDER BY Description";
        $result = $conn->query($qry);
        $pdftxt = '';
        //$row_cnt = ceil($result->num_rows / 4);
        $row_cnt = ceil($result->num_rows / 3);
        if ($result->num_rows > 0) {
            $i = 0;
            $tbl1 = '<table cellspacing="0" cellpadding="3" border="1"><tr><td>Model</td><td style="text-align:right">Frames Book Price</td><td style="text-align:right">Discount Price</td></tr>';
            $tbl2 = '<table cellspacing="0" cellpadding="3" border="1"><tr><td>Model</td><td style="text-align:right">Frames Book Price</td><td style="text-align:right">Discount Price</td></tr>';
            $tbl3 = '<table cellspacing="0" cellpadding="3" border="1"><tr><td>Model</td><td style="text-align:right">Frames Book Price</td><td style="text-align:right">Discount Price</td></tr>';
            $tbl4 = '<table cellspacing="0" cellpadding="3" border="1"><tr><td>Model</td><td style="text-align:right">Frames Book Price</td><td style="text-align:right">Discount Price</td></tr>';
            while ($row = $result->fetch_object()) {
                $font_size = '';
                if (strlen($row->Description) > 17) {
                    $font_size = 'style="font-size:7px;"';
                }
                if ($i < $row_cnt) {
                    $tbl1 .= '<tr><td ' . $font_size . '>' . $row->Description . '</td><td style="text-align:right">$' . number_format($row->price, 2) . '</td><td style="text-align:right">$' . number_format($row->DiscountPrice, 2) . '</td></tr>';
                } else if ($i >= $row_cnt && $i < ($row_cnt * 2)) {
                    $tbl2 .= '<tr><td ' . $font_size . '>' . $row->Description . '</td><td style="text-align:right">$' . number_format($row->price, 2) . '</td><td style="text-align:right">$' . number_format($row->DiscountPrice, 2) . '</td></tr>';
                } else if ($i >= ($row_cnt * 2) && $i < ($row_cnt * 3)) {
                    $tbl3 .= '<tr><td ' . $font_size . '>' . $row->Description . '</td><td style="text-align:right">$' . number_format($row->price, 2) . '</td><td style="text-align:right">$' . number_format($row->DiscountPrice, 2) . '</td></tr>';
                } /* else if ($i >= ($row_cnt * 3) && $i < ($row_cnt * 4)) {
                  $tbl4 .= '<tr><td ' . $font_size . '>' . $row->Description . '</td><td style="text-align:right">$' . number_format($row->price, 2) . '</td><td style="text-align:right">$' . number_format($row->DiscountPrice, 2) . '</td></tr>';
                  } */
                $i++;
            }
            $tbl1 .= '</table>';
            $tbl2 .= '</table>';
            $tbl3 .= '</table>';
            //$tbl4 .= '</table>';
            $pdftxt = '<table cellspacing="0" cellpadding="0" border="0">';
            //$pdftxt .= '<tr><td style="vertical-align: top;">' . $tbl1 . '</td><td style="vertical-align: top;">' . $tbl2 . '</td><td style="vertical-align: top;">' . $tbl3 . '</td><td style="vertical-align: top;">' . $tbl4 . '</td></tr>';
            $pdftxt .= '<tr><td style="vertical-align: top;">' . $tbl1 . '</td><td style="vertical-align: top;">' . $tbl2 . '</td><td style="vertical-align: top;">' . $tbl3 . '</td></tr>';
            $pdftxt .= '</table>';
        }
        //echo $pdftxt; exit;
        //create price PDF      
        //define('PDF_PAGE_FORMAT', 'LETTER');
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Capri');
        $pdf->SetTitle($CustCode);
        $pdf->SetSubject($subject);
        $pdf->SetKeywords($CustCode);
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(2, PDF_MARGIN_TOP + 15, 2, true);
        $pdf->SetHeaderMargin(2);
        //$pdf->SetPrintHeader(true);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->AddPage('L', 'A4');
        //$pdf->AddPage();
        $pdf->Write(0, $subject, '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 8);
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
        <div class="row"><div class="col-sm-12"><p class="text-info">(Description and Price columns are sortable. Click on them to sort)</p></div></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="price_table">                
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="cursor: pointer;">Description</th>
                        <th scope="col" style="cursor: pointer;">Price</th>
                        <th scope="col" style="cursor: pointer;">Discount Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select * from WCustPriceTable where CustID=$CustID $condition ORDER BY Description";
                    $result = $conn->query($qry);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_object()) {
                            $price += $row->price;
                            ?>
                            <tr>                                
                                <td><?php echo $row->Description; ?></td>                            
                                <td>$<?php echo number_format($row->price, 2); ?></td>
                                <td>$<?php echo number_format($row->DiscountPrice, 2); ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr><td colspan="2">No data!!!</td></tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
<?php
include("includes/footer.php");
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#price_table').DataTable({
            paging: false,
            searching: false,
            bInfo: false
        });
    });
</script>