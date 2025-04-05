<?php
error_reporting(0);

//ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//define('K_PATH_MAIN','/home/bitnami/htdocs/mmglass/upload/');
//define('TCPDF_FONTS','/home/bitnami/htdocs/mmglass/tcpdf/fonts/');
include("includes/config.php");
include("tcpdf/tcpdf.php");
require("phpmailer/src/PHPMailer.php");
require("includes/smtp.php");
require("phpmailer/src/SMTP.php");
require("phpmailer/src/Exception.php");

if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
$WebID = $_GET['JobID'];
$qry = "select * from JobTable where WebID=" . $WebID;
$result = $conn->query($qry);
$row = $result->fetch_object();
$JobID = $row->JobID;
$Address = $row->Address;
$Apt = $row->Apt;
$City = $row->City;
$_SESSION['JobID'] = $JobID;
$msg = "";

//PDF class
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = "images/MMGlasslogo.jpg";
        $this->Image($image_file, 10, 10, 32, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        //        $this->Cell(0, 0, $_SESSION['JobID'], 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $text = 'M&M GLASS';
        $this->writeHTMLCell($w = 0, $h = 0, $x = 80, $y = '', $text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $this->SetFont('helvetica', 'B', 11);
        $text = 'P.O. Box 190330';
        $this->writeHTMLCell($w = 0, $h = 0, $x = 88, $y = '', $text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $this->SetFont('helvetica', 'B', 11);
        $text = 'Brooklyn, NY 11219';
        $this->writeHTMLCell($w = 0, $h = 0, $x = 86, $y = '', $text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $this->SetFont('helvetica', 'B', 11);
        $text = 'Tel:718-804-3024&nbsp;&nbsp;&nbsp;www.mmglassnyc.com<br>';
        $this->writeHTMLCell($w = 0, $h = 0, $x = 70, $y = '', $text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $text = '<hr><br>';
        $this->writeHTMLCell($w = 0, $h = 0, $x = 10, $y = '', $text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

//////////////////////
if (isset($_POST['save'])) {
    $tennant_email = trim($_POST['tennant_email']);
    //Signature upload
    if (isset($_POST['main_sign']) && trim($_POST['main_sign']) != '') {
        $folderPath = "/home/bitnami/htdocs/mmglass/upload/";
        $image_parts = explode(";base64,", $_POST['main_sign']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.' . $image_type;
        file_put_contents($file, $image_base64);
    } else {
        $file = '';
    }
    if (isset($_POST['main_techsign']) && trim($_POST['main_techsign']) != '') {
        $folderPath = "/home/bitnami/htdocs/mmglass/upload/";
        $image_parts = explode(";base64,", $_POST['main_techsign']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $techfile = $folderPath . uniqid() . '.' . $image_type;
        file_put_contents($techfile, $image_base64);
    } else {
        $techfile = '';
    }
    //Upload images
    if (isset($_FILES["attach1"]["name"]) && $_FILES["attach1"]["name"] != '') {
        $filename = $_FILES["attach1"]["name"];
        $tempname = $_FILES["attach1"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach2"]["name"]) && $_FILES["attach2"]["name"] != '') {
        $filename = $_FILES["attach2"]["name"];
        $tempname = $_FILES["attach2"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach3"]["name"]) && $_FILES["attach3"]["name"] != '') {
        $filename = $_FILES["attach3"]["name"];
        $tempname = $_FILES["attach3"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach4"]["name"]) && $_FILES["attach4"]["name"] != '') {
        $filename = $_FILES["attach4"]["name"];
        $tempname = $_FILES["attach4"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach5"]["name"]) && $_FILES["attach5"]["name"] != '') {
        $filename = $_FILES["attach5"]["name"];
        $tempname = $_FILES["attach5"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach6"]["name"]) && $_FILES["attach6"]["name"] != '') {
        $filename = $_FILES["attach6"]["name"];
        $tempname = $_FILES["attach6"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    //Save data into DB table
    $total_box = $_POST['total_box'];
    $pdf_text_arr = array();
    $pdf_text_arr1 = array();
    for ($i = 0; $i < $total_box; $i++) {
        $Room = '';
        $Floor = $Top = $Bottom = $RegGlass = $additional_repairs = $LargeGlass = $DoublePaneLarge = $DoublePaneRegular = $RegRepairs = $UltraLift = 0;
        $BlackTip = $BlockTackle = $LAMI = $Plexi = $RoughWire = $PolyWire = $RoughWireClear = $PolyWireClear = 0;
        $Height = $Width = 0.0;
        $InsulatedUnit = $NewScreen = $ScreenRepair = $Moldings = $WindowGuards = $Capping = 0;
        $Locks = $Shoes = $TiltLatch = $Pivot = $Caps = 0;
        $Notes = '';
        $room_key = $i == 0 ? "rdoroom" : "rdoroom" . $i;
        $Room = $_POST[$room_key][0];
        if (empty($Room)) {
            $Room = trim($_POST['custom_room'][$i]);
        }
        $Floor = $_POST['Floor'][$i];
        $Room_pdf = $Room;
        if ($Room == 'Hallway') {
            $Room_pdf = 'Hallway-' . $Floor;
        }


        $RegGlassStr = '';
        $LargeGlassStr = '';
        $additional_repairsStr = '';
        $TopBottomStr = '';
        $Top = isset($_POST['top_glass'][$i]) ? 1 : 0;
        $Top_pdf = isset($_POST['top_glass'][$i]) ? '<img src="images/tick.png"> Top' : '';
        $Bottom = isset($_POST['bottom_glass'][$i]) ? 1 : 0;
        $Bottom_pdf = isset($_POST['bottom_glass'][$i]) ? '<img src="images/tick.png"> Bottom' : '';
        $RegGlass = trim($_POST['regular_glass'][$i]);
        $additional_repairs = trim($_POST['additional_repair'][$i]);
        $DoublePaneRegular = isset($_POST['double_pane_rg'][$i]) ? 1 : 0;
        $DoublePaneRegular_pdf = isset($_POST['double_pane_rg'][$i]) ? '<img src="images/tick.png"> Double Pane' : '';
        $LargeGlass = trim($_POST['large_glass'][$i]);
        $DoublePaneLarge = isset($_POST['double_pane_lg'][$i]) ? 1 : 0;
        $DoublePaneLarge_pdf = isset($_POST['double_pane_lg'][$i]) ? '<img src="images/tick.png"> Double Pane' : '';
        if ($RegGlass != '' && $RegGlass > 0) {
            $RegGlassStr = $RegGlass . ' Regular Glass';
        }
        if ($LargeGlass != '' && $LargeGlass > 0) {
            $LargeGlassStr = $LargeGlass . ' Large Glass';
        }
        if ($additional_repairs != '' && $additional_repairs > 0) {
            $additional_repairsStr = 'Additional Repairs: ' . $additional_repairs;
        }
        if ($Top == 1 && $Bottom == 1) {
            $TopBottomStr = ' - Top and Bottom';
        } else if ($Top == 1 && $Bottom == 0) {
            $TopBottomStr = ' - Top';
        } else if ($Top == 0 && $Bottom == 1) {
            $TopBottomStr = ' - Bottom';
        }
        if (!empty($TopBottomStr)) {
            $RegGlassStr = ($RegGlassStr != '') ? $RegGlassStr . $TopBottomStr : '';
            $LargeGlassStr = ($LargeGlassStr != '') ? $LargeGlassStr . $TopBottomStr : '';
        }
        //////////////add_repairs
        if (!empty($additional_repairs)) {
            $addrepair_key = $i == 0 ? "additional_repairs" : "additional_repairs" . $i;
            $RedTip_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'red') ? 1 : 0;
            $UltraLift_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'ultra') ? 1 : 0;
            $BlackTip_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'black') ? 1 : 0;
            $BlockTackle_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'block') ? 1 : 0;
            $Locks = trim($_POST['additional_locks'][$i]);
            $Shoes = trim($_POST['additional_Shoes'][$i]);
            $TiltLatch = trim($_POST['additional_TiltLatch'][$i]);
            $Caps = trim($_POST['additional_Caps'][$i]);
        } else {
            $repair_key = $i == 0 ? "repairs" : "repairs" . $i;
            $RedTip_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'red') ? 1 : 0;
            $UltraLift_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'ultra') ? 1 : 0;
            $BlackTip_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'black') ? 1 : 0;
            $BlockTackle_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'block') ? 1 : 0;

            $Locks = trim($_POST['locks'][$i]);
            $Shoes = trim($_POST['shoes'][$i]);
            $TiltLatch = trim($_POST['tiltlatch'][$i]);
            $Caps = trim($_POST['caps'][$i]);
        }
        //////////add_repairs end

        $RegRepairs = $RedTip_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        $UltraLift = $UltraLift_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        $BlackTip = $BlackTip_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        $BlockTackle = $BlockTackle_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle) && !empty($_POST['sash_value'][$i])) {
            $BlackTip = trim($_POST['sash_value'][$i]);
        }

        $glass_type_key = $i == 0 ? "glass_type" : "glass_type" . $i;
        $glass_type = $_POST[$glass_type_key][0];
        $glass_type_pdf = $_POST[$glass_type_key][0];
        if ($glass_type == 'Lami') {
            $LAMI = 1;
            $glass_type_pdf = $glass_type;
        } else if ($glass_type == 'Plexi') {
            $Plexi = 1;
            $glass_type_pdf = $glass_type;
        } else if ($glass_type == 'RoughWire') {
            $RoughWire = 1;
            $glass_type_pdf = 'RW';
        } else if ($glass_type == 'PolyWire') {
            $PolyWire = 1;
            $glass_type_pdf = 'PW';
        }
        $RoughWireClear = isset($_POST['clear_glass_rw'][$i]) ? 1 : 0;
        $RoughWireClear_pdf = isset($_POST['clear_glass_rw'][$i]) ? 'Yes' : 'No';
        $glass_type_pdf = isset($_POST['clear_glass_rw'][$i]) ? $glass_type_pdf . ' CG' : $glass_type_pdf;
        $PolyWireClear = isset($_POST['clear_glass_pw'][$i]) ? 1 : 0;
        $PolyWireClear_pdf = isset($_POST['clear_glass_pw'][$i]) ? 'Yes' : 'No';
        $glass_type_pdf = isset($_POST['clear_glass_pw'][$i]) ? $glass_type_pdf . ' CG' : $glass_type_pdf;
        $Clear_pdf = '';
        if ($RoughWireClear == 1) {
            $Clear_pdf = 'CG ' . $RoughWireClear_pdf;
        } else if ($PolyWireClear == 1) {
            $Clear_pdf = 'CG ' . $PolyWireClear_pdf;
        }
        $Width = floatval(trim($_POST['size_width'][$i]));
        $Height = floatval(trim($_POST['size_height'][$i]));
        $glass_qty = floatval(trim($_POST['glass_qty'][$i]));
        $InsulatedUnit = trim($_POST['insulated_unit'][$i]);
        $NewScreen = trim($_POST['new_screen'][$i]);
        $ScreenRepair = trim($_POST['screen_repair'][$i]);
        $Moldings = trim($_POST['moldings'][$i]);
        $WindowGuards = trim($_POST['window_guards'][$i]);
        $Capping = trim($_POST['capping'][$i]);

        $Pivot = trim($_POST['pivot'][$i]);
        $Notes = trim($_POST['notes'][$i]);
        $Floor = empty($Floor) ? 0 : $Floor;
        $RegGlass = empty($RegGlass) ? 0 : $RegGlass;
        $LargeGlass = empty($LargeGlass) ? 0 : $LargeGlass;
        $LargeGlass = empty($LargeGlass) ? 0 : $LargeGlass;
        $InsulatedUnit = empty($InsulatedUnit) ? 0 : $InsulatedUnit;
        $NewScreen = empty($NewScreen) ? 0 : $NewScreen;
        $ScreenRepair = empty($ScreenRepair) ? 0 : $ScreenRepair;
        $Moldings = empty($Moldings) ? 0 : $Moldings;
        $WindowGuards = empty($WindowGuards) ? 0 : $WindowGuards;
        $Capping = empty($Capping) ? 0 : $Capping;
        $Locks = empty($Locks) ? 0 : $Locks;
        $Shoes = empty($Shoes) ? 0 : $Shoes;
        $TiltLatch = empty($TiltLatch) ? 0 : $TiltLatch;
        $Pivot = empty($Pivot) ? 0 : $Pivot;
        $Caps = empty($Caps) ? 0 : $Caps;
        $additional_repairs = empty($additional_repairs) ? 0 : $additional_repairs;
         $query = "insert into JobDetailsTable(JobID,Room,Floor,Top,Bottom,RegGlass,DoublePaneRegular,LargeGlass,DoublePaneLarge,"
        . "RedTip,UltraLift,BlackTip,BlockTackle,LAMI,Plexi,RoughWire,RoughWireClear,PolyWire,PolyWireClear,Height,Width,InsulatedUnit,"
        . "NewScreen,ScreenRepair,Moldings,WindowGuards,Capping,Locks,Shoes,TiltLatch,Pivot,Caps,Notes,RedTip_c,UltraLift_c,BlackTip_c,BlockTackle_c,additional,GTQty,Status) values "
        . "('" . $JobID . "','" . $Room . "','" . $Floor . "','" . $Top . "','" . $Bottom . "','" . $RegGlass . "','" . $DoublePaneRegular . "','" . $LargeGlass . "',"
        . "'" . $DoublePaneLarge . "','" . $RegRepairs . "','" . $UltraLift . "','" . $BlackTip . "',"
        . "'" . $BlockTackle . "','" . $LAMI . "','" . $Plexi . "','" . $RoughWire . "','" . $RoughWireClear . "','" . $PolyWire . "','" . $PolyWireClear . "','" . $Height . "',"
        . "'" . $Width . "','" . $InsulatedUnit . "','" . $NewScreen . "','" . $ScreenRepair . "','" . $Moldings . "','" . $WindowGuards . "',"
        . "'" . $Capping . "','" . $Locks . "','" . $Shoes . "','" . $TiltLatch . "','" . $Pivot . "','" . $Caps . "','" . $Notes . "','" . $RedTip_c . "','" . $UltraLift_c . "','" . $BlackTip_c . "','" . $BlockTackle_c . "','" . $additional_repairs . "','" . $glass_qty . "','1')";

        $conn->query($query);
        //Create PDF
//        echo 'here';
//        exit;
        $pdftxt = '<table>';
        $pdftxt .= '<tr><td colspan="4">' . $Room_pdf . '</td></tr>';
        $size_str = '';
        if (!empty($Width) || !empty($Height) || !empty($glass_qty)) {
            $size_str = ' Size: ' . $Width . 'x' . $Height . '<br> Qty: ' . $glass_qty;
        }
        if ($glass_type_pdf != '' || $Width != '' || $Height != '' || $RegGlass != '' || $LargeGlass != '' || $additional_repairs != '' || $glass_qty != '') {
            $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Glass</b></td></tr>';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="1">' . $glass_type_pdf . $size_str . '</td>';
            //$pdftxt .= '<td>' . $Top_pdf . '<br>' . $Bottom_pdf . '</td>';
            $pdftxt .= '<td colspan="3">' . $RegGlassStr . ' ' . $DoublePaneRegular_pdf . '<br>' . $LargeGlassStr . '  &nbsp;' . $DoublePaneLarge_pdf . '</td>';
            //      $pdftxt .= '<td>' . $DoublePaneRegular_pdf . '<br>' . $DoublePaneLarge_pdf . '</td>';
            $pdftxt .= '</tr>';

            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="1"></td>';
            $pdftxt .= '<td colspan="3">' . $additional_repairsStr . '</td>';
            $pdftxt .= '</tr>';
            $pdftxt .= '<tr><td colspan="4"><hr></td></tr>';
        }

        if (!empty($additional_repairsStr)) {
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Caps)) {
                $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Additional Repair</b></td></tr>';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td >RedTip: <u>' . $RegRepairs . '</u><br>Locks:    <u>' . $Locks . '</u></td>';
                $pdftxt .= '<td >UltraLift: <u>' . $UltraLift . '</u><br>Shoes:    <u>' . $Shoes . '</u></td>';

                $pdftxt .= '<td >BlackTip: <u>' . $BlackTip . '</u><br>TiltLatch:    <u>' . $TiltLatch . '</u></td>';
                $pdftxt .= '<td >BlockTackle: <u>' . $BlockTackle . '</u><br>Pivot:    <u>' . $Caps . '</u></td>';
                $pdftxt .= '</tr>';
                $pdftxt .= '<tr><td colspan="4"><hr></td></tr>';
            }
        } else {
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Pivot) || !empty($Caps)) {
                $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Repair</b></td></tr>';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td >RedTip: <u>' . $RegRepairs . '</u><br>Locks:    <u>' . $Locks . '</u></td>';
                $pdftxt .= '<td >UltraLift: <u>' . $UltraLift . '</u><br>Shoes:    <u>' . $Shoes . '</u></td>';

                $pdftxt .= '<td >BlackTip: <u>' . $BlackTip . '</u><br>TiltLatch:    <u>' . $TiltLatch . '</u></td>';
                $pdftxt .= '<td >BlockTackle: <u>' . $BlockTackle . '</u><br>Pivot:    <u>' . $Pivot . '</u>&nbsp;&nbsp;&nbsp;Caps:    <u>' . $Caps . '</u></td>';
                $pdftxt .= '</tr>';
                $pdftxt .= '<tr><td colspan="4"><hr></td></tr>';
            }
        }

        if ($InsulatedUnit != '' || $ScreenRepair != '' || $WindowGuards != '' || $NewScreen != '' || $Moldings != '' || $Capping != '') {
            $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Others</b></td></tr>';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>InsulatedUnit: <u>' . $InsulatedUnit . '</u></td>';
            $pdftxt .= '<td>ScreenRepair: <u>' . $ScreenRepair . '</u></td>';
            $pdftxt .= '<td colspan="2">WindowGuards:    <u>' . $WindowGuards . '</u></td>';
            $pdftxt .= '</tr>';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>NewScreen: <u>' . $NewScreen . '</u></td>';
            $pdftxt .= '<td>Moldings: <u>' . $Moldings . '</u></td>';
            $pdftxt .= '<td colspan="2">Capping: <u>' . $Capping . '</u></td>';
            $pdftxt .= '</tr>';
        }
//    $pdftxt .= '<tr><td colspan="4">Notes: ' . $Notes . '</td></tr>';
//    $pdftxt .= '<hr>';
        $pdftxt .= '</table>';
        $pdf_text_arr[] = $pdftxt;
    }
    // create new PDF document
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MMGlass');
    $pdf->SetTitle($JobID);
    $pdf->SetSubject($JobID);
    $pdf->SetKeywords('MMGlass');
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // set margins  
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 14);
    $text = '<b>JobID:</b> ' . $_SESSION['JobID'];
    $pdf->writeHTMLCell($w = 0, $h = 0, $x = 130, $y = 40, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $text = '<b>Date:</b> ' . date('m/d/Y');
    $pdf->writeHTMLCell($w = 0, $h = 0, $x = 130, $y = 50, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $pdf->SetFont('helvetica', '', 11);
    preg_match('~>\K[^<>]*(?=<)~', $Address, $match);
    $pdftxt_header = '<br><br><table cellspacing="5" cellpadding="3" >';
    $pdftxt_header .= '<tr>';
    $pdftxt_header .= '<td colspan="1" style="border: 1px solid black;"><b>Address:</b> ' . $match[0] . '</td>';
    $pdftxt_header .= '<td style="border: 1px solid black; "><b>APT:</b> ' . $Apt . '</td></tr><tr>';
    $pdftxt_header .= '<td style="border: 1px solid black; "><b>City:</b> ' . $City . '</td>';
    $pdftxt_header .= '<td style="border: 1px solid black; "><b>Technician:</b> ' . $_SESSION['User']['TechName'] . '</td>';
    $pdftxt_header .= '</tr>';
    $pdftxt_header .= '</table>';
    $tbl1 = <<<EOD
            $pdftxt_header
            EOD;
    $pdf->writeHTML($tbl1, true, true, false, false, '');
    $pdf->SetFont('helvetica', '', 12);
    $i = 0;
    foreach ($pdf_text_arr as $pdf_text) {
        if ($i % 2 == 0 && $i > 0) {
            $pdf->AddPage();
            $pdf_text = '<br><br><br><br>' . $pdf_text;
        }
        $tbl = <<<EOD
            $pdf_text
            EOD;
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $i++;
    }
    //    $shortcodes = "CG = Clear Glass, RG = Regular Glass, , LG = Large Glass, DP = Double Pane, RT = Red Tip, ";
//    $shortcodes .= "UL = UltraLift Repairs, BTR = Black Tip Repairs, BT = Block & Tackle, ";
//    $shortcodes .= "IU = Insulated Unit, NS = New Screen, MO = Moldings, CA = Capping, SR = Screen Repair, ";
//    $shortcodes .= "WG = Window Guards, TL = TiltLatch, PV = Pivot";
//    $pdf->writeHTML($shortcodes, true, false, false, false, '');
    $pdftxt_sign = '<br><br><table cellspacing="5" cellpadding="3">';
    $pdftxt_sign .= '<tr><td>Customer Signature</td><td>Tech Signature</td></tr></table>';
    $tbl_sign = <<<EOD
            $pdftxt_sign
            EOD;
    $pdf->writeHTML($tbl_sign, true, true, false, false, '');
    if (!empty($file)) {
        $pdf->Image($file, 0);
    }
    if (!empty($techfile)) {
        $pdf->Image($techfile, 100);
    }
    $pdf->writeHTML('<br><br><br><br><br><br><hr>', true, false, false, false, '');
    $pdf_name = $JobID . '-' . date('Y-m-d-H-i-s');
    $pdfpath = "/home/bitnami/htdocs/mmglass/JobDetailsPDFs/$pdf_name.pdf";
    $pdf->Output($pdfpath, 'F');
//    $pdf->Output($pdfpath);
//    exit;
    //Send email
    $mail = new PHPMailer(true);
    //send email to tenant
    if (!empty($tennant_email)) {
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP(); //Send using SMTP
            $mail->Host = SMTP_HOST; //Set the SMTP server to send through
            $mail->SMTPAuth = true; //Enable SMTP authentication
            $mail->Username = SMTP_USERNAME; //SMTP username
            $mail->Password = SMTP_PASSWORD; //SMTP password
            //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $mail->setFrom(FROM_EMAIL_ADDRESS, FROM_NAME);
            $mail->addAddress($tennant_email); //Add a recipient 
//      $mail->addAddress($tennant_email); //Add a recipient 
            $mail->addAttachment($pdfpath);
            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'Job Done';
            $mail->Body = 'Your job has been done. Please check the attached PDF with this email.';
            $mail->send();
        } catch (Exception $e) {
            
        }
    }

    $mail = new PHPMailer(true);
    //Send email to admin  
    $to_email = 'server@mazelrealty.com';
    $Subject = 'ImportWebJob ' . $JobID;
    $Body = $JobID . ' has been done';
    try {
        //Server settings
        $mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = SMTP_HOST; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = SMTP_USERNAME; //SMTP username
        $mail->Password = SMTP_PASSWORD; //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom(FROM_EMAIL_ADDRESS, FROM_NAME);
        $mail->addAddress($to_email); //Add a recipient    
        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $Subject;
        $mail->Body = $Body;
        $mail->send();
        unset($_SESSION['JobID']);
        header("Location:todayjobs.php");
        $msg = '<div class="row"><div class="alert alert-success" role="alert">Job has been saved successfully!</div></div>';
    } catch (Exception $e) {
        $msg = '<div class="row"><div class="alert alert-danger" role="alert">Job has been saved successfully but email has not been sent!!!</div></div>';
    }
    //////////////////////////////
}
if (isset($_POST['not_done'])) {
    $tennant_email = trim($_POST['tennant_email']);
    //Signature upload
    if (isset($_POST['main_sign']) && trim($_POST['main_sign']) != '') {
        $folderPath = "/home/bitnami/htdocs/mmglass/upload/";
        $image_parts = explode(";base64,", $_POST['main_sign']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.' . $image_type;
        file_put_contents($file, $image_base64);
    } else {
        $file = '';
    }
    if (isset($_POST['main_techsign']) && trim($_POST['main_techsign']) != '') {
        $folderPath = "/home/bitnami/htdocs/mmglass/upload/";
        $image_parts = explode(";base64,", $_POST['main_techsign']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $techfile = $folderPath . uniqid() . '.' . $image_type;
        file_put_contents($techfile, $image_base64);
    } else {
        $techfile = '';
    }
    //Upload images
    if (isset($_FILES["attach1"]["name"]) && $_FILES["attach1"]["name"] != '') {
        $filename = $_FILES["attach1"]["name"];
        $tempname = $_FILES["attach1"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach2"]["name"]) && $_FILES["attach2"]["name"] != '') {
        $filename = $_FILES["attach2"]["name"];
        $tempname = $_FILES["attach2"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach3"]["name"]) && $_FILES["attach3"]["name"] != '') {
        $filename = $_FILES["attach3"]["name"];
        $tempname = $_FILES["attach3"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach4"]["name"]) && $_FILES["attach4"]["name"] != '') {
        $filename = $_FILES["attach4"]["name"];
        $tempname = $_FILES["attach4"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach5"]["name"]) && $_FILES["attach5"]["name"] != '') {
        $filename = $_FILES["attach5"]["name"];
        $tempname = $_FILES["attach5"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    if (isset($_FILES["attach6"]["name"]) && $_FILES["attach6"]["name"] != '') {
        $filename = $_FILES["attach6"]["name"];
        $tempname = $_FILES["attach6"]["tmp_name"];
        $folder = "JobPics/" . $filename;
        if (move_uploaded_file($tempname, $folder)) {
            $img_qry = "insert into JobPics(JobID,Picture) values('" . $JobID . "','" . $folder . "')";
            $conn->query($img_qry);
        }
    }
    //Save data into DB table
    $total_box = $_POST['total_box'];
    $pdf_text_arr = array();
    $pdf_text_arr1 = array();
    for ($i = 0; $i < $total_box; $i++) {
        $Room = '';
        $Floor = $Top = $Bottom = $RegGlass = $additional_repairs = $LargeGlass = $DoublePaneLarge = $DoublePaneRegular = $RegRepairs = $UltraLift = 0;
        $BlackTip = $BlockTackle = $LAMI = $Plexi = $RoughWire = $PolyWire = $RoughWireClear = $PolyWireClear = 0;
        $Height = $Width = 0.0;
        $InsulatedUnit = $NewScreen = $ScreenRepair = $Moldings = $WindowGuards = $Capping = 0;
        $Locks = $Shoes = $TiltLatch = $Pivot = $Caps = 0;
        $Notes = '';
        $room_key = $i == 0 ? "rdoroom" : "rdoroom" . $i;
        $Room = $_POST[$room_key][0];
        if (empty($Room)) {
            $Room = trim($_POST['custom_room'][$i]);
        }
        $Floor = $_POST['Floor'][$i];
        $Room_pdf = $Room;
        if ($Room == 'Hallway') {
            $Room_pdf = 'Hallway-' . $Floor;
        }

        $RegGlassStr = '';
        $LargeGlassStr = '';
        $additional_repairsStr = '';
        $TopBottomStr = '';
        $Top = isset($_POST['top_glass'][$i]) ? 1 : 0;
        $Top_pdf = isset($_POST['top_glass'][$i]) ? '<img src="images/tick.png"> Top' : '';
        $Bottom = isset($_POST['bottom_glass'][$i]) ? 1 : 0;
        $Bottom_pdf = isset($_POST['bottom_glass'][$i]) ? '<img src="images/tick.png"> Bottom' : '';
        $RegGlass = trim($_POST['regular_glass'][$i]);
        $additional_repairs = trim($_POST['additional_repair'][$i]);
        $DoublePaneRegular = isset($_POST['double_pane_rg'][$i]) ? 1 : 0;
        $DoublePaneRegular_pdf = isset($_POST['double_pane_rg'][$i]) ? '<img src="images/tick.png"> Double Pane' : '';
        $LargeGlass = trim($_POST['large_glass'][$i]);
        $DoublePaneLarge = isset($_POST['double_pane_lg'][$i]) ? 1 : 0;
        $DoublePaneLarge_pdf = isset($_POST['double_pane_lg'][$i]) ? '<img src="images/tick.png"> Double Pane' : '';
        if ($RegGlass != '' && $RegGlass > 0) {
            $RegGlassStr = $RegGlass . ' Regular Glass';
        }
        if ($LargeGlass != '' && $LargeGlass > 0) {
            $LargeGlassStr = $LargeGlass . ' Large Glass';
        }
        if ($additional_repairs != '' && $additional_repairs > 0) {
            $additional_repairsStr = 'Additional Repairs: ' . $additional_repairs;
        }
        if ($Top == 1 && $Bottom == 1) {
            $TopBottomStr = ' - Top and Bottom';
        } else if ($Top == 1 && $Bottom == 0) {
            $TopBottomStr = ' - Top';
        } else if ($Top == 0 && $Bottom == 1) {
            $TopBottomStr = ' - Bottom';
        }
        if (!empty($TopBottomStr)) {
            $RegGlassStr = ($RegGlassStr != '') ? $RegGlassStr . $TopBottomStr : '';
            $LargeGlassStr = ($LargeGlassStr != '') ? $LargeGlassStr . $TopBottomStr : '';
        }
        //////////////add_repairs
        if (!empty($additional_repairs)) {
            $addrepair_key = $i == 0 ? "additional_repairs" : "additional_repairs" . $i;
            $RedTip_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'red') ? 1 : 0;
            $UltraLift_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'ultra') ? 1 : 0;
            $BlackTip_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'black') ? 1 : 0;
            $BlockTackle_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'block') ? 1 : 0;
            $Locks = trim($_POST['additional_locks'][$i]);
            $Shoes = trim($_POST['additional_Shoes'][$i]);
            $TiltLatch = trim($_POST['additional_TiltLatch'][$i]);
            $Caps = trim($_POST['additional_Caps'][$i]);
        } else {
            $repair_key = $i == 0 ? "repairs" : "repairs" . $i;
            $RedTip_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'red') ? 1 : 0;
            $UltraLift_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'ultra') ? 1 : 0;
            $BlackTip_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'black') ? 1 : 0;
            $BlockTackle_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'block') ? 1 : 0;

            $Locks = trim($_POST['locks'][$i]);
            $Shoes = trim($_POST['shoes'][$i]);
            $TiltLatch = trim($_POST['tiltlatch'][$i]);
            $Caps = trim($_POST['caps'][$i]);
        }
        //////////add_repairs end

        $RegRepairs = $RedTip_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        $UltraLift = $UltraLift_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        $BlackTip = $BlackTip_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        $BlockTackle = $BlockTackle_c == 1 ? trim($_POST['sash_value'][$i]) : 0;
        if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle) && !empty($_POST['sash_value'][$i])) {
            $BlackTip = trim($_POST['sash_value'][$i]);
        }

        $glass_type_key = $i == 0 ? "glass_type" : "glass_type" . $i;
        $glass_type = $_POST[$glass_type_key][0];
        $glass_type_pdf = $_POST[$glass_type_key][0];
        if ($glass_type == 'Lami') {
            $LAMI = 1;
            $glass_type_pdf = $glass_type;
        } else if ($glass_type == 'Plexi') {
            $Plexi = 1;
            $glass_type_pdf = $glass_type;
        } else if ($glass_type == 'RoughWire') {
            $RoughWire = 1;
            $glass_type_pdf = 'RW';
        } else if ($glass_type == 'PolyWire') {
            $PolyWire = 1;
            $glass_type_pdf = 'PW';
        }
        $RoughWireClear = isset($_POST['clear_glass_rw'][$i]) ? 1 : 0;
        $RoughWireClear_pdf = isset($_POST['clear_glass_rw'][$i]) ? 'Yes' : 'No';
        $glass_type_pdf = isset($_POST['clear_glass_rw'][$i]) ? $glass_type_pdf . ' CG' : $glass_type_pdf;
        $PolyWireClear = isset($_POST['clear_glass_pw'][$i]) ? 1 : 0;
        $PolyWireClear_pdf = isset($_POST['clear_glass_pw'][$i]) ? 'Yes' : 'No';
        $glass_type_pdf = isset($_POST['clear_glass_pw'][$i]) ? $glass_type_pdf . ' CG' : $glass_type_pdf;
        $Clear_pdf = '';
        if ($RoughWireClear == 1) {
            $Clear_pdf = 'CG ' . $RoughWireClear_pdf;
        } else if ($PolyWireClear == 1) {
            $Clear_pdf = 'CG ' . $PolyWireClear_pdf;
        }
        $Width = floatval(trim($_POST['size_width'][$i]));
        $Height = floatval(trim($_POST['size_height'][$i]));
        $glass_qty = floatval(trim($_POST['glass_qty'][$i]));
        $InsulatedUnit = trim($_POST['insulated_unit'][$i]);
        $NewScreen = trim($_POST['new_screen'][$i]);
        $ScreenRepair = trim($_POST['screen_repair'][$i]);
        $Moldings = trim($_POST['moldings'][$i]);
        $WindowGuards = trim($_POST['window_guards'][$i]);
        $Capping = trim($_POST['capping'][$i]);

        $Pivot = trim($_POST['pivot'][$i]);
        $Notes = trim($_POST['notes'][$i]);
         $Floor = empty($Floor) ? 0 : $Floor;
        $RegGlass = empty($RegGlass) ? 0 : $RegGlass;
        $LargeGlass = empty($LargeGlass) ? 0 : $LargeGlass;
        $LargeGlass = empty($LargeGlass) ? 0 : $LargeGlass;
        $InsulatedUnit = empty($InsulatedUnit) ? 0 : $InsulatedUnit;
        $NewScreen = empty($NewScreen) ? 0 : $NewScreen;
        $ScreenRepair = empty($ScreenRepair) ? 0 : $ScreenRepair;
        $Moldings = empty($Moldings) ? 0 : $Moldings;
        $WindowGuards = empty($WindowGuards) ? 0 : $WindowGuards;
        $Capping = empty($Capping) ? 0 : $Capping;
        $Locks = empty($Locks) ? 0 : $Locks;
        $Shoes = empty($Shoes) ? 0 : $Shoes;
        $TiltLatch = empty($TiltLatch) ? 0 : $TiltLatch;
        $Pivot = empty($Pivot) ? 0 : $Pivot;
        $Caps = empty($Caps) ? 0 : $Caps;
        $additional_repairs = empty($additional_repairs) ? 0 : $additional_repairs;
        $query = "insert into JobDetailsTable(JobID,Room,Floor,Top,Bottom,RegGlass,DoublePaneRegular,LargeGlass,DoublePaneLarge,"
                . "RedTip,UltraLift,BlackTip,BlockTackle,LAMI,Plexi,RoughWire,RoughWireClear,PolyWire,PolyWireClear,Height,Width,InsulatedUnit,"
                . "NewScreen,ScreenRepair,Moldings,WindowGuards,Capping,Locks,Shoes,TiltLatch,Pivot,Caps,Notes,RedTip_c,UltraLift_c,BlackTip_c,BlockTackle_c,additional,GTQty,Status) values "
                . "('" . $JobID . "','" . $Room . "','" . $Floor . "','" . $Top . "','" . $Bottom . "','" . $RegGlass . "','" . $DoublePaneRegular . "','" . $LargeGlass . "',"
                . "'" . $DoublePaneLarge . "','" . $RegRepairs . "','" . $UltraLift . "','" . $BlackTip . "',"
                . "'" . $BlockTackle . "','" . $LAMI . "','" . $Plexi . "','" . $RoughWire . "','" . $RoughWireClear . "','" . $PolyWire . "','" . $PolyWireClear . "','" . $Height . "',"
                . "'" . $Width . "','" . $InsulatedUnit . "','" . $NewScreen . "','" . $ScreenRepair . "','" . $Moldings . "','" . $WindowGuards . "',"
                . "'" . $Capping . "','" . $Locks . "','" . $Shoes . "','" . $TiltLatch . "','" . $Pivot . "','" . $Caps . "','" . $Notes . "','" . $RedTip_c . "','" . $UltraLift_c . "','" . $BlackTip_c . "','" . $BlockTackle_c . "','" . $additional_repairs . "','" . $glass_qty . "','0')";
        $conn->query($query);
        //Create PDF
//       exit;
        $pdftxt = '<table>';
        $pdftxt .= '<tr><td colspan="4">' . $Room_pdf . '</td></tr>';
        $size_str = '';
        if (!empty($Width) || !empty($Height) || !empty($glass_qty)) {
            $size_str = ' Size: ' . $Width . 'x' . $Height . '<br> Qty: ' . $glass_qty;
        }
        if ($glass_type_pdf != '' || $Width != '' || $Height != '' || $RegGlass != '' || $LargeGlass != '' || $additional_repairs != '' || $glass_qty != '') {
            $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Glass</b></td></tr>';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="1">' . $glass_type_pdf . $size_str . '</td>';
            //$pdftxt .= '<td>' . $Top_pdf . '<br>' . $Bottom_pdf . '</td>';
            $pdftxt .= '<td colspan="3">' . $RegGlassStr . ' ' . $DoublePaneRegular_pdf . '<br>' . $LargeGlassStr . '  &nbsp;' . $DoublePaneLarge_pdf . '</td>';
            //      $pdftxt .= '<td>' . $DoublePaneRegular_pdf . '<br>' . $DoublePaneLarge_pdf . '</td>';
            $pdftxt .= '</tr>';

            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="1"></td>';
            $pdftxt .= '<td colspan="3">' . $additional_repairsStr . '</td>';
            $pdftxt .= '</tr>';
            $pdftxt .= '<tr><td colspan="4"><hr></td></tr>';
        }

        if (!empty($additional_repairsStr)) {
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Caps)) {
                $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Additional Repair</b></td></tr>';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td >RedTip: <u>' . $RegRepairs . '</u><br>Locks:    <u>' . $Locks . '</u></td>';
                $pdftxt .= '<td >UltraLift: <u>' . $UltraLift . '</u><br>Shoes:    <u>' . $Shoes . '</u></td>';

                $pdftxt .= '<td >BlackTip: <u>' . $BlackTip . '</u><br>TiltLatch:    <u>' . $TiltLatch . '</u></td>';
                $pdftxt .= '<td >BlockTackle: <u>' . $BlockTackle . '</u><br>Pivot:    <u>' . $Caps . '</u></td>';
                $pdftxt .= '</tr>';
                $pdftxt .= '<tr><td colspan="4"><hr></td></tr>';
            }
        } else {
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Pivot) || !empty($Caps)) {
                $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Repair</b></td></tr>';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td >RedTip: <u>' . $RegRepairs . '</u><br>Locks:    <u>' . $Locks . '</u></td>';
                $pdftxt .= '<td >UltraLift: <u>' . $UltraLift . '</u><br>Shoes:    <u>' . $Shoes . '</u></td>';

                $pdftxt .= '<td >BlackTip: <u>' . $BlackTip . '</u><br>TiltLatch:    <u>' . $TiltLatch . '</u></td>';
                $pdftxt .= '<td >BlockTackle: <u>' . $BlockTackle . '</u><br>Pivot:    <u>' . $Pivot . '</u>&nbsp;&nbsp;&nbsp;Caps:    <u>' . $Caps . '</u></td>';
                $pdftxt .= '</tr>';
                $pdftxt .= '<tr><td colspan="4"><hr></td></tr>';
            }
        }

        if ($InsulatedUnit != '' || $ScreenRepair != '' || $WindowGuards != '' || $NewScreen != '' || $Moldings != '' || $Capping != '') {
            $pdftxt .= '<tr><td colspan="4"><b style="text-align: center;">Others</b></td></tr>';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>InsulatedUnit: <u>' . $InsulatedUnit . '</u></td>';
            $pdftxt .= '<td>ScreenRepair: <u>' . $ScreenRepair . '</u></td>';
            $pdftxt .= '<td colspan="2">WindowGuards:    <u>' . $WindowGuards . '</u></td>';
            $pdftxt .= '</tr>';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>NewScreen: <u>' . $NewScreen . '</u></td>';
            $pdftxt .= '<td>Moldings: <u>' . $Moldings . '</u></td>';
            $pdftxt .= '<td colspan="2">Capping: <u>' . $Capping . '</u></td>';
            $pdftxt .= '</tr>';
        }
