<?php

include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
if (isset($_GET['CollectionID']) && $_GET['CollectionID'] != '') {
    $CollectionID = trim($_GET['CollectionID']);
    $CustID = trim($_GET['CustID']);
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = "select Description from WCustHasNot where CollectionID=$CollectionID AND CustID=$CustID";
                    $result = $conn->query($qry);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_object()) {                            
                            ?>
                            <tr>                                
                                <td><?php echo $row->Description; ?></td>                                
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr><td>No data!!!</td></tr>
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