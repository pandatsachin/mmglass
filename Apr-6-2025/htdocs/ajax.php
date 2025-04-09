<?php

include("includes/config.php");
if(isset($_POST['TempOrderID'])) {
  $TempOrderID = $_POST['TempOrderID'];
  $qry = "delete from TempOrder WHERE TempOrderID='" . $TempOrderID . "'";
  $result = $conn->query($qry);
  $data = array('msg' => 'success');
  echo json_encode($data);
  exit;
} else if(isset($_POST['Action']) && $_POST['Action'] == 'UpdateTempOrder') {
  $ItemDesc = $_POST['ItemDesc'];
  $ColorName = $_POST['ColorName'];
  $Size = $_POST['Size'];
  $Qty = $_POST['Qty'];
  $SalesmanID = $_POST['SalesmanID'];
  if(!empty($ItemDesc) && !empty($ColorName) && !empty($Size) && !empty($Qty)) {
    $qry = "select * from TempOrder where ItemDesc='" . $ItemDesc . "' AND ColorName='" . $ColorName . "'"
        . " AND Size='" . $Size . "' AND SalesmanID='" . $SalesmanID . "'";
    $result = $conn->query($qry);
    if($result->num_rows > 0) {
      $row = $result->fetch_object();
      if($row->Qty != $Qty) {
        $tqry = "update TempOrder set Qty='" . $Qty . "' where TempOrderID=" . $row->TempOrderID;
      }
    } else {
      $tqry = "insert into TempOrder(ItemDesc,ColorName,Size,Qty,SalesmanID)"
          . " values('" . $ItemDesc . "','" . $ColorName . "','" . $Size . "','" . $Qty . "','" . $SalesmanID . "')";
    }
    $conn->query($tqry);
  }
  $data = array('msg' => 'success');
  echo json_encode($data);
  exit;
} else if(isset($_POST['Action']) && $_POST['Action'] == 'DeleteTempOrder') {
  $ItemDesc = $_POST['ItemDesc'];
  $ColorName = $_POST['ColorName'];
  $Size = $_POST['Size'];
  $SalesmanID = $_POST['SalesmanID'];
  $qry = "delete from TempOrder where ItemDesc='" . $ItemDesc . "' AND ColorName='" . $ColorName . "'"
      . " AND Size='" . $Size . "' AND SalesmanID='" . $SalesmanID . "'";
  $conn->query($qry);
  $data = array('msg' => 'success');
  echo json_encode($data);
  exit;
} else if(isset($_POST['Action']) && $_POST['Action'] == 'SaveTopData') {
  $NewAccount = $_POST['NewAccount'];
  $BuyerName = $_POST['BuyerName'];
  $APName = $_POST['APName'];
  $APEmail = $_POST['APEmail'];
  $Phone1 = $_POST['Phone1'];
  $CompanyLegalName = $_POST['CompanyLegalName'];
  $Street1 = $_POST['Street1'];
  $City1 = $_POST['City1'];
  $State1 = $_POST['State1'];
  $Zip1 = $_POST['Zip1'];
  $BGAccount = $_POST['BGAccount'];
  $Company1 = $_POST['Company1'];
  $BuyerName1 = $_POST['BuyerName1'];
  $BuyerEmailAddress = $_POST['BuyerEmailAddress'];
  $Address1 = $_POST['Address1'];
  $Phone2 = $_POST['Phone2'];
  $PurchaseOrder = $_POST['PurchaseOrder'];
  $ShipVia = $_POST['ShipVia'];
  $BillingTerms = $_POST['BillingTerms'];
  $DropShipAddress = $_POST['DropShipAddress'];
  $BillTo = $_POST['BillTo'];
  $Company2 = $_POST['Company2'];
  $Address2 = $_POST['Address2'];
  $City2 = $_POST['City2'];
  $State2 = $_POST['State2'];
  $Zip2 = $_POST['Zip2'];
  $Phone3 = $_POST['Phone3'];
  $SpecInstructions = isset($_POST['SpecInstructions']) ? $_POST['SpecInstructions'] : '';
  $PricingLevel = $_POST['PricingLevel'];
  $CCNumber = $_POST['CCNumber'];
  $ExpDate = $_POST['ExpDate'];
  $ThreeDigitCode = $_POST['ThreeDigitCode'];
  $CardStmtAddress = $_POST['CardStmtAddress'];
  $SalesmanID = $_POST['SalesmanID'];
  $qry = "update TempOrderTop SET NewAccount='" . $NewAccount . "',BuyerName='" . $BuyerName . "',"
      . "APName='" . $APName . "',APEmail='" . $APEmail . "',Phone1='" . $Phone1 . "',"
      . "CompanyLegalName='" . $CompanyLegalName . "',Street1='" . $Street1 . "',City1='" . $City1 . "',"
      . "State1='" . $State1 . "',Zip1='" . $Zip1 . "',BGAccount='" . $BGAccount . "',"
      . "Company1='" . $Company1 . "',BuyerName1='" . $BuyerName1 . "',BuyerEmailAddress='" . $BuyerEmailAddress . "',"
      . "Address1='" . $Address1 . "',Phone2='" . $Phone2 . "',PurchaseOrder='" . $PurchaseOrder . "',"
      . "ShipVia='" . $ShipVia . "',DropShipAddress='" . $DropShipAddress . "',BillTo='" . $BillTo . "',"
      . "Company2='" . $Company2 . "',Address2='" . $Address2 . "',City2='" . $City2 . "',"
      . "State2='" . $State2 . "',Zip2='" . $Zip2 . "',BillingTerms='" . $BillingTerms . "',Phone3='" . $Phone3 . "',"
      . "SpecInstructions='" . $SpecInstructions . "',PricingLevel='" . $PricingLevel . "',CCNumber='" . $CCNumber . "',"
      . "ExpDate='" . $ExpDate . "',ThreeDigitCode='" . $ThreeDigitCode . "',CardStmtAddress='" . $CardStmtAddress . "' WHERE SalesmanID='" . $SalesmanID . "'";
  $conn->query($qry);
  $data = array('msg' => 'success');
  echo json_encode($data);
  exit;
}
?>