//    $pdftxt .= '<tr><td colspan="4">Notes: ' . $Notes . '</td></tr>';
//    $pdftxt .= '<hr>';
        $pdftxt .= '</table>';
        $pdf_text_arr[] = $pdftxt;
    }
    // create new PDF document
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MMGlass');
    $pdf->SetTitle($JobID);
    $pdf->SetSubject($JobID);
    $pdf->SetKeywords('MMGlass');
    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // set margins  
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 14);
    $text = '<b>JobID:</b> ' . $_SESSION['JobID'];
    $pdf->writeHTMLCell($w = 0, $h = 0, $x = 130, $y = 40, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $text = '<b>Date:</b> ' . date('m/d/Y');
    $pdf->writeHTMLCell($w = 0, $h = 0, $x = 130, $y = 50, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $pdf->SetFont('helvetica', '', 11);
    preg_match('~>\K[^<>]*(?=<)~', $Address, $match);
    $pdftxt_header = '<br><br><table cellspacing="5" cellpadding="3" >';
    $pdftxt_header .= '<tr>';
    $pdftxt_header .= '<td colspan="1" style="border: 1px solid black;"><b>Address:</b> ' . $match[0] . '</td>';
    $pdftxt_header .= '<td style="border: 1px solid black; "><b>APT:</b> ' . $Apt . '</td></tr><tr>';
    $pdftxt_header .= '<td style="border: 1px solid black; "><b>City:</b> ' . $City . '</td>';
    $pdftxt_header .= '<td style="border: 1px solid black; "><b>Technician:</b> ' . $_SESSION['User']['TechName'] . '</td>';
    $pdftxt_header .= '</tr>';
    $pdftxt_header .= '</table>';
    $tbl1 = <<<EOD
            $pdftxt_header
            EOD;
    $pdf->writeHTML($tbl1, true, true, false, false, '');
    $pdf->SetFont('helvetica', '', 12);
    $i = 0;
    foreach ($pdf_text_arr as $pdf_text) {
        if ($i % 2 == 0 && $i > 0) {
            $pdf->AddPage();
            $pdf_text = '<br><br><br><br>' . $pdf_text;
        }
        $tbl = <<<EOD
            $pdf_text
            EOD;
        $pdf->writeHTML($tbl, true, false, false, false, '');
        $i++;
    }
    //    $shortcodes = "CG = Clear Glass, RG = Regular Glass, , LG = Large Glass, DP = Double Pane, RT = Red Tip, ";
//    $shortcodes .= "UL = UltraLift Repairs, BTR = Black Tip Repairs, BT = Block & Tackle, ";
//    $shortcodes .= "IU = Insulated Unit, NS = New Screen, MO = Moldings, CA = Capping, SR = Screen Repair, ";
//    $shortcodes .= "WG = Window Guards, TL = TiltLatch, PV = Pivot";
//    $pdf->writeHTML($shortcodes, true, false, false, false, '');
    $pdftxt_sign = '<br><br><table cellspacing="5" cellpadding="3">';
    $pdftxt_sign .= '<tr><td>Customer Signature</td><td>Tech Signature</td></tr></table>';
    $tbl_sign = <<<EOD
            $pdftxt_sign
            EOD;
    $pdf->writeHTML($tbl_sign, true, true, false, false, '');
    if (!empty($file)) {
        $pdf->Image($file, 0);
    }
    if (!empty($techfile)) {
        $pdf->Image($techfile, 100);
    }
    $pdf->writeHTML('<br><br><br><br><br><br><hr>', true, false, false, false, '');
    $pdf_name = $JobID . '-' . date('Y-m-d-H-i-s');
    $pdfpath = "/home/bitnami/htdocs/mmglass/JobDetailsPDFs/$pdf_name.pdf";
    $pdf->Output($pdfpath, 'F');
//    $pdf->Output($pdfpath);
//    exit;
    //Send email
    $mail = new PHPMailer(true);
    //send email to tenant
    if (!empty($tennant_email)) {
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP(); //Send using SMTP
            $mail->Host = SMTP_HOST; //Set the SMTP server to send through
            $mail->SMTPAuth = true; //Enable SMTP authentication
            $mail->Username = SMTP_USERNAME; //SMTP username
            $mail->Password = SMTP_PASSWORD; //SMTP password
            //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $mail->setFrom(FROM_EMAIL_ADDRESS, FROM_NAME);
            $mail->addAddress($tennant_email); //Add a recipient 
//      $mail->addAddress($tennant_email); //Add a recipient 
            $mail->addAttachment($pdfpath);
            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'Job Done';
            $mail->Body = 'Your job has been done. Please check the attached PDF with this email.';
            $mail->send();
        } catch (Exception $e) {
            
        }
    }
    $mail = new PHPMailer(true);
    //Send email to admin  
    $to_email = 'server@mazelrealty.com';
    $Subject = 'ImportWebJob ' . $JobID;
    $Body = $JobID . ' has been done';
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = SMTP_HOST; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = SMTP_USERNAME; //SMTP username
        $mail->Password = SMTP_PASSWORD; //SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom(FROM_EMAIL_ADDRESS, FROM_NAME);
        $mail->addAddress($to_email); //Add a recipient    
        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $Subject;
        $mail->Body = $Body;
        $mail->send();
        unset($_SESSION['JobID']);
        header("Location:todayjobs.php");
        $msg = '<div class="row"><div class="alert alert-success" role="alert">Job has been saved successfully!</div></div>';
    } catch (Exception $e) {
        $msg = '<div class="row"><div class="alert alert-danger" role="alert">Job has been saved successfully but email has not been sent!!!</div></div>';
    }
    //////////////////////////////
}
//////////////////////
?>
<?php
include("includes/header.php");
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<link type="text/css" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
      rel="stylesheet">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<link type="text/css" href="css/jquery.signature.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery.signature.js"></script>
