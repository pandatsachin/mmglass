<?php
include("includes/config.php");

$status_msg = '';
$disabled = false;
if (isset($_POST['import'])) {
    $qry = "UPDATE cron set SetTime=now(),status=1 WHERE id=1";
    $conn->query($qry);
}
$qry = "select * from cron";
$result = $conn->query($qry);
$row = $result->fetch_object();
if ($row->status == 1) {
    $status_msg = 'Import process is set!!!';
    $disabled = true;
} else if ($row->status == 2) {
    $status_msg = 'Import process is running!!!';
    $disabled = true;
}
?>
<?php
include("includes/header.php");
?>
<body class="text-center">
    <form class="form-signin" name="login" action="" method="post">
        <!--<img class="mb-4" src="qb.gif" alt="" width="72" height="72">-->
        <h1 class="h3 mb-3 font-weight-normal"><?php echo $status_msg; ?></h1>            
        <button <?php echo ($disabled) ? "disabled='disabled'" : ''; ?> class="btn btn-lg btn-primary btn-block" type="submit" name="import">Import CSVs</button>
    </form>
</body>
<?php
include("includes/footer.php");
?>