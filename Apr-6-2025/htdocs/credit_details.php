<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
$CreditID = 0;
if (isset($_GET['CreditID']) && $_GET['CreditID'] != '') {
    $CreditID = trim($_GET['CreditID']);
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
        <div class="table-responsive">
            <table class="table table-striped table-bordered">                
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Description</th>
                        <th scope="col">Color Name</th>
                        <th scope="col">Size</th>
                        <th scope="col">Amount Returned</th>
                        <th scope="col">Amount In</th>
                        <th scope="col">Amount Out</th>
                        <th scope="col">Price</th>
                        <th scope="col">Extended Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select * from WCreditDetails where CreditID=$CreditID";
                    $result = $conn->query($qry);
                    if ($result->num_rows > 0) {
                        $amt_return = 0;
                        $amt_in = 0;
                        $amt_out = 0;
                        $price = 0;
                        $ex_price = 0;
                        while ($row = $result->fetch_object()) {
                            $amt_return += $row->AmountReturned;
                            $amt_in += $row->AmountIn;
                            $amt_out += $row->AmountOut;
                            $price += $row->Price;
                            $ex_price += $row->ExtendedPrice;
                            ?>
                            <tr>
                                <td><?php echo $row->Description; ?></td>
                                <td><?php echo $row->ColorName; ?></td>
                                <td><?php echo $row->Size; ?></td>
                                <td>$<?php echo $row->AmountReturned; ?></td>
                                <td>$<?php echo $row->AmountIn; ?></td>
                                <td>$<?php echo $row->AmountOut; ?></td>                            
                                <td>$<?php echo $row->Price; ?></td>
                                <td>$<?php echo $row->ExtendedPrice; ?></td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td colspan="3"></td>
                            <td><b><?php echo $amt_return; ?></b></td>
                            <td><b><?php echo $amt_in; ?></b></td>
                            <td><b><?php echo $amt_out; ?></b></td>
                            <td><b>$<?php echo $price; ?></b></td>
                            <td><b>$<?php echo $ex_price; ?></b></td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr><td colspan="8">No data!!!</td></tr>
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