<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '2056M');
//echo phpinfo(); exit;
include("includes/config_cmd.php");
include("includes/smtp.php");
require("includes/phpmailer/src/PHPMailer.php");
require("includes/phpmailer/src/SMTP.php");
require("includes/phpmailer/src/Exception.php");
//////////////////////////////
$cron_qry = "select * from cron";
$cresult = $conn->query($cron_qry);
$crow = $cresult->fetch_object();
if ($crow->status == 1) {
  updateCron($conn, 2);
  $csvDir = '/home/bitnami/htdocs/CSVFiles/';
  //$files = array_diff(scandir($csvDir), array('.', '..'));
  $files = glob("$csvDir*.csv");
  foreach ($files as $file) {
    $tblName = basename($file, ".csv");
    emptyTable($conn, $tblName);
    switch ($tblName) {
      case "SalesForceGrid":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Barcode = trim($data[0]);
              $ItemID = trim($data[1]);
              $Size = trim($data[2]);
              $ColorName = trim($data[3]);
              $A = empty(trim($data[4])) ? 0.0 : trim($data[4]);
              $B = empty(trim($data[5])) ? 0.0 : trim($data[5]);
              $ED = empty(trim($data[6])) ? 0.0 : trim($data[6]);
              $Circ = empty(trim($data[7])) ? 0.0 : trim($data[7]);
              $row_arr[] = "('" . $Barcode . "','" . $ItemID . "','" . $Size . "','" . $ColorName . "'"
                      . ",'" . $A . "','" . $B . "','" . $ED . "','" . $Circ . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO SalesForceGrid(Barcode,ItemID,Size,ColorName,A,B,ED,Circ) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "ColorTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $ColorID = $conn->real_escape_string($data[0]);
              $ColorName = mb_convert_encoding(clean_string($data[1]), 'UTF-8', 'ISO-8859-1');
              $row_arr[] = "('" . $ColorID . "','" . $ColorName . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO ColorTable(ColorID,ColorName) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "ItemColors":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $ItemID = $conn->real_escape_string($data[0]);
              $ColorID = $conn->real_escape_string($data[1]);
              $ColorID = empty($ColorID) ? 0 : $ColorID;
              $imageFile = $conn->real_escape_string($data[2]);
              $row_arr[] = "('" . $ItemID . "','" . $ColorID . "','" . $imageFile . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO ItemColors(ItemID,ColorID,imageFile) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "ItemSizes":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $ItemID = $conn->real_escape_string($data[0]);
              $SizeID = $conn->real_escape_string($data[1]);
              $ItemID = empty($ItemID)?0:$ItemID;
              $SizeID = empty($SizeID)?0:$SizeID;
              $row_arr[] = "('" . $ItemID . "','" . $SizeID . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO ItemSizes(ItemID,SizeID) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "ItemTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $ItemID = $conn->real_escape_string($data[0]);
              $Description = $conn->real_escape_string($data[1]);
              $ItemCode = $conn->real_escape_string($data[2]);
              $ListPrice = $conn->real_escape_string(str_replace('$', '', $data[3]));
              $PriceA = $conn->real_escape_string(str_replace('$', '', $data[4]));
              $PriceB = $conn->real_escape_string(str_replace('$', '', $data[5]));
              $PriceC = $conn->real_escape_string(str_replace('$', '', $data[6]));
              $row_arr[] = "('" . $ItemID . "','" . $Description . "','" . $ItemCode . "','" . $ListPrice . "','" . $PriceA . "','" . $PriceB . "','" . $PriceC . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO ItemTable(ItemID,Description,ItemCode,ListPrice,PriceA,PriceB,PriceC) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "SizeTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $SizeID = $conn->real_escape_string($data[0]);
              $Size = $conn->real_escape_string($data[1]);
              $row_arr[] = "('" . $SizeID . "','" . $Size . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO SizeTable(SizeID,Size) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WCreditDetails":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Description = $conn->real_escape_string($data[1]);
              $ColorName = $conn->real_escape_string($data[2]);
              $Size = $conn->real_escape_string($data[3]);
              $LineNote = $conn->real_escape_string($data[9]);
              $Price = 0.0;
              if (isset($data[7]) && !empty(trim($data[7]))) {
                $Price = substr(trim($data[7]), 1);
              }
              $ExtendedPrice = 0.0;
              if (isset($data[8]) && !empty(trim($data[8]))) {
                $ExtendedPrice = substr(trim($data[8]), 1);
              }
              $row_arr[] = "('" . $data[0] . "','" . $Description . "','" . $ColorName . "','" . $Size . "','" . $data[4] . "','" . $data[5] . "','" . $data[6] . "','" . $Price . "','" . $ExtendedPrice . "','" . $LineNote . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WCreditDetails(CreditID,Description,ColorName,Size,AmountReturned,AmountIn,AmountOut,Price,ExtendedPrice,LineNote) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WCreditTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
           while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                    if ($i > 0 && !empty($data[0])) {
                        $CustCode = empty($data[1]) ? 0 : $conn->real_escape_string($data[1]);
                        $dtime = empty($data[3]) ? 0 : strtotime(str_replace('-', '/', $data[3]));
                        $Custid = empty($data[2]) ? 0 : substr($data[2], 1);
                        $freight = empty($data[4]) ? 0 : substr($data[4], 1);
                        $OriginalAmount = empty($data[5]) ? 0 : substr($data[5], 1);
                        $row_arr[] = "('" . $data[0] . "','" . $CustCode . "','" . $Custid . "','" . $dtime . "','" . $freight . "','" . $OriginalAmount . "')";
                    }
                    $i++;
                }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WCreditTable(CreditID,CustCode,Custid,Date,freight,OriginalAmount) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WCustHasNot":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Description = $conn->real_escape_string($data[2]);
              $row_arr[] = "('" . $data[0] . "','" . $data[1] . "','" . $Description . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WCustHasNot(CustID,CollectionID,Description) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WCustomerTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $CustCode = trim($data[1]);
              $CustName = trim($data[2]);
              $CustName = mb_convert_encoding(clean_string($CustName), 'UTF-8', 'ISO-8859-1');
              $CustAddress = trim($data[3]);
              $CustAddress = mb_convert_encoding(clean_string($CustAddress), 'UTF-8', 'ISO-8859-1');
              $City = trim($data[5]);
              $City = mb_convert_encoding(clean_string($City), 'UTF-8', 'ISO-8859-1');
              $State = trim($data[6]);
              $State = mb_convert_encoding(clean_string($State), 'UTF-8', 'ISO-8859-1');
              $email = trim($data[11]);
              $email = mb_convert_encoding(clean_string($email), 'UTF-8', 'ISO-8859-1');
              $CreditPolicy = trim($data[12]);
              $CreditPolicy = mb_convert_encoding(clean_string($CreditPolicy), 'UTF-8', 'ISO-8859-1');
              $Balance = 0.0;
              if (isset($data[10]) && !empty(trim($data[10]))) {
                $Balance_Str = trim($data[10]);
                if (substr($Balance_Str, 0, 2) == '($') {
                  $Balance = str_replace('($', '-', $Balance_Str);
                  $Balance = str_replace(')', '', $Balance);
                } else {
                  $Balance = substr($Balance_Str, 1);
                }
              }
              $SalesmanID = 0;
              if (isset($data[9]) && !empty($data[9])) {
                $SalesmanID = trim($data[9]);
              }
              $row_arr[] = "('" . $data[0] . "','" . $CustCode . "','" . $CustName . "','" . $CustAddress . "','" . $data[4] . "','" . $City . "','" . $State . "','" . $data[7] . "','" . $data[8] . "','" . $SalesmanID . "','" . $Balance . "','" . $email . "','" . $CreditPolicy . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WCustomerTable(CustID,CustCode,CustName,CustAddress,CustPhone,City,State,Zip,Fax,SalesmanID,Balance,email,CreditPolicy) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WCustPriceTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Description = $conn->real_escape_string($data[1]);
              $price = 0.0;
              if (isset($data[2]) && !empty(trim($data[2]))) {
                $price = substr($data[2], 1);
              }
              $DiscountPrice = 0.0;
              if (isset($data[3]) && !empty(trim($data[3]))) {
                $DiscountPrice = substr($data[3], 1);
              }
              $row_arr[] = "('" . $data[0] . "','" . $Description . "','" . $price . "','" . $DiscountPrice . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WCustPriceTable(CustID,Description,price,DiscountPrice) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WInvoiceDetails":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Description = $conn->real_escape_string($data[1]);
              $ColorName = $conn->real_escape_string($data[2]);
              $Size = $conn->real_escape_string($data[3]);
              $Price = 0.0;
              if (isset($data[7]) && !empty(trim($data[7]))) {
                $Price_Str = trim($data[7]);
                if (substr($Price_Str, 0, 2) == '($') {
                  $Price = str_replace('($', '-', $Price_Str);
                  $Price = str_replace(')', '', $Price);
                } else {
                  $Price = substr($Price_Str, 1);
                }
              }
              $BackOrdered = 0;
              if (isset($data[6]) && !empty(trim($data[6]))) {
                $BackOrdered = trim($data[6]);
              }
              $AmountOrdered = 0;
              if (isset($data[4]) && !empty(trim($data[4]))) {
                $AmountOrdered = trim($data[4]);
              }
              $AmountShipped = 0;
              if (isset($data[5]) && !empty(trim($data[5]))) {
                $AmountShipped = trim($data[5]);
              }
              $row_arr[] = "('" . $data[0] . "','" . $Description . "','" . $ColorName . "','" . $Size . "','" . $AmountOrdered . "','" . $AmountShipped . "','" . $BackOrdered . "','" . $Price . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WInvoiceDetails(InvoiceNumber,Description,ColorName,Size,AmountOrdered,AmountShipped,BackOrdered,Price) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WInvoiceTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $PONumber = $conn->real_escape_string($data[5]);
              $dtime = strtotime(str_replace('-', '/', $data[2]));
              $TrackingNumber = $conn->real_escape_string($data[7]);
              $Freight = 0.0;
              if (isset($data[3]) && !empty(trim($data[3]))) {
                $Freight_Str = trim($data[3]);
                if (substr($Freight_Str, 0, 2) == '($') {
                  $Freight = str_replace('($', '-', $Freight_Str);
                  $Freight = str_replace(')', '', $Freight);
                } else {
                  $Freight = substr($Freight_Str, 1);
                }
              }
              $Amount = 0.0;
              if (isset($data[4]) && !empty(trim($data[4]))) {
                $Amount_Str = trim($data[4]);
                if (substr($Amount_Str, 0, 2) == '($') {
                  $Amount = str_replace('($', '-', $Amount_Str);
                  $Amount = str_replace(')', '', $Amount);
                } else {
                  $Amount = substr($Amount_Str, 1);
                }
              }
              $Balance = 0.0;
              if (isset($data[6]) && !empty(trim($data[6]))) {
                $Balance_Str = trim($data[6]);
                if (substr($Balance_Str, 0, 2) == '($') {
                  $Balance = str_replace('($', '-', $Balance_Str);
                  $Balance = str_replace(')', '', $Balance);
                } else {
                  $Balance = substr($Balance_Str, 1);
                }
              }
              $CustID = 0;
              if (isset($data[0]) && !empty(trim($data[0]))) {
                $CustID = trim($data[0]);
              }
              $InvoiceNumber = 0;
              if (isset($data[1]) && !empty(trim($data[1]))) {
                $InvoiceNumber = trim($data[1]);
              }
              $row_arr[] = "('" . $CustID . "','" . $InvoiceNumber . "','" . $dtime . "','" . $Freight . "','" . $Amount . "','" . $PONumber . "','" . $Balance . "','" . $TrackingNumber . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WInvoiceTable(CustID,InvoiceNumber,Date,Freight,Amount,PONumber,Balance,TrackingNumber) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WPaymentsTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $type = $conn->real_escape_string($data[2]);
              $CheckNumber = $conn->real_escape_string($data[3]);
              $dtime = strtotime(str_replace('-', '/', $data[1]));
              $SumOfAmount = 0.0;
              if (isset($data[4]) && !empty(trim($data[4]))) {
                $SumOfAmount_Str = trim($data[4]);
                if (substr($SumOfAmount_Str, 0, 2) == '($') {
                  $SumOfAmount = str_replace('($', '-', $SumOfAmount_Str);
                  $SumOfAmount = str_replace(')', '', $SumOfAmount);
                } else {
                  $SumOfAmount = substr($SumOfAmount_Str, 1);
                }
              }
              $row_arr[] = "('" . $data[0] . "','" . $dtime . "','" . $type . "','" . $CheckNumber . "','" . $SumOfAmount . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WPaymentsTable(CustID,date,type,CheckNumber,SumOfAmount) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WSalesmanTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Name = $conn->real_escape_string($data[1]);
              $SalesmanCode = $conn->real_escape_string($data[2]);
              $SalesmanEmail = $conn->real_escape_string($data[3]);
              $row_arr[] = "('" . $data[0] . "','" . $Name . "','" . $SalesmanCode . "','" . $SalesmanEmail . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WSalesmanTable(SalesmanID,Name,SalesmanCode,SalesmanEmail) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WUsageDetails":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Description = $conn->real_escape_string($data[2]);
              $ColorName = $conn->real_escape_string($data[3]);
              $Size = $conn->real_escape_string($data[4]);
              $row_arr[] = "('" . $data[0] . "','" . $data[1] . "','" . $Description . "','" . $ColorName . "','" . $Size . "','" . $data[5] . "','" . $data[6] . "','" . $data[7] . "','" . $data[8] . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WUsageDetails(ItemID,CollectionID,Description,ColorName,Size,month,year,`usage`,custid) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
      case "WUsageTable":
        $LagTableID = insertUpdateLogTable($conn, $tblName);
        $i = 0;
        if (($handle = fopen($file, "r")) !== FALSE) {
          $row_arr = array();
          while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($i > 0) {
              $Description = $conn->real_escape_string($data[3]);
              $val_1 = empty($data[4]) ? 0 : $data[4];
              $val_2 = empty($data[5]) ? 0 : $data[5];
              $val_3 = empty($data[6]) ? 0 : $data[6];
              $val_4 = empty($data[7]) ? 0 : $data[7];
              $val_5 = empty($data[8]) ? 0 : $data[8];
              $val_6 = empty($data[9]) ? 0 : $data[9];
              $val_7 = empty($data[10]) ? 0 : $data[10];
              $val_8 = empty($data[11]) ? 0 : $data[11];
              $val_9 = empty($data[12]) ? 0 : $data[12];
              $val_10 = empty($data[13]) ? 0 : $data[13];
              $val_11 = empty($data[14]) ? 0 : $data[14];
              $val_12 = empty($data[15]) ? 0 : $data[15];
              $row_arr[] = "('" . $data[0] . "','" . $data[1] . "','" . $data[2] . "','" . $Description . "','" . $val_1 . "','" . $val_2 . "','" . $val_3 . "','" . $val_4 . "','" . $val_5 . "','" . $val_6 . "','" . $val_7 . "','" . $val_8 . "','" . $val_9 . "','" . $val_10 . "','" . $val_11 . "','" . $val_12 . "')";
            }
            $i++;
          }
          if (count($row_arr) > 0) {
            for ($i = 0; $i <= count($row_arr); $i += 5000) {
              $new_arr = array_slice($row_arr, $i, 5000);
              $row_str = implode(",", $new_arr);
              try {
                $qry = "INSERT INTO WUsageTable(ItemID,CollectionID,custid,Description,`1`,`2`,`3`,`4`,`5`,`6`,`7`,`8`,`9`,`10`,`11`,`12`) values $row_str";
                try {
                  $conn->query($qry);
                } catch (mysqli_sql_exception $e) {
                  $message = "Data error/special characters in line# " . $i;
                  $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                  $conn->query($log_qry);
                  $mail = new PHPMailer(true);
                  send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
                }
              } catch (mysqli_sql_exception $e) {
                $message = "Data error in line# " . $i . ", Exception:" . $e->getMessage();
                $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
                $conn->query($log_qry);
                $mail = new PHPMailer(true);
                send_mail($mail, $tblName, $i, FROM_EMAIL_ADDRESS, SMTP_USERNAME, SMTP_PASSWORD, FROM_NAME);
              }
            }
          }
        }
        insertUpdateLogTable($conn, $tblName, $LagTableID);
        break;
    }
  }
  updateCron($conn, 0);
}

