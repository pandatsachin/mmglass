<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
if (isset($_GET['CustID']) && $_GET['CustID'] != '') {
    $CustID = trim($_GET['CustID']);
    $CustCode = trim($_GET['CustCode']);
    $Balance = trim($_GET['Balance']);
}
?>
<?php
include("includes/header.php");
?>
<body style="align-items: baseline;">
    <?php
    include("includes/userlinks.php");
    ?>
    <main role="main" class="container" style="margin-top: 35px;"> 
        <div class="row"><div class="col-sm-12"><h2>Customer: <?php echo $CustCode; ?></h2></div></div>
        <div class="row"><div class="col-sm-12"><h2>Balance: <?php echo $Balance; ?></h2></div></div>
        <div class="clearfix"></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">                
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Type</th>
                        <th scope="col">Check Number</th>                        
                        <th scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select * from WPaymentsTable where CustID=$CustID ORDER BY date DESC";
                    $result = $conn->query($qry);
                    if ($result->num_rows > 0) {
                        $tot_amt = 0;
                        while ($row = $result->fetch_object()) {
                            $tot_amt += $row->SumOfAmount;
                            ?>
                            <tr>
                                <td><?php echo date(CUSTOM_DATE_FORMAT, $row->date); ?></td>
                                <td><?php echo $row->type; ?></td>
                                <td><?php echo $row->CheckNumber; ?></td>
                                <td>$<?php echo $row->SumOfAmount; ?></td>
                            </tr>
                        <?php }
                        ?>
                        <tr><td colspan="3"></td><td><b>$<?php echo $tot_amt; ?></b></td></tr>
                        <?php
                    } else {
                        ?>
                        <tr><td colspan="4">No data!!!</td></tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
<?php
include("includes/footer.php");
?>