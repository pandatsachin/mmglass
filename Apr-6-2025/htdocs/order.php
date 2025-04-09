<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include("includes/config.php");
include("includes/smtp.php");
require("includes/phpmailer/src/PHPMailer.php");
require("includes/phpmailer/src/SMTP.php");
require("includes/phpmailer/src/Exception.php");
include("tcpdf/tcpdf.php");
if(!isset($_SESSION['User'])) {
    header("Location:index.php");
}
$SalesmanID = $_SESSION['User']['SalesmanID'];
//Update/GET TempOrderTop Table
$qry = "select * from TempOrderTop where SalesmanID='" . $SalesmanID . "'";
$result = $conn->query($qry);
if($result->num_rows == 0) {
    $tqry = "insert into TempOrderTop set SalesmanID='" . $SalesmanID . "'";
    $conn->query($tqry);
}
$get_temp_qry = "select * from TempOrderTop where SalesmanID='" . $SalesmanID . "'";
$temp_res = $conn->query($get_temp_qry);
$temp_data = $temp_res->fetch_object();
$NewAccountCheck = "";
$DropShipAddressCheck = "";
$BillToCheck = "";
$NewDivStyle = "display: none; margin-bottom: 20px;";
if($temp_data->NewAccount == 1) {
    $NewAccountCheck = "checked";
    $NewDivStyle = "margin-bottom: 20px;";
}
if($temp_data->DropShipAddress == 1) {
    $DropShipAddressCheck = "checked";
}
if($temp_data->BillTo == 1) {
    $BillToCheck = "checked";
}
//////////////////////////////
$msg = '';
$ItemID = '';
$Color = '';
$Size = '';
if(isset($_GET['ItemID']) && $_GET['ItemID'] != '') {
    $ItemID = trim($_GET['ItemID']);
    $Color = trim($_GET['Color']);
    $Size = trim($_GET['Size']);
}
if(isset($_POST['submitorder'])) {
    //New customer
    $addNewCust = 0;
    if(isset($_POST['addNewCust']) && $_POST['addNewCust'] == 'on') {
        $addNewCust = 1;
    }
    $top_shipto = 0;
    if(isset($_POST['top_shipto']) && $_POST['top_shipto'] == 'on') {
        $top_shipto = 1;
    }
    $top_billto = 0;
    if(isset($_POST['top_billto']) && $_POST['top_billto'] == 'on') {
        $top_billto = 1;
    }
    $BuyerName = $conn->real_escape_string($_POST['BuyerName']);
    $APName = $conn->real_escape_string($_POST['APName']);
    $APStatementEmail = $conn->real_escape_string($_POST['APStatementEmail']);
    $BuyerPhone = $conn->real_escape_string($_POST['BuyerPhone']);
    $DBA = $conn->real_escape_string($_POST['DBA']);
    $BuyerStreet = $conn->real_escape_string($_POST['BuyerStreet']);
    $BuyerCity = $conn->real_escape_string($_POST['BuyerCity']);
    $BuyerState = $conn->real_escape_string($_POST['BuyerState']);
    $BuyerZip = $conn->real_escape_string($_POST['BuyerZip']);
    ////////
    $CustCode = $conn->real_escape_string($_POST['CustCode']);
    $company = $conn->real_escape_string($_POST['company']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $po = $conn->real_escape_string($_POST['po']);
    $ShipVia = $conn->real_escape_string($_POST['ShipVia']);
    $BillingTerms = $conn->real_escape_string($_POST['BillingTerms']);
    $DropShip = 0;
    if(isset($_POST['DropShip']) && $_POST['DropShip'] == 'on') {
        $DropShip = 1;
    }
    $lower_billto = 0;
    if(isset($_POST['lower_billto']) && $_POST['lower_billto'] == 'on') {
        $lower_billto = 1;
    }
    $ShipName = $conn->real_escape_string($_POST['ShipName']);
    $ShipAddress = $conn->real_escape_string($_POST['ShipAddress']);
    $ShipCity = $conn->real_escape_string($_POST['ShipCity']);
    $ShipState = $conn->real_escape_string($_POST['ShipState']);
    $ShipZip = $conn->real_escape_string($_POST['ShipZip']);
    $ShipPhone = $conn->real_escape_string($_POST['ShipPhone']);
    $SpecialInstructionsArr = array_filter($_POST['SpecialInstructions']);
    $SpecialInstructions = implode('|', $SpecialInstructionsArr);
    $NameOnCard = $conn->real_escape_string($_POST['NameOnCard']);
    $CardNumber = $conn->real_escape_string($_POST['CardNumber']);
    $ExpDate = $conn->real_escape_string($_POST['ExpDate']);
    $SecurityNumber = $conn->real_escape_string($_POST['SecurityNumber']);
    $CardStatementAddress = $conn->real_escape_string($_POST['CardStatementAddress']);
    $odr_qry = "INSERT INTO OrderTable set CustCode='" . $CustCode . "',company='" . $company . "',name='" . $name . "',"
        . "email='" . $email . "',address='" . $address . "',phone='" . $phone . "',po='" . $po . "',ShipVia='" . $ShipVia . "',"
        . "ShipName='" . $ShipName . "',ShipAddress='" . $ShipAddress . "',ShipCity='" . $ShipCity . "',"
        . "ShipState='" . $ShipState . "',ShipZip='" . $ShipZip . "',BillingTerms='" . $BillingTerms . "',ShipPhone='" . $ShipPhone . "',"
        . "SpecialInstructions='" . $SpecialInstructions . "',InvoiceNumber='" . $po . "',BuyerName='" . $name . "',APName='" . $APName . "',"
        . "APStatementEmail='" . $APStatementEmail . "',BGAccount='" . $BGAccount . "',BusinessName='" . $BusinessName . "',"
        . "DBA='" . $DBA . "',Notes='',CallerName='" . $_SESSION['User']['Name'] . "',OrderSource='',NameOnCard='" . $NameOnCard . "',"
        . "CardNumber='" . $CardNumber . "',ExpDate='" . $ExpDate . "',SecurityNumber='" . $SecurityNumber . "',"
        . "CardStatementAddress='" . $CardStatementAddress . "',NewAcct='" . $addNewCust . "',BuyerEmail='" . $email . "',"
        . "BuyerPhone='" . $BuyerPhone . "',ShipToAddress=$top_shipto,BillToAddress=$top_billto,DropShipAddress=$DropShip,"
        . "BillDropShip=$lower_billto,Dname='" . $BuyerName . "',Daddress='" . $BuyerStreet . "',Dcity='" . $BuyerCity . "',"
        . "Dstate='" . $BuyerState . "',Dzip='" . $BuyerZip . "',Dphone='" . $BuyerPhone . "'";
    $conn->query($odr_qry);
    $OrderID = $conn->insert_id;
    $pdftxt = '';
    $total_frames = 0;
    if(count($_POST['frame_style']) > 0 && $OrderID > 0) {
        $i = 0;
        $pdftxt .= '<table cellspacing="0" cellpadding="3" border="1">';
        $pdftxt .= '<tr><td>FRAME/STYLE</td><td>Color</td><td>Size</td><td>Qty</td><td>RX/TRAY#</td></tr>';
        for($i = 0; $i < count($_POST['frame_style']); $i++) {
            $ItemID = $_POST['frame_style'][$i];
            if($ItemID != '') {
                $ColorID = $_POST['frame_color'][$i];
                $SizeID = $_POST['frame_size'][$i];
                $Qty = $_POST['qty'][$i];
                $RX = $_POST['tray'][$i];
                if(!empty($ColorID) && !empty($SizeID) && !empty($Qty)) {
                    $detail_qry = "INSERT INTO OrderDetails(OrderID,ItemID,ColorID,SizeID,Qty,RX)"
                        . " values('" . $OrderID . "','" . $ItemID . "','" . $ColorID . "',"
                        . "'" . $SizeID . "','" . $Qty . "','" . $RX . "')";
                    $conn->query($detail_qry);
                    $pdftxt .= '<tr><td>' . getNameFromID($conn, $ItemID, 'frame') . '</td><td>' . getNameFromID($conn, $ColorID, 'color') . '</td><td>' . getNameFromID($conn, $SizeID, 'size') . '</td><td>' . $Qty . '</td><td>' . $RX . '</td></tr>';
                    $total_frames += $Qty;
                }
            }
        }
        $pdftxt .= '<tr><td><b>Total Frames</b></td><td></td><td></td><td><b>' . $total_frames . '</b></td><td></td></tr>';
        $pdftxt .= '</table>';
    }
    if($OrderID > 0) {
        $subject = "New order details";
        if(!empty($SpecialInstructions)) {
            $subject .= ' (Please see Note)';
        }
        //Delete temporder data
        $del_qry = "delete from TempOrderTop where SalesmanID=" . $SalesmanID;
        $conn->query($del_qry);
        $del_qry = "delete from TempOrder where SalesmanID=" . $SalesmanID;
        $conn->query($del_qry);
        //create price PDF        
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Capri');
        $pdf->SetTitle($OrderID);
        $pdf->SetSubject($subject);
        $pdf->SetKeywords($OrderID);
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
        if(@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->AddPage();
        $pdf->Write(0, $subject, '', 0, 'L', true, 0, false, false, 0);
        $pdf->SetFont('helvetica', '', 12);
        if($addNewCust == 1) {
            $pdf->CheckBox('newaccount', 5, true, array(), array(), 'OK');
        } else {
            $pdf->CheckBox('newaccount', 5, false, array(), array(), 'OK');
        }
        $pdf->Cell(35, 5, 'New Account');
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'OrderID:');
        $pdf->Cell(0, 10, $OrderID);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'CustCode:');
        $pdf->Cell(65, 10, $CustCode);
        $pdf->Cell(40, 10, 'BusinessName:');
        $pdf->Cell(0, 10, $DBA);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Company:');
        $pdf->Cell(65, 10, $company);
        $pdf->Cell(40, 10, 'DBA:');
        $pdf->Cell(0, 9, $DBA);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Name:');
        $pdf->Cell(0, 10, $name);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Email:');
        $pdf->Cell(0, 10, $email);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Address:');
        $pdf->Cell(0, 10, $address);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Phone:');
        $pdf->Cell(65, 10, $phone);
        $pdf->Cell(40, 10, 'CallerName:');
        $pdf->Cell(0, 10, $_SESSION['User']['Name']);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Po:');
        $pdf->Cell(65, 10, $po);
        $pdf->Cell(40, 10, 'OrderSource:');
        $pdf->Cell(0, 10, '');
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'ShipVia:');
        $pdf->Cell(65, 10, $ShipVia);
        $pdf->Cell(40, 10, 'NameOnCard:');
        $pdf->Cell(0, 10, $NameOnCard);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'InvoiceNumber:');
        $pdf->Cell(65, 10, $po);
        $pdf->Cell(40, 10, 'Card:');
        $pdf->Cell(0, 10, $CardNumber);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'BGAccount:');
        $pdf->Cell(65, 10, $CustCode);
        $pdf->Cell(40, 10, 'expDate:');
        $pdf->Cell(0, 10, $ExpDate . ' Sec:' . $SecurityNumber);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'BuyerName:');
        $pdf->Cell(65, 10, $BuyerName);
        $pdf->Cell(42, 10, 'CardAddress:');
        $pdf->Cell(0, 10, $CardStatementAddress);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'APName:');
        $pdf->Cell(65, 10, $APName);
        //$pdf->Cell(42, 10, 'ShipToAddress:');
        //$pdf->Cell(0, 10, $ShipAddress);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'APStatement:');
        $pdf->Cell(65, 10, '');
        //$pdf->Cell(41, 10, 'ShipName:');
        //$pdf->Cell(0, 10, $ShipName);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Bill To:');
        $pdf->Cell(65, 10, $DBA);
        $pdf->Cell(42, 10, 'ShipTo:');
        $pdf->Cell(0, 10, $ShipName);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Address:');
        $pdf->Cell(65, 10, $BuyerStreet);
        $pdf->Cell(42, 10, 'Address:');
        $pdf->Cell(0, 10, $ShipAddress);
        $pdf->Ln(10);

        $pdf->Cell(45, 10, '');
        $pdf->Cell(41, 10, $BuyerCity . ' ' . $BuyerState . ' ' . $BuyerZip);
        $pdf->Cell(55, 10, '');
        $pdf->Cell(0, 9, $ShipCity . ' ' . $ShipState . ' ' . $ShipZip);

        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Phone:');
        $pdf->Cell(65, 10, $BuyerPhone);
        $pdf->Cell(42, 10, 'ShipPhone:');
        $pdf->Cell(0, 10, $ShipPhone);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Billing Terms:');
        $pdf->Cell(65, 10, $BillingTerms);
        $pdf->Ln(10);
        $pdf->Cell(45, 10, 'Special Instructions:');
        foreach($SpecialInstructionsArr as $InsStr) {
            $pdf->Ln(5);
            $pdf->Cell(0, 10, $InsStr);
        }
        /* $pdf->Cell(0, 10, $SpecialInstructions);
          $pdf->Ln(10);
          $pdf->Cell(45, 10, 'Notes:');
          $pdf->Cell(0, 10, ''); */
        $pdf->AddPage();
        $tbl = <<<EOD
            $pdftxt
            EOD;
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $pdfpath = "/opt/bitnami/apache/htdocs/order_pdfs/$OrderID.pdf";
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
            $mail->addAddress($_SESSION['User']['SalesmanEmail']);     //Add a recipient     
            $mail->AddCC('orders@caprioptics.com');
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AddAttachment($pdfpath);
            $mail->send();
            $msg = '<div class="alert alert-success" role="alert">Order has been saved and sent!!!</div>';
            @unlink($pdfpath);
        } catch(Exception $e) {
            $msg = '<div class="alert alert-danger" role="alert">Something is wrong!!!</div>';
        }
    }
}