<script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>
<style>
    .kbw-signature {
        width: 250px;
        height: 100px;
    }

    #sign canvas {
        width: 100% !important;
        height: auto;
    }
</style>

<body style="align-items: baseline;">
    <?php
    include("includes/userlinks.php");
    ?>
    <main role="main" class="container jobs_page" style="margin-top: 35px;">
        <h1 style="text-align: center;">Job Form</h1>
        <?php echo $msg; ?>
        <form name="job_form" method="post" action="" class="job_form" id="job_form" enctype="multipart/form-data">
            <div id="main_div">
                <div id="form_div">
                    <div class="form-group row mt-3 room_section">
                        <div class="col-sm-12 form-check form-check-inline">
                            <label for="AdmissionID" class="col-form-label"><strong>Select Room</strong></label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Kitchen">
                            <label class="form-check-label" for="room">Kitchen</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Bedroom">
                            <label class="form-check-label" for="room">Bedroom</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Bathroom">
                            <label class="form-check-label" for="Room">Bathroom</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Living Room">
                            <label class="form-check-label" for="Room">Living Room</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Hallway">
                            <label class="form-check-label" for="Room">Hallway</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Basement">
                            <label class="form-check-label" for="Room">Basement</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Lobby">
                            <label class="form-check-label" for="Room">Lobby</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Front Door">
                            <label class="form-check-label" for="Room">Front Door</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Vest Door">
                            <label class="form-check-label" for="Room">Vest Door</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Sidelite">
                            <label class="form-check-label" for="Room">Sidelite</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Skylight">
                            <label class="form-check-label" for="Room">Skylight</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Office">
                            <label class="form-check-label" for="Room">Office</label>
                        </div>
                        <div class="col-sm-12 form-check form-check-inline">
                            <input class="form-check-input rdoclsroom" type="radio" name="rdoroom[]" value="Classroom">
                            <label class="form-check-label" for="Room">Classroom</label>
                        </div>
                        <div class="col-sm-2 form-check form-check-inline width">
                            <input name="custom_room[]" type="text" class="form-control" placeholder="custom room">
                        </div>
                    </div>
                    <div class="form-group row floordiv" style="display: none;">
                        <label for="Done" class="col-sm-1 col-form-label">Floor</label>
                        <div class="col-sm-2">
                            <input type="number" name="Floor[]" id="Floor" class="form-control">
                        </div>
                    </div>
                    <hr>
                    <section>

                        <!--/////////////////////////////////////-->
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="row">
                                    <div class="col-sm-6"><input type="checkbox" class="top_glass_c " name="top_glass[]" value="1"> Top</div>
                                    <div class="col-sm-6"><input type="checkbox" class="bottom_glass_c " name="bottom_glass[]" value="1"> Bottom</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">Regular Glass:</div>
                                    <div class="col-sm-6"><input name="regular_glass[]" class="r_glass form-control" type="text"
                                                                 class="form-control width" placeholder="Quantity"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-12"><input type="checkbox" name="double_pane_rg[]" value="1"> Double Pane</div>

                                </div>

                                <div class="row mt-2">
                                    <div class="col-sm-6">Large Glass:</div>
                                    <div class="col-sm-6"><input name="large_glass[]" class="l_glass form-control" type="text"
                                                                 class="form-control width" placeholder="Quantity"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-12"><input type="checkbox" name="double_pane_lg[]" value="1"> Double Pane</div>

                                </div>
                            </div>
                            <div class="col-sm-4">

                                <div class="row mt-4">
                                    <div class="col-sm-6">Additional Repairs:</div>
                                    <div class="col-sm-6"><input name="additional_repair[]" class="form-control" type="text"
                                                                 class="form-control width" placeholder="Quantity"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">Red Tip Repairs</div>
                                    <div class="col-sm-6"><input  type="radio" name="additional_repairs[]" value="red"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">UltraLift Repairs</div>
                                    <div class="col-sm-6"><input type="radio" name="additional_repairs[]" value="ultra"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">Black Tip Repairs</div>
                                    <div class="col-sm-6"><input type="radio" name="additional_repairs[]" value="black"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">Block &Tackle Repairs</div>
                                    <div class="col-sm-6"><input type="radio" name="additional_repairs[]" value="block"></div>
                                </div>

                            </div>
                            <div class="col-sm-4">
                                <div class="row mt-4">
                                    <div class="col-sm-6">Lock</div>
                                    <div class="col-sm-6"><input name="additional_locks[]" type="text" class="form-control"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">Shoes</div>
                                    <div class="col-sm-6"><input name="additional_Shoes[]" type="text" class="form-control"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">TiltLatch</div>
                                    <div class="col-sm-6"><input name="additional_TiltLatch[]" type="text" class="form-control"></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">Caps</div>
                                    <div class="col-sm-6"><input name="additional_Caps[]" type="text" class="form-control"></div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12 "><strong>Glass Type</strong></div>
                            <div class=" col-sm-12">
                                <input type="radio" name="glass_type[]" class="chkbox_gt" value="Lami">
                                <label class="" for="wire">Lami</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" col-sm-12">
                                <input type="radio" name="glass_type[]" class="chkbox_gt" value="Plexi">
                                <label class="" for="wire">Plexi</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <input type="radio" name="glass_type[]" class="chkbox_gt" value="RoughWire">
                                <label class="" for="">Rough Wire</label>
                                <input type="checkbox" name="clear_glass_rw[]" value="1"> Clear
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-sm-4">
                                <input type="radio" name="glass_type[]" class="chkbox_gt" value="PolyWire">
                                <label class="mr-3" for="">Poly Wire  </label>
                                <input type="checkbox" name="clear_glass_pw[]" value="1"> Clear
                            </div>
                        </div>

                        <div class="row mt-2 custom-row">
                            <div class="col-sm-1">Size</div>
                            <div class="col-sm-2"> <input name="size_width[]" type="text" class="form-control width glass_size_width" placeholder="Width"></div>
                            <div class="col-sm-2"> <input name="size_height[]" type="text" class="form-control width glass_size_height" placeholder="Height"></div>
                            <div class="col-sm-3"> <input name="glass_qty[]" type="text" class="form-control width glass_qty" placeholder="Quantity"></div>
                        </div>
                        <hr>
                    </section>
                    <section>
                        <div class="row mt-2">
                            <div class="col-sm-12 "><strong>Sash Repairs</strong></div>
                            <div class="col-sm-2">Red Tip Repairs</div>
                            <div class="text-right col-sm-1"><input class="rdosr blocks blocks_a" type="radio" name="repairs[]" value="red"></div>

                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">UltraLift Repairs</div>
                            <div class="text-right col-sm-1"><input class="rdosr blocks blocks_a" type="radio" name="repairs[]" value="ultra"></div>

                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2">Black Tip Repairs</div>
                            <div class="text-right col-sm-1"><input class="rdosr blocks blocks_a" type="radio" name="repairs[]" value="black"></div>

                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">Block &Tackle Repairs</div>
                            <div class="text-right col-sm-1"><input class="rdosr blocks blocks_a" type="radio" name="repairs[]" value="block"></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">Sash Repairs</div>

                            <div class="col-sm-3 "><input name="sash_value[]" type="text" class="form-control width txtrdosr"
                                                          placeholder="Quantity"></div>
                        </div>
                        <hr>
                    </section>
                    <section>
                        <div class="row mt-2">
                            <div class="col-sm-2">Lock</div>
                            <div class="col-sm-2"> <input name="locks[]" type="text" class="form-control width lstp"></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">Shoes</div>
                            <div class="col-sm-2 "> <input name="shoes[]" type="text" class="form-control width lstp"></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2">TiltLatch</div>
                            <div class="col-sm-2"> <input name="tiltlatch[]" type="text" class="form-control width lstp">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">Caps</div>
                            <div class="col-sm-2 "> <input name="caps[]" type="text" class="form-control width"></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2">Pivot</div>
                            <div class="col-sm-2"> <input name="pivot[]" type="text" class="form-control width lstp"></div>
                        </div>
                        <hr>
                    </section>
                    <section>
                        <div class="row mt-2">
                            <div class="col-sm-2">Insulated Unit</div>
                            <div class="col-sm-2"> <input name="insulated_unit[]" type="text" class="form-control width"></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">New Screen</div>
                            <div class="col-sm-2 "> <input name="new_screen[]" type="text" class="form-control width">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2">Moldings</div>
                            <div class="col-sm-2"> <input name="moldings[]" type="text" class="form-control width">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">Capping</div>
                            <div class="col-sm-2 "> <input name="capping[]" type="text" class="form-control width">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2">Screen Repair</div>
                            <div class="col-sm-2"> <input name="screen_repair[]" type="text" class="form-control width">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-2 ">Window Guards</div>
                            <div class="col-sm-2 "><input name="window_guards[]" type="text" class="form-control width">
                            </div>
                        </div>
                        <hr>
                    </section>
                    <div class="form-group row mt-2">
                        <label for="Instructions" class="col-sm-2 col-form-label">Notes</label>
                        <div class="col-sm-3 width">
                            <textarea class="form-control" id="Instructions" name="notes[]" rows="3"></textarea>
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
            <div class="row mt-2">
                <div class="col-sm-8">Use the following file control to upload images.</div>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="row mt-2">
                        <div class="col-sm-12 mt-2">
                            <input type="file" id="formFileMultiple" class="form-control" name="attach1" />
                        </div>
                        <div class="col-sm-12 mt-2" style="display:none;" id="image2">
                            <input type="file" class="form-control" name="attach2" />
                        </div>
                        <div class="col-sm-12 mt-2" style="display:none;" id="image3">
                            <input type="file" class="form-control" name="attach3" />
                        </div>
                        <div class="col-sm-12 mt-2" style="display:none;" id="image4">
                            <input type="file" class="form-control" name="attach4" />
                        </div>
                        <div class="col-sm-12 mt-2" style="display:none;" id="image5">
                            <input type="file" class="form-control" name="attach5" />
                        </div>
                        <div class="col-sm-12 mt-2" style="display:none;" id="image6">
                            <input type="file" class="form-control" name="attach6" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <span class="btn btn-success" id="upload_more_image">Upload more images +</span>
                    <input type="hidden" name="image_number" id="image_number" value="1">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-sm-12 ">
                    Tenant Signature<br>
                    <div id="sign"></div><br>
                    <button id="clear_sign">Clear Signature</button>
                    <textarea id="main_sign" name="main_sign" style="display: none"></textarea>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-sm-2 "><input name="tennant_email" type="text" class="form-control" placeholder="Tenant Email">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-sm-12 ">
                    Tech Signature<br>
                    <div id="techsign"></div><br>
                    <button id="clear_techsign">Clear Signature</button>
                    <textarea id="main_techsign" name="main_techsign" style="display: none"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-3 mb-5" name="save" id="btndone">Done</button>
            <button type="submit" class="btn btn-primary mt-3 mb-5" name="not_done" id="not_done">Not Complete</button>
        </form>
    </main>
