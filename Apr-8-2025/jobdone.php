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

//echo $JobID;exit;
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
    $pdftxt = '<table cellpadding="5" style="border: 1px solid black;">';

    $total_box = $_POST['total_box'];

    $pdf_text_arr = array();

    for ($i = 0;
            $i < $total_box;
            $i++) {
        $Room = '';
        $Floor = $Top = $Bottom = $RegGlass = $additional_repairs = $LargeGlass = $DoublePaneLarge = $DoublePaneRegular = $RegRepairs = $UltraLift_c = $RedTip_c = $BlackTip_c = $BlockTackle_c = $UltraLift = 0;
        $BlackTip = $BlockTackle = $LAMI = $Plexi = $RoughWire = $PolyWire = $RoughWireClear = $PolyWireClear = 0;
        $sash_value = 0;
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
        $topbottom = '';
        $TopBottomStr = '';
        $TopBottomStr1 = '';
        $Top = isset($_POST['top_glass'][$i]) ? 1 : 0;
        if ($Top !== 0) {
            $Top_pdf = '<img src="images/tick.png"> <strong>Top</strong>';
        }
//        $Top_pdf = isset($_POST['top_glass'][$i]) ? '<img src="images/tick.png"> <strong>Top</strong>' : '';
        $Bottom = isset($_POST['bottom_glass'][$i]) ? 1 : 0;
        if ($Bottom !== 0) {
            $Bottom_pdf = '<img src="images/tick.png"> <strong> Bottom</strong>';
        }
//        $Bottom_pdf = isset($_POST['bottom_glass'][$i]) ? '<img src="images/tick.png"> <strong> Bottom</strong>' : '';
        $RegGlass = trim($_POST['regular_glass'][$i]);
        $additional_repairs = trim($_POST['additional_repair'][$i]);
        $DoublePaneRegular = isset($_POST['double_pane_rg'][$i]) ? 1 : 0;
        $DoublePaneRegular_pdf = isset($_POST['double_pane_rg'][$i]) ? ' Double Pane' : '';
        $LargeGlass = trim($_POST['large_glass'][$i]);
        $DoublePaneLarge = isset($_POST['double_pane_lg'][$i]) ? 1 : 0;
        $DoublePaneLarge_pdf = isset($_POST['double_pane_lg'][$i]) ? ' Double Pane' : '';

        if ($RegGlass != '' && $RegGlass > 0) {
            $RegGlassStr = 'Standard Clear Glass <u style="font-size: 12px;">' . $RegGlass . '</u>';
        }
        if ($LargeGlass != '' && $LargeGlass > 0) {
            $LargeGlassStr = 'Large Clear Glass <u style="font-size: 12px;">' . $LargeGlass . '</u>';
        }
        if ($additional_repairs != '' && $additional_repairs > 0) {
            $additional_repairsStr = 'Additional Repairs: <u>' . $additional_repairs . '</u>';
        }



        if ($Top == 1 && $Bottom == 1) {
            $TopBottomStr = $Top_pdf . '<br>';
            $TopBottomStr1 = $Bottom_pdf;
        } else if ($Top == 1 && $Bottom == 0) {
            $TopBottomStr = $Top_pdf . '<br>';
        } else if ($Top == 0 && $Bottom == 1) {
            $TopBottomStr1 = $Bottom_pdf;
        }
        if (!empty($TopBottomStr) || !empty($TopBottomStr1)) {
            $RegGlassStr = ($RegGlassStr != '') ? $RegGlassStr : '';
            $LargeGlassStr = ($LargeGlassStr != '') ? $LargeGlassStr : '';
        }
//////////////add_repairs
        if (!empty($additional_repairs)) {
            $RedTip_c = trim($_POST['additional_repairs_red'][$i]);
            $UltraLift_c = trim($_POST['additional_repairs_ultra'][$i]);
            $BlackTip_c = trim($_POST['additional_repairs_black'][$i]);
            $BlockTackle_c = trim($_POST['additional_repairs_block'][$i]);

            $Locks = trim($_POST['additional_locks'][$i]);
            $Shoes = trim($_POST['additional_Shoes'][$i]);
            $TiltLatch = trim($_POST['additional_TiltLatch'][$i]);
            $Caps = trim($_POST['additional_Caps'][$i]);
        } else {
            $RegRepairs = trim($_POST['repairs_red'][$i]);
            $UltraLift = trim($_POST['repairs_ultra'][$i]);
            $BlackTip = trim($_POST['repairs_black'][$i]);
            $BlockTackle = trim($_POST['repairs_block'][$i]);
            $Locks = trim($_POST['locks'][$i]);
            $Shoes = trim($_POST['shoes'][$i]);
            $TiltLatch = trim($_POST['tiltlatch'][$i]);
            $Caps = trim($_POST['caps'][$i]);
            $sash_value = trim($_POST['sash_value'][$i]);
        }
//////////add_repairs end
//        if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle) && !empty($_POST['sash_value'][$i])) {
//            $BlackTip = trim($_POST['sash_value'][$i]);
//        }

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
        $glass_type_pdf = isset($_POST['clear_glass_rw'][$i]) ? $glass_type_pdf : $glass_type_pdf;
        $PolyWireClear = isset($_POST['clear_glass_pw'][$i]) ? 1 : 0;
        $PolyWireClear_pdf = isset($_POST['clear_glass_pw'][$i]) ? 'Yes' : 'No';
        $glass_type_pdf = isset($_POST['clear_glass_pw'][$i]) ? $glass_type_pdf : $glass_type_pdf;
        $Clear_pdf = '';
        if ($RoughWireClear == 1) {
            $Clear_pdf = 'CG ' . $RoughWireClear_pdf;
        } else if ($PolyWireClear == 1) {
            $Clear_pdf = 'CG ' . $PolyWireClear_pdf;
        }
        $Clear_pdf_CG = '';
        if ($RoughWireClear == 1 || $PolyWireClear == 1) {
            $Clear_pdf_CG = 'CG ';
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
        $RegRepairs = empty($RegRepairs) ? 0 : $RegRepairs;
        $UltraLift = empty($UltraLift) ? 0 : $UltraLift;
        $BlackTip = empty($BlackTip) ? 0 : $BlackTip;
        $BlockTackle = empty($BlockTackle) ? 0 : $BlockTackle;
        $RedTip_c = empty($RedTip_c) ? 0 : $RedTip_c;
        $UltraLift_c = empty($UltraLift_c) ? 0 : $UltraLift_c;
        $BlackTip_c = empty($BlackTip_c) ? 0 : $BlackTip_c;
        $BlockTackle_c = empty($BlockTackle_c) ? 0 : $BlockTackle_c;
        $sash_value = empty($sash_value) ? 0 : $sash_value;

        $query = "insert into JobDetailsTable(JobID,Room,Floor,Top,Bottom,RegGlass,DoublePaneRegular,LargeGlass,DoublePaneLarge,"
                . "RedTip,UltraLift,BlackTip,BlockTackle,LAMI,Plexi,RoughWire,RoughWireClear,PolyWire,PolyWireClear,Height,Width,InsulatedUnit,"
                . "NewScreen,ScreenRepair,Moldings,WindowGuards,Capping,Locks,Shoes,TiltLatch,Pivot,Caps,Notes,RedTip_c,UltraLift_c,BlackTip_c,BlockTackle_c,additional,GTQty,Status,SashRepairs) values "
                . "('" . $JobID . "','" . $Room . "','" . $Floor . "','" . $Top . "','" . $Bottom . "','" . $RegGlass . "','" . $DoublePaneRegular . "','" . $LargeGlass . "',"
                . "'" . $DoublePaneLarge . "','" . $RegRepairs . "','" . $UltraLift . "','" . $BlackTip . "',"
                . "'" . $BlockTackle . "','" . $LAMI . "','" . $Plexi . "','" . $RoughWire . "','" . $RoughWireClear . "','" . $PolyWire . "','" . $PolyWireClear . "','" . $Height . "',"
                . "'" . $Width . "','" . $InsulatedUnit . "','" . $NewScreen . "','" . $ScreenRepair . "','" . $Moldings . "','" . $WindowGuards . "',"
                . "'" . $Capping . "','" . $Locks . "','" . $Shoes . "','" . $TiltLatch . "','" . $Pivot . "','" . $Caps . "','" . $Notes . "','" . $RedTip_c . "','" . $UltraLift_c . "','" . $BlackTip_c . "','" . $BlockTackle_c . "','" . $additional_repairs . "','" . $glass_qty . "','1','" . $sash_value . "')";

        $conn->query($query);
        $RegRepairs = $RedTip_c;
        $UltraLift = $UltraLift_c;
        $BlackTip = $BlackTip_c;
        $BlockTackle = $BlockTackle_c;
//Create PDF
//        echo 'here';
//        exit;
//        $pdftxt = '<div style="margin-bottom: -3px; padding-bottom: -3px;">';

        $Floor = empty($Floor) ? '<u>    </u>' : ' <strong>Floor</strong> - <u style="font-size: 12px;">' . $Floor . '</u>';
        $RegGlass = empty($RegGlass) ? '<u>    </u>' : $RegGlass;
        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
        $InsulatedUnit = empty($InsulatedUnit) ? '<u>    </u>' : $InsulatedUnit;
        $NewScreen = empty($NewScreen) ? '<u>    </u>' : $NewScreen;
        $ScreenRepair = empty($ScreenRepair) ? '<u>    </u>' : $ScreenRepair;
        $Moldings = empty($Moldings) ? '<u>    </u>' : $Moldings;
        $WindowGuards = empty($WindowGuards) ? '<u style="font-size: 12px;">    </u> ' : '<u style="font-size: 12px;">' . $WindowGuards . '</u> ';
        $Capping = empty($Capping) ? '<u style="font-size: 12px;">    </u>' : '<u style="font-size: 12px;">' . $Capping . '</u> ';
        $Locks = empty($Locks) ? '<u style="font-size: 12px;">    </u>' : $Locks;
        $Shoes = empty($Shoes) ? '<u style="font-size: 12px;">    </u>' : $Shoes;
        $TiltLatch = empty($TiltLatch) ? '<u style="font-size: 12px;">    </u>' : $TiltLatch;
        $Pivot = empty($Pivot) ? '<u style="font-size: 12px;">    </u>' : $Pivot;
        $Caps = empty($Caps) ? '<u style="font-size: 12px;">    </u>' : $Caps;

        $Width = empty($Width) ? '<u>    </u>' : $Width;
        $Height = empty($Height) ? '<u>    </u>' : $Height;
        $glass_qty = empty($glass_qty) ? '<u>    </u>' : $glass_qty;

        $size_str = '';
        if (!empty($Width) || !empty($Height) || !empty($glass_qty)) {
            $size_str = ' Size  <u  style="font-size: 30px;">' . $Width . '</u> x <u   style="font-size: 30px;">' . $Height . '</u> ';
            $size_str1 = ' Size  <u  style="font-size: 30px;">' . $Width . '</u> x <u  style="font-size: 30px;">' . $Height . '</u>';
        }
        if (empty($RegGlassStr)) {
            $size_str1 = '  Size  <u>   </u> x <u>   </u>';
        }
        if (empty($LargeGlassStr)) {
            $size_str = '  Size  <u>   </u> x <u>   </u>';
        }
        if (empty($RegGlassStr) && empty($LargeGlassStr)) {
            $size_str1 = '  Size  <u>   </u> x <u>   </u>';
        }

        $RegGlassStr = empty($RegGlassStr) ? 'Standard Clear Glass <u>   </u> ' . $size_str1 : $RegGlassStr . ' ' . $size_str;

        $LargeGlassStr = empty($LargeGlassStr) ? ' Large Clear Glass <u>   </u> ' . $size_str : $LargeGlassStr . ' ' . $size_str;

//        $RegGlassStr = empty($RegGlassStr) ? 'Glass: <u>    </u>' : $RegGlassStr;
//        $LargeGlassStr = empty($LargeGlassStr) ? 'Glass:<u style="width:1%;">    </u>' : $LargeGlassStr;
        if (!empty($Room_pdf)) {
            if ($Room == 'Hallway') {
                $Room_pdf = '&nbsp;<img src="images/tick.png"> <strong>Hallway</strong>' . $Floor . '<br>&nbsp;<img src="images/check-box.png"> <strong>Skylight</strong>';
            } elseif ($Room == 'Skylight') {
                $Room_pdf = '&nbsp;<img src="images/check-box.png"> <strong>Hallway Floor - <u>    </u></strong><br>&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong><br>';
            } elseif ($Room == 'Front Door') {
                $Room_pdf = '&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
            } elseif ($Room == 'Vest Door') {
                $Room_pdf = '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
            } elseif ($Room == 'Sidelite') {
                $Room_pdf = '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong>';
            }
//            else {
//                $Room_pdf = '';
//                $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>';
//                $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>';
//                $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
//            }
        } else {
            $Room_pdf = '';
            $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>';
            $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>';
            $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
        }

//        echo $glass_type_pdf;exit;
        if (!empty($glass_type_pdf)) {
            if ($glass_type_pdf == 'Lami') {
                $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/tick.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
            } elseif ($glass_type_pdf == 'Plexi') {
                $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/tick.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
            } elseif ($glass_type_pdf == 'RW') {
                if ($glass_type_pdf == 'RW' || $Clear_pdf_CG == 'CG') {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                } else {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                }
            } elseif ($glass_type_pdf == 'PW') {
                if ($glass_type_pdf == 'PW' || $Clear_pdf_CG == 'CG') {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                } else {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                }
            }
//            $glass_type_pdf = '<img src="images/tick.png"> ' . $glass_type_pdf;
        } else {
            $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
        }

        $glass_type_pdf_hall = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
        $glass_type_pdf_door = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';

        $size_str2 = '';
        if ($Room == 'Hallway' || $Room == 'Skylight' || $Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite') {

            if (!empty($Width) && !empty($Height)) {
                $size_str2 = ' <strong>Size</strong> <u  style="font-size: 12px;">' . $Width . '</u> <strong>x</strong> <u  style="font-size: 12px;">' . $Height . '</u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">' . $Moldings . '</u>';
            } elseif (!empty($Width)) {
                $size_str2 = ' <strong>Size</strong> <u  style="font-size: 12px;">' . $Width . '</u> <strong>x</strong> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">' . $Moldings . '</u>';
            } elseif (!empty($Height)) {
                $size_str2 = ' <strong>Size</strong> <strong>x</strong> <u  style="font-size: 12px;">' . $Height . '</u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">' . $Moldings . '</u>';
            } else {
                $size_str2 = ' <strong>Size</strong> <u  style="font-size: 12px;">  </u> <strong>x</strong> <u  style="font-size: 12px;">   </u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">   </u>';
            }
        }
        $size_str2_hall = ' <strong>Size</strong> <u  style="font-size: 12px;">  </u> <strong>x</strong> <u  style="font-size: 12px;">   </u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">   </u>';
        $size_str2_door = ' <strong>Size</strong> <u  style="font-size: 12px;">  </u> <strong>x</strong> <u  style="font-size: 12px;">   </u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">   </u>';

        $hfloor = '';
        if ($Room == 'Hallway' || $Room == 'Skylight') {
            $hfloor = $glass_type_pdf . ' ' . $size_str2;
        }
        $Room_pdf_sta = '';
        $Room_pdf_sta .= '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>';
        $Room_pdf_sta .= '&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>';
        $Room_pdf_sta .= '&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
        $Room_pdf_hall = '';
        $Room_pdf_hall .= '&nbsp;<img src="images/check-box.png"> <strong>Hallway Floor - <u>    </u></strong><br>';
        $Room_pdf_hall .= '&nbsp;<img src="images/check-box.png"> <strong>Skylight</strong><br>';

        $string = $Notes;
        $words = explode(' ', $string); // Split the string into an array of words
        $first_five_words = array_slice($words, 0, 5); // Get the first 5 words
//        $Notes = implode(' ', $first_five_words); // Print the first 5 words separated by space
        $Notes = ''; // Print the first 5 words separated by space
        if (!empty($Notes)) {
            $notes_pdf = '';
            $notes_pdf = '<table width="100%" style="margin: 0; padding: 0;">
                <tr><td width="100%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"> <span style="font-size: 12px;">' . $Notes . '</span></td><td style="margin: 0; padding: 0;"></td></tr>
            </table>';
        } else {
            $notes_pdf = '';
            $notes_pdf = '<table width="100%" style="margin: 0; padding: 0;">
                <tr><td width="100%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"></td></tr>
            </table>';
        }

        $TopBottomStr = empty($TopBottomStr) ? '<img src="images/check-box.png">  <strong>Top</strong><br>' : $TopBottomStr;
        $TopBottomStr1 = empty($TopBottomStr1) ? '<img src="images/check-box.png"> <strong> Bottom</strong>' : $TopBottomStr1;
        $DoublePaneRegular_pdf = empty($DoublePaneRegular_pdf) ? '' : $DoublePaneRegular_pdf;
        $DoublePaneLarge = empty($DoublePaneLarge) ? '' : $DoublePaneLarge;
        if (!empty($additional_repairsStr)) {
            $additional_repairsStr = empty($additional_repairsStr) ? 'Additional Repairs: <u>    </u>' : $additional_repairsStr;
        }
        $additional_repairs = empty($additional_repairs) ? '<u>    </u>' : $additional_repairs;
//        $RegRepairs = $RedTip_c == '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $UltraLift = $UltraLift_c != '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $BlackTip = $BlackTip_c != '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $BlockTackle = $BlockTackle_c != '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';

        $add_rep = '';
        if (!empty($additional_repairsStr)) {
            $add_rep .= $additional_repairsStr . ' ';
            $add_rep .= 'RegRepairs: <u>' . $RegRepairs . '</u> ';
            $add_rep .= 'UltraLift: <u>' . $UltraLift . '</u> ';
            $add_rep .= 'BlockTackle: <u>' . $BlockTackle . '</u> ';
            $add_rep .= 'BlackTip: <u>' . $BlackTip . '</u> ';
            $add_rep .= 'Locks: <u>' . $Locks . '</u> ';
            $add_rep .= 'Shoes: <u>' . $Shoes . '</u> ';
            $add_rep .= 'TL: <u>' . $TiltLatch . '</u> ';
            $add_rep .= 'Pivot: <u>' . $Pivot . '</u> ';
            $add_rep .= 'Caps: <u>' . $Caps . '</u> ';
        } else {
            $add_rep .= 'RegRepairs: <u>' . $RegRepairs . '</u> ';
            $add_rep .= 'UltraLift: <u>' . $UltraLift . '</u> ';
            $add_rep .= 'BlockTackle: <u>' . $BlockTackle . '</u> ';
            $add_rep .= 'BlackTip: <u>' . $BlackTip . '</u> ';
            $add_rep .= 'Locks: <u>' . $Locks . '</u> ';
            $add_rep .= 'Shoes: <u>' . $Shoes . '</u> ';
            $add_rep .= 'TL: <u>' . $TiltLatch . '</u> ';
            $add_rep .= 'Pivot: <u>' . $Pivot . '</u> ';
            $add_rep .= 'Caps: <u>' . $Caps . '</u> ';
        }
        $WG_str = '';
        $WG_str .= '<strong>WG</strong>  ' . $WindowGuards;
        $Cap_str = '';
        $Cap_str .= '<strong>Cap</strong>  ' . $Capping;

        $WG_str_hall = '';
        $WG_str_hall .= '<strong>WG</strong> <u>    </u>';
        $Cap_str_hall = '';
        $Cap_str_hall .= '<strong>Cap</strong> <u>    </u>';

        $WG_str_door = '';
        $WG_str_door .= '<strong>WG</strong> <u>    </u>';
        $Cap_str_door = '';
        $Cap_str_door .= '<strong>Cap</strong> <u>    </u>';
//static door start
        if ($i === 0) {
            if (!empty($Room_pdf) && $Room !== 'Front Door' && $Room !== 'Vest Door' && $Room !== 'Sidelite') {
                $pdftxt .= '<tr>';

                $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf_sta . '</td>';
                $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $glass_type_pdf_door . ' ' . $size_str2_door . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
                $pdftxt .= '</tr>';
//new tr
                $pdftxt .= '<tr>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
                $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= $WG_str_door;
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= $Cap_str_door;
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';
                $pdftxt .= '</table>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

                $pdftxt .= '</tr>';
            }
        }

//static door end

        $pdftxt .= '<tr>';

        if (!empty($Room) && ($Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite')) {
            $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf . '</td>';
        } elseif ($Room == 'Skylight') {
            $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf . '</td>';
        } elseif ($Room == 'Hallway') {
            $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf . '</td>';
        }

        if ($Room == 'Hallway' || $Room == 'Skylight') {
            $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $hfloor . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
        } else {
            if (!empty($Room) && ($Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite')) {
                $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $hfloor . ' ' . $glass_type_pdf . ' ' . $size_str2 . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
            }
        }
        $pdftxt .= '</tr>';

//new tr
        if (!empty($Room) && ($Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite')) {
            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
            $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>';
            $pdftxt .= $WG_str;
            $pdftxt .= '</td>';
            $pdftxt .= '<td>';
            $pdftxt .= $Cap_str;
            $pdftxt .= '</td>';

            $pdftxt .= '</tr>';
            $pdftxt .= '</table>';
            $pdftxt .= '</td>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

            $pdftxt .= '</tr>';
        } elseif ($Room == 'Hallway' || $Room == 'Skylight') {
            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
            $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>';
            $pdftxt .= $WG_str;
            $pdftxt .= '</td>';
            $pdftxt .= '<td>';
            $pdftxt .= $Cap_str;
            $pdftxt .= '</td>';

            $pdftxt .= '</tr>';
            $pdftxt .= '</table>';
            $pdftxt .= '</td>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

            $pdftxt .= '</tr>';
        }
//        start hallway and skylite static
        if ($i == 0) {
            if (empty($hfloor) && ($Room !== 'Hallway' && $Room !== 'Skylight')) {
                $pdftxt .= '<tr>';

                $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf_hall . '</td>';
                $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $glass_type_pdf_hall . ' ' . $size_str2_hall . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
                $pdftxt .= '</tr>';
//new tr
                $pdftxt .= '<tr>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
                $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= $WG_str_hall;
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= $Cap_str_hall;
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';
                $pdftxt .= '</table>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

                $pdftxt .= '</tr>';
//        end hallway and skylite
            }
        }
    }


////////////////////////////////////second loop////////////////////////////////////////////
    $total_box = $_POST['total_box'];

    $pdf_text_arr = array();

    for ($i = 0;
            $i < $total_box;
            $i++) {
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
            $Room_pdf = '<strong>Hallway</strong>-' . $Floor;
        }


        $RegGlassStr = '';
        $LargeGlassStr = '';
        $additional_repairsStr = '';
        $TopBottomStr = '';
        $TopBottomStr1 = '';
        $Top = isset($_POST['top_glass'][$i]) ? 1 : 0;
        if ($Top !== 0) {
            $Top_pdf = '<img src="images/tick.png"> <strong>Top</strong>';
        }
//        $Top_pdf = isset($_POST['top_glass'][$i]) ? '<img src="images/tick.png"> <strong>Top</strong>' : '';
        $Bottom = isset($_POST['bottom_glass'][$i]) ? 1 : 0;
        if ($Bottom !== 0) {
            $Bottom_pdf = '<img src="images/tick.png"> <strong> Bottom</strong>';
        }
//        $Bottom_pdf = isset($_POST['bottom_glass'][$i]) ? '<img src="images/tick.png"> <strong> Bottom</strong>' : '';
        $RegGlass = trim($_POST['regular_glass'][$i]);
        $additional_repairs = trim($_POST['additional_repair'][$i]);
        $DoublePaneRegular = isset($_POST['double_pane_rg'][$i]) ? 1 : 0;
//        $DoublePaneRegular_pdf = isset($_POST['double_pane_rg'][$i]) ? '<img src="images/tick.png"> DP' : '';
        $LargeGlass = trim($_POST['large_glass'][$i]);
        $DoublePaneLarge = isset($_POST['double_pane_lg'][$i]) ? 1 : 0;
//        $DoublePaneLarge_pdf = isset($_POST['double_pane_lg'][$i]) ? '<img src="images/tick.png"> DP' : '';
        if (!empty($DoublePaneRegular) || !empty($DoublePaneLarge)) {
            $DoublePaneLarge_and_Regular_pdf = '<img src="images/tick.png"> <strong>DP</strong>';
        } else {
            $DoublePaneLarge_and_Regular_pdf = '<img src="images/check-box.png"> <strong>DP</strong>';
        }

        if ($RegGlass != '' && $RegGlass > 0) {
            $RegGlassStr = '<strong>S Glass</strong> <u style="font-size: 12px;">' . $RegGlass . '</u>';
        }
        if ($LargeGlass != '' && $LargeGlass > 0) {
            $LargeGlassStr = '<strong>L Glass</strong> <u style="font-size: 12px;">' . $LargeGlass . '</u>';
        }
        if ($additional_repairs != '' && $additional_repairs > 0) {
            $additional_repairsStr = 'Additional Repairs <u style="font-size: 12px;">' . $additional_repairs . '</u>';
        }



        if ($Top == 1 && $Bottom == 1) {
            $TopBottomStr = $Top_pdf . '<br>';
            $TopBottomStr1 = $Bottom_pdf;
        } else if ($Top == 1 && $Bottom == 0) {
            $TopBottomStr = $Top_pdf . '<br>';
        } else if ($Top == 0 && $Bottom == 1) {
            $TopBottomStr1 = $Bottom_pdf;
        }
        if (!empty($TopBottomStr) || !empty($TopBottomStr1)) {
            $RegGlassStr = ($RegGlassStr != '') ? $RegGlassStr : '';
            $LargeGlassStr = ($LargeGlassStr != '') ? $LargeGlassStr : '';
        }
//////////////add_repairs
        if (!empty($additional_repairs)) {
            $RedTip_c = trim($_POST['additional_repairs_red'][$i]);
            $UltraLift_c = trim($_POST['additional_repairs_ultra'][$i]);
            $BlackTip_c = trim($_POST['additional_repairs_black'][$i]);
            $BlockTackle_c = trim($_POST['additional_repairs_block'][$i]);

            $Locks = trim($_POST['additional_locks'][$i]);
            $Shoes = trim($_POST['additional_Shoes'][$i]);
            $TiltLatch = trim($_POST['additional_TiltLatch'][$i]);
            $Caps = trim($_POST['additional_Caps'][$i]);
        } else {

            $RedTip_c = trim($_POST['repairs_red'][$i]);
            $UltraLift_c = trim($_POST['repairs_ultra'][$i]);
            $BlackTip_c = trim($_POST['repairs_black'][$i]);
            $BlockTackle_c = trim($_POST['repairs_block'][$i]);
            $Locks = trim($_POST['locks'][$i]);
            $Shoes = trim($_POST['shoes'][$i]);
            $TiltLatch = trim($_POST['tiltlatch'][$i]);
            $Caps = trim($_POST['caps'][$i]);
        }
//////////add_repairs end
//        if (!empty($RedTip_c)) {
//            $RegRepairs = trim($_POST['sash_value'][$i]);
//        }
//        if (!empty($UltraLift_c)) {
//            $UltraLift = trim($_POST['sash_value'][$i]);
//        }
//        if (!empty($BlackTip_c)) {
//            $BlackTip = trim($_POST['sash_value'][$i]);
//        }
//        if (!empty($BlockTackle_c)) {
//            $BlockTackle = trim($_POST['sash_value'][$i]);
//        }
//        if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle) && !empty($_POST['sash_value'][$i])) {
//            $BlackTip = trim($_POST['sash_value'][$i]);
//        }


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

//        $conn->query($query);
        $RegRepairs = $RedTip_c;
        $UltraLift = $UltraLift_c;
        $BlackTip = $BlackTip_c;
        $BlockTackle = $BlockTackle_c;
//Create PDF
//        echo 'here';
//        exit;
//        $pdftxt = '<div style="margin-bottom: -3px; padding-bottom: -3px;">';

        $Floor = empty($Floor) ? '<u>    </u>' : $Floor;
        $RegGlass = empty($RegGlass) ? '<u>    </u>' : $RegGlass;
        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
//        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
        $InsulatedUnit = empty($InsulatedUnit) ? '<u>    </u>' : $InsulatedUnit;
        $NewScreen = empty($NewScreen) ? '<u>    </u>' : $NewScreen;
        $ScreenRepair = empty($ScreenRepair) ? '<u>    </u>' : $ScreenRepair;
        $Moldings = empty($Moldings) ? '<u>    </u>' : $Moldings;
        $WindowGuards = empty($WindowGuards) ? '<u>    </u>' : $WindowGuards;
        $Capping = empty($Capping) ? '<u>    </u>' : $Capping;
        $Locks = empty($Locks) ? '<u>    </u>' : $Locks;
        $Shoes = empty($Shoes) ? '<u>    </u>' : $Shoes;
        $TiltLatch = empty($TiltLatch) ? '<u>    </u>' : $TiltLatch;
        $Pivot = empty($Pivot) ? '<u>    </u>' : $Pivot;
        $Caps = empty($Caps) ? '<u>    </u>' : $Caps;

        $Width = empty($Width) ? '<u>    </u>' : $Width;
        $Height = empty($Height) ? '<u>    </u>' : $Height;
        $glass_qty = empty($glass_qty) ? '<u>    </u>' : $glass_qty;

        $size_str = '';
        if (!empty($Width) || !empty($Height) || !empty($glass_qty)) {
            $size_str = ' <strong>Size</strong>  <u>' . $Width . '</u> <strong>x</strong> <u style="font-size: 12px;">' . $Height . '</u> ';
            $size_str1 = ' <strong>Size</strong>  <u  style="font-size: 12px;">' . $Width . '</u> x <u  style="font-size: 12px;">' . $Height . '</u> ';
        }
        if (empty($RegGlassStr)) {
            $size_str1 = '  <strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">   </u> ';
        }
        if (empty($LargeGlassStr)) {
            $size_str = '  <strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">   </u> ';
        }
        if (empty($RegGlassStr) && empty($LargeGlassStr)) {
            $size_str1 = '  <strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">   </u> ';
        }

        $RegGlassStr = empty($RegGlassStr) ? '<strong>S Glass</strong> <u style="font-size: 12px;">   </u> ' : $RegGlassStr . ' ';
        $LargeGlassStr = empty($LargeGlassStr) ? ' <strong>L Glass</strong> <u style="font-size: 12px;">   </u> ' : $LargeGlassStr . ' ';

//        $DoublePaneLarge_pdf = empty($DoublePaneLarge_pdf) ? '<img src="images/check-box.png"> DP' : $DoublePaneLarge_pdf . ' ';

        if (!empty($Room_pdf)) {
            if ($Room == 'Hallway') {
                $Room_pdf = '<img src="images/tick.png"> <strong>Hallway</strong>';
            } elseif ($Room == 'Skylight') {
                $Room_pdf = '<img src="images/tick.png"><strong>' . $Room_pdf . '</strong>';
            } else {
                $Room_pdf = '<u>' . $Room_pdf . '</u>';
            }
        } else {
            $Room_pdf4 = '';
            $Room_pdf4 .= '<img src="images/check-box.png"> Front Door<br>';
            $Room_pdf4 .= ' <img src="images/check-box.png"> Vest Door<br>';
            $Room_pdf4 .= '<img src="images/check-box.png"> Sidelite';
        }


        if (!empty($glass_type_pdf)) {
            $glass_type_pdf = '<img src="images/tick.png"> ' . $glass_type_pdf;
        } else {
            $glass_type_pdf = '<img src="images/check-box.png"> Lami <img src="images/check-box.png"> Plexi <img src="images/check-box.png"> PW ';
        }
//        $size_str = '';
//        if (empty($Width) && empty($Height)) {
//            if($Width == '' || $Height == ''){
//            $size_str = ' Size <u style="font-size: 12px;">' . $Width . '</u> x <u style="font-size: 12px;">' . $Height . ' </u>';
//        } else {
//            $size_str = ' Size: <u style="font-size: 12px;">' . $Width . '</u> x <u style="font-size: 12px;">' . $Height . ' </u>';
//        }
//        }
        $size_str3 = '';
        if ($Room == 'Hallway' || $Room == 'Skylight' || $Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite') {

            $size_str3 = '<strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">     </u>';
        } else {
            if (!empty($Width) && !empty($Height)) {
                $size_str3 = ' <strong>Size</strong> <u style="font-size: 12px;">' . $Width . '</u> <strong>x</strong> <u style="font-size: 12px;">' . $Height . '</u>';
            } elseif (!empty($Width)) {
                $size_str3 = ' <strong>Size</strong> <u style="font-size: 12px;">' . $Width . '</u> <strong>x</strong>';
            } elseif (!empty($Height)) {
                $size_str3 = ' <strong>Size</strong> <strong>x</strong> <u style="font-size: 12px;">' . $Height . '</u>';
            }
        }


        if ($Room == 'Hallway' || $Room == 'Skylight') {
            $hfloor = '';
            $hfloor = 'Floor <u>' . $Floor . ' ' . $glass_type_pdf . ' ' . $size_str3 . '</u>';
        }
        $string = $Notes;
        $words = explode(' ', $string); // Split the string into an array of words
        $first_five_words = array_slice($words, 0, 5); // Get the first 5 words
//        $Notes = implode(' ', $first_five_words);
        $Notes = '';
        if (empty($Notes)) {
            $Notes = '';
            $notes_pdf = '<table width="100%" style="margin: 0; padding: 0;">
                <tr><td width="100%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"></td><td style="margin: 0; padding: 0;">' . $Notes . '</td></tr>
            </table>';
        }
        if ($Room == 'Kitchen') {
            $Room_pdf = '<img src="images/tick.png"> <strong>KIT</strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } elseif ($Room == 'Bedroom') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/tick.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } elseif ($Room == 'Bathroom') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/tick.png"> <strong> BATH</strong> &nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } elseif ($Room == 'Living Room') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/tick.png"> <strong> LR</strong>';
        } elseif ($Room == '') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } else {
            if ($Room == 'Hallway' || $Room == 'Skylight' || $Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite') {
                $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
            } else {
                $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong><br><strong>' . $Room_pdf . '</strong>';
            }
        }

//        $Room_pdf = empty($Room_pdf) ? $Room_pdf . 'Room' : $Room_pdf;

        $TopBottomStr = empty($TopBottomStr) ? '<img src="images/check-box.png"> <strong>Top</strong><br>' : $TopBottomStr;
        $TopBottomStr1 = empty($TopBottomStr1) ? '<img src="images/check-box.png"> <strong> Bottom</strong>' : $TopBottomStr1;
        $topbottom = $TopBottomStr . $TopBottomStr1;

        $DoublePaneRegular_pdf = empty($DoublePaneRegular_pdf) ? '' : $DoublePaneRegular_pdf;
        $DoublePaneLarge = empty($DoublePaneLarge) ? '' : $DoublePaneLarge;
        if (!empty($additional_repairsStr)) {
            $additional_repairsStr = empty($additional_repairsStr) ? 'Additional Repairs  <u>    </u>' : $additional_repairsStr;
        }

        $additional_repairs = empty($additional_repairs) ? '<u>    </u>' : '' . $additional_repairs;
        if ($RedTip_c === '') {
            $RegRepairs = '<u>    </u>';
        }
        if ($UltraLift_c === '') {
            $UltraLift = '<u>    </u>';
        }

        if ($BlackTip_c === '') {
            $BlackTip = '<u>    </u>';
        }
        if ($BlockTackle_c === '') {
            $BlockTackle = '<u>    </u>';
        }
//        $RegRepairs = $RedTip_c != '' ? trim($_POST['sash_value'][$i]) : '<u>'.$RegRepairs.'</u>';
//        $UltraLift = $UltraLift_c!= '' ? trim($_POST['sash_value'][$i]) : '<u>'.$UltraLift.'</u>';
//        $BlackTip = $BlackTip_c!= '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $BlockTackle = $BlockTackle_c!= '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';


        $IU_str = '';
        $IU_str .= ' <strong>IU</strong> <u style="font-size: 12px;">' . $InsulatedUnit . '</u>';
        $NewScreen_str = '';
        $NewScreen_str .= '<strong>NewScreen</strong>  <u style="font-size: 12px;">' . $NewScreen . '</u>';
        $ScreenRepair_str = '';
        $ScreenRepair_str .= ' <strong>ScreenRep</strong>  <u style="font-size: 12px;">' . $ScreenRepair . '</u> ';

        $pdftxt .= '<tr>';
        $pdftxt .= '<td colspan="2" style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; width:25%;">';
        $pdftxt .= '<table style="width: 100%;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width: 50%;" rowspan="2">' . $Room_pdf . '</td>';
//        $pdftxt .= '<td style="width: 50%; text-align: left;">' . $Room_pdf . '</td>';
        $pdftxt .= '<td style="width: 50%;">' . $TopBottomStr . $TopBottomStr1 . '</td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td style="width:75%; border-top: 1px solid black; ">';
        $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= $RegGlassStr;
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= $LargeGlassStr;
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= $DoublePaneLarge_and_Regular_pdf;
        $pdftxt .= '</td>';

        $pdftxt .= '<td>';
        $pdftxt .= $size_str3;
        $pdftxt .= '</td>';

        $pdftxt .= '<td>';
        $pdftxt .= $IU_str;
        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= $NewScreen_str;
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= $ScreenRepair_str;
        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';

        $pdftxt .= '</td>';
//        $pdftxt .= '<td colspan="2" style="border-top: 1px solid black; width:75%;">' . $RegGlassStr . $LargeGlassStr . ' ' . $DoublePaneLarge_and_Regular_pdf . ' ' . $size_str3 . $other_str . '</td>';
        $pdftxt .= '</tr>';

        $pdftxt .= '<tr>';
        $pdftxt .= '<td colspan="2"  style=" border-left: 1px solid black; border-right: 1px solid black; border-bottom:1px solid black;  width:25%;">';
        $pdftxt .= '<table style="width: 100%;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width: 50%; text-align: left;"></td>';
        $pdftxt .= '<td style="width: 50%; text-align: left;"></td>';

        $pdftxt .= '<td style="width: 50%;"></td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td colspan="2" style="border-left: 1px solid black;  width:75%;">';
        $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
//echo 'sac'.$RegRepairs;
        if (!empty($additional_repairsStr)) {
//            echo $RedTip_c;
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Caps)) {
//                if (!empty($RedTip_c)) {
////                    $RegRepairs = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $RegRepairs;
//                } else {
//                    $RegRepairs = '<u>    </u>';
//                }
//                if (!empty($UltraLift_c)) {
////                    $UltraLift = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $UltraLift;
//                } else {
//                    $UltraLift = '<u>    </u>';
//                }
//                if (!empty($BlackTip_c)) {
////                    $BlackTip = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $BlackTip;
//                } else {
//                    $BlackTip = '<u>    </u>';
//                }
//                if (!empty($BlockTackle_c)) {
////                    $BlockTackle = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $BlockTackle;
//                }
//                else {
//                    $BlockTackle = '<u>    </u>';
//                }
                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>RB</strong> <u style="font-size: 12px;">' . $RegRepairs . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>UL</strong> <u style="font-size: 12px;">' . $UltraLift . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BTB</strong> <u style="font-size: 12px;">' . $BlockTackle . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BB</strong> <u style="font-size: 12px;">' . $BlackTip . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Locks</strong> <u style="font-size: 12px;">' . $Locks . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Shoes</strong> <u >' . $Shoes . '</u>';
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';

                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>TL</strong> <u style="font-size: 12px;">' . $TiltLatch . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Pivot</strong> <u style="font-size: 12px;">' . $Pivot . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Caps</strong> <u style="font-size: 12px;">' . $Caps . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>WG</strong> <u style="font-size: 12px;">' . $WindowGuards . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Molding</strong> <u style="font-size: 12px;">' . $Moldings . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Cap</strong> <u style="font-size: 12px;">' . $Capping . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';

//                $pdftxt .= '<tr>';
//                $pdftxt .= '<td>';
//                $pdftxt .= '<strong>AddRep</strong> <u style="font-size: 12px;">' . $additional_repairs . '</u>';
//                $pdftxt .= '</td>';
//
//                $pdftxt .= '</tr>';
// Notes
                $pdftxt .= '<tr>';
                $pdftxt .= '<td style="width:100%;">'; // Add margin-top here


                $pdftxt .= '<table cellpadding="" width="100%" >';
                $pdftxt .= '<strong>Notes </strong><tr><td width="90%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"><span style="font-size: 12px;">' . $Notes . '</span></td></tr>';

                $pdftxt .= '</table>';

                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';
            }
        } else {
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Pivot) || !empty($Caps)) {

                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>RB</strong> <u style="font-size: 12px;">' . $RegRepairs . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>UL</strong> <u style="font-size: 12px;">' . $UltraLift . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BTB </strong> <u style="font-size: 12px;">' . $BlockTackle . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BB</strong> <u style="font-size: 12px;">' . $BlackTip . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Locks</strong> <u style="font-size: 12px;">' . $Locks . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Shoes</strong> <u style="font-size: 12px;">' . $Shoes . '</u>';
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';

                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>TL</strong> <u style="font-size: 12px;">' . $TiltLatch . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Pivot</strong> <u style="font-size: 12px;">' . $Pivot . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Caps</strong> <u style="font-size: 12px;">' . $Caps . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>WG</strong> <u style="font-size: 12px;">' . $WindowGuards . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Molding</strong> <u style="font-size: 12px;">' . $Moldings . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Cap</strong> <u style="font-size: 12px;">' . $Capping . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';

// Notes
                $pdftxt .= '<tr>';
                $pdftxt .= '<td style="width:100%;">'; // Add margin-top here


                $pdftxt .= '<table cellpadding="" width="100%" >';
                $pdftxt .= '<strong>Notes </strong><tr><td width="90%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"><span style="font-size: 12px;">' . $Notes . '</span></td></tr>';

                $pdftxt .= '</table>';

                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';
            }
        }

        $pdftxt .= '</table>';

        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
    }

    $Room_pdf_static = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';

