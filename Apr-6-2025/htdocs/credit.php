<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
if (isset($_GET['CustID']) && $_GET['CustID'] != '') {
    $CustID = trim($_GET['CustID']);
    $CustCode = trim($_GET['CustCode']);
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
        <div class="row"><div class="col-sm-12"><h2><?php echo $CustCode; ?></h2></div></div>
        <div class="clearfix"></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">                
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Actions</th>
                        <th scope="col">Credit ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Freight</th>
                        <th scope="col">Original Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select * from WCreditTable where CustID=$CustID ORDER BY Date DESC";
                    $result = $conn->query($qry);
                    if ($result->num_rows > 0) {
                        $fr_amt = 0;
                        $org_amt = 0;
                        while ($row = $result->fetch_object()) {
                            $credit_details = "credit_details.php?CreditID=$row->CreditID";
                            $fr_amt += $row->freight;
                            $org_amt += $row->OriginalAmount;
                            ?>
                            <tr>
                                <td><a class="btn btn-primary" href="<?php echo $credit_details; ?>" role="button">Details</a></td>                                
                                <td><?php echo $row->CreditID; ?></td>
                                <td><?php echo date(CUSTOM_DATE_FORMAT, $row->Date); ?></td>
                                <td>$<?php echo $row->freight; ?></td>
                                <td>$<?php echo $row->OriginalAmount; ?></td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td colspan="3"></td>
                            <td><b>$<?php echo $fr_amt; ?></b></td>
                            <td><b>$<?php echo $org_amt; ?></b></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr><td colspan="5">No data!!!</td></tr>
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