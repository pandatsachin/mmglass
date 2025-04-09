<?php

include("includes/config.php");
if (isset($_POST['CustCode'])) {
    $CustCode = $_POST['CustCode'];
    $qry = "SELECT * FROM WCustomerTable WHERE CustCode='" . $CustCode . "'";
    $result = $conn->query($qry);
    $response = array();
    if ($result->num_rows > 0) {
        // If customer exists, set exists flag and populate data
        $row = $result->fetch_object();
        $response['exists'] = true;
        $response['CustName'] = $row->CustName;
        $response['CustAddress'] = $row->CustAddress;
        $response['CustPhone'] = $row->CustPhone;
        $response['email'] = $row->email;
    } else {
        // If customer doesn't exist, set exists flag to false
        $response['exists'] = false;
    }
    echo json_encode($response);
    exit;


} else if (isset($_POST['eachitemID'])) {
    $item_id = $_POST['eachitemID'];
    $qty = $_POST['qty'];
    $tray = $_POST['tray'];
    $colorID = $_POST['colorID'];
    $sizeID = $_POST['sizeID'];
    $qry = "select IC.ColorID,CT.ColorName from ItemColors IC, ColorTable CT "
            . "WHERE IC.ColorID=CT.ColorID AND IC.ItemID=" . $item_id . " ORDER BY CT.ColorName";
    $result = $conn->query($qry);
    $color_arr = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_object()) {
            $color_arr[] = $row->ColorID;
        }
    }
    $qry1 = "select ISS.SizeID,ST.`Size` from ItemSizes ISS, SizeTable ST "
            . " WHERE ISS.SizeID=ST.SizeID AND ISS.ItemID=" . $item_id . " ORDER BY ST.SizeID";
    $result1 = $conn->query($qry1);
    $size_arr = array();
    if ($result1->num_rows > 0) {
        while ($row1 = $result1->fetch_object()) {
            $size_arr[] = $row1->SizeID;
        }
    }
    if (($colorID != '' && $sizeID != '') || ($colorID == '' && $sizeID == '')) {
        foreach ($color_arr as $colrid) {
            foreach ($size_arr as $sizeid) {
                $str .= '<tr><td><a title="Delete" class="btn btn-danger del-row" href="#" role="button">X</a></td><td><select style="width:350px;" class="frame_style form-control chosen-select" name="frame_style[]">';
                $qry = "select * from ItemTable ORDER BY Description";
                $result = $conn->query($qry);
                if ($result->num_rows > 0) {
                    $str .= '<option value="">---Select---</option>';
                    while ($row = $result->fetch_object()) {
                        $Price_Str = 'a="' . $row->PriceA . '" b="' . $row->PriceB . '" c="' . $row->PriceC . '"';
                        if ($item_id == $row->ItemID) {
                            $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '" selected>' . $row->Description . '</option>';
                        } else {
                            $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '">' . $row->Description . '</option>';
                        }
                    }
                }
                $qry1 = "select * from ItemTable ORDER BY ItemCode";
                $result1 = $conn->query($qry1);
                if ($result1->num_rows > 0) {
                    while ($row1 = $result1->fetch_object()) {
                        $Price_Str = 'a="' . $row1->PriceA . '" b="' . $row1->PriceB . '" c="' . $row1->PriceC . '"';
                        $str .= '<option ' . $Price_Str . ' value="' . $row1->ItemID . '">' . $row1->ItemCode . '</option>';
                    }
                }
                $str .= '</select></td>';
                $str .= '<td>' . getColrDD($conn, $item_id, $colrid) . '</td>';
                $str .= '<td>' . getSizeDD($conn, $item_id, $sizeid) . '</td>';
                $str .= '<td><input type="text" class="frame_qty" name="qty[]" value="' . $qty . '"></td>';
                $str .= '<td><a title="Each" class="btn btn-info add-each" href="#" role="button">+ Each Size and Color</a></td><td><input type="text" name="tray[]" value="' . $tray . '"></td></tr>';
            }
        }
    } else if ($colorID != '') {
        foreach ($size_arr as $sizeid) {
            $str .= '<tr><td><a title="Delete" class="btn btn-danger del-row" href="#" role="button">X</a></td><td><select style="width:350px;" class="frame_style form-control chosen-select" name="frame_style[]">';
            $qry = "select * from ItemTable ORDER BY Description";
            $result = $conn->query($qry);
            if ($result->num_rows > 0) {
                $str .= '<option value="">---Select---</option>';
                while ($row = $result->fetch_object()) {
                    $Price_Str = 'a="' . $row->PriceA . '" b="' . $row->PriceB . '" c="' . $row->PriceC . '"';
                    if ($item_id == $row->ItemID) {
                        $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '" selected>' . $row->Description . '</option>';
                    } else {
                        $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '">' . $row->Description . '</option>';
                    }
                }
            }
            $qry1 = "select * from ItemTable ORDER BY ItemCode";
            $result1 = $conn->query($qry1);
            if ($result1->num_rows > 0) {
                while ($row1 = $result1->fetch_object()) {
                    $Price_Str = 'a="' . $row1->PriceA . '" b="' . $row1->PriceB . '" c="' . $row1->PriceC . '"';
                    $str .= '<option ' . $Price_Str . ' value="' . $row1->ItemID . '">' . $row1->ItemCode . '</option>';
                }
            }
            $str .= '</select></td>';
            $str .= '<td>' . getColrDD($conn, $item_id, $colorID) . '</td>';
            $str .= '<td>' . getSizeDD($conn, $item_id, $sizeid) . '</td>';
            $str .= '<td><input type="text" class="frame_qty" name="qty[]" value="' . $qty . '"></td>';
            $str .= '<td><a title="Each" class="btn btn-info add-each" href="#" role="button">+ Each Size and Color</a></td><td><input type="text" name="tray[]" value="' . $tray . '"></td></tr>';
        }
    } else if ($sizeID != '') {
        foreach ($color_arr as $colrid) {
            $str .= '<tr><td><a title="Delete" class="btn btn-danger del-row" href="#" role="button">X</a></td><td><select style="width:350px;" class="frame_style form-control chosen-select" name="frame_style[]">';
            $qry = "select * from ItemTable ORDER BY Description";
            $result = $conn->query($qry);
            if ($result->num_rows > 0) {
                $str .= '<option value="">---Select---</option>';
                while ($row = $result->fetch_object()) {
                    $Price_Str = 'a="' . $row->PriceA . '" b="' . $row->PriceB . '" c="' . $row->PriceC . '"';
                    if ($item_id == $row->ItemID) {
                        $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '" selected>' . $row->Description . '</option>';
                    } else {
                        $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '">' . $row->Description . '</option>';
                    }
                }
            }
            $qry1 = "select * from ItemTable ORDER BY ItemCode";
            $result1 = $conn->query($qry1);
            if ($result1->num_rows > 0) {
                while ($row1 = $result1->fetch_object()) {
                    $Price_Str = 'a="' . $row1->PriceA . '" b="' . $row1->PriceB . '" c="' . $row1->PriceC . '"';
                    $str .= '<option ' . $Price_Str . ' value="' . $row1->ItemID . '">' . $row1->ItemCode . '</option>';
                }
            }
            $str .= '</select></td>';
            $str .= '<td>' . getColrDD($conn, $item_id, $colrid) . '</td>';
            $str .= '<td>' . getSizeDD($conn, $item_id, $sizeID) . '</td>';
            $str .= '<td><input type="text" class="frame_qty" name="qty[]" value="' . $qty . '"></td>';
            $str .= '<td><a title="Each" class="btn btn-info add-each" href="#" role="button">+ Each Size and Color</a></td><td><input type="text" name="tray[]" value="' . $tray . '"></td></tr>';
        }
    }
    $data = array('msg' => 'success', 'data' => $str);
    echo json_encode($data);
    exit;
} else if (isset($_POST['itemID'])) {
    $item_id = $_POST['itemID'];
    $Color = $_POST['Color'];
    $Size = $_POST['Size'];
    $qry = "select IC.ColorID,CT.ColorName from ItemColors IC, ColorTable CT "
            . "WHERE IC.ColorID=CT.ColorID AND IC.ItemID=" . $item_id . " ORDER BY CT.ColorName";
    $result = $conn->query($qry);
    $str = 'NA';
    if ($result->num_rows > 0) {
        $str = '<select class="frame_color" name="frame_color[]">';
        $str .= '<option value="">---Select---</option>';
        while ($row = $result->fetch_object()) {
            $selected = ($Color == $row->ColorName) ? 'selected' : '';
            $str .= '<option value="' . $row->ColorID . '" ' . $selected . '>' . $row->ColorName . '</option>';
        }
        $str .= '</select>';
    }
    $qry1 = "select ISS.SizeID,ST.`Size` from ItemSizes ISS, SizeTable ST "
            . " WHERE ISS.SizeID=ST.SizeID AND ISS.ItemID=" . $item_id . " ORDER BY ST.SizeID";
    $result1 = $conn->query($qry1);
    $str1 = 'NA';
    $sel_str = '<option value="">---Select---</option>';
    $optn_str = '';
    if ($result1->num_rows > 0) {
        $str1 = '<select class="frame_size" name="frame_size[]">';
        $i = 0;
        while ($row1 = $result1->fetch_object()) {
            $selected1 = ($Size == $row1->Size) ? 'selected' : '';
            $optn_str .= '<option value="' . $row1->SizeID . '" ' . $selected1 . '>' . $row1->Size . '</option>';
            $i++;
        }
        if ($i > 1) {
            $str1 = $str1 . $sel_str . $optn_str;
        } else {
            $str1 = $str1 . $optn_str;
        }
        $str1 .= '</select>';
    }
    $str2 = '<input type="text" class="frame_qty" name="qty[]" >';
    $str3 = '<input type="text" name="tray[]" >';
    $data = array('msg' => 'success', 'data' => $str, 'data1' => $str1, 'data2' => $str2, 'data3' => $str3);
    echo json_encode($data);
    exit;
} else if (isset($_POST['SalesmanID'])) {
    $str = '';
    $SalesmanID = $_POST['SalesmanID'];
    $data_qry = "select IT.ItemID,IT.PriceA,IT.PriceB,IT.PriceC,TOR.ItemDesc,CT.ColorID,ST.SizeID,TOR.Qty from TempOrder TOR,ItemTable IT,ColorTable CT,SizeTable ST "
            . " where (TOR.ItemDesc=IT.Description OR TOR.ItemDesc=IT.ItemCode) AND "
            . " TOR.ColorName=CT.ColorName AND TOR.Size=ST.Size AND SalesmanID=" . $SalesmanID . " order by TempOrderID";
    $res = $conn->query($data_qry);
    if ($res->num_rows > 0) {
        while ($drow = $res->fetch_object()) {
            $str .= '<tr><td><a title="Delete" class="btn btn-danger del-row" href="#" role="button">X</a></td><td><select style="width:350px;" class="frame_style form-control chosen-select" name="frame_style[]">';
            $qry = "select * from ItemTable ORDER BY Description";
            $result = $conn->query($qry);
            if ($result->num_rows > 0) {
                $str .= '<option value="">---Select---</option>';
                while ($row = $result->fetch_object()) {
                    $Price_Str = 'a="' . $row->PriceA . '" b="' . $row->PriceB . '" c="' . $row->PriceC . '"';
                    if ($drow->ItemDesc == $row->Description) {
                        $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '" selected>' . $row->Description . '</option>';
                    } else {
                        $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '">' . $row->Description . '</option>';
                    }
                }
            }
            $qry1 = "select * from ItemTable ORDER BY ItemCode";
            $result1 = $conn->query($qry1);
            if ($result1->num_rows > 0) {
                while ($row1 = $result1->fetch_object()) {
                    $Price_Str = 'a="' . $row1->PriceA . '" b="' . $row1->PriceB . '" c="' . $row1->PriceC . '"';
                    if ($drow->ItemDesc == $row1->ItemCode) {
                        $str .= '<option ' . $Price_Str . ' value="' . $row1->ItemID . '" selected>' . $row1->ItemCode . '</option>';
                    } else {
                        $str .= '<option ' . $Price_Str . ' value="' . $row1->ItemID . '">' . $row1->ItemCode . '</option>';
                    }
                }
            }
            $str .= '</select></td>';
            $str .= '<td>' . getColrDD($conn, $drow->ItemID, $drow->ColorID) . '</td>';
            $str .= '<td>' . getSizeDD($conn, $drow->ItemID, $drow->SizeID) . '</td>';
            $str .= '<td><input type="text" class="frame_qty" name="qty[]" value="' . $drow->Qty . '"></td>';
            $str .= '<td><a title="Each" class="btn btn-info add-each" href="#" role="button">+ Each Size and Color</a></td><td><input type="text" name="tray[]" value=""></td></tr>';
        }
    }
    $data = array('msg' => 'success', 'data' => $str);
    echo json_encode($data);
    exit;
} else {
    $str = '<tr><td><a title="Delete" class="btn btn-danger del-row" href="#" role="button">X</a></td><td><select style="width:350px;" class="frame_style form-control chosen-select" name="frame_style[]">';
    $qry = "select * from ItemTable ORDER BY Description";
    $result = $conn->query($qry);
    if ($result->num_rows > 0) {
        $str .= '<option value="">---Select---</option>';
        while ($row = $result->fetch_object()) {
            $Price_Str = 'a="' . $row->PriceA . '" b="' . $row->PriceB . '" c="' . $row->PriceC . '"';
            $str .= '<option ' . $Price_Str . ' value="' . $row->ItemID . '">' . $row->Description . '</option>';
        }
    }
    $qry1 = "select * from ItemTable ORDER BY ItemCode";
    $result1 = $conn->query($qry1);
    if ($result1->num_rows > 0) {
        while ($row1 = $result1->fetch_object()) {
            $Price_Str = 'a="' . $row1->PriceA . '" b="' . $row1->PriceB . '" c="' . $row1->PriceC . '"';
            $str .= '<option ' . $Price_Str . ' value="' . $row1->ItemID . '">' . $row1->ItemCode . '</option>';
        }
    }
    $str .= '</select></td><td></td><td></td><td></td>';
    $str .= '<td><a title="Each" class="btn btn-info add-each" href="#" role="button">+ Each Size and Color</a></td><td></td></tr>';
    $data = array('msg' => 'success', 'data' => $str);
    echo json_encode($data);
    exit;
}

