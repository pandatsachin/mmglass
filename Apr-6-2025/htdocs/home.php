<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
include("includes/pagination.php");
$condition = "SalesmanID=" . $_SESSION['User']['SalesmanID'];
$keyword = '';
if (isset($_POST['search'])) {
    $keyword = trim($_POST['keyword']);
    header("Location:home.php?keyword=" . $keyword);
}
if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
    $keyword = trim($_GET['keyword']);
    $condition .= " AND (CustCode LIKE '%" . $keyword . "%'";
    $condition .= " OR CustName LIKE '%" . $keyword . "%'";
    $condition .= " OR CustAddress LIKE '%" . $keyword . "%'";
    $condition .= " OR CustPhone LIKE '%" . $keyword . "%'";
    $condition .= " OR City LIKE '%" . $keyword . "%'";
    $condition .= " OR Fax LIKE '%" . $keyword . "%'";
    $condition .= " OR email LIKE '%" . $keyword . "%')";
}
$pages = new Paginator;
$pages->default_ipp = 15;
$cnt_qry = "SELECT * FROM WCustomerTable where $condition";
$sql_forms = $conn->query($cnt_qry);
$pages->items_total = $sql_forms->num_rows;
$pages->mid_range = 9;
$pages->paginate();
?>
<?php
include("includes/header.php");
?>
<body style="align-items: baseline;">
    <?php
    include("includes/userlinks.php");
    ?>
    <main role="main" class="container" style="margin-top: 35px;">
        <div class="clearfix"></div>        
        <div class="row">
            <div class="col-sm-12">
                <form name="search" method="post" action="" class="form-inline">
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="keyword" class="sr-only">Keyword</label>
                        <input value="<?php echo $keyword; ?>" type="text" class="form-control" name="keyword" id="keyword" placeholder="Keyword">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2" name="search">Search</button>
                </form>
            </div>
        </div>
        <!--<div class="row marginTop">
            <div class="col-sm-12 paddingLeft pagerfwt">
        <?php if ($pages->items_total > 0) { ?>
            <?php echo $pages->display_pages(); ?>
            <?php echo $pages->display_items_per_page(); ?>
            <?php echo $pages->display_jump_menu(); ?>
        <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>-->
        <div class="clearfix"></div>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="width: 16rem;">Actions</th>
                        <th scope="col">CustCode</th>
                        <th scope="col">CustName</th>
                        <th scope="col">CustAddress</th>
                        <th scope="col">CustPhone</th>
                        <th scope="col">City</th>
                        <th scope="col">State</th>
                        <th scope="col">Zip</th>
                        <th scope="col">Fax</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Email</th>
                        <th scope="col">CreditPolicy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select * from WCustomerTable where $condition $pages->limit";
                    $result = $conn->query($qry);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_object()) {
                            $invoice_link = "invoice.php?CustID=$row->CustID&CustCode=$row->CustCode";
                            $credit_link = "credit.php?CustID=$row->CustID&CustCode=$row->CustCode";
                            $usage_link = "usage.php?CustID=$row->CustID&CustCode=$row->CustCode";
                            $price_link = "price.php?CustID=$row->CustID&CustCode=$row->CustCode";
                            $payment_link = "payment.php?CustID=$row->CustID&CustCode=$row->CustCode&Balance=$row->Balance";
                            ?>
                            <tr>
                                <td style="padding-left: 0; padding-right: 0;">
                                    <a title="Invoices" class="btn btn-primary" href="<?php echo $invoice_link; ?>" role="button">I</a>
                                    <a title="Credits" class="btn btn-secondary" href="<?php echo $credit_link; ?>" role="button">C</a>
                                    <a title="Usage" class="btn btn-success" href="<?php echo $usage_link; ?>" role="button">U</a>
                                    <a title="Prices" class="btn btn-warning" href="<?php echo $price_link; ?>" role="button">P</a>
                                    <a title="Payments" class="btn btn-info" href="<?php echo $payment_link; ?>" role="button">PA</a>
                                </td>
                                <td><?php echo $row->CustCode; ?></td>
                                <td><?php echo $row->CustName; ?></td>
                                <td><?php echo $row->CustAddress; ?></td>
                                <td><?php echo $row->CustPhone; ?></td>
                                <td><?php echo $row->City; ?></td>                            
                                <td><?php echo $row->State; ?></td>
                                <td><?php echo $row->Zip; ?></td>
                                <td><?php echo $row->Fax; ?></td>
                                <td>$<?php echo $row->Balance; ?></td>
                                <td><?php echo $row->email; ?></td>
                                <td><?php echo $row->CreditPolicy; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr><td colspan="12">No data!!!</td></tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="row marginTop">
            <div class="col-sm-12 paddingLeft pagerfwt">
                <?php if ($pages->items_total > 0) { ?>
                    <?php echo $pages->display_pages(); ?>
                    <?php echo $pages->display_items_per_page(); ?>
                    <?php echo $pages->display_jump_menu(); ?>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </main>
</body>
<?php
include("includes/footer.php");
?>