function emptyTable($conn, $tblName) {
  try {
    $qry = "TRUNCATE TABLE " . $tblName;
    $conn->query($qry);
  } catch (exception $e) {
    $message = "Table not found, Exception:" . $e->getMessage();
    $log_qry = "insert into InsertErrorLogs(table_name,message) values('" . $tblName . "','" . $message . "')";
    $conn->query($log_qry);
  }
}

function updateCron($conn, $status) {
  $qry = "UPDATE cron set SetTime=now(),status=$status";
  $conn->query($qry);
}

function insertUpdateLogTable($conn, $FileName, $LogTableID = '') {
  if (empty($LogTableID)) {
    $qry = "insert into LogTable(FileName,StartTime) values('" . $FileName . "',now())";
    $conn->query($qry);
    return $conn->insert_id;
  } else {
    $qry = "UPDATE LogTable set EndTime=now() where LogTableID=" . $LogTableID;
    $conn->query($qry);
  }
}

function clean_string($str) {
  $str = str_replace("�", "'", $str);
  $str = addslashes($str);
  return $str;
}

function send_mail($mail, $tblName, $i, $from_email_address, $smtp_username, $smtp_password, $from_name) {
  $subject = "Error in $tblName.csv";
  $body = "Data error/special characters in line# " . $i;
  try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();  //Send using SMTP
    $mail->Host = 'smtp.gmail.com';   //Set the SMTP server to send through
    $mail->SMTPAuth = true;  //Enable SMTP authentication
    $mail->Username = $smtp_username;   //SMTP username
    $mail->Password = $smtp_password; //SMTP password
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    //Recipients
    $mail->setFrom($from_email_address, $from_name);
    $mail->addAddress('eligruen@gmail.com');
    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
  } catch (Exception $e) {
    
  }
}
//echo 'Done';
//exit;
?>