function getColrDD($conn, $item_id, $ColrID) {
    $qry = "select IC.ColorID,CT.ColorName from ItemColors IC, ColorTable CT "
            . "WHERE IC.ColorID=CT.ColorID AND IC.ItemID=" . $item_id . " ORDER BY CT.ColorName";
    $result = $conn->query($qry);
    $str = 'NA';
    if ($result->num_rows > 0) {
        $str = '<select class="frame_color" name="frame_color[]">';
        $str .= '<option value="">---Select---</option>';
        while ($row = $result->fetch_object()) {
            if ($ColrID == $row->ColorID) {
                $str .= '<option value="' . $row->ColorID . '" selected>' . $row->ColorName . '</option>';
            } else {
                $str .= '<option value="' . $row->ColorID . '">' . $row->ColorName . '</option>';
            }
        }
        $str .= '</select>';
    }
    return $str;
}

function getSizeDD($conn, $item_id, $SizeID) {
    $qry1 = "select ISS.SizeID,ST.`Size` from ItemSizes ISS, SizeTable ST "
            . " WHERE ISS.SizeID=ST.SizeID AND ISS.ItemID=" . $item_id . " ORDER BY ST.SizeID";
    $result1 = $conn->query($qry1);
    $str1 = 'NA';
    $optn_str = '';
    $sel_str = '<option value="">---Select---</option>';
    if ($result1->num_rows > 0) {
        $str1 = '<select class="frame_size" name="frame_size[]">';
        $i = 0;
        while ($row1 = $result1->fetch_object()) {
            if ($SizeID == $row1->SizeID) {
                $optn_str .= '<option value="' . $row1->SizeID . '" selected>' . $row1->Size . '</option>';
            } else {
                $optn_str .= '<option value="' . $row1->SizeID . '">' . $row1->Size . '</option>';
            }
            $i++;
        }
        if ($i > 1) {
            $str1 = $str1 . $sel_str . $optn_str;
        } else {
            $str1 = $str1 . $optn_str;
        }
        $str1 .= '</select>';
    }
    return $str1;
}

?>