//    $Room_pdf = empty($Room_pdf) ? $Room_pdf . '<img src="images/check-box.png">  Room:' : $Room_pdf;
//    $TopBottomStr = empty($TopBottomStr) ? '<img src="images/check-box.png">  Top<br>' : $TopBottomStr;
//    $TopBottomStr1 = empty($TopBottomStr1) ? '<img src="images/check-box.png">  Bottom' : $TopBottomStr1;
    for ($i = 0;
            $i < 1;
            $i++) {

        $pdftxt .= '<tr>';
        $pdftxt .= '<td rowspan="2" style=" border-left: 1px solid black; border-right: 1px solid black; width:25%;">';
        $pdftxt .= '<table style="width: 100%;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td >';
        $pdftxt .= $Room_pdf_static;
        $pdftxt .= '</td>';
        $pdftxt .= '<td >';
        $pdftxt .= '<img src="images/check-box.png"><strong> Top</strong><br>';
        $pdftxt .= '<img src="images/check-box.png"> <strong> Bottom</strong>';
        $pdftxt .= '</td>';
//
        $pdftxt .= '</tr>';
//
        $pdftxt .= '</table>';
        $pdftxt .= '</td>';

        $pdftxt .= '<td style="width:75%; border-top: 1px solid black; ">';
        $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>S Glass</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>L Glass</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong><img src="images/check-box.png"> <strong> DP</strong></strong> <u style="font-size: 12px;"></u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Size</strong> <u style="font-size: 12px;">    </u> <strong>x</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>IU</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>NewScreen</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>ScreenRep</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';
//        $pdftxt .= '<strong>S Glass</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//
//        $pdftxt .= ' <strong>L Glass</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//        $pdftxt .= ' <img src="images/check-box.png"> <strong>DP</strong><span style="text-decoration: underline; white-space: pre; font-size: 14px;"></span>';
//        $pdftxt .= ' <strong>Size</strong>  <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//        $pdftxt .= ' <strong>x</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//        $pdftxt .= ' <strong>IU</strong>  <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span><br>';
//        $pdftxt .= '<strong>NewScreen</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">           </span>';
//        $pdftxt .= ' <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ScreenRep</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">             </span>';

        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
////        second tr
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width: 75%; border-left: 1px solid black; padding: 8px;">';
        $pdftxt .= '<table>';
// Second row
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>RB</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>UL</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>BTB</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>BB</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Locks</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Shoes</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
//
//// Third row
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>TL</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Pivot</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Caps</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>WG</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Moldings</strong><u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Cap</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';

//         Notes
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width:100%;">'; // Add margin-top here


        $pdftxt .= '<table cellpadding="2" width="100%" style="margin: 0; padding: 0;">';
        $pdftxt .= '<strong>Notes</strong><tr><td width="90%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"></td></tr>';

        $pdftxt .= '</table>';

        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';

        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
    }


    $pdftxt .= '</table>';
    $pdf_text_arr[] = $pdftxt;

    $pdftxt .= '</table>';

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
    $pdf->SetMargins(PDF_MARGIN_LEFT, 41, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 11);

// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
    $text = '<table cellspacing="2" cellpadding="">';
    $text .= '<tr>';
    $text .= '<td width="6%"><b>JobID:</b></td>'; // Label without underline
    $text .= '<td width="72%" style="border-bottom: 1px solid black; margin: 0; padding: 0;" colspan="1">' . $_SESSION['JobID'] . '</td>'; // Underlined text
    $text .= '<td width="7%">&nbsp;&nbsp;   <b>Date:</b></td>'; // Label without underline
    $text .= '<td width="15%" style="border-bottom: 1px solid black; margin: 0; padding: 0;" colspan="1">' . date('m/d/Y') . '</td>'; // Underlined text
    $text .= '</tr>';
    $text .= '</table>';

    $pdf->writeHTMLCell($w = 0, $h = 7, $x = 15, $y = 40, $text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//    $text = '<b>JobID:</b> ' . $_SESSION['JobID'];
//    $pdf->writeHTMLCell($w = 0, $h = 10, $x = 16, $y = 40, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
//   $text = '<b>Date:</b> ' . date('m/d/Y');
//    $pdf->writeHTMLCell($w = 0, $h = 10, $x = 16, $y = 50, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $pdf->SetFont('helvetica', '', 9);
    preg_match('~>\K[^<>]*(?=<)~', $Address, $match);
    $pdftxt_header = '<table cellspacing="0" cellpadding="" style="border-collapse: collapse;">'; // Add cellpadding to add space inside cells
    $pdftxt_header .= '<tr style="padding-bottom: 10px;">';
    $pdftxt_header .= '<td width="9%" style="padding-bottom: 10px;"><b>Address:</b></td>'; // Add padding-bottom for spacing
    $pdftxt_header .= '<td width="47%" style="border-bottom: 1px solid black; margin: 0; padding-bottom: 10px;" colspan="1">' . $match[0] . '</td>'; // Underlined text with padding
    $pdftxt_header .= '<td width="6%" style="padding-bottom: 10px;">&nbsp;&nbsp;<b>APT:</b></td>'; // Add padding-bottom for spacing
    $pdftxt_header .= '<td width="10%" style="border-bottom: 1px solid black; margin: 0; padding-bottom: 10px;" colspan="1">' . $Apt . '</td>'; // Underlined text with padding
    $pdftxt_header .= '<td width="12%" style="padding-bottom: 10px;">&nbsp;&nbsp;<b>Technician:</b></td>'; // Add padding-bottom for spacing
    $pdftxt_header .= '<td width="15%" style="border-bottom: 1px solid black; margin: 0; padding-bottom: 10px;" colspan="1">' . $_SESSION['User']['TechName'] . '</td>'; // Underlined text with padding
    $pdftxt_header .= '</tr><br><br>';
    $pdftxt_header .= '</table>';
    $tbl1 = <<<EOD
            $pdftxt_header
            EOD;
    $pdf->writeHTML($tbl1, true, true, false, false, '');
    $pdf->SetFont('helvetica', '', 10);
    $i = 0;
    foreach ($pdf_text_arr as $pdf_text) {
        if ($i % 2 == 0 && $i > 0) {
            $pdf->AddPage();
            $pdf_text = '' . $pdf_text;
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

    $html = '<table style="width: 100%;">'; // Set the height of the table here
// Get the current Y position
    $initialY = $pdf->GetY();
    if (!empty($file)) {
        $width = 60;  // Width of the image
        $height = 20; // Height of the image
        // Add the first image at a certain X and Y position
        $pdf->Image($file, 17, $initialY, $width, $height);
        // After adding the first image, update the Y position for the next image
        $initialY = $pdf->GetY();  // Get the new Y position after the first image
    }
// Check if the second tech image file is not empty
    if (!empty($techfile)) {
        $width = 60;  // Width of the second image
        $height = 20; // Height of the second image
        // Add the second image with the updated Y position
        $pdf->Image($techfile, 97, $initialY, $width, $height);
        // After adding the second image, update the Y position again if needed
        $initialY = $pdf->GetY();  // Get the new Y position after the second image
    }

    if (!empty($file)) {
        $html .= '<tr>';
        $html .= '<td style="width: 35%; height: 100px; text-align: center;">';
        $html .= '<br><br><div style=" border-bottom: 1px solid black;">';
        $html .= '<img src="' . $file . '" width="100" height="80">';
        $html .= '</div>';

        $html .= '<strong style="text-align: left;">Tenant Signature</strong>'; // Add label here
        $html .= '</td>';
        $html .= '<td style="width: 10%;"></td>';
    } else {
        $html .= '<tr><td style="width: 35%;"></td><td style="width: 10%;"></td>';
    }
    if (!empty($techfile)) {
        $html .= '<td style="width: 35%; height: 100px; text-align: center;">';
        $html .= '<strong></strong><br><div style="border-bottom: 1px solid black;">';
        $html .= '<img src="' . $techfile . '" width="100" height="80">';
        $html .= '</div><br>';
        $html .= '<strong style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;Tech Signature</strong>';
        $html .= '</td></tr>';
    } else {
        $html .= '<td style="width: 35%;"></td></tr>';
    }
//$html .= '<tr><td><strong>Tenant Signature1</strong></td><td></td><td><strong>Tech Signature1</strong></td></tr>';
    $html .= '</table>';
//    end
    $html .= '<tr>';
    $html .= '<td style="width: 40%; font-size: 8px;"><br><br><strong>KIT</strong> = KITCHEN, <strong>BR</strong> = BEDROOM, <strong>BATH</strong> = BATHROOM, <br><strong>LR</strong> = LIVING ROOM, <strong>RW</strong> = ROUGH WIRE, <strong>PW</strong> = POLY WIRE, <br><strong>CG</strong> = CLEAR GLASS, <strong>IU</strong> = INSULATED UNITS, <strong>TL</strong> = TILT LATCH, <br><strong>RB</strong> = RED TIP BALANCES, <strong>BB</strong> = BLACK TIP BALANCES, <br><strong>DP</strong> = DOUBLE PANE, <strong>S GLASS</strong> = STANDARD GLASS, <br><strong>L GLASS</strong> = LARGE GLASS, <strong>UL</strong> = ULTRA LIFT BALANCE, <br><strong>WG</strong> = Window guards, <strong>Cap</strong> = Capping, <strong>BTB</strong> = BlockTackle, <br><strong>ScreenRep</strong> = SCREENREPAIR
</td>';
    $html .= '</tr>';
    $html .= '</table>';
    $tagvs = [
        'div' => [
            0 => ['h' => 3.2, 'n' => 3.2],
            1 => ['h' => 0, 'n' => 0]
        ],
    ];
    $pdf->setHtmlVSpace($tagvs);
    $pdf->writeHTML($html, true, true, false, false, '');

//    $pdf->writeHTML('<br><br><br><br><br><hr>', true, false, false, false, '');
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
    $pdftxt = '<table cellpadding="5" style="border: 1px solid black;">';

    $total_box = $_POST['total_box'];

    $pdf_text_arr = array();

    for ($i = 0;
            $i < $total_box;
            $i++) {
        $Room = '';
        $Floor = $Top = $Bottom = $RegGlass = $additional_repairs = $LargeGlass = $DoublePaneLarge = $DoublePaneRegular = $RegRepairs = $UltraLift_c = $RedTip_c = $BlackTip_c = $BlockTackle_c = $UltraLift = 0;
        $BlackTip = $BlockTackle = $LAMI = $Plexi = $RoughWire = $PolyWire = $RoughWireClear = $PolyWireClear = 0;
        $sash_value = 0;
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
        $topbottom = '';
        $TopBottomStr = '';
        $TopBottomStr1 = '';
        $Top = isset($_POST['top_glass'][$i]) ? 1 : 0;
        if ($Top !== 0) {
            $Top_pdf = '<img src="images/tick.png"> <strong>Top</strong>';
        }
//        $Top_pdf = isset($_POST['top_glass'][$i]) ? '<img src="images/tick.png"> <strong>Top</strong>' : '';
        $Bottom = isset($_POST['bottom_glass'][$i]) ? 1 : 0;
        if ($Bottom !== 0) {
            $Bottom_pdf = '<img src="images/tick.png"> <strong> Bottom</strong>';
        }
//        $Bottom_pdf = isset($_POST['bottom_glass'][$i]) ? '<img src="images/tick.png"> <strong> Bottom</strong>' : '';
        $RegGlass = trim($_POST['regular_glass'][$i]);
        $additional_repairs = trim($_POST['additional_repair'][$i]);
        $DoublePaneRegular = isset($_POST['double_pane_rg'][$i]) ? 1 : 0;
        $DoublePaneRegular_pdf = isset($_POST['double_pane_rg'][$i]) ? ' Double Pane' : '';
        $LargeGlass = trim($_POST['large_glass'][$i]);
        $DoublePaneLarge = isset($_POST['double_pane_lg'][$i]) ? 1 : 0;
        $DoublePaneLarge_pdf = isset($_POST['double_pane_lg'][$i]) ? ' Double Pane' : '';

        if ($RegGlass != '' && $RegGlass > 0) {
            $RegGlassStr = 'Standard Clear Glass <u style="font-size: 12px;">' . $RegGlass . '</u>';
        }
        if ($LargeGlass != '' && $LargeGlass > 0) {
            $LargeGlassStr = 'Large Clear Glass <u style="font-size: 12px;">' . $LargeGlass . '</u>';
        }
        if ($additional_repairs != '' && $additional_repairs > 0) {
            $additional_repairsStr = 'Additional Repairs: <u>' . $additional_repairs . '</u>';
        }



        if ($Top == 1 && $Bottom == 1) {
            $TopBottomStr = $Top_pdf . '<br>';
            $TopBottomStr1 = $Bottom_pdf;
        } else if ($Top == 1 && $Bottom == 0) {
            $TopBottomStr = $Top_pdf . '<br>';
        } else if ($Top == 0 && $Bottom == 1) {
            $TopBottomStr1 = $Bottom_pdf;
        }
        if (!empty($TopBottomStr) || !empty($TopBottomStr1)) {
            $RegGlassStr = ($RegGlassStr != '') ? $RegGlassStr : '';
            $LargeGlassStr = ($LargeGlassStr != '') ? $LargeGlassStr : '';
        }
//////////////add_repairs
        if (!empty($additional_repairs)) {
            $RedTip_c = trim($_POST['additional_repairs_red'][$i]);
            $UltraLift_c = trim($_POST['additional_repairs_ultra'][$i]);
            $BlackTip_c = trim($_POST['additional_repairs_black'][$i]);
            $BlockTackle_c = trim($_POST['additional_repairs_block'][$i]);

            $Locks = trim($_POST['additional_locks'][$i]);
            $Shoes = trim($_POST['additional_Shoes'][$i]);
            $TiltLatch = trim($_POST['additional_TiltLatch'][$i]);
            $Caps = trim($_POST['additional_Caps'][$i]);
        } else {
            $RegRepairs = trim($_POST['repairs_red'][$i]);
            $UltraLift = trim($_POST['repairs_ultra'][$i]);
            $BlackTip = trim($_POST['repairs_black'][$i]);
            $BlockTackle = trim($_POST['repairs_block'][$i]);
            $Locks = trim($_POST['locks'][$i]);
            $Shoes = trim($_POST['shoes'][$i]);
            $TiltLatch = trim($_POST['tiltlatch'][$i]);
            $Caps = trim($_POST['caps'][$i]);
            $sash_value = trim($_POST['sash_value'][$i]);
        }
//////////add_repairs end
//        if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle) && !empty($_POST['sash_value'][$i])) {
//            $BlackTip = trim($_POST['sash_value'][$i]);
//        }

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
        $glass_type_pdf = isset($_POST['clear_glass_rw'][$i]) ? $glass_type_pdf : $glass_type_pdf;
        $PolyWireClear = isset($_POST['clear_glass_pw'][$i]) ? 1 : 0;
        $PolyWireClear_pdf = isset($_POST['clear_glass_pw'][$i]) ? 'Yes' : 'No';
        $glass_type_pdf = isset($_POST['clear_glass_pw'][$i]) ? $glass_type_pdf : $glass_type_pdf;
        $Clear_pdf = '';
        if ($RoughWireClear == 1) {
            $Clear_pdf = 'CG ' . $RoughWireClear_pdf;
        } else if ($PolyWireClear == 1) {
            $Clear_pdf = 'CG ' . $PolyWireClear_pdf;
        }
        $Clear_pdf_CG = '';
        if ($RoughWireClear == 1 || $PolyWireClear == 1) {
            $Clear_pdf_CG = 'CG ';
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
        $RegRepairs = empty($RegRepairs) ? 0 : $RegRepairs;
        $UltraLift = empty($UltraLift) ? 0 : $UltraLift;
        $BlackTip = empty($BlackTip) ? 0 : $BlackTip;
        $BlockTackle = empty($BlockTackle) ? 0 : $BlockTackle;
        $RedTip_c = empty($RedTip_c) ? 0 : $RedTip_c;
        $UltraLift_c = empty($UltraLift_c) ? 0 : $UltraLift_c;
        $BlackTip_c = empty($BlackTip_c) ? 0 : $BlackTip_c;
        $BlockTackle_c = empty($BlockTackle_c) ? 0 : $BlockTackle_c;
        $sash_value = empty($sash_value) ? 0 : $sash_value;

        $query = "insert into JobDetailsTable(JobID,Room,Floor,Top,Bottom,RegGlass,DoublePaneRegular,LargeGlass,DoublePaneLarge,"
                . "RedTip,UltraLift,BlackTip,BlockTackle,LAMI,Plexi,RoughWire,RoughWireClear,PolyWire,PolyWireClear,Height,Width,InsulatedUnit,"
                . "NewScreen,ScreenRepair,Moldings,WindowGuards,Capping,Locks,Shoes,TiltLatch,Pivot,Caps,Notes,RedTip_c,UltraLift_c,BlackTip_c,BlockTackle_c,additional,GTQty,Status,SashRepairs) values "
                . "('" . $JobID . "','" . $Room . "','" . $Floor . "','" . $Top . "','" . $Bottom . "','" . $RegGlass . "','" . $DoublePaneRegular . "','" . $LargeGlass . "',"
                . "'" . $DoublePaneLarge . "','" . $RegRepairs . "','" . $UltraLift . "','" . $BlackTip . "',"
                . "'" . $BlockTackle . "','" . $LAMI . "','" . $Plexi . "','" . $RoughWire . "','" . $RoughWireClear . "','" . $PolyWire . "','" . $PolyWireClear . "','" . $Height . "',"
                . "'" . $Width . "','" . $InsulatedUnit . "','" . $NewScreen . "','" . $ScreenRepair . "','" . $Moldings . "','" . $WindowGuards . "',"
                . "'" . $Capping . "','" . $Locks . "','" . $Shoes . "','" . $TiltLatch . "','" . $Pivot . "','" . $Caps . "','" . $Notes . "','" . $RedTip_c . "','" . $UltraLift_c . "','" . $BlackTip_c . "','" . $BlockTackle_c . "','" . $additional_repairs . "','" . $glass_qty . "','1','" . $sash_value . "')";

        $conn->query($query);
        $RegRepairs = $RedTip_c;
        $UltraLift = $UltraLift_c;
        $BlackTip = $BlackTip_c;
        $BlockTackle = $BlockTackle_c;
//Create PDF
//        echo 'here';
//        exit;
//        $pdftxt = '<div style="margin-bottom: -3px; padding-bottom: -3px;">';

        $Floor = empty($Floor) ? '<u>    </u>' : ' <strong>Floor</strong> - <u style="font-size: 12px;">' . $Floor . '</u>';
        $RegGlass = empty($RegGlass) ? '<u>    </u>' : $RegGlass;
        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
        $InsulatedUnit = empty($InsulatedUnit) ? '<u>    </u>' : $InsulatedUnit;
        $NewScreen = empty($NewScreen) ? '<u>    </u>' : $NewScreen;
        $ScreenRepair = empty($ScreenRepair) ? '<u>    </u>' : $ScreenRepair;
        $Moldings = empty($Moldings) ? '<u>    </u>' : $Moldings;
        $WindowGuards = empty($WindowGuards) ? '<u style="font-size: 12px;">    </u> ' : '<u style="font-size: 12px;">' . $WindowGuards . '</u> ';
        $Capping = empty($Capping) ? '<u style="font-size: 12px;">    </u>' : '<u style="font-size: 12px;">' . $Capping . '</u> ';
        $Locks = empty($Locks) ? '<u style="font-size: 12px;">    </u>' : $Locks;
        $Shoes = empty($Shoes) ? '<u style="font-size: 12px;">    </u>' : $Shoes;
        $TiltLatch = empty($TiltLatch) ? '<u style="font-size: 12px;">    </u>' : $TiltLatch;
        $Pivot = empty($Pivot) ? '<u style="font-size: 12px;">    </u>' : $Pivot;
        $Caps = empty($Caps) ? '<u style="font-size: 12px;">    </u>' : $Caps;

        $Width = empty($Width) ? '<u>    </u>' : $Width;
        $Height = empty($Height) ? '<u>    </u>' : $Height;
        $glass_qty = empty($glass_qty) ? '<u>    </u>' : $glass_qty;

        $size_str = '';
        if (!empty($Width) || !empty($Height) || !empty($glass_qty)) {
            $size_str = ' Size  <u  style="font-size: 30px;">' . $Width . '</u> x <u   style="font-size: 30px;">' . $Height . '</u> ';
            $size_str1 = ' Size  <u  style="font-size: 30px;">' . $Width . '</u> x <u  style="font-size: 30px;">' . $Height . '</u>';
        }
        if (empty($RegGlassStr)) {
            $size_str1 = '  Size  <u>   </u> x <u>   </u>';
        }
        if (empty($LargeGlassStr)) {
            $size_str = '  Size  <u>   </u> x <u>   </u>';
        }
        if (empty($RegGlassStr) && empty($LargeGlassStr)) {
            $size_str1 = '  Size  <u>   </u> x <u>   </u>';
        }

        $RegGlassStr = empty($RegGlassStr) ? 'Standard Clear Glass <u>   </u> ' . $size_str1 : $RegGlassStr . ' ' . $size_str;

        $LargeGlassStr = empty($LargeGlassStr) ? ' Large Clear Glass <u>   </u> ' . $size_str : $LargeGlassStr . ' ' . $size_str;

//        $RegGlassStr = empty($RegGlassStr) ? 'Glass: <u>    </u>' : $RegGlassStr;
//        $LargeGlassStr = empty($LargeGlassStr) ? 'Glass:<u style="width:1%;">    </u>' : $LargeGlassStr;
        if (!empty($Room_pdf)) {
            if ($Room == 'Hallway') {
                $Room_pdf = '&nbsp;<img src="images/tick.png"> <strong>Hallway</strong>' . $Floor . '<br>&nbsp;<img src="images/check-box.png"> <strong>Skylight</strong>';
            } elseif ($Room == 'Skylight') {
                $Room_pdf = '&nbsp;<img src="images/check-box.png"> <strong>Hallway Floor - <u>    </u></strong><br>&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong><br>';
            } elseif ($Room == 'Front Door') {
                $Room_pdf = '&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
            } elseif ($Room == 'Vest Door') {
                $Room_pdf = '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
            } elseif ($Room == 'Sidelite') {
                $Room_pdf = '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>&nbsp;<img src="images/tick.png"> <strong>' . $Room_pdf . '</strong>';
            }
//            else {
//                $Room_pdf = '';
//                $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>';
//                $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>';
//                $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
//            }
        } else {
            $Room_pdf = '';
            $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>';
            $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>';
            $Room_pdf .= '&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
        }

//        echo $glass_type_pdf;exit;
        if (!empty($glass_type_pdf)) {
            if ($glass_type_pdf == 'Lami') {
                $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/tick.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
            } elseif ($glass_type_pdf == 'Plexi') {
                $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/tick.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
            } elseif ($glass_type_pdf == 'RW') {
                if ($glass_type_pdf == 'RW' || $Clear_pdf_CG == 'CG') {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                } else {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                }
            } elseif ($glass_type_pdf == 'PW') {
                if ($glass_type_pdf == 'PW' || $Clear_pdf_CG == 'CG') {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                } else {
                    $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/tick.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
                }
            }
//            $glass_type_pdf = '<img src="images/tick.png"> ' . $glass_type_pdf;
        } else {
            $glass_type_pdf = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
        }

        $glass_type_pdf_hall = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';
        $glass_type_pdf_door = '<img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <span style="display: inline-block; margin: 0; width: 12px; height: 12px;"><strong>Lami</strong>&nbsp;&nbsp;&nbsp;&nbsp; </span> <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;" src="images/check-box.png"> <strong>Plexi</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>PW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>RW</strong>&nbsp;&nbsp;&nbsp;&nbsp; <img  style="display: inline-block; margin: 0; width: 12px; height: 12px;"src="images/check-box.png"> <strong>CG</strong>&nbsp;&nbsp;&nbsp;&nbsp; ';

        $size_str2 = '';
        if ($Room == 'Hallway' || $Room == 'Skylight' || $Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite') {

            if (!empty($Width) && !empty($Height)) {
                $size_str2 = ' <strong>Size</strong> <u  style="font-size: 12px;">' . $Width . '</u> <strong>x</strong> <u  style="font-size: 12px;">' . $Height . '</u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">' . $Moldings . '</u>';
            } elseif (!empty($Width)) {
                $size_str2 = ' <strong>Size</strong> <u  style="font-size: 12px;">' . $Width . '</u> <strong>x</strong> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">' . $Moldings . '</u>';
            } elseif (!empty($Height)) {
                $size_str2 = ' <strong>Size</strong> <strong>x</strong> <u  style="font-size: 12px;">' . $Height . '</u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">' . $Moldings . '</u>';
            } else {
                $size_str2 = ' <strong>Size</strong> <u  style="font-size: 12px;">  </u> <strong>x</strong> <u  style="font-size: 12px;">   </u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">   </u>';
            }
        }
        $size_str2_hall = ' <strong>Size</strong> <u  style="font-size: 12px;">  </u> <strong>x</strong> <u  style="font-size: 12px;">   </u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">   </u>';
        $size_str2_door = ' <strong>Size</strong> <u  style="font-size: 12px;">  </u> <strong>x</strong> <u  style="font-size: 12px;">   </u> <strong>&nbsp;&nbsp;&nbsp;&nbsp; Moldings</strong> <u  style="font-size: 12px;">   </u>';

        $hfloor = '';
        if ($Room == 'Hallway' || $Room == 'Skylight') {
            $hfloor = $glass_type_pdf . ' ' . $size_str2;
        }
        $Room_pdf_sta = '';
        $Room_pdf_sta .= '&nbsp;<img src="images/check-box.png"> <strong>Front Door</strong><br>';
        $Room_pdf_sta .= '&nbsp;<img src="images/check-box.png"> <strong>Vest Door</strong><br>';
        $Room_pdf_sta .= '&nbsp;<img src="images/check-box.png"> <strong>Sidelite</strong>';
        $Room_pdf_hall = '';
        $Room_pdf_hall .= '&nbsp;<img src="images/check-box.png"> <strong>Hallway Floor - <u>    </u></strong><br>';
        $Room_pdf_hall .= '&nbsp;<img src="images/check-box.png"> <strong>Skylight</strong><br>';

        $string = $Notes;
        $words = explode(' ', $string); // Split the string into an array of words
        $first_five_words = array_slice($words, 0, 5); // Get the first 5 words
//        $Notes = implode(' ', $first_five_words); // Print the first 5 words separated by space
        $Notes = ''; // Print the first 5 words separated by space
        if (!empty($Notes)) {
            $notes_pdf = '';
            $notes_pdf = '<table width="100%" style="margin: 0; padding: 0;">
                <tr><td width="100%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"> <span style="font-size: 12px;">' . $Notes . '</span></td><td style="margin: 0; padding: 0;"></td></tr>
            </table>';
        } else {
            $notes_pdf = '';
            $notes_pdf = '<table width="100%" style="margin: 0; padding: 0;">
                <tr><td width="100%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"></td></tr>
            </table>';
        }

        $TopBottomStr = empty($TopBottomStr) ? '<img src="images/check-box.png">  <strong>Top</strong><br>' : $TopBottomStr;
        $TopBottomStr1 = empty($TopBottomStr1) ? '<img src="images/check-box.png"> <strong> Bottom</strong>' : $TopBottomStr1;
        $DoublePaneRegular_pdf = empty($DoublePaneRegular_pdf) ? '' : $DoublePaneRegular_pdf;
        $DoublePaneLarge = empty($DoublePaneLarge) ? '' : $DoublePaneLarge;
        if (!empty($additional_repairsStr)) {
            $additional_repairsStr = empty($additional_repairsStr) ? 'Additional Repairs: <u>    </u>' : $additional_repairsStr;
        }
        $additional_repairs = empty($additional_repairs) ? '<u>    </u>' : $additional_repairs;
//        $RegRepairs = $RedTip_c == '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $UltraLift = $UltraLift_c != '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $BlackTip = $BlackTip_c != '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $BlockTackle = $BlockTackle_c != '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';

        $add_rep = '';
        if (!empty($additional_repairsStr)) {
            $add_rep .= $additional_repairsStr . ' ';
            $add_rep .= 'RegRepairs: <u>' . $RegRepairs . '</u> ';
            $add_rep .= 'UltraLift: <u>' . $UltraLift . '</u> ';
            $add_rep .= 'BlockTackle: <u>' . $BlockTackle . '</u> ';
            $add_rep .= 'BlackTip: <u>' . $BlackTip . '</u> ';
            $add_rep .= 'Locks: <u>' . $Locks . '</u> ';
            $add_rep .= 'Shoes: <u>' . $Shoes . '</u> ';
            $add_rep .= 'TL: <u>' . $TiltLatch . '</u> ';
            $add_rep .= 'Pivot: <u>' . $Pivot . '</u> ';
            $add_rep .= 'Caps: <u>' . $Caps . '</u> ';
        } else {
            $add_rep .= 'RegRepairs: <u>' . $RegRepairs . '</u> ';
            $add_rep .= 'UltraLift: <u>' . $UltraLift . '</u> ';
            $add_rep .= 'BlockTackle: <u>' . $BlockTackle . '</u> ';
            $add_rep .= 'BlackTip: <u>' . $BlackTip . '</u> ';
            $add_rep .= 'Locks: <u>' . $Locks . '</u> ';
            $add_rep .= 'Shoes: <u>' . $Shoes . '</u> ';
            $add_rep .= 'TL: <u>' . $TiltLatch . '</u> ';
            $add_rep .= 'Pivot: <u>' . $Pivot . '</u> ';
            $add_rep .= 'Caps: <u>' . $Caps . '</u> ';
        }
        $WG_str = '';
        $WG_str .= '<strong>WG</strong>  ' . $WindowGuards;
        $Cap_str = '';
        $Cap_str .= '<strong>Cap</strong>  ' . $Capping;

        $WG_str_hall = '';
        $WG_str_hall .= '<strong>WG</strong> <u>    </u>';
        $Cap_str_hall = '';
        $Cap_str_hall .= '<strong>Cap</strong> <u>    </u>';

        $WG_str_door = '';
        $WG_str_door .= '<strong>WG</strong> <u>    </u>';
        $Cap_str_door = '';
        $Cap_str_door .= '<strong>Cap</strong> <u>    </u>';
//static door start
        if ($i === 0) {
            if (!empty($Room_pdf) && $Room !== 'Front Door' && $Room !== 'Vest Door' && $Room !== 'Sidelite') {
                $pdftxt .= '<tr>';

                $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf_sta . '</td>';
                $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $glass_type_pdf_door . ' ' . $size_str2_door . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
                $pdftxt .= '</tr>';
//new tr
                $pdftxt .= '<tr>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
                $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= $WG_str_door;
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= $Cap_str_door;
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';
                $pdftxt .= '</table>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

                $pdftxt .= '</tr>';
            }
        }

//static door end

        $pdftxt .= '<tr>';

        if (!empty($Room) && ($Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite')) {
            $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf . '</td>';
        } elseif ($Room == 'Skylight') {
            $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf . '</td>';
        } elseif ($Room == 'Hallway') {
            $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf . '</td>';
        }

        if ($Room == 'Hallway' || $Room == 'Skylight') {
            $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $hfloor . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
        } else {
            if (!empty($Room) && ($Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite')) {
                $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $hfloor . ' ' . $glass_type_pdf . ' ' . $size_str2 . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
            }
        }
        $pdftxt .= '</tr>';

//new tr
        if (!empty($Room) && ($Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite')) {
            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
            $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>';
            $pdftxt .= $WG_str;
            $pdftxt .= '</td>';
            $pdftxt .= '<td>';
            $pdftxt .= $Cap_str;
            $pdftxt .= '</td>';

            $pdftxt .= '</tr>';
            $pdftxt .= '</table>';
            $pdftxt .= '</td>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

            $pdftxt .= '</tr>';
        } elseif ($Room == 'Hallway' || $Room == 'Skylight') {
            $pdftxt .= '<tr>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
            $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
            $pdftxt .= '<tr>';
            $pdftxt .= '<td>';
            $pdftxt .= $WG_str;
            $pdftxt .= '</td>';
            $pdftxt .= '<td>';
            $pdftxt .= $Cap_str;
            $pdftxt .= '</td>';

            $pdftxt .= '</tr>';
            $pdftxt .= '</table>';
            $pdftxt .= '</td>';
            $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

            $pdftxt .= '</tr>';
        }
//        start hallway and skylite static
        if ($i == 0) {
            if (empty($hfloor) && ($Room !== 'Hallway' && $Room !== 'Skylight')) {
                $pdftxt .= '<tr>';

                $pdftxt .= '<td rowspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:25%;">' . $Room_pdf_hall . '</td>';
                $pdftxt .= '<td colspan="2" style="border-right: 1px solid black; border-bottom: 1px solid black; width:75%;">' . $glass_type_pdf_hall . ' ' . $size_str2_hall . '<br><strong>Notes</strong> ' . $notes_pdf . '</td>';
                $pdftxt .= '</tr>';
//new tr
                $pdftxt .= '<tr>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:45%;">';
                $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= $WG_str_hall;
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= $Cap_str_hall;
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';
                $pdftxt .= '</table>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td colspan="2" style="border-bottom: 1px solid black; border-top: 1px solid black; width:30%;"></td>';

                $pdftxt .= '</tr>';
//        end hallway and skylite
            }
        }
    }


////////////////////////////////////second loop////////////////////////////////////////////
    $total_box = $_POST['total_box'];

    $pdf_text_arr = array();

    for ($i = 0;
            $i < $total_box;
            $i++) {
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
            $Room_pdf = '<strong>Hallway</strong>-' . $Floor;
        }


        $RegGlassStr = '';
        $LargeGlassStr = '';
        $additional_repairsStr = '';
        $TopBottomStr = '';
        $TopBottomStr1 = '';
        $Top = isset($_POST['top_glass'][$i]) ? 1 : 0;
        if ($Top !== 0) {
            $Top_pdf = '<img src="images/tick.png"> <strong>Top</strong>';
        }
//        $Top_pdf = isset($_POST['top_glass'][$i]) ? '<img src="images/tick.png"> <strong>Top</strong>' : '';
        $Bottom = isset($_POST['bottom_glass'][$i]) ? 1 : 0;
        if ($Bottom !== 0) {
            $Bottom_pdf = '<img src="images/tick.png"> <strong> Bottom</strong>';
        }
//        $Bottom_pdf = isset($_POST['bottom_glass'][$i]) ? '<img src="images/tick.png"> <strong> Bottom</strong>' : '';
        $RegGlass = trim($_POST['regular_glass'][$i]);
        $additional_repairs = trim($_POST['additional_repair'][$i]);
        $DoublePaneRegular = isset($_POST['double_pane_rg'][$i]) ? 1 : 0;
//        $DoublePaneRegular_pdf = isset($_POST['double_pane_rg'][$i]) ? '<img src="images/tick.png"> DP' : '';
        $LargeGlass = trim($_POST['large_glass'][$i]);
        $DoublePaneLarge = isset($_POST['double_pane_lg'][$i]) ? 1 : 0;
//        $DoublePaneLarge_pdf = isset($_POST['double_pane_lg'][$i]) ? '<img src="images/tick.png"> DP' : '';
        if (!empty($DoublePaneRegular) || !empty($DoublePaneLarge)) {
            $DoublePaneLarge_and_Regular_pdf = '<img src="images/tick.png"> <strong>DP</strong>';
        } else {
            $DoublePaneLarge_and_Regular_pdf = '<img src="images/check-box.png"> <strong>DP</strong>';
        }

        if ($RegGlass != '' && $RegGlass > 0) {
            $RegGlassStr = '<strong>S Glass</strong> <u style="font-size: 12px;">' . $RegGlass . '</u>';
        }
        if ($LargeGlass != '' && $LargeGlass > 0) {
            $LargeGlassStr = '<strong>L Glass</strong> <u style="font-size: 12px;">' . $LargeGlass . '</u>';
        }
        if ($additional_repairs != '' && $additional_repairs > 0) {
            $additional_repairsStr = 'Additional Repairs <u style="font-size: 12px;">' . $additional_repairs . '</u>';
        }



        if ($Top == 1 && $Bottom == 1) {
            $TopBottomStr = $Top_pdf . '<br>';
            $TopBottomStr1 = $Bottom_pdf;
        } else if ($Top == 1 && $Bottom == 0) {
            $TopBottomStr = $Top_pdf . '<br>';
        } else if ($Top == 0 && $Bottom == 1) {
            $TopBottomStr1 = $Bottom_pdf;
        }
        if (!empty($TopBottomStr) || !empty($TopBottomStr1)) {
            $RegGlassStr = ($RegGlassStr != '') ? $RegGlassStr : '';
            $LargeGlassStr = ($LargeGlassStr != '') ? $LargeGlassStr : '';
        }
//////////////add_repairs
        if (!empty($additional_repairs)) {
            $RedTip_c = trim($_POST['additional_repairs_red'][$i]);
            $UltraLift_c = trim($_POST['additional_repairs_ultra'][$i]);
            $BlackTip_c = trim($_POST['additional_repairs_black'][$i]);
            $BlockTackle_c = trim($_POST['additional_repairs_block'][$i]);

            $Locks = trim($_POST['additional_locks'][$i]);
            $Shoes = trim($_POST['additional_Shoes'][$i]);
            $TiltLatch = trim($_POST['additional_TiltLatch'][$i]);
            $Caps = trim($_POST['additional_Caps'][$i]);
        } else {

            $RedTip_c = trim($_POST['repairs_red'][$i]);
            $UltraLift_c = trim($_POST['repairs_ultra'][$i]);
            $BlackTip_c = trim($_POST['repairs_black'][$i]);
            $BlockTackle_c = trim($_POST['repairs_block'][$i]);
            $Locks = trim($_POST['locks'][$i]);
            $Shoes = trim($_POST['shoes'][$i]);
            $TiltLatch = trim($_POST['tiltlatch'][$i]);
            $Caps = trim($_POST['caps'][$i]);
        }
//////////add_repairs end
//        if (!empty($RedTip_c)) {
//            $RegRepairs = trim($_POST['sash_value'][$i]);
//        }
//        if (!empty($UltraLift_c)) {
//            $UltraLift = trim($_POST['sash_value'][$i]);
//        }
//        if (!empty($BlackTip_c)) {
//            $BlackTip = trim($_POST['sash_value'][$i]);
//        }
//        if (!empty($BlockTackle_c)) {
//            $BlockTackle = trim($_POST['sash_value'][$i]);
//        }
//        if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle) && !empty($_POST['sash_value'][$i])) {
//            $BlackTip = trim($_POST['sash_value'][$i]);
//        }


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

//        $conn->query($query);
        $RegRepairs = $RedTip_c;
        $UltraLift = $UltraLift_c;
        $BlackTip = $BlackTip_c;
        $BlockTackle = $BlockTackle_c;
//Create PDF
//        echo 'here';
//        exit;
//        $pdftxt = '<div style="margin-bottom: -3px; padding-bottom: -3px;">';

        $Floor = empty($Floor) ? '<u>    </u>' : $Floor;
        $RegGlass = empty($RegGlass) ? '<u>    </u>' : $RegGlass;
        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
//        $LargeGlass = empty($LargeGlass) ? '<u>    </u>' : $LargeGlass;
        $InsulatedUnit = empty($InsulatedUnit) ? '<u>    </u>' : $InsulatedUnit;
        $NewScreen = empty($NewScreen) ? '<u>    </u>' : $NewScreen;
        $ScreenRepair = empty($ScreenRepair) ? '<u>    </u>' : $ScreenRepair;
        $Moldings = empty($Moldings) ? '<u>    </u>' : $Moldings;
        $WindowGuards = empty($WindowGuards) ? '<u>    </u>' : $WindowGuards;
        $Capping = empty($Capping) ? '<u>    </u>' : $Capping;
        $Locks = empty($Locks) ? '<u>    </u>' : $Locks;
        $Shoes = empty($Shoes) ? '<u>    </u>' : $Shoes;
        $TiltLatch = empty($TiltLatch) ? '<u>    </u>' : $TiltLatch;
        $Pivot = empty($Pivot) ? '<u>    </u>' : $Pivot;
        $Caps = empty($Caps) ? '<u>    </u>' : $Caps;

        $Width = empty($Width) ? '<u>    </u>' : $Width;
        $Height = empty($Height) ? '<u>    </u>' : $Height;
        $glass_qty = empty($glass_qty) ? '<u>    </u>' : $glass_qty;

        $size_str = '';
        if (!empty($Width) || !empty($Height) || !empty($glass_qty)) {
            $size_str = ' <strong>Size</strong>  <u>' . $Width . '</u> <strong>x</strong> <u style="font-size: 12px;">' . $Height . '</u> ';
            $size_str1 = ' <strong>Size</strong>  <u  style="font-size: 12px;">' . $Width . '</u> x <u  style="font-size: 12px;">' . $Height . '</u> ';
        }
        if (empty($RegGlassStr)) {
            $size_str1 = '  <strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">   </u> ';
        }
        if (empty($LargeGlassStr)) {
            $size_str = '  <strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">   </u> ';
        }
        if (empty($RegGlassStr) && empty($LargeGlassStr)) {
            $size_str1 = '  <strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">   </u> ';
        }

        $RegGlassStr = empty($RegGlassStr) ? '<strong>S Glass</strong> <u style="font-size: 12px;">   </u> ' : $RegGlassStr . ' ';
        $LargeGlassStr = empty($LargeGlassStr) ? ' <strong>L Glass</strong> <u style="font-size: 12px;">   </u> ' : $LargeGlassStr . ' ';

//        $DoublePaneLarge_pdf = empty($DoublePaneLarge_pdf) ? '<img src="images/check-box.png"> DP' : $DoublePaneLarge_pdf . ' ';

        if (!empty($Room_pdf)) {
            if ($Room == 'Hallway') {
                $Room_pdf = '<img src="images/tick.png"> <strong>Hallway</strong>';
            } elseif ($Room == 'Skylight') {
                $Room_pdf = '<img src="images/tick.png"><strong>' . $Room_pdf . '</strong>';
            } else {
                $Room_pdf = '<u>' . $Room_pdf . '</u>';
            }
        } else {
            $Room_pdf4 = '';
            $Room_pdf4 .= '<img src="images/check-box.png"> Front Door<br>';
            $Room_pdf4 .= ' <img src="images/check-box.png"> Vest Door<br>';
            $Room_pdf4 .= '<img src="images/check-box.png"> Sidelite';
        }


        if (!empty($glass_type_pdf)) {
            $glass_type_pdf = '<img src="images/tick.png"> ' . $glass_type_pdf;
        } else {
            $glass_type_pdf = '<img src="images/check-box.png"> Lami <img src="images/check-box.png"> Plexi <img src="images/check-box.png"> PW ';
        }
//        $size_str = '';
//        if (empty($Width) && empty($Height)) {
//            if($Width == '' || $Height == ''){
//            $size_str = ' Size <u style="font-size: 12px;">' . $Width . '</u> x <u style="font-size: 12px;">' . $Height . ' </u>';
//        } else {
//            $size_str = ' Size: <u style="font-size: 12px;">' . $Width . '</u> x <u style="font-size: 12px;">' . $Height . ' </u>';
//        }
//        }
        $size_str3 = '';
        if ($Room == 'Hallway' || $Room == 'Skylight' || $Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite') {

            $size_str3 = '<strong>Size</strong> <u style="font-size: 12px;">   </u> <strong>x</strong> <u style="font-size: 12px;">     </u>';
        } else {
            if (!empty($Width) && !empty($Height)) {
                $size_str3 = ' <strong>Size</strong> <u style="font-size: 12px;">' . $Width . '</u> <strong>x</strong> <u style="font-size: 12px;">' . $Height . '</u>';
            } elseif (!empty($Width)) {
                $size_str3 = ' <strong>Size</strong> <u style="font-size: 12px;">' . $Width . '</u> <strong>x</strong>';
            } elseif (!empty($Height)) {
                $size_str3 = ' <strong>Size</strong> <strong>x</strong> <u style="font-size: 12px;">' . $Height . '</u>';
            }
        }


        if ($Room == 'Hallway' || $Room == 'Skylight') {
            $hfloor = '';
            $hfloor = 'Floor <u>' . $Floor . ' ' . $glass_type_pdf . ' ' . $size_str3 . '</u>';
        }
        $string = $Notes;
        $words = explode(' ', $string); // Split the string into an array of words
        $first_five_words = array_slice($words, 0, 5); // Get the first 5 words
//        $Notes = implode(' ', $first_five_words);
        $Notes = '';
        if (empty($Notes)) {
            $Notes = '';
            $notes_pdf = '<table width="100%" style="margin: 0; padding: 0;">
                <tr><td width="100%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"></td><td style="margin: 0; padding: 0;">' . $Notes . '</td></tr>
            </table>';
        }
        if ($Room == 'Kitchen') {
            $Room_pdf = '<img src="images/tick.png"> <strong>KIT</strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } elseif ($Room == 'Bedroom') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/tick.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } elseif ($Room == 'Bathroom') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/tick.png"> <strong> BATH</strong> &nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } elseif ($Room == 'Living Room') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/tick.png"> <strong> LR</strong>';
        } elseif ($Room == '') {
            $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
        } else {
            if ($Room == 'Hallway' || $Room == 'Skylight' || $Room == 'Front Door' || $Room == 'Vest Door' || $Room == 'Sidelite') {
                $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';
            } else {
                $Room_pdf = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong><br><strong>' . $Room_pdf . '</strong>';
            }
        }

//        $Room_pdf = empty($Room_pdf) ? $Room_pdf . 'Room' : $Room_pdf;

        $TopBottomStr = empty($TopBottomStr) ? '<img src="images/check-box.png"> <strong>Top</strong><br>' : $TopBottomStr;
        $TopBottomStr1 = empty($TopBottomStr1) ? '<img src="images/check-box.png"> <strong> Bottom</strong>' : $TopBottomStr1;
        $topbottom = $TopBottomStr . $TopBottomStr1;

        $DoublePaneRegular_pdf = empty($DoublePaneRegular_pdf) ? '' : $DoublePaneRegular_pdf;
        $DoublePaneLarge = empty($DoublePaneLarge) ? '' : $DoublePaneLarge;
        if (!empty($additional_repairsStr)) {
            $additional_repairsStr = empty($additional_repairsStr) ? 'Additional Repairs  <u>    </u>' : $additional_repairsStr;
        }

        $additional_repairs = empty($additional_repairs) ? '<u>    </u>' : '' . $additional_repairs;
        if ($RedTip_c === '') {
            $RegRepairs = '<u>    </u>';
        }
        if ($UltraLift_c === '') {
            $UltraLift = '<u>    </u>';
        }

        if ($BlackTip_c === '') {
            $BlackTip = '<u>    </u>';
        }
        if ($BlockTackle_c === '') {
            $BlockTackle = '<u>    </u>';
        }
//        $RegRepairs = $RedTip_c != '' ? trim($_POST['sash_value'][$i]) : '<u>'.$RegRepairs.'</u>';
//        $UltraLift = $UltraLift_c!= '' ? trim($_POST['sash_value'][$i]) : '<u>'.$UltraLift.'</u>';
//        $BlackTip = $BlackTip_c!= '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';
//        $BlockTackle = $BlockTackle_c!= '' ? trim($_POST['sash_value'][$i]) : '<u>    </u>';


        $IU_str = '';
        $IU_str .= ' <strong>IU</strong> <u style="font-size: 12px;">' . $InsulatedUnit . '</u>';
        $NewScreen_str = '';
        $NewScreen_str .= '<strong>NewScreen</strong>  <u style="font-size: 12px;">' . $NewScreen . '</u>';
        $ScreenRepair_str = '';
        $ScreenRepair_str .= ' <strong>ScreenRep</strong>  <u style="font-size: 12px;">' . $ScreenRepair . '</u> ';

        $pdftxt .= '<tr>';
        $pdftxt .= '<td colspan="2" style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; width:25%;">';
        $pdftxt .= '<table style="width: 100%;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width: 50%;" rowspan="2">' . $Room_pdf . '</td>';
//        $pdftxt .= '<td style="width: 50%; text-align: left;">' . $Room_pdf . '</td>';
        $pdftxt .= '<td style="width: 50%;">' . $TopBottomStr . $TopBottomStr1 . '</td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td style="width:75%; border-top: 1px solid black; ">';
        $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= $RegGlassStr;
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= $LargeGlassStr;
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= $DoublePaneLarge_and_Regular_pdf;
        $pdftxt .= '</td>';

        $pdftxt .= '<td>';
        $pdftxt .= $size_str3;
        $pdftxt .= '</td>';

        $pdftxt .= '<td>';
        $pdftxt .= $IU_str;
        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= $NewScreen_str;
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= $ScreenRepair_str;
        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';

        $pdftxt .= '</td>';
//        $pdftxt .= '<td colspan="2" style="border-top: 1px solid black; width:75%;">' . $RegGlassStr . $LargeGlassStr . ' ' . $DoublePaneLarge_and_Regular_pdf . ' ' . $size_str3 . $other_str . '</td>';
        $pdftxt .= '</tr>';

        $pdftxt .= '<tr>';
        $pdftxt .= '<td colspan="2"  style=" border-left: 1px solid black; border-right: 1px solid black; border-bottom:1px solid black;  width:25%;">';
        $pdftxt .= '<table style="width: 100%;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width: 50%; text-align: left;"></td>';
        $pdftxt .= '<td style="width: 50%; text-align: left;"></td>';

        $pdftxt .= '<td style="width: 50%;"></td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td colspan="2" style="border-left: 1px solid black;  width:75%;">';
        $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
//echo 'sac'.$RegRepairs;
        if (!empty($additional_repairsStr)) {
//            echo $RedTip_c;
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Caps)) {
//                if (!empty($RedTip_c)) {
////                    $RegRepairs = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $RegRepairs;
//                } else {
//                    $RegRepairs = '<u>    </u>';
//                }
//                if (!empty($UltraLift_c)) {
////                    $UltraLift = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $UltraLift;
//                } else {
//                    $UltraLift = '<u>    </u>';
//                }
//                if (!empty($BlackTip_c)) {
////                    $BlackTip = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $BlackTip;
//                } else {
//                    $BlackTip = '<u>    </u>';
//                }
//                if (!empty($BlockTackle_c)) {
////                    $BlockTackle = trim($_POST['sash_value'][$i]);
//                    $additional_repairs += (int) $BlockTackle;
//                }
//                else {
//                    $BlockTackle = '<u>    </u>';
//                }
                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>RB</strong> <u style="font-size: 12px;">' . $RegRepairs . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>UL</strong> <u style="font-size: 12px;">' . $UltraLift . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BTB</strong> <u style="font-size: 12px;">' . $BlockTackle . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BB</strong> <u style="font-size: 12px;">' . $BlackTip . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Locks</strong> <u style="font-size: 12px;">' . $Locks . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Shoes</strong> <u >' . $Shoes . '</u>';
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';

                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>TL</strong> <u style="font-size: 12px;">' . $TiltLatch . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Pivot</strong> <u style="font-size: 12px;">' . $Pivot . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Caps</strong> <u style="font-size: 12px;">' . $Caps . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>WG</strong> <u style="font-size: 12px;">' . $WindowGuards . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Molding</strong> <u style="font-size: 12px;">' . $Moldings . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Cap</strong> <u style="font-size: 12px;">' . $Capping . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';

//                $pdftxt .= '<tr>';
//                $pdftxt .= '<td>';
//                $pdftxt .= '<strong>AddRep</strong> <u style="font-size: 12px;">' . $additional_repairs . '</u>';
//                $pdftxt .= '</td>';
//
//                $pdftxt .= '</tr>';
// Notes
                $pdftxt .= '<tr>';
                $pdftxt .= '<td style="width:100%;">'; // Add margin-top here


                $pdftxt .= '<table cellpadding="" width="100%" >';
                $pdftxt .= '<strong>Notes </strong><tr><td width="90%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"><span style="font-size: 12px;">' . $Notes . '</span></td></tr>';

                $pdftxt .= '</table>';

                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';
            }
        } else {
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Pivot) || !empty($Caps)) {

                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>RB</strong> <u style="font-size: 12px;">' . $RegRepairs . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>UL</strong> <u style="font-size: 12px;">' . $UltraLift . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BTB </strong> <u style="font-size: 12px;">' . $BlockTackle . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>BB</strong> <u style="font-size: 12px;">' . $BlackTip . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Locks</strong> <u style="font-size: 12px;">' . $Locks . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Shoes</strong> <u style="font-size: 12px;">' . $Shoes . '</u>';
                $pdftxt .= '</td>';

                $pdftxt .= '</tr>';

                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>TL</strong> <u style="font-size: 12px;">' . $TiltLatch . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Pivot</strong> <u style="font-size: 12px;">' . $Pivot . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Caps</strong> <u style="font-size: 12px;">' . $Caps . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>WG</strong> <u style="font-size: 12px;">' . $WindowGuards . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Molding</strong> <u style="font-size: 12px;">' . $Moldings . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>Cap</strong> <u style="font-size: 12px;">' . $Capping . '</u>';
                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';

// Notes
                $pdftxt .= '<tr>';
                $pdftxt .= '<td style="width:100%;">'; // Add margin-top here


                $pdftxt .= '<table cellpadding="" width="100%" >';
                $pdftxt .= '<strong>Notes </strong><tr><td width="90%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"><span style="font-size: 12px;">' . $Notes . '</span></td></tr>';

                $pdftxt .= '</table>';

                $pdftxt .= '</td>';
                $pdftxt .= '</tr>';
            }
        }

        $pdftxt .= '</table>';

        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
    }

    $Room_pdf_static = '<img src="images/check-box.png"> <strong>KIT</strong>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> BR</strong><br><img src="images/check-box.png"> <strong> BATH</strong>&nbsp;&nbsp;<br><img src="images/check-box.png"> <strong> LR</strong>';

//    $Room_pdf = empty($Room_pdf) ? $Room_pdf . '<img src="images/check-box.png">  Room:' : $Room_pdf;
//    $TopBottomStr = empty($TopBottomStr) ? '<img src="images/check-box.png">  Top<br>' : $TopBottomStr;
//    $TopBottomStr1 = empty($TopBottomStr1) ? '<img src="images/check-box.png">  Bottom' : $TopBottomStr1;
    for ($i = 0;
            $i < 1;
            $i++) {

        $pdftxt .= '<tr>';
        $pdftxt .= '<td rowspan="2" style=" border-left: 1px solid black; border-right: 1px solid black; width:25%;">';
        $pdftxt .= '<table style="width: 100%;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td >';
        $pdftxt .= $Room_pdf_static;
        $pdftxt .= '</td>';
        $pdftxt .= '<td >';
        $pdftxt .= '<img src="images/check-box.png"><strong> Top</strong><br>';
        $pdftxt .= '<img src="images/check-box.png"> <strong> Bottom</strong>';
        $pdftxt .= '</td>';
//
        $pdftxt .= '</tr>';
//
        $pdftxt .= '</table>';
        $pdftxt .= '</td>';

        $pdftxt .= '<td style="width:75%; border-top: 1px solid black; ">';
        $pdftxt .= '<table style="width: 100%; margin: 0; padding: 0;">';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>S Glass</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>L Glass</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong><img src="images/check-box.png"> <strong> DP</strong></strong> <u style="font-size: 12px;"></u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Size</strong> <u style="font-size: 12px;">    </u> <strong>x</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>IU</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>NewScreen</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>ScreenRep</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';
//        $pdftxt .= '<strong>S Glass</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//
//        $pdftxt .= ' <strong>L Glass</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//        $pdftxt .= ' <img src="images/check-box.png"> <strong>DP</strong><span style="text-decoration: underline; white-space: pre; font-size: 14px;"></span>';
//        $pdftxt .= ' <strong>Size</strong>  <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//        $pdftxt .= ' <strong>x</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span>';
//        $pdftxt .= ' <strong>IU</strong>  <span style="text-decoration: underline; white-space: pre; font-size: 14px;">   </span><br>';
//        $pdftxt .= '<strong>NewScreen</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">           </span>';
//        $pdftxt .= ' <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ScreenRep</strong> <span style="text-decoration: underline; white-space: pre; font-size: 14px;">             </span>';

        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
////        second tr
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width: 75%; border-left: 1px solid black; padding: 8px;">';
        $pdftxt .= '<table>';
// Second row
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>RB</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>UL</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>BTB</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>BB</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Locks</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Shoes</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
//
//// Third row
        $pdftxt .= '<tr>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>TL</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Pivot</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Caps</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>WG</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Moldings</strong><u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '<td>';
        $pdftxt .= '<strong>Cap</strong> <u style="font-size: 12px;">    </u>';
        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';

//         Notes
        $pdftxt .= '<tr>';
        $pdftxt .= '<td style="width:100%;">'; // Add margin-top here


        $pdftxt .= '<table cellpadding="2" width="100%" style="margin: 0; padding: 0;">';
        $pdftxt .= '<strong>Notes</strong><tr><td width="90%" style="border-bottom: 1px solid black; margin: 0; padding: 0;"></td></tr>';

        $pdftxt .= '</table>';

        $pdftxt .= '</td>';
        $pdftxt .= '</tr>';
        $pdftxt .= '</table>';

        $pdftxt .= '</td>';

        $pdftxt .= '</tr>';
    }


    $pdftxt .= '</table>';
    $pdf_text_arr[] = $pdftxt;

    $pdftxt .= '</table>';

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
    $pdf->SetMargins(PDF_MARGIN_LEFT, 41, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 11);

// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
        require_once(dirname(__FILE__) . '/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
    $text = '<table cellspacing="2" cellpadding="">';
    $text .= '<tr>';
    $text .= '<td width="6%"><b>JobID:</b></td>'; // Label without underline
    $text .= '<td width="72%" style="border-bottom: 1px solid black; margin: 0; padding: 0;" colspan="1">' . $_SESSION['JobID'] . '</td>'; // Underlined text
    $text .= '<td width="7%">&nbsp;&nbsp;   <b>Date:</b></td>'; // Label without underline
    $text .= '<td width="15%" style="border-bottom: 1px solid black; margin: 0; padding: 0;" colspan="1">' . date('m/d/Y') . '</td>'; // Underlined text
    $text .= '</tr>';
    $text .= '</table>';

    $pdf->writeHTMLCell($w = 0, $h = 7, $x = 15, $y = 40, $text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

//    $text = '<b>JobID:</b> ' . $_SESSION['JobID'];
//    $pdf->writeHTMLCell($w = 0, $h = 10, $x = 16, $y = 40, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
//   $text = '<b>Date:</b> ' . date('m/d/Y');
//    $pdf->writeHTMLCell($w = 0, $h = 10, $x = 16, $y = 50, $text, $border = 1, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $pdf->SetFont('helvetica', '', 9);
    preg_match('~>\K[^<>]*(?=<)~', $Address, $match);
    $pdftxt_header = '<table cellspacing="0" cellpadding="" style="border-collapse: collapse;">'; // Add cellpadding to add space inside cells
    $pdftxt_header .= '<tr style="padding-bottom: 10px;">';
    $pdftxt_header .= '<td width="9%" style="padding-bottom: 10px;"><b>Address:</b></td>'; // Add padding-bottom for spacing
    $pdftxt_header .= '<td width="47%" style="border-bottom: 1px solid black; margin: 0; padding-bottom: 10px;" colspan="1">' . $match[0] . '</td>'; // Underlined text with padding
    $pdftxt_header .= '<td width="6%" style="padding-bottom: 10px;">&nbsp;&nbsp;<b>APT:</b></td>'; // Add padding-bottom for spacing
    $pdftxt_header .= '<td width="10%" style="border-bottom: 1px solid black; margin: 0; padding-bottom: 10px;" colspan="1">' . $Apt . '</td>'; // Underlined text with padding
    $pdftxt_header .= '<td width="12%" style="padding-bottom: 10px;">&nbsp;&nbsp;<b>Technician:</b></td>'; // Add padding-bottom for spacing
    $pdftxt_header .= '<td width="15%" style="border-bottom: 1px solid black; margin: 0; padding-bottom: 10px;" colspan="1">' . $_SESSION['User']['TechName'] . '</td>'; // Underlined text with padding
    $pdftxt_header .= '</tr><br><br>';
    $pdftxt_header .= '</table>';
    $tbl1 = <<<EOD
            $pdftxt_header
            EOD;
    $pdf->writeHTML($tbl1, true, true, false, false, '');
    $pdf->SetFont('helvetica', '', 10);
    $i = 0;
    foreach ($pdf_text_arr as $pdf_text) {
        if ($i % 2 == 0 && $i > 0) {
            $pdf->AddPage();
            $pdf_text = '' . $pdf_text;
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
//      $html = '<table style="width: 100%;">'; // Set the height of the table here
// Get the current Y position
    $initialY = $pdf->GetY();
    if (!empty($file)) {
        $width = 60;  // Width of the image
        $height = 20; // Height of the image
        // Add the first image at a certain X and Y position
        $pdf->Image($file, 17, $initialY, $width, $height);
        // After adding the first image, update the Y position for the next image
        $initialY = $pdf->GetY();  // Get the new Y position after the first image
    }
// Check if the second tech image file is not empty
    if (!empty($techfile)) {
        $width = 60;  // Width of the second image
        $height = 20; // Height of the second image
        // Add the second image with the updated Y position
        $pdf->Image($techfile, 97, $initialY, $width, $height);
        // After adding the second image, update the Y position again if needed
        $initialY = $pdf->GetY();  // Get the new Y position after the second image
    }

    if (!empty($file)) {
        $html .= '<tr>';
        $html .= '<td style="width: 35%; height: 100px; text-align: center;">';
        $html .= '<br><br><div style=" border-bottom: 1px solid black;">';
        $html .= '<img src="' . $file . '" width="100" height="80">';
        $html .= '</div>';

        $html .= '<strong style="text-align: left;">Tenant Signature</strong>'; // Add label here
        $html .= '</td>';
        $html .= '<td style="width: 10%;"></td>';
    } else {
        $html .= '<tr><td style="width: 35%;"></td><td style="width: 10%;"></td>';
    }
    if (!empty($techfile)) {
        $html .= '<td style="width: 35%; height: 100px; text-align: center;">';
        $html .= '<strong></strong><br><div style="border-bottom: 1px solid black;">';
        $html .= '<img src="' . $techfile . '" width="100" height="80">';
        $html .= '</div><br>';
        $html .= '<strong style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;Tech Signature</strong>';
        $html .= '</td></tr>';
    } else {
        $html .= '<td style="width: 35%;"></td></tr>';
    }
//$html .= '<tr><td><strong>Tenant Signature1</strong></td><td></td><td><strong>Tech Signature1</strong></td></tr>';
    $html .= '</table>';
//    end
    $html .= '<tr>';
    $html .= '<td style="width: 40%; font-size: 8px;"><br><br><strong>KIT</strong> = KITCHEN, <strong>BR</strong> = BEDROOM, <strong>BATH</strong> = BATHROOM, <br><strong>LR</strong> = LIVING ROOM, <strong>RW</strong> = ROUGH WIRE, <strong>PW</strong> = POLY WIRE, <br><strong>CG</strong> = CLEAR GLASS, <strong>IU</strong> = INSULATED UNITS, <strong>TL</strong> = TILT LATCH, <br><strong>RB</strong> = RED TIP BALANCES, <strong>BB</strong> = BLACK TIP BALANCES, <br><strong>DP</strong> = DOUBLE PANE, <strong>S GLASS</strong> = STANDARD GLASS, <br><strong>L GLASS</strong> = LARGE GLASS, <strong>UL</strong> = ULTRA LIFT BALANCE, <br><strong>WG</strong> = Window guards, <strong>Cap</strong> = Capping, <strong>BTB</strong> = BlockTackle, <br><strong>ScreenRep</strong> = SCREENREPAIR
</td>';
    $html .= '</tr>';
    $html .= '</table>';
    $tagvs = [
        'div' => [
            0 => ['h' => 3.2, 'n' => 3.2],
            1 => ['h' => 0, 'n' => 0]
        ],
    ];
    $pdf->setHtmlVSpace($tagvs);
    $pdf->writeHTML($html, true, true, false, false, '');

//    $pdf->writeHTML('<br><br><br><br><br><hr>', true, false, false, false, '');
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                    <div class="gls_tp_divvv">

                        <section id="glass_section" class="glass_section">

                            <!--/////////////////////////////////////-->
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-sm-6"><input type="checkbox" class="top_glass_c " name="top_glass[]" value="1"> Top</div>
                                        <div class="col-sm-6"><input type="checkbox" class="bottom_glass_c " name="bottom_glass[]" value="1"> Bottom</div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">Regular Glass:</div>
                                        <div class="col-sm-6"><input name="regular_glass[]" class="r_glass form-control addmorevaliR forzero" type="text"
                                                                     class="form-control width" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-12"><input type="checkbox" name="double_pane_rg[]" value="1"> Double Pane</div>

                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-sm-6">Large Glass:</div>
                                        <div class="col-sm-6"><input name="large_glass[]" class="l_glass form-control addmorevaliL forzero" type="text"
                                                                     class="form-control width" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-12"><input type="checkbox" name="double_pane_lg[]" value="1"> Double Pane</div>

                                    </div>
                                </div>
                                <div class="col-sm-4">

                                    <div class="row mt-4">
                                        <div class="col-sm-6">Additional Repairs:</div>
                                        <div class="col-sm-6"><input name="additional_repair[]" class="form-control additional_Balances" type="text"
                                                                     class="form-control width" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">Red Tip Balances</div>
                                        <div class="col-sm-6"><input class="form-control add_r addrepB" type="text" name="additional_repairs_red[]" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">UltraLift Balances</div>
                                        <div class="col-sm-6"><input class="form-control add_u addrepB" type="text" name="additional_repairs_ultra[]" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">Black Tip Balances</div>
                                        <div class="col-sm-6"><input class="form-control add_bt addrepB" type="text" name="additional_repairs_black[]" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">Block &Tackle Balances</div>
                                        <div class="col-sm-6"><input class="form-control add_bta addrepB" type="text" name="additional_repairs_block[]" placeholder="Quantity"></div>
                                    </div>

                                </div>
                                <div class="col-sm-4">
                                    <div class="row mt-4">
                                        <div class="col-sm-6">Lock</div>
                                        <div class="col-sm-6"><input name="additional_locks[]" type="text" class="form-control additional_l addlstcp" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">Shoes</div>
                                        <div class="col-sm-6"><input name="additional_Shoes[]" type="text" class="form-control additional_s addlstcp" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">TiltLatch</div>
                                        <div class="col-sm-6"><input name="additional_TiltLatch[]" type="text" class="form-control additional_t addlstcp" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">Caps</div>
                                        <div class="col-sm-6"><input name="additional_Caps[]" type="text" class="form-control additional_c addlstcp" placeholder="Quantity"></div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-sm-6">Pivot</div>
                                        <div class="col-sm-6"><input name="additional_Pivot[]" type="text" class="form-control additional_p addlstcp" placeholder="Quantity"></div>
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
                    </div>
                    <div class="saassh_reepp_divvv">
                        <section id="Sash_Repairs" class="Sash_Repairs">
                            <div class="row">
                                <div class="col-sm-2"><input type="checkbox" class="top_glass_r " name="top_glass[]" value="1"> Top</div>
                                <div class="col-sm-2"><input type="checkbox" class="bottom_glass_r " name="bottom_glass[]" value="1"> Bottom</div>
                            </div>
                            <div class="row mt-2">

                                <div class="col-sm-2"><strong>Sash Repairs</strong></div>
                                <div class="col-sm-2"><input name="sash_value[]" type="text" class="form-control txtrdosr sash_t_b" placeholder="Quantity"></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2">Red Tip Balances</div>
                                <div class="text-right col-sm-2"><input class="rdosr blocks blocks_a form-control" type="text" name="repairs_red[]" placeholder="Quantity"></div>

                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2 ">UltraLift Balances</div>
                                <div class="text-right col-sm-2"><input class="rdosr blocks blocks_a form-control" type="text" name="repairs_ultra[]" placeholder="Quantity"></div>

                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2">Black Tip Balances</div>
                                <div class="text-right col-sm-2"><input class="rdosr blocks blocks_a form-control" type="text" name="repairs_black[]" placeholder="Quantity"></div>

                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2">Block &Tackle Balances</div>
                                <div class="text-right col-sm-2"><input class="rdosr blocks blocks_a form-control" type="text" name="repairs_block[]" placeholder="Quantity"></div>
                            </div>
                            <hr>
                        </section>
                        <section id="Sash_Rep_sec1" class="Sash_Rep_sec1">
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
                                <div class="col-sm-2 "> <input name="caps[]" type="text" class="form-control width lstp"></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2">Pivot</div>
                                <div class="col-sm-2"> <input name="pivot[]" type="text" class="form-control width lstp"></div>
                            </div>
                            <hr>
                        </section>
                        <section id="Sash_Rep_sec2" class="Sash_Rep_sec2">
                            <div class="row mt-2">
                                <div class="col-sm-2">Insulated Unit</div>
                                <div class="col-sm-2"> <input name="insulated_unit[]" type="text" class="form-control width i_u"></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2 ">New Screen</div>
                                <div class="col-sm-2 "> <input name="new_screen[]" type="text" class="form-control width n_s">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2">Moldings</div>
                                <div class="col-sm-2"> <input name="moldings[]" type="text" class="form-control width m_d">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2 ">Capping</div>
                                <div class="col-sm-2 "> <input name="capping[]" type="text" class="form-control width c_p">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2">Screen Repair</div>
                                <div class="col-sm-2"> <input name="screen_repair[]" type="text" class="form-control width s_r">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-2 ">Window Guards</div>
                                <div class="col-sm-2 "><input name="window_guards[]" type="text" class="form-control width w_g">
                                </div>
                            </div>
                            <hr>
                        </section>
                        <section id="Sash_Rep_sec3" class="Sash_Rep_sec3">

                            <div class="form-group row mt-2">
                                <label for="Instructions" class="col-sm-2 col-form-label">Notes</label>
                                <div class="col-sm-3 width">
                                    <textarea class="form-control" id="Instructions" name="notes[]" rows="3"></textarea>
                                </div>
                            </div>
                        </section>

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
                    <textarea id="main_techsign" name="main_techsign" style="display: none" required></textarea>
                </div>
            </div>
 <div class="row mt-3">
    <!-- Button for "Done" -->
    <div class="col-2 col-sm-2 col-md-2 d-flex justify-content-center mb-3">
        <button 
            type="submit" 
            class="btn btn-success w-100" 
            name="save" 
            id="btndone">
            Done
        </button>
    </div>
    <!-- Button for "Not Complete" -->
    <div class="col-2 col-sm-2 col-md-2 d-flex justify-content-center mb-3">
        <button 
            type="submit" 
            class="btn btn-primary w-100" 
            name="not_done" 
            id="not_done">
            Not Complete
        </button>
    </div>
</div>
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

//add button 
    $(document).on('click', '#addElementField', function () {
// validateLastFormSection for validation onclick add+ button
        function validateLastFormSection() {
            var alert_msg = '';  // Initialize an empty alert message
            var isValid = true;  // Flag to check if the last section is valid

            var i = 0;
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
                                isValid = false;

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
                                isValid = false;

                            }
                        }
                        m++;
                    });
                }
                i++;
            });

//        ---------------------sash repaire top bottom valid.
            var new_i = 0;
//var new_alert_msg = "";
            $(".top_glass_r").each(function () {
                var new_top_bottom_sel = false;
                if ($(this).is(":checked")) {
                    new_top_bottom_sel = true;
                } else {
                    var new_j = 0;
                    $(".bottom_glass_r").each(function () {
                        if (new_j === new_i && $(this).is(":checked")) {
                            new_top_bottom_sel = true;
                        }
                        new_j++;
                    });
                }
                if (new_top_bottom_sel) {
                    var new_m = 0;
                    var new_reg_glass_val = '';
//                var new_large_glass_val = '';
                    $(".sash_t_b").each(function () {
                        if (new_i === new_m) {
                            if ($(this).val() !== '') {
                                new_reg_glass_val = $(this).val();
                            }

//                        var new_n = 0;
//                        $(".sash_t_b").each(function () {
//                            if (new_n === new_m && $(this).val() !== '') {
//                                new_large_glass_val = $(this).val();
//                            }
//                            new_n++;
//                        });
                            if (new_reg_glass_val === '') {
                                alert_msg += 'Please enter value in sash repaire section\n';
                                isValid = false;

                            }
                        }
                        new_m++;
                    });
                } else {
                    var new_m = 0;
                    var new_reg_glass_val = '';
//                var new_large_glass_val = '';
                    $(".sash_t_b").each(function () {
                        if (new_i === new_m) {
                            if ($(this).val() !== '') {
                                new_reg_glass_val = $(this).val();
                            }

//                        var new_n = 0;
//                        $(".sash_t_b").each(function () {
//                            if (new_n === new_m && $(this).val() !== '') {
//                                new_large_glass_val = $(this).val();
//                            }
//                            new_n++;
//                        });
                            if (new_reg_glass_val !== '') {
                                alert_msg += 'Please select top or bottom in sash repairs section\n';
                                isValid = false;
                            }
                        }
                        new_m++;
                    });
                }
                new_i++;
            });
//--------------------Size and Qty is missing
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
                            isValid = false;
                        }
                    }
                    gi = 0;
                    mi++;
                }
            });

