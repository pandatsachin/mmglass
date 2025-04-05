<?php

ini_set('display_errors', 1);
echo $request = file_get_contents('Json.txt');
$data = json_decode($request);
echo '<pre>';
print_r($data);
echo "Done";
exit;
?>