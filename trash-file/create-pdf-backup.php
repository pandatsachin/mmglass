<?php
error_reporting(0);
//ini_set('display_errors', 1);

include("includes/config.php");
include("tcpdf/tcpdf.php");

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
if (isset($_POST['generate_pdf'])) {
    $jobID = $conn->real_escape_string($_POST['jobid']);
    $sql = "SELECT * FROM JobDetailsTable WHERE JobID = '$jobID'";
    $result = $conn->query($sql);
    $row = $result->fetch_object();

    $pdftxt = '<table cellpadding="5" style="border: 1px solid black;">';

////////////////////////////////////second loop////////////////////////////////////////////
    $total_box = mysqli_num_rows($result);

    
    $pdf_text_arr = array();

    for ($i = 0;
            $i < $total_box;
            $i++) {
//                    echo '<pre>'; print_r($row);

        $Room = '';
        $Floor = $Top = $Bottom = $RegGlass = $additional_repairs = $LargeGlass = $DoublePaneLarge = $DoublePaneRegular = $RegRepairs = $UltraLift = 0;
        $BlackTip = $BlockTackle = $LAMI = $Plexi = $RoughWire = $PolyWire = $RoughWireClear = $PolyWireClear = 0;
        $Height = $Width = 0.0;
        $InsulatedUnit = $NewScreen = $ScreenRepair = $Moldings = $WindowGuards = $Capping = 0;
        $Locks = $Shoes = $TiltLatch = $Pivot = $Caps = 0;
        $Notes = '';
        $room_key = $i == 0 ? "rdoroom" : "rdoroom" . $i;

        $Room = $row->Room;
        if (empty($Room)) {
            $Room = $row->Room[$i];
        }
        $Floor = $row->Floor[$i];
        $Room_pdf = $Room;

        if ($Room == 'Hallway') {
            $Room_pdf = 'Hallway-' . $Floor;
        }


        $RegGlassStr = '';
        $LargeGlassStr = '';
        $additional_repairsStr = '';
        $TopBottomStr = '';
        $TopBottomStr1 = '';
        $Top = $row->Top[$i] ? 1 : 0;
        if ($Top !== 0) {
            $Top_pdf = '<img src="images/tick.png"> <strong>Top</strong>';
        }
//        $Top_pdf = isset($_POST['top_glass'][$i]) ? '<img src="images/tick.png"> <strong>Top</strong>' : '';
        $Bottom = $row->Bottom[$i] ? 1 : 0;
        if ($Bottom !== 0) {
            $Bottom_pdf = '<img src="images/tick.png"> <strong> Bottom</strong>';
        }
//        $Bottom_pdf = isset($_POST['bottom_glass'][$i]) ? '<img src="images/tick.png"> <strong> Bottom</strong>' : '';
        $RegGlass = $row->RegGlass[$i];
        $additional_repairs = $row->additional[$i];
        $DoublePaneRegular = $row->DoublePaneRegular[$i] ? 1 : 0;

//        $DoublePaneRegular_pdf = isset($_POST['double_pane_rg'][$i]) ? '<img src="images/tick.png"> DP' : '';
        $LargeGlass = $row->LargeGlass[$i];
        $DoublePaneLarge = $row->DoublePaneLarge[$i] ? 1 : 0;

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
//            echo 'asddc';
            $addrepair_key = $i == 0 ? "additional_repairs" : "additional_repairs" . $i;
            $RedTip_c = $row->RedTip_c;
            $UltraLift_c = $row->UltraLift_c;
            $BlackTip_c = $row->BlackTip_c;
            $BlockTackle_c = $row->BlockTackle_c;
//            $BlackTip_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'black') ? 1 : 0;
//            $BlockTackle_c = (isset($_POST[$addrepair_key][0]) && $_POST[$addrepair_key][0] == 'block') ? 1 : 0;
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
//            $RedTip_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'red') ? 1 : 0;
//            $UltraLift_c = ($row->UltraLift && $row->UltraLift_c == 'ultra') ? 1 : 0;
//            $BlackTip_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'black') ? 1 : 0;
//            $BlockTackle_c = (isset($_POST[$repair_key][0]) && $_POST[$repair_key][0] == 'block') ? 1 : 0;

            $Locks = $row->Locks[$i];
            $Shoes = $row->Shoes[$i];
            $TiltLatch = $row->TiltLatch[$i];
            $Caps = $row->Caps[$i];
        }
//////////add_repairs end

        $RegRepairs = $RedTip_c == 1 ? $row->RedTip[$i] : 0;
         $UltraLift = $UltraLift_c == 1 ? $row->UltraLift[$i] : 0;
        $BlackTip = $BlackTip_c == 1 ? $row->BlackTip[$i] : 0;
        $BlockTackle = $BlockTackle_c == 1 ? $row->BlockTackle[$i] : 0;
        if (empty($RegRepairs) && empty($UltraLift) && empty($BlackTip) && empty($BlockTackle) && !empty($_POST['sash_value'][$i])) {
            $BlackTip = $row->BlockTackle[$i];
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

        $RoughWireClear = $row->RoughWireClear[$i] ? 1 : 0;
        $RoughWireClear_pdf = $row->RoughWireClear[$i] ? 'Yes' : 'No';
        $glass_type_pdf = $row->RoughWireClear[$i] ? $glass_type_pdf : $glass_type_pdf;
        $PolyWireClear = $row->PolyWireClear[$i] ? 1 : 0;
        $PolyWireClear_pdf = $row->PolyWireClear[$i] ? 'Yes' : 'No';
        $glass_type_pdf = $row->PolyWireClear[$i] ? $glass_type_pdf : $glass_type_pdf;
        $Clear_pdf = '';
        if ($RoughWireClear == 1) {
            $Clear_pdf = 'CG ' . $RoughWireClear_pdf;
        } else if ($PolyWireClear == 1) {
            $Clear_pdf = 'CG ' . $PolyWireClear_pdf;
        }
        $Width = $row->Width[$i];
        $Height = $row->Height[$i];
        $glass_qty = $row->GTQty[$i];
        $InsulatedUnit = $row->InsulatedUnit[$i];
        $NewScreen = $row->NewScreen[$i];
        $ScreenRepair = $row->ScreenRepair[$i];
        $Moldings = $row->Moldings[$i];
        $WindowGuards = $row->WindowGuards[$i];
        $Capping = $row->Capping[$i];
        $Pivot = $row->Pivot[$i];
        $Notes = $row->Notes[$i];

      ///////////////////////////////////////////////////////////////////////////////////////////
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
        $Notes = implode(' ', $first_five_words);
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
        $RegRepairs = ($RedTip_c == 1) ? $row->RedTip[$i] : '<u>    </u>';
        $UltraLift = $UltraLift_c == 1 ? $row->UltraLift[$i] : '<u>    </u>';
        $BlackTip = $BlackTip_c == 1 ? $row->BlackTip[$i] : '<u>    </u>';
        $BlockTackle = $BlockTackle_c == 1 ? $row->BlockTackle[$i] : '<u>    </u>';

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

        if (!empty($additional_repairsStr)) {
            if (!empty($RegRepairs) || !empty($Locks) || !empty($UltraLift) || !empty($Shoes) || !empty($BlackTip) || !empty($TiltLatch) || !empty($BlockTackle) || !empty($Caps)) {
                if ($RedTip_c == 1) {
                    echo $RegRepairs = trim($_POST['sash_value'][$i]);
                    $additional_repairs += (int) $RegRepairs;
                    exit;
                } else {
                    $RegRepairs = '<u>    </u>';
                }

                if ($UltraLift_c == 1) {
                    $UltraLift = trim($_POST['sash_value'][$i]);
                    $additional_repairs += (int) $UltraLift;
                } else {
                    $UltraLift = '<u>    </u>';
                }

                if ($BlackTip_c == 1) {
                    $BlackTip = trim($_POST['sash_value'][$i]);
                    $additional_repairs += (int) $BlackTip;
                } else {
                    $BlackTip = '<u>    </u>';
                }

                if ($BlockTackle_c == 1) {
                    $BlockTackle = trim($_POST['sash_value'][$i]);
                    $additional_repairs += (int) $BlockTackle;
                } else {
                    $BlockTackle = '<u>    </u>';
                }
                $pdftxt .= '<tr>';
                $pdftxt .= '<td>';
                $pdftxt .= '<strong>RB</strong> <u style="font-size: 12px;">' . $additional_repairs . '</u>';
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
    $initialY = $pdf->GetY();
    $imageHeight = 20; // Desired height for both images
    $imageWidth = 60;  // Desired width for both images
// Place tenant image if available
    if (!empty($file)) {
        $pdf->Image($file, 15, $initialY, $imageWidth, $imageHeight);

// Draw the underline for tenant image
        $pdf->Line(15, $initialY + $imageHeight + 1, 15 + $imageWidth, $initialY + $imageHeight + 1);
    }

// Place tech image if available
    if (!empty($techfile)) {
        $pdf->Image($techfile, 100, $initialY, $imageWidth, $imageHeight);

// Draw the underline for tech image
        $pdf->Line(100, $initialY + $imageHeight + 1, 100 + $imageWidth, $initialY + $imageHeight + 1);
    }

// Update the Y position to account for the image height and underlines
    $pdf->SetY($initialY + $imageHeight + 3); // Adjust the 3 to control the distance between the underline and the next content
// Determine and write signature table
    if (!empty($techfile) && !empty($file)) {
        $pdftxt_sign = '<table cellspacing="0" cellpadding="0"><tr><td><strong>Tenant Signature</strong></td><td><strong>Tech Signature</strong></td></tr></table>';
    } elseif (!empty($file)) {
        $pdftxt_sign = '<table cellspacing="0" cellpadding="0"><tr><td><strong>Tenant Signature</strong></td><td></td></tr></table>';
    } elseif (!empty($techfile)) {
        $pdftxt_sign = '<table cellspacing="0" cellpadding="0"><tr><td></td><td><strong>Tech Signature</strong></td></tr></table>';
    } else {
//        $space = '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
        $space = '<span style="border-bottom: 1px solid black; display: inline-block; width: 300px;">&nbsp;</span>';

        $pdftxt_sign = '<table cellspacing="0" cellpadding="0"><tr><td>' . $space . '<br><strong>Tenant Signature</strong></td><td>' . $space . '<br><strong>Tech Signature</strong></td></tr></table>';
    }


    if (!empty($pdftxt_sign)) {
        $tbl_sign = <<<EOD
        $pdftxt_sign
    EOD;
        $pdf->writeHTML($tbl_sign, true, true, false, false, '');
    }

//    end

    $html .= '<tr>';
    $html .= '<td style="font-size: 8px;"><strong>KIT</strong> = KITCHEN, <strong>BR</strong> = BEDROOM, <strong>BATH</strong> = BATHROOM, <br><strong>LR</strong> = LIVING ROOM, <strong>RW</strong> = ROUGH WIRE, <strong>PW</strong> = POLY WIRE, <br><strong>CG</strong> = CLEAR GLASS, <strong>IU</strong> = INSULATED UNITS, <strong>TL</strong> = TILT LATCH, <br><strong>RB</strong> = RED TIP BALANCES, <strong>BB</strong> = BLACK TIP BALANCES, <br><strong>DP</strong> = DOUBLE PANE, <strong>S GLASS</strong> = STANDARD GLASS, <br><strong>L GLASS</strong> = LARGE GLASS, <strong>UL</strong> = ULTRA LIFT BALANCE, <br><strong>WG</strong> = Window guards, <strong>Cap</strong> = Capping, <strong>BTB</strong> = BlockTackle, <br><strong>ScreenRep</strong> = SCREENREPAIR
</td>';

    $html .= '</tr>';
    $html .= '</table>';
    $pdf->writeHTML($html, true, true, false, false, '');

//    $pdf->writeHTML('<br><br><br><br><br><hr>', true, false, false, false, '');
    $pdf_name = $JobID . '-' . date('Y-m-d-H-i-s');
    $pdfpath = "/home/bitnami/htdocs/mmglass/JobDetailsPDFs/$pdf_name.pdf";
    $pdf->Output($pdfpath, 'I');
//    $pdf->Output($pdfpath);
    exit;
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
                
                <button type="submit" name="generate_pdf" class="btn btn-primary">generate pdf</button>
            </form>
            <!-- show message -->
            <?php if (!empty($msg)) echo $msg; ?>
        </div>
    </body>
</html>
