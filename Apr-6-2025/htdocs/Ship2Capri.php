<?php
$serverName = "108.58.60.27"; 
$connectionInfo = array("Database" => "capri", "UID" => "gruen", "PWD" => "gruen1");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die( print_r( sqlsrv_errors(), true));
}
?>