function getNameFromID($conn, $id, $type) {
    if($type == 'frame') {
        $qry = "select ItemCode from ItemTable where ItemID=" . $id;
        $result = $conn->query($qry);
        $row = $result->fetch_object();
        return $row->ItemCode;
    } else if($type == 'color') {
        $qry = "select ColorName from ColorTable where ColorID=" . $id;
        $result = $conn->query($qry);
        $row = $result->fetch_object();
        return $row->ColorName;
    } else if($type == 'size') {
        $qry = "select Size from SizeTable where SizeID=" . $id;
        $result = $conn->query($qry);
        $row = $result->fetch_object();
        return $row->Size;
    }
}
?>
<?php
include("includes/header.php");
?>
<link href="chosen/prism.css" rel="stylesheet">
<link href="chosen/chosen.css" rel="stylesheet">
<body style="align-items: baseline;">
    <?php
    include("includes/userlinks.php");
    ?>
    <main role="main" class="container" style="margin-top: 35px;"> 
        <div class = "page-header" style="text-align: center;"><h3>ORDER FORM</h3></div>
        <hr>
        <?php echo $msg; ?>
        <div class="row">
            <div class="col-sm">Capri Optics / Welling Eyewear<br>1421 38th Street. Brooklyn, NY 11218<br>718-633-2300 - 800-221-3544<br>www.caprioptics.com</div>
            <div class="col-sm" style="text-align: right;">Fax: 718-633-2304<br>Fax: 800-633-2319<br>Email: capri@caprioptics.com</div>                
        </div>
        <form name="order_form" method="post" action="" class="order_form" id="order_form">   
            <div class="form-row" style="margin: 0 0 10px 20px;">
                <div class="col">
                    <input <?php echo $NewAccountCheck; ?> name="addNewCust" class="form-check-input" type="checkbox" id="addNewCust">
                    <label class="form-check-label" for="gridCheck">New Account</label>
                </div>
                <div class="col" id="top_checkboxes" style="display: none;">
                    <!--<div class="form-row">
                      <div class="col" style="padding-left: 15px;">
                        <input name="top_shipto" class="form-check-input" type="checkbox" id="top_shipto">
                        <label class="form-check-label" for="gridCheck">Ship To</label>
                      </div>
                      <div class="col">
                        <input name="top_billto" class="form-check-input" type="checkbox" id="top_billto">
                        <label class="form-check-label" for="gridCheck">Bill To</label>
                      </div>
                    </div>-->
                </div>
            </div>
            <div id="new_cust_div" style="<?php echo $NewDivStyle; ?>">
                <div class="form-row">
                    <div class="col">
                        <input value="<?php echo $temp_data->BuyerName; ?>" name="BuyerName" id="BuyerName" type="text" class="form-control" placeholder="Buyer Name">
                    </div>
                    <div class="col">
                        <input value="<?php echo $temp_data->CompanyLegalName; ?>" name="DBA" id="DBA" type="text" class="form-control" placeholder="Company Legal Name / DBA">            
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <input value="<?php echo $temp_data->APName; ?>" name="APName" id="APName" type="text" class="form-control" placeholder="AP Name">
                    </div>
                    <div class="col">
                        <input value="<?php echo $temp_data->Street1; ?>" name="BuyerStreet" id="BuyerStreet" type="text" class="form-control" placeholder="Street">            
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <input value="<?php echo $temp_data->APEmail; ?>" name="APStatementEmail" id="APStatementEmail" type="text" class="form-control" placeholder="A/P Email">
                    </div>
                    <div class="col">
                        <input value="<?php echo $temp_data->City1; ?>" name="BuyerCity" id="BuyerCity" type="text" class="form-control" placeholder="City">            
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <input value="<?php echo $temp_data->Phone1; ?>" name="BuyerPhone" id="BuyerPhone" type="text" class="form-control" placeholder="Phone">
                    </div>
                    <div class="col">
                        <input value="<?php echo $temp_data->State1; ?>" name="BuyerState" id="BuyerState" type="text" class="form-control" placeholder="State">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">

                    </div>
                    <div class="col">
                        <input value="<?php echo $temp_data->Zip1; ?>" name="BuyerZip" id="BuyerZip" type="text" class="form-control" placeholder="Zip">
                    </div>
                </div>        
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->BGAccount; ?>" name="CustCode" id="CustCode" type="text" class="form-control" required="true" placeholder="Capri Optics / BG Account #">
                </div>
                <div class="col">
                    <div class="form-row">
                        <div class="col" style="padding-left: 25px;">
                            <input <?php echo $DropShipAddressCheck; ?> name="DropShip" class="form-check-input" type="checkbox" id="DropShip">
                            <label class="form-check-label" for="gridCheck">Drop Ship Address</label>
                        </div>
                        <div class="col">
                            <input <?php echo $BillToCheck; ?> name="lower_billto" class="form-check-input" type="checkbox" id="lower_billto">
                            <label class="form-check-label" for="gridCheck">Bill To</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->Company1; ?>" name="company" id="company" type="text" class="form-control" placeholder="Company">
                </div>
                <div class="col">
                    <input value="<?php echo $temp_data->Company2; ?>" name="ShipName" id="ShipName" type="text" class="form-control" placeholder="Company">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->BuyerName1; ?>" name="name" id="name" type="text" class="form-control" placeholder="Buyer Name">
                </div>
                <div class="col">
                    <input value="<?php echo $temp_data->Address2; ?>" name="ShipAddress" id="ShipAddress" type="text" class="form-control" placeholder="Address">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->BuyerEmailAddress; ?>" name="email" id="email" type="text" class="form-control" placeholder="Buyer Email Address">
                </div>
                <div class="col">
                    <input value="<?php echo $temp_data->City2; ?>" name="ShipCity" id="ShipCity" type="text" class="form-control" placeholder="City">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->Address1; ?>" name="address" id="address" type="text" class="form-control" placeholder="Address">
                </div>
                <div class="col">
                    <input value="<?php echo $temp_data->State2; ?>" name="ShipState" id="ShipState" type="text" class="form-control" placeholder="State">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->Phone2; ?>" name="phone" id="phone" type="text" class="form-control" placeholder="Phone">
                </div>
                <div class="col">
                    <input value="<?php echo $temp_data->Zip2; ?>" name="ShipZip" id="ShipZip" type="text" class="form-control" placeholder="Zip">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->PurchaseOrder; ?>" name="po" id="po" type="text" class="form-control" placeholder="Purchase Order #">
                </div>
                <div class="col">
                    <input value="<?php echo $temp_data->Phone3; ?>" name="ShipPhone" id="ShipPhone" type="text" class="form-control" placeholder="Phone">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <select name="ShipVia" id="ShipVia" class="form-control ShipVia">
                        <option value="" <?php
                        if($temp_data->ShipVia == '') {
                            echo "selected;";
                        }
                        ?>>Select Ship Via</option>
                        <option value="Cust Pick Up" <?php
                        if($temp_data->ShipVia == 'Cust Pick Up') {
                            echo "selected;";
                        }
                        ?>>Cust Pick Up</option>
                        <option value="First Class" <?php
                        if($temp_data->ShipVia == 'First Class') {
                            echo "selected;";
                        }
                        ?>>First Class</option>
                        <option value="Priority Mail" <?php
                        if($temp_data->ShipVia == 'Priority Mail') {
                            echo "selected;";
                        }
                        ?>>Priority Mail</option>
                        <option value="UPS Blue (2 Business days)" <?php
                        if($temp_data->ShipVia == 'UPS Blue (2 Business days)') {
                            echo "selected;";
                        }
                        ?>>UPS Blue (2 Business days)</option>
                        <option value="UPS Ground" <?php
                        if($temp_data->ShipVia == 'UPS Ground') {
                            echo "selected;";
                        }
                        ?>>UPS Ground</option>
                        <option value="UPS Orange (3 Business days)" <?php
                        if($temp_data->ShipVia == 'UPS Orange (3 Business days)') {
                            echo "selected;";
                        }
                        ?>>UPS Orange (3 Business days)</option>
                        <option value="UPS Red (1 Business day)" <?php
                        if($temp_data->ShipVia == 'UPS Red (1 Business day)') {
                            echo "selected;";
                        }
                        ?>>UPS Red (1 Business day)</option>
                    </select>
                </div>
                <div class="col">                    
                  <!--<textarea class="form-control" name="SpecialInstructions" id="SpecialInstructions" rows="3" placeholder="Special Instructions / Billing Terms"><?php echo $temp_data->SpecInstructions; ?></textarea>-->
                    <input type="text" class="form-control" name="SpecialInstructions[]" placeholder="Add Note">
                    <button type="button" class="btn btn-info" id="addNewNote">+ Add Next Note</button>
                </div>
            </div> 
            <div class="form-row">
                <div class="col">
                    <select name="BillingTerms" id="BillingTerms" class="form-control ShipVia">
                        <option value="" <?php
                        if(isset($temp_data->BillingTerms) && $temp_data->BillingTerms == '') {
                            echo "selected;";
                        }
                        ?>>Select Billing Terms</option>
                        <option value="Net 30 days" <?php
                        if(isset($temp_data->BillingTerms) && $temp_data->BillingTerms == 'Net 30 days') {
                            echo "selected;";
                        }
                        ?>>Net 30 days</option>
                        <option value="Net 30-60 days" <?php
                        if(isset($temp_data->BillingTerms) && $temp_data->BillingTerms == 'Net 30-60 days') {
                            echo "selected;";
                        }
                        ?>>Net 30-60 days</option>
                        <option value="Net 30-60-90 days" <?php
                        if(isset($temp_data->BillingTerms) && $temp_data->BillingTerms == 'Net 30-60-90 days') {
                            echo "selected;";
                        }
                        ?>>Net 30-60-90 days</option>
                        <option value="Net 30-60-90-120 days" <?php
                        if(isset($temp_data->BillingTerms) && $temp_data->BillingTerms == 'Net 30-60-90-120 days') {
                            echo "selected;";
                        }
                        ?>>Net 30-60-90-120 days</option>            
                    </select>
                </div>
                <div class="col">          
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->CCNumber; ?>" name="CardNumber" id="CardNumber" type="text" class="form-control" placeholder="Credit Card # No Spaces">          
                </div>
                <div class="col">
                    <input style="display:none;" value="<?php echo $temp_data->PricingLevel; ?>" name="NameOnCard" id="NameOnCard" type="text" class="form-control" placeholder="Pricing Level A,B,C, Other">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->ExpDate; ?>" name="ExpDate" id="ExpDate" type="text" class="form-control" placeholder="Expiration Date 00/00/0000">
                </div>
                <div class="col">
                    <input value="<?php echo $temp_data->ThreeDigitCode; ?>" name="SecurityNumber" id="SecurityNumber" type="text" class="form-control" placeholder="3 Digit Sec Code">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <input value="<?php echo $temp_data->CardStmtAddress; ?>" name="CardStatementAddress" id="CardStatementAddress" type="text" class="form-control" placeholder="Card Statement Address">
                </div>
            </div>
            <div class="form-row">
                <div class="col">
                    <lable>Select Price</lable>
                    <select name="SelectPrice" id="SelectPrice" class="form-control ShipVia">            
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                    </select>
                </div>
                <div class="col"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="order_details">                
                    <thead class="thead-light">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">FRAME/STYLE</th>
                            <th scope="col">COLOR</th>
                            <th scope="col">SIZE</th>
                            <th scope="col">Qty</th>
                            <th scope="col"></th>
                            <th scope="col">RX/TRAY#</th>                        
                        </tr>
                    </thead>
                    <tbody>
                        <!--<tr>
                            <td><a title="Delete" class="btn btn-danger" href="#" role="button">X</a></td>
                            <td><a class="btn btn-alert" href="#" role="button">X</a></td>
                            <td><a class="btn btn-alert" href="#" role="button">X</a></td>
                            <td><a class="btn btn-alert" href="#" role="button">X</a></td>
                            <td><a class="btn btn-alert" href="#" role="button">X</a></td>
                            <td><a class="btn btn-alert" href="#" role="button">X</a></td>
                            <td><a class="btn btn-alert" href="#" role="button">X</a></td>
                        </tr>-->                        
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-info" id="addNewRow">+ Add</button>
            <button type="submit" class="btn btn-primary" name="submitorder" id="submitorder">Submit Order</button>
        </form>
    </main>
