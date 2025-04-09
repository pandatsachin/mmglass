<?php

ini_set('display_errors', 1);
//$connection = odbc_connect("Driver={SQL Server Native Client 10.0};Server=108.58.60.27, 1433;Database=capri;", "gruen", "gruen1");
$serverName = "108.58.60.27, 1433"; //serverName\instanceName, portNumber (default is 1433)
$connectionInfo = array("CharacterSet" => "UTF-8", "Database" => "capri", "UID" => "gruen", "PWD" => "gruen1");
$conn = sqlsrv_connect($serverName, $connectionInfo);
if($conn) {
//if($connection) {
  echo "Connection established.<br />";
} else {
  echo "Connection could not be established.<br />";
  die(print_r(sqlsrv_errors(), true));
}
echo 'hello';
exit;
if(isset($_GET['Shipid']) && trim($_GET['Shipid']) != '') {
  $ShipID = trim($_GET['Shipid']);
  $sql = "insert into Ship(ShipID) values('" . $ShipID . "')";
  $stmt = sqlsrv_query($conn, $sql);
}