//additional_Balances check value
            // Check for .additional_Balances and .addlstcp sections
            var additionalBalanceFilled = $('#main_div > div').last().find('.additional_Balances').filter(function () {
                return $(this).val().trim() !== "";
            }).length > 0;

            var addlstcpFilled = $('#main_div > div').last().find('.addlstcp').filter(function () {
                return $(this).val().trim() !== "";
            }).length > 0;

// Check if all inputs with classes .addmorevaliR in the last appended section are empty
            var addMoreValiR_Empty = $('#main_div > div').last().find('.addmorevaliR').filter(function () {
                return $(this).val().trim() === "";
            }).length === $('#main_div > div').last().find('.addmorevaliR').length;

// Check if all inputs with classes .addmorevaliL in the last appended section are empty
            var addMoreValiL_Empty = $('#main_div > div').last().find('.addmorevaliL').filter(function () {
                return $(this).val().trim() === "";
            }).length === $('#main_div > div').last().find('.addmorevaliL').length;
//
//// Check if all inputs with classes .addmorevaliR for .addlstcp section are empty
//            var addlstcpR_Empty = $('#main_div > div').last().find('.addmorevaliR').filter(function () {
//                return $(this).val().trim() === "";
//            }).length === $('#main_div > div').last().find('.addmorevaliR').length;
//
//// Check if all inputs with classes .addmorevaliL for .addlstcp section are empty
//            var addlstcp_EmptyL_Empty = $('#main_div > div').last().find('.addmorevaliL').filter(function () {
//                return $(this).val().trim() === "";
//            }).length === $('#main_div > div').last().find('.addmorevaliL').length;

