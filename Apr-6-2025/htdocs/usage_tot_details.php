<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
if (isset($_GET['CustID']) && $_GET['CustID'] != '') {
    $CustID = trim($_GET['CustID']);
    $Description = trim($_GET['Description']);
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
                        <th scope="col">Usage</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select SUM(`usage`) as tot,Description,ColorName,Size from WUsageDetails where custid='" . $CustID . "' AND Description='" . $Description . "' GROUP BY Description,ColorName,Size ORDER BY ColorName,Size";
                    $result = $conn->query($qry);
                    $tot_usage = 0;
                    if ($result->num_rows > 0) {
                        $key = '';
                        $key_tot = 0;
                        while ($row = $result->fetch_object()) {
                            ?>
                            <tr>                                
                                <td><?php echo $row->Description; ?></td>                                
                                <td><?php echo $row->ColorName; ?></td>                                
                                <td><?php echo $row->Size; ?></td>                               
                                <td><?php echo $row->tot; ?></td>                                
                            </tr>
                            <?php
                        }
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