</body>
<?php
include("includes/footer.php");
?>
<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="chosen/prism.js" type="text/javascript" charset="utf-8"></script>
<script src="chosen/init.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    var ItemID = '<?php echo $ItemID; ?>';
    var Color = '<?php echo $Color; ?>';
    var Size = '<?php echo $Size; ?>';

    $("#order_form").submit(function () {
      $("#submitorder").hide();
    });
    $('#order_form').bind('keypress keydown keyup', function (e) {
      if (e.keyCode == 13) {
        e.preventDefault();
      }
    });
    function calculateQty() {
      if ($('.final_total').length > 0) {
        $('.final_total').remove();
      }
      var tot_qty = 0;
      var tot_price = 0;
      var tot_qty_box = $('.frame_qty').length;
      var i = 0;
      var selected_price = $('#SelectPrice').val();
      $(".frame_qty").each(function () {
        if ($(this).val() != '') {
          var cur_qty = parseInt($(this).val());
          var fstd = $(this).closest('td');
          var curprice = fstd.prev('td').prev('td').prev('td').find('select option:selected').attr(selected_price);
          tot_price = tot_price + (cur_qty * curprice);
          tot_qty = parseInt(tot_qty) + cur_qty;
        }
        i++;
        if (i === tot_qty_box) {
          $(this).after(' <p class="final_total"><b>Total: ' + tot_qty + ', Price: $' + tot_price.toFixed(2) + '</b></p>');
        }
      });
    }
    function updateTempOrder() {
      var SalesmanID = '<?php echo $_SESSION['User']['SalesmanID']; ?>';
      $('#order_details tbody tr').each((tr_idx, tr) => {
        var ItemDesc = '';
        var ColorName = '';
        var Size = '';
        var Qty = '';
        $(tr).children('td').each((td_idx, td) => {
          if (td_idx == 1) {
            ItemDesc = $(td).find('select option:selected').text();
          }
          if (td_idx == 2) {
            ColorName = $(td).find('select option:selected').text();
          }
          if (td_idx == 3) {
            Size = $(td).find('select option:selected').text();
          }
          if (td_idx == 4) {
            Qty = $(td).find('input').val();
          }
        });
        if (ItemDesc !== '' && ColorName !== '' && Size !== '' && Qty !== '' && ColorName !== '---Select---' && Size !== '---Select---') {
          $.ajax({
            type: "POST",
            url: "ajax.php",
            dataType: "JSON",
            data: {Action: 'UpdateTempOrder', ItemDesc: ItemDesc, ColorName: ColorName, Size: Size, Qty: Qty, SalesmanID: SalesmanID},
            success: function (data) {
              if (data.msg == 'success') {
                //$(this).closest('tr').remove();
                //alert("Item has been removed from the cart!!!");
              }
            }
          });
        }
      });
    }
    function deleteTempOrder(ItemDesc, ColorName, Size) {
      var SalesmanID = '<?php echo $_SESSION['User']['SalesmanID']; ?>';
      if (ItemDesc !== '' && ColorName !== '' && Size !== '') {
        $.ajax({
          type: "POST",
          url: "ajax.php",
          dataType: "JSON",
          data: {Action: 'DeleteTempOrder', ItemDesc: ItemDesc, ColorName: ColorName, Size: Size, SalesmanID: SalesmanID},
          success: function (data) {
            if (data.msg == 'success') {
              //$(this).closest('tr').remove();
              //alert("Item has been removed from the cart!!!");
            }
          }
        });
      }
    }
    function addrows(n) {
      for (var i = 0; i < n; i++) {
        $.ajax({
          type: "GET",
          url: "tablerows.php",
          dataType: "JSON",
          success: function (data) {
            if (data.msg == 'success') {
              $("#order_details tbody").append(data.data);
              $.getScript("chosen/init.js", function (data, textStatus, jqxhr) {});
            }
          }
        });
      }
    }
    function addDataRows() {
      console.log("here");
      $.ajax({
        type: "POST",
        url: "tablerows.php",
        dataType: "JSON",
        data: {SalesmanID: '<?php echo $SalesmanID; ?>'},
        success: function (data) {
          console.log(data);
          if (data.msg == 'success') {
            $("#order_details tbody").append(data.data);
            addrows(6);
          }
        }
      });
    }
    addDataRows();
    setTimeout(calculateQty, 1000);
    $(document).on('change', '.frame_style', function () {
      var itemID = $(this).val();
      var fstd = $(this).closest('td');
      $.ajax({
        type: "POST",
        url: "tablerows.php",
        dataType: "JSON",
        data: {itemID: itemID, Color: Color, Size: Size},
        beforeSend: function () {
          fstd.next('td').html('loading...');
          fstd.next('td').next('td').html('loading...');
          fstd.next('td').next('td').next('td').html('loading...');
          fstd.next('td').next('td').next('td').next('td').next('td').html('loading...');
        },
        success: function (data) {
          if (data.msg == 'success') {
            fstd.next('td').html(data.data);
            fstd.next('td').next('td').html(data.data1);
            fstd.next('td').next('td').next('td').html(data.data2);
            fstd.next('td').next('td').next('td').next('td').next('td').html(data.data3);
          }
        }
      });
    });
    $(document).on('click', '#addNewRow', function () {
      addrows(1);
    });
    $(document).on('click', '#addNewNote', function () {
      $(this).before('<input type="text" class="form-control" name="SpecialInstructions[]" placeholder="Add Note">');
    });
    $(document).on('click', '.del-row', function (e) {
      e.preventDefault();
      var fstd = $(this).closest('td');
      var ItemDesc = fstd.next('td').find('select option:selected').text();
      var ColorName = fstd.next('td').next('td').find('select option:selected').text();
      var Size = fstd.next('td').next('td').next('td').find('select option:selected').text();
      deleteTempOrder(ItemDesc, ColorName, Size);
      $(this).closest('tr').remove();
      calculateQty();
    });
    $(document).on('click', '.add-each', function (e) {
      e.preventDefault();
      var fstd = $(this).closest('td');
      var fstr = $(this).closest('tr');
      var next_row = fstr.next('tr').length;
      var itemID = fstd.prev('td').prev('td').prev('td').prev('td').find("select").val();
      var colorID = fstd.prev('td').prev('td').prev('td').find("select").val();
      var sizeID = fstd.prev('td').prev('td').find("select").val();
      var qty = fstd.prev('td').find("input").val();
      var tray = fstd.next('td').find("input").val();
      if (itemID == '') {
        alert("Please select an item!");
        return false;
      }
      /*if (colorID == '' && sizeID == '') {
       alert("Please a color or a size!");
       return false;
       }*/
      if (qty == '' || qty == 0) {
        alert("Please enter qty!");
        return false;
      } else if (itemID != '' && qty != '') {
        $.ajax({
          type: "POST",
          url: "tablerows.php",
          dataType: "JSON",
          data: {eachitemID: itemID, qty: qty, tray: tray, colorID: colorID, sizeID: sizeID},
          success: function (data) {
            if (data.msg == 'success') {
              fstr.after(data.data);
              fstr.remove();
              if (next_row == 0) {
                addrows(1);
              }
              calculateQty();
              updateTempOrder();
            }
          }
        });
      }
    });
    $(document).on('focusout', '.frame_qty', function (e) {
      e.preventDefault();
      //calculateQty();
      setTimeout(calculateQty, 1000);
      updateTempOrder();
    });
    $(document).on('change', '#SelectPrice', function (e) {
      e.preventDefault();
      calculateQty();
    });
    $(document).on('focusout', 'input[name="CustCode"]', function (e) {
      e.preventDefault();
      var CustCode = $(this).val();
      if (CustCode != '') {
        $.ajax({
          type: "POST",
          url: "tablerows.php",
          dataType: "JSON",
          data: {CustCode: CustCode},
          success: function (data) {
            if (data.msg == 'success') {
              $('input[name="name"]').val(data.CustName);
              $('input[name="address"]').val(data.CustAddress);
              $('input[name="phone"]').val(data.CustPhone);
              $('input[name="email"]').val(data.email);
            }
          }
        });
      }
    });
    //$('.autoSelect').combobox();
    $(document).on('click', '#addNewCust', function () {
      if ($(this).is(':checked')) {
        $('#new_cust_div').show(500);
        $('#top_checkboxes').show(500);
        saveTopData();
      } else {
        $('#new_cust_div').hide(500);
        $('#top_checkboxes').hide(500);
        saveTopData();
      }
    });
    $(document).on('keyup', '.frame_qty', function (e) {
      e.preventDefault();
      var fstr = $(this).closest('tr');
      var next_row = fstr.next('tr').length;
      if (next_row == 0) {
        addrows(1);
      }
    });
    $(window).on('load', function () {
      if (ItemID != '') {
        setTimeout(function () {
          var firstsel = $('.frame_style').first();
          firstsel.val(ItemID).change();
        }, 1000);
      }
    });
    $(document).on('click', '#DropShip,#lower_billto', function () {
      saveTopData();
    });
    $(document).on('focusout', '#BuyerName,#APName,#APStatementEmail,#BuyerPhone,#DBA,#BuyerStreet,#BuyerCity,#BuyerState,#BuyerZip,#CustCode,#company,#name,#email,#address,#phone,#po,#ShipVia,#BillingTerms,#ShipName,#ShipAddress,#ShipCity,#ShipState,#ShipZip,#ShipPhone,#SpecialInstructions,#NameOnCard,#CardNumber,#ExpDate,#SecurityNumber,#CardStatementAddress', function () {
      saveTopData();
    });
    function saveTopData() {
      var NewAccount = 0;
      if ($('#addNewCust').is(':checked')) {
        NewAccount = 1;
      } else {
        NewAccount = 0;
      }
      var BuyerName = $('#BuyerName').val();
      var APName = $('#APName').val();
      var APEmail = $('#APStatementEmail').val();
      var Phone1 = $('#BuyerPhone').val();
      var CompanyLegalName = $('#DBA').val();
      var Street1 = $('#BuyerStreet').val();
      var City1 = $('#BuyerCity').val();
      var State1 = $('#BuyerState').val();
      var Zip1 = $('#BuyerZip').val();
      var BGAccount = $('#CustCode').val();
      var Company1 = $('#company').val();
      var BuyerName1 = $('#name').val();
      var BuyerEmailAddress = $('#email').val();
      var Address1 = $('#address').val();
      var Phone2 = $('#phone').val();
      var PurchaseOrder = $('#po').val();
      var ShipVia = $('#ShipVia').val();
      var BillingTerms = $('#BillingTerms').val();
      var DropShipAddress = 0;
      if ($('#DropShip').is(':checked')) {
        DropShipAddress = 1;
      } else {
        DropShipAddress = 0;
      }
      var BillTo = 0;
      if ($('#lower_billto').is(':checked')) {
        BillTo = 1;
      } else {
        BillTo = 0;
      }
      var Company2 = $('#ShipName').val();
      var Address2 = $('#ShipAddress').val();
      var City2 = $('#ShipCity').val();
      var State2 = $('#ShipState').val();
      var Zip2 = $('#ShipZip').val();
      var Phone3 = $('#ShipPhone').val();
      var SpecInstructions = $('#SpecialInstructions').val();
      var PricingLevel = $('#NameOnCard').val();
      var CCNumber = $('#CardNumber').val();
      var ExpDate = $('#ExpDate').val();
      var ThreeDigitCode = $('#SecurityNumber').val();
      var CardStmtAddress = $('#CardStatementAddress').val();
      var SalesmanID = '<?php echo $SalesmanID; ?>';
      $.ajax({
        type: "POST",
        url: "ajax.php",
        dataType: "JSON",
        data: {Action: 'SaveTopData', NewAccount: NewAccount, BuyerName: BuyerName, APName: APName, APEmail: APEmail,
          Phone1: Phone1, CompanyLegalName: CompanyLegalName, Street1: Street1, City1: City1, State1: State1, Zip1: Zip1,
          BGAccount: BGAccount, Company1: Company1, BuyerName1: BuyerName1, BuyerEmailAddress: BuyerEmailAddress,
          Address1: Address1, Phone2: Phone2, PurchaseOrder: PurchaseOrder, ShipVia: ShipVia, BillingTerms: BillingTerms, DropShipAddress: DropShipAddress,
          BillTo: BillTo, Company2: Company2, Address2: Address2, City2: City2, State2: State2, Zip2: Zip2,
          Phone3: Phone3, SpecInstructions: SpecInstructions, PricingLevel: PricingLevel, CCNumber: CCNumber,
          ExpDate: ExpDate, ThreeDigitCode: ThreeDigitCode, CardStmtAddress: CardStmtAddress, SalesmanID: '<?php echo $SalesmanID; ?>'},
        success: function (data) {
          if (data.msg == 'success') {

          }
        }
      });
    }
</script>