// Trigger a single alert if conditions are met
            if ((additionalBalanceFilled && addMoreValiR_Empty && addMoreValiL_Empty) ||
                    (addlstcpFilled && addMoreValiR_Empty && addMoreValiL_Empty)) {
                alert_msg += 'please fill the glass pane section input box\n';
                isValid = false;
            }

//    for add repair balances
            // Check if any input with the class .additional_Balances in the last appended section has a value
            var addrepBFilled = $('#main_div > div').last().find('.addrepB').filter(function () {
                return $(this).val().trim() !== "";
            }).length > 0;

            // Check if all inputs with classes .addmorevaliR in the last appended section are empty
            var addrepVali_Empty = $('#main_div > div').last().find('.additional_Balances').filter(function () {
                return $(this).val().trim() === "";
            }).length === $('#main_div > div').last().find('.additional_Balances').length;

            // Trigger the alert if the conditions are met for the last appended form section
            if (addrepBFilled && addrepVali_Empty) {
                alert_msg += 'please fill the additional repair input box\n';
                isValid = false;
            }
//for sash repair section
            var sashFilled = $('#main_div > div').last().find('.rdosr').filter(function () {
                return $(this).val().trim() !== "";
            }).length > 0;
            // Check if all inputs with classes .addmorevaliR in the last appended section are empty
            var addMoreValisash_t_b_Empty = $('#main_div > div').last().find('.sash_t_b').filter(function () {
                return $(this).val().trim() === "";
            }).length === $('#main_div > div').last().find('.sash_t_b').length;
            // Trigger the alert if the conditions are met for the last appended form section
            if (sashFilled && addMoreValisash_t_b_Empty) {
                alert_msg += 'please fill the sash repair section input box\n';
                isValid = false;
            }
            // Check if any input with the class .zero in the last appended section has a value of 0
            let hasZero = false;
            $('#main_div > div').last().find('.forzero').each(function () {
                let value = $(this).val().trim();  // Get the value and remove any extra whitespace
                if (value === '0') {
                    hasZero = true;
                    $(this).focus();  // Focus on the input with a value of 0
                    return false;  // Exit the loop after finding the first 0
                }
            });
            if (hasZero) {
                alert_msg += 'Please enter a value greater than 0 in the glass section.\n';
                isValid = false;
            }
            // Display any alert message for missing information
            if (alert_msg !== '') {
                alert(alert_msg);
                isValid = false;
            }
            return isValid;
        }
        // Only proceed if the last form section is valid
        if (!validateLastFormSection()) {
            return;  // Exit if validation fails, no new form will be appended
        }
        var cur_val = parseInt($('#total_box').val());
        var form_html = $('#form_div').html();
        var new_name = "rdoroom" + cur_val;
        var new_glass_type = "glass_type" + cur_val;
