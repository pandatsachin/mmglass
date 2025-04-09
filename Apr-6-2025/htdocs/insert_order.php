<?php
include("includes/config.php");

if(isset($_POST['submitorder'])) {
  $orderid = trim($_POST['orderid']);
  $frame = trim($_POST['frame']);
  $qry = "select ItemID from ItemTable where Description='" . $frame . "' OR ItemCode='" . $frame . "'";
  $res = $conn->query($qry);
  $data = $res->fetch_object();
  $itemid = $data->ItemID;
  $color = trim($_POST['color']);
  $qry = "select ColorID from ColorTable where ColorName='" . $color . "'";
  $res = $conn->query($qry);
  $data = $res->fetch_object();
  $colorid = $data->ColorID;
  $size = trim($_POST['size']);
  $qry = "select SizeID from SizeTable where Size='" . $size . "'";
  $res = $conn->query($qry);
  $data = $res->fetch_object();
  $sizeid = $data->SizeID;
  $qty = trim($_POST['qty']);
  if($itemid == '' || $sizeid == '' || $colorid == '') {
    echo "something is wrong, try again!!!";
    exit;
  } else {
    $iqry = "insert into OrderDetails(OrderID,ItemID,ColorID,SizeID,Qty) values('" . $orderid . "','" . $itemid . "','" . $colorid . "','" . $sizeid . "','" . $qty . "')";
    $conn->query($iqry);
    echo "data inserted";
  }
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
    <form name="form1" method="post" action="" class="form1">
      <div class="form-row">
        <div class="col">
          <input value="396" name="orderid" type="text" class="form-control" placeholder="Order ID">
          <input value="" name="frame" type="text" class="form-control" placeholder="Frame">
          <input value="" name="color" type="text" class="form-control" placeholder="Color">
          <input value="" name="size" type="text" class="form-control" placeholder="Size">
          <input value="1" name="qty" type="text" class="form-control" placeholder="Qty">
        </div>        
      </div>
      <button type="submit" class="btn btn-primary" name="submitorder">Submit Order</button>
    </form>
  </main>
</body>
<?php
include("includes/footer.php");
?>