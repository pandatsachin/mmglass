<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
if (isset($_GET['InvoiceNumber']) && $_GET['InvoiceNumber'] != '') {
    $InvoiceNumber = trim($_GET['InvoiceNumber']);
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
                        <th scope="col">Amount Ordered</th>
                        <th scope="col">Amount Shipped</th>
                        <th scope="col">Back Ordered</th>
                        <th scope="col">Price</th>
                        <th scope="col">Extended Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select * from WInvoiceDetails where InvoiceNumber=$InvoiceNumber";
                    $result = $conn->query($qry);
                    if ($result->num_rows > 0) {
                        $amt_ordered = 0;
                        $amt_shipped = 0;
                        $back_ordered = 0;
                        //$price = 0;
                        while ($row = $result->fetch_object()) {
                            $amt_ordered += $row->AmountOrdered;
                            $amt_shipped += $row->AmountShipped;
                            $back_ordered += $row->BackOrdered;
                            //$price += $row->Price;
                            $dex_amt = number_format(($row->AmountShipped * $row->Price), 2);
                            $ex_amt += $dex_amt;
                            ?>
                            <tr>
                                <td><?php echo $row->Description; ?></td>
                                <td><?php echo $row->ColorName; ?></td>
                                <td><?php echo $row->Size; ?></td>
                                <td><?php echo $row->AmountOrdered; ?></td>
                                <td><?php echo $row->AmountShipped; ?></td>
                                <td><?php echo $row->BackOrdered; ?></td>                            
                                <td>$<?php echo $row->Price; ?></td>
                                <td>$<?php echo $dex_amt; ?></td>
                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td colspan="3"></td>
                            <td><b><?php echo $amt_ordered; ?></b></td>
                            <td><b><?php echo $amt_shipped; ?></b></td>
                            <td><b><?php echo $back_ordered; ?></b></td>
                            <td><!--<b>$<?php //echo $price; ?></b>--></td>
                            <td><b>$<?php echo $ex_amt; ?></b></td>
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