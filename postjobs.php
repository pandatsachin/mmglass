<?php

include("includes/config.php");
$request = file_get_contents('php://input');
if(!empty($request)) {
  $del_qry = "delete from JobTable";
  $conn->query($del_qry);
  $data = json_decode($request);
  foreach ($data as $row) {
    $Measurement = trim($row->Measurement);
    $jOrder = trim($row->jOrder);
    $TechID = trim($row->TechID);
    $WebID = trim($row->WebID);
    $JobID = trim($row->JobID);
    $Address = trim($row->Address);
    $Apt = trim($row->Apt);
    $JobDescription = addslashes(trim($row->JobDescription));
    $Priority = trim($row->Priority);
    $City = trim($row->City);
    $Schedule = trim($row->Schedule);
    $JobDate = $row->JobDate;
    $qry = "insert into JobTable(JobID,WebID,TechID,Address,JobDescription,Apt,Schedule,jOrder,Measurement,JobDate,City,Priority)"
        . " values('" . $JobID . "','" . $WebID . "','" . $TechID . "','" . $Address . "','" . $JobDescription . "','" . $Apt . "','" . $Schedule . "','" . $jOrder . "','" . $Measurement . "','" . $JobDate . "','" . $City . "','" . $Priority . "')";
    $conn->query($qry);
  }
}
echo "Done";
exit;
?>