//        var new_repairs = "repairs" + cur_val;
        var new_double_pane_rg = "double_pane_rg[" + cur_val + "]";
        var new_double_pane_lg = "double_pane_lg[" + cur_val + "]";
        var new_top_glass = "top_glass[" + cur_val + "]";
        var new_bottom_glass = "bottom_glass[" + cur_val + "]";

        form_html = form_html.replace(/rdoroom/g, new_name);
        form_html = form_html.replace(/glass_type/g, new_glass_type);
//        form_html = form_html.replace(/repairs/g, new_repairs);
        form_html = form_html.replace(/double_pane_rg\[\]/g, new_double_pane_rg);
        form_html = form_html.replace(/double_pane_lg\[\]/g, new_double_pane_lg);
        form_html = form_html.replace(/top_glass\[\]/g, new_top_glass);
        form_html = form_html.replace(/bottom_glass\[\]/g, new_bottom_glass);

        $('#main_div').append('<div><hr>' + form_html + '<span class="btn btn-danger remove_box"> Remove -</span></div>');
        $('#total_box').val(cur_val + 1);
        $('.saassh_reepp_divvv').last().show(); // This ensures the appended content is visible
        $('.gls_tp_divvv').last().show();

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
//    done button
    $(document).on('click', '#btndone, #not_done', function (e) {
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

//        ---------------------sash repaire top bottom valid.
        var new_i = 0;
//var new_alert_msg = "";
        $(".top_glass_r").each(function () {
            var new_top_bottom_sel = false;
            if ($(this).is(":checked")) {
                new_top_bottom_sel = true;
            } else {
                var new_j = 0;
                $(".bottom_glass_r").each(function () {
                    if (new_j === new_i && $(this).is(":checked")) {
                        new_top_bottom_sel = true;
                    }
                    new_j++;
                });
            }
            if (new_top_bottom_sel) {
                var new_m = 0;
                var new_reg_glass_val = '';
//                var new_large_glass_val = '';
                $(".sash_t_b").each(function () {
                    if (new_i === new_m) {
                        if ($(this).val() !== '') {
                            new_reg_glass_val = $(this).val();
                        }

//                        var new_n = 0;
//                        $(".sash_t_b").each(function () {
//                            if (new_n === new_m && $(this).val() !== '') {
//                                new_large_glass_val = $(this).val();
//                            }
//                            new_n++;
//                        });
                        if (new_reg_glass_val === '') {
                            alert_msg += 'Please enter value in sash repaire section\n';
                        }
                    }
                    new_m++;
                });
            } else {
                var new_m = 0;
                var new_reg_glass_val = '';
//                var new_large_glass_val = '';
                $(".sash_t_b").each(function () {
                    if (new_i === new_m) {
                        if ($(this).val() !== '') {
                            new_reg_glass_val = $(this).val();
                        }

//                        var new_n = 0;
//                        $(".sash_t_b").each(function () {
//                            if (new_n === new_m && $(this).val() !== '') {
//                                new_large_glass_val = $(this).val();
//                            }
//                            new_n++;
//                        });
                        if (new_reg_glass_val !== '') {
                            alert_msg += 'Please select top or bottom in sash repairs section\n';
                        }
                    }
                    new_m++;
                });
            }
            new_i++;
        });
//--------------------Size and Qty is missing
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
//        start
        // Check for .additional_Balances and .addlstcp sections
        var additionalBalanceFilled = $('#main_div > div').last().find('.additional_Balances').filter(function () {
            return $(this).val().trim() !== "";
        }).length > 0;

        var addlstcpFilled = $('#main_div > div').last().find('.addlstcp').filter(function () {
            return $(this).val().trim() !== "";
        }).length > 0;

// Check if all inputs with classes .addmorevaliR in the last appended section are empty
        var addMoreValiR_Empty = $('#main_div > div').last().find('.addmorevaliR').filter(function () {
            return $(this).val().trim() === "";
        }).length === $('#main_div > div').last().find('.addmorevaliR').length;

// Check if all inputs with classes .addmorevaliL in the last appended section are empty
        var addMoreValiL_Empty = $('#main_div > div').last().find('.addmorevaliL').filter(function () {
            return $(this).val().trim() === "";
        }).length === $('#main_div > div').last().find('.addmorevaliL').length;

// Check if all inputs with classes .addmorevaliR for .addlstcp section are empty
//        var addlstcpR_Empty = $('#main_div > div').last().find('.addmorevaliR').filter(function () {
//            return $(this).val().trim() === "";
//        }).length === $('#main_div > div').last().find('.addmorevaliR').length;
//
//// Check if all inputs with classes .addmorevaliL for .addlstcp section are empty
//        var addlstcp_EmptyL_Empty = $('#main_div > div').last().find('.addmorevaliL').filter(function () {
//            return $(this).val().trim() === "";
//        }).length === $('#main_div > div').last().find('.addmorevaliL').length;

// Trigger a single alert if conditions are met
        if ((additionalBalanceFilled && addMoreValiR_Empty && addMoreValiL_Empty) ||
                (addlstcpFilled && addMoreValiR_Empty && addMoreValiL_Empty)) {
            alert_msg += 'please fill the glass pane section input box\n';
        }

//    for add repair balances
        // Check if any input with the class .additional_Balances in the last appended section has a value
        var addrepBFilled = $('#main_div > div').last().find('.addrepB').filter(function () {
            return $(this).val().trim() !== "";
        }).length > 0;
        // Check if all inputs with classes .addmorevaliR in the last appended section are empty
        var addrepVali_Empty = $('#main_div > div').last().find('.additional_Balances').filter(function () {
            return $(this).val().trim() === "";
        }).length === $('#main_div > div').last().find('.additional_Balances').length;
        // Trigger the alert if the conditions are met for the last appended form section
        if (addrepBFilled && addrepVali_Empty) {
            alert_msg += 'please fill the additional repair input box\n';
        }
//for sash repair section
        var sashFilled = $('#main_div > div').last().find('.rdosr').filter(function () {
            return $(this).val().trim() !== "";
        }).length > 0;
        // Check if all inputs with classes .addmorevaliR in the last appended section are empty
        var addMoreValisash_t_b_Empty = $('#main_div > div').last().find('.sash_t_b').filter(function () {
            return $(this).val().trim() === "";
        }).length === $('#main_div > div').last().find('.sash_t_b').length;
        // Trigger the alert if the conditions are met for the last appended form section
        if (sashFilled && addMoreValisash_t_b_Empty) {
            alert_msg += 'please fill the sash repair section input box\n';
        }
        // Check if any input with the class .zero in the last appended section has a value of 0
        let hasZero = false;
        $('#main_div > div').last().find('.forzero').each(function () {
            let value = $(this).val().trim();  // Get the value and remove any extra whitespace
            if (value === '0') {
                hasZero = true;
                $(this).focus();  // Focus on the input with a value of 0
                return false;  // Exit the loop after finding the first 0
            }
        });
        if (hasZero) {
            alert_msg += 'Please enter a value greater than 0 in the glass section.\n';
        }
        if ($('#main_techsign').val().trim() === '') {
            alert_msg += 'Tech Signature is Missing';
        }
        if (alert_msg !== '') {
            alert(alert_msg);
            return false;
        } else {
            $(this).hide();
        }
   if (alert_msg && alert_msg.trim() === "") {
            $('#btndone, #not_done').prop('disabled', true);  // Disable both buttons if alert_msg is empty
        } else {
            $('#btndone, #not_done').prop('disabled', false);  // Enable both buttons
        }
    });