</body>
<?php
include("includes/footer.php");
?>
<script type="text/javascript">
    $(document).on('change', '.rdoclsroom', function () {
        var room = $(this).val();
        if (room == 'Hallway') {
            $(this).parent('div').parent('div').next('.floordiv').show(1000);
        } else {
            $(this).parent('div').parent('div').next('.floordiv').hide(1000);
        }
    });
    $(document).ready(function () {
        var sig1 = $('#sign').signature({syncField: '#main_sign', syncFormat: 'PNG'});
        $('#clear_sign').click(function (e) {
            e.preventDefault();
            sig1.signature('clear');
            $("#main_sign").val('');
        });
    });
    $(document).ready(function () {
        var sig1 = $('#techsign').signature({syncField: '#main_techsign', syncFormat: 'PNG'});
        $('#clear_techsign').click(function (e) {
            e.preventDefault();
            sig1.signature('clear');
            $("#main_techsign").val('');
        });
    });
    $(document).on('click', '#addElementField', function () {
        var cur_val = parseInt($('#total_box').val());
        var form_html = $('#form_div').html();
        var new_name = "rdoroom" + cur_val;
        var new_glass_type = "glass_type" + cur_val;
        var new_repairs = "repairs" + cur_val;
        form_html = form_html.replace(/rdoroom/g, new_name);
        form_html = form_html.replace(/glass_type/g, new_glass_type);
        form_html = form_html.replace(/repairs/g, new_repairs);
        $('#main_div').append('<div><hr>' + form_html + '<span class="btn btn-danger remove_box"> Remove -</span></div>');
        $('#total_box').val(cur_val + 1);
    });
    $(document).on('click', '.remove_box', function () {
        $(this).parent('div').remove();
        var cur_val = parseInt($('#total_box').val());
        $('#total_box').val(cur_val - 1);
    });
    $(document).on('click', '#upload_more_image', function () {
        var cur_image_val = parseInt($('#image_number').val());
        $('#image_number').val((cur_image_val + 1));
        var show_image_div = "image" + (cur_image_val + 1);
        $('#' + show_image_div).show(1000);
    });

    $(document).on('click', '#btndone, #not_done', function () {
        var i = 0;
        var alert_msg = "";
        $(".top_glass_c").each(function () {
            var top_bottom_sel = false;
            if ($(this).is(":checked")) {
                top_bottom_sel = true;
            } else {
                var j = 0;
                $(".bottom_glass_c").each(function () {
                    if (j === i && $(this).is(":checked")) {
                        top_bottom_sel = true;
                    }
                    j++;
                });
            }
            if (top_bottom_sel) {
                var m = 0;
                var reg_glass_val = '';
                var large_glass_val = '';
                $(".r_glass").each(function () {
                    if (i === m) {
                        if ($(this).val() !== '') {
                            reg_glass_val = $(this).val();
                        }

                        var n = 0;
                        $(".l_glass").each(function () {
                            if (n === m && $(this).val() !== '') {
                                large_glass_val = $(this).val();
                            }
                            n++;
                        });
                        if (reg_glass_val === '' && large_glass_val === '') {
                            alert_msg += 'Please enter regular glass or large glass value in Glass pane section\n';
                        }
                    }
                    m++;
                });
            } else {
                var m = 0;
                var reg_glass_val = '';
                var large_glass_val = '';
                $(".r_glass").each(function () {
                    if (i === m) {
                        if ($(this).val() !== '') {
                            reg_glass_val = $(this).val();
                        }

                        var n = 0;
                        $(".l_glass").each(function () {
                            if (n === m && $(this).val() !== '') {
                                large_glass_val = $(this).val();
                            }
                            n++;
                        });
                        if (reg_glass_val !== '' || large_glass_val !== '') {
                            alert_msg += 'Please select top or bottom in Glass pane section\n';
                        }
                    }
                    m++;
                });
            }
            i++;
        });

        var gi = 0;
        var mi = 0;
        var glass_type_check = false;
        $(".chkbox_gt").each(function () {
            if (gi === 0) {
                glass_type_check = false;
            }
            if ($(this).is(":checked")) {
                glass_type_check = true;
            }
            gi++;
            if (gi === 4) {
                if (glass_type_check) {
                    var gswi = 0;
                    var gshi = 0;
                    var gsqi = 0;
                    var glass_size_width_val = '';
                    var glass_size_height_val = '';
                    var glass_qty_val = '';
                    $(".glass_size_width").each(function () {
                        if (gswi === mi) {
                            if ($(this).val() !== '') {
                                glass_size_width_val = $(this).val();
                            }
                        }
                        gswi++;
                    });

                    $(".glass_size_height").each(function () {
                        if (gshi === mi) {
                            if ($(this).val() !== '') {
                                glass_size_height_val = $(this).val();
                            }
                        }
                        gshi++;
                    });

                    $(".glass_qty").each(function () {
                        if (gsqi === mi) {
                            if ($(this).val() !== '') {
                                glass_qty_val = $(this).val();
                            }
                        }
                        gsqi++;
                    });
                    if (glass_size_width_val === '' || glass_size_height_val === '' || glass_qty_val === '') {
                        alert_msg += 'Size and Qty is missing in the Glass type section\n';
                    }
                }
                gi = 0;
                mi++;
            }
        });
        var si = 0;
        var sit = 0;
        var sash_repair_check = false;
        var tot_run = 0;
        $(".rdosr").each(function () {
            tot_run++;
            if (si === 0) {
                sash_repair_check = false;
            }
            if ($(this).is(":checked")) {
                sash_repair_check = true;
            }
            si++;
            if (si === 4) {
                if (sash_repair_check) {
                    var srwi = 0;
                    var sash_repair_val = '';
                    var lstp_val = '';
                    $(".txtrdosr").each(function () {
                        if (srwi === sit) {
                            if ($(this).val() !== '') {
                                sash_repair_val = $(this).val();
                            }
                        }
                        srwi++;
                    });
                    if (sash_repair_val === '') {
                        alert_msg += 'Please enter quantity for Sash Repair\n';
                    }
                    $(".lstp").each(function (ind) {
                        var cpr = parseInt(tot_run - 4);
                        if (ind >= cpr && ind < tot_run) {
                            if ($(this).val() !== '') {
                                lstp_val = $(this).val();
                            }
                        }
                    });
                    if (lstp_val === '') {
                        alert_msg += 'Please enter quantity for Lock, Shoes, Caps or Pivot\n';
                    }
                }
                si = 0;
                sit++;
            }
        });
        //Reverse validation    
        var blk = 0;
        var tot1 = 0;
        var lstp_val1 = '';
        $(".lstp").each(function () {
            blk++;
            tot1++;
            //if (blk === 0) {
            //lstp_val1 = '';
            //}
            if (blk === 4) {
                lstp_val1 = '';
                var cpr1 = parseInt(tot1 - 4);
                $(".lstp").each(function (ind1) {
                    if (ind1 >= cpr1 && ind1 < tot1) {
                        if ($(this).val() !== '') {
                            lstp_val1 = $(this).val();
                        }
                    }
                });
                if (lstp_val1 !== '') {
                    var sash_repair_check1 = false;
                    $(".rdosr").each(function (ind2) {
                        if (ind2 >= cpr1 && ind2 < tot1) {
                            if ($(this).is(":checked")) {
                                sash_repair_check1 = true;
                            }
                        }
                    });
                    if (!sash_repair_check1) {
                        alert_msg += 'Please select any one Sash Repairs\n';
                    }
                }
                blk = 0;
            }
        });

        if (alert_msg !== '') {
            alert(alert_msg);
            return false;
        } else {
            $(this).hide();
        }
    });

</script>