</script>




<script>
    $(document).ready(function () {
        $("#main_div").on('change paste keyup', ".gls_tp_divvv input", function () {
            var top_true = false;
            $(this).closest(".gls_tp_divvv").find(':checkbox').each(function () {
                if ($(this).is(':checked')) {
                    top_true = true;
                }
            });
            $(this).closest(".gls_tp_divvv").find('input:text').each(function () {
                if ($(this).val() != "") {
                    top_true = true;
                }
            });
            $(this).closest(".gls_tp_divvv").find('input[type="radio"]').each(function () {
                if ($(this).is(':checked')) {
                    top_true = true;
                }
            });
            if (top_true) {
                $(this).closest(".gls_tp_divvv").next('.saassh_reepp_divvv').hide();
            } else {
                $(this).closest(".gls_tp_divvv").next('.saassh_reepp_divvv').show();
            }
        });

        $("#main_div").on('change paste keyup', ".saassh_reepp_divvv input", function () {
            var bottom_true = false;
            $(this).closest(".saassh_reepp_divvv").find(':checkbox').each(function () {
                if ($(this).is(':checked')) {
                    bottom_true = true;
                }
            });
            $(this).closest(".saassh_reepp_divvv").find('input:text').each(function () {
                if ($(this).val() != "") {
                    bottom_true = true;
                }
            });
            $(this).closest(".saassh_reepp_divvv").find('input[type="radio"]').each(function () {
                if ($(this).is(':checked')) {
                    bottom_true = true;
                }
            });
            if (bottom_true) {
                $(this).closest(".saassh_reepp_divvv").prev('.gls_tp_divvv').hide();
            } else {
                $(this).closest(".saassh_reepp_divvv").prev('.gls_tp_divvv').show();
            }
        });
    });
</script>  
