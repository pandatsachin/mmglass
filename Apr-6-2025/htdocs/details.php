<?php
include("includes/config.php");
if(!isset($_SESSION['User'])) {
  header("Location:index.php");
}
?>
<?php
$SalesmanID = $_SESSION['User']['SalesmanID'];
$ItemID = '';
$ItemName = '';
//Post temp form
if(isset($_POST) && $_POST['ItemDesc']) {
  $ItemDesc = trim($_POST['ItemDesc']);
  $ColorName = trim($_POST['ColorName']);
  $ColorNameArr = explode('>>>>', $ColorName);
  $Size = trim($_POST['Size']);
  $SizeArr = explode('>>>>', $Size);
  $Qty = trim($_POST['Qty']);
  $QtyArr = explode('>>>>', $Qty);
  $i = 0;
  foreach ($ColorNameArr as $CVal) {
    $SVal = $SizeArr[$i];
    $QVal = $QtyArr[$i];
    if(!empty($ItemDesc) && !empty($CVal) && !empty($SVal) && !empty($QVal)) {
      $insert_qry = "insert into TempOrder(ItemDesc,ColorName,Size,Qty,SalesmanID)"
          . " values('" . $ItemDesc . "','" . $CVal . "','" . $SVal . "','" . $QVal . "','" . $SalesmanID . "')";
      $conn->query($insert_qry);
    }
    $i++;
  }
}
if(isset($_GET['ItemID']) && $_GET['ItemID'] != '') {
  $ItemID = trim($_GET['ItemID']);
  $ItemName = trim($_GET['ItemName']);
}
include("includes/header.php");
$option_str = '<option value="">Search</option>';
$qry = "select * from ItemTable order by Description";
$result = $conn->query($qry);
if($result->num_rows > 0) {
  while ($row = $result->fetch_object()) {
    $selected = ($ItemName == $row->Description) ? 'selected' : '';
    $option_str .= '<option value="' . $row->ItemID . '" ' . $selected . '>' . $row->Description . '</option>';
  }
  $qry = "select * from ItemTable order by Description";
  $result = $conn->query($qry);
  while ($row = $result->fetch_object()) {
    $selected = ($ItemName == $row->ItemCode) ? 'selected' : '';
    $option_str .= '<option value="' . $row->ItemID . '" ' . $selected . '>' . $row->ItemCode . '</option>';
  }
}
?>
<link href="chosen/prism.css" rel="stylesheet">
<link href="chosen/chosen.css" rel="stylesheet">
<body style="align-items: baseline;">
  <?php
  include("includes/userlinks.php");
  ?>
  <main role="main" class="container" style="margin-top: 35px; padding-bottom: 25px;">
    <div class="clearfix"></div>        
    <div class="row">
      <div class="col-sm-12">
        <form name="search" method="post" action="" class="form-inline">
          <div class="form-group mx-sm-3 mb-2">
            <label for="search" class="sr-only">Search</label>                        
            <select class="form-control chosen-select" name="search" id="search" placeholder="type to search...">
              <?php echo $option_str; ?>
            </select>
          </div>                    
        </form>
      </div>
    </div>    
    <?php
    if($ItemID != '') {
      $img_qry = "select IC.imageFile,CT.ColorName from ItemColors IC,ColorTable CT "
          . " where IC.ColorID=CT.ColorID and ItemID=" . $ItemID . " order by CT.ColorName";
      $img_res = $conn->query($img_qry);
      ?>
      <div class="row" style="margin-top: 25px;">
        <div class="col">
          <?php
          if($img_res->num_rows > 0) {
            $first_img = $img_res->fetch_object();
            ?>
            <div class="row">
              <div class="col"><img class="img-fluid" src="Pictures/<?php echo $first_img->imageFile; ?>"></div>
            </div>                        
            <div class="row" style="margin-top: 10px;">
              <?php
              $img_resall = $conn->query($img_qry);
              while ($img_row = $img_resall->fetch_object()) {
                ?>
                <div class="col">
                  <img style="cursor: pointer;" class="img-thumbnail" src="Pictures/<?php echo $img_row->imageFile; ?>">
                  <div class="clearfix"></div>
                  <div style="text-align: center;"><?php echo $img_row->ColorName; ?></div>
                </div>
                <?php
              }
              ?>
            </div>
            <?php
          }
          ?>
        </div>
        <div class="col">
          <div class="row">
            <div class="col">
              <button type="submit" class="btn btn-primary" id="previous" title="Previous"><</button>
            </div>
            <div class="col">
              <button type="submit" class="btn btn-primary" id="next" title="Next">></button>
            </div>
          </div>
          <div class="row"><h2><?php echo $ItemName; ?></h2></div>
          <div class="row">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" id="gridtable">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">COLOR</th>
                    <th scope="col">SIZE</th>
                    <th scope="col">A</th>
                    <th scope="col">B</th>
                    <th scope="col">ED</th>
                    <th scope="col">CIRC</th>
                    <th scope="col">UPC</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $data_qry = "select * from SalesForceGrid where ItemID=" . $ItemID . " order by ColorName";
                  $data_res = $conn->query($data_qry);
                  if($data_res->num_rows > 0) {
                    while ($data_row = $data_res->fetch_object()) {
                      ?>
                      <tr>
                        <td><?php echo $data_row->ColorName; ?></td>
                        <td><?php echo $data_row->Size; ?></td>
                        <td><?php echo ($data_row->A == 0.00 ? '' : $data_row->A); ?></td>
                        <td><?php echo ($data_row->B == 0.00 ? '' : $data_row->B); ?></td>
                        <td><?php echo ($data_row->ED == 0.00 ? '' : $data_row->ED); ?></td>
                        <td><?php echo ($data_row->Circ == 0.00 ? '' : $data_row->Circ); ?></td>
                        <td><?php echo $data_row->Barcode; ?></td>
                        <td><input type="text" name="qty" size="3"></td>
                        <td><a title="Each Size and Color" class="btn btn-info add-each-frame-image" href="#" role="button">+</a></td>
                        <!--<td><button type="submit" class="btn btn-primary addorder" name="submitorder">Order</button></td>-->
                        <!--<td><a href="order.php?ItemID=<?php echo $ItemID ?>&Color=<?php echo $data_row->ColorName ?>&Size=<?php echo $data_row->Size ?>">Order</a></td>-->
                      </tr>
                      <?php
                    }
                  }
                  ?>
                </tbody>
              </table>
              <button type="submit" class="btn btn-primary addorder" name="submitorder">Add to cart</button>
            </div>
          </div>
        </div>
      </div>
      <?php
    }
    ?>
    <div class="row"><h2>Items added in cart</h2></div>
    <div class="row">
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead class="thead-light">
            <tr>              
              <th scope="col">FRAME/STYLE</th>
              <th scope="col">COLOR</th>
              <th scope="col">SIZE</th>
              <th scope="col">Qty</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $temp_items = 0;
            $data_qry = "select * from TempOrder where SalesmanID=" . $SalesmanID . " order by TempOrderID";
            $data_res = $conn->query($data_qry);
            if($data_res->num_rows > 0) {
              while ($data_row = $data_res->fetch_object()) {
                $temp_items++;
                ?>
                <tr>                  
                  <td><?php echo $data_row->ItemDesc; ?></td>
                  <td><?php echo $data_row->ColorName; ?></td>
                  <td><?php echo $data_row->Size; ?></td>
                  <td><?php echo $data_row->Qty; ?></td>
                  <td><a TempOrderID="<?php echo $data_row->TempOrderID; ?>" title="Delete" class="btn btn-danger del-item" href="#" role="button">X</a></td>
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
        <?php if($temp_items > 0) { ?>
          <a class="btn btn-primary" href="order.php" role="button">Place Order</a>
          <?php
        }
        ?>
      </div>
    </div>  
    <form name="tempform" id="tempform" action="" method="post">
      <input type="hidden" name="ItemDesc" id="ItemDesc">
      <input type="hidden" name="ColorName" id="ColorName">
      <input type="hidden" name="Size" id="Size">
      <input type="hidden" name="Qty" id="Qty">
    </form>
  </main>
</body>
<?php
include("includes/footer.php");
?>
<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="chosen/prism.js" type="text/javascript" charset="utf-8"></script>
<script src="chosen/init.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
  $('#search').on('change', function () {
    var ItemID = $(this).val();
    var ItemName = $('#search option:selected').text();
    window.location.href = 'details.php?ItemID=' + ItemID + '&ItemName=' + ItemName;
  });
  $('#previous').on('click', function () {
    var ItemID = $('#search option:selected').prev().val();
    var ItemName = $('#search option:selected').prev().text();
    if (ItemID != '') {
      window.location.href = 'details.php?ItemID=' + ItemID + '&ItemName=' + ItemName;
    } else {
      alert('You are on first item!');
    }
  });
  $('#next').on('click', function () {
    var ItemID = $('#search option:selected').next().val();
    var ItemName = $('#search option:selected').next().text();
    if (ItemID != '') {
      window.location.href = 'details.php?ItemID=' + ItemID + '&ItemName=' + ItemName;
    } else {
      alert('You are on last item!');
    }
  });
  $('.img-thumbnail').on('click', function () {
    var tsrc = $(this).attr('src');
    $(".img-fluid")
            .fadeOut(400, function () {
              $(".img-fluid").attr('src', tsrc);
            })
            .fadeIn(400);
  });
  $('.addorder').on('click', function (e) {
    e.preventDefault();
    var ItemName = $('#search option:selected').text();
    var ColorArr = [];
    var SizeArr = [];
    var QtyArr = [];
    $('#gridtable tbody tr').each((tr_idx, tr) => {
      var ColorName = '';
      var Size = '';
      $(tr).children('td').each((td_idx, td) => {
        //console.log('[' + tr_idx + ',' + td_idx + '] => ' + $(td).text());
        if (td_idx == 0) {
          ColorName = $(td).text();
        }
        if (td_idx == 1) {
          Size = $(td).text();
        }
        if (td_idx == 7) {
          var Qty = $(td).find('input').val();
          if (Qty != '' && Qty != 0) {
            QtyArr.push(Qty);
            ColorArr.push(ColorName);
            SizeArr.push(Size);
          }
        }
      });
    });
    if (QtyArr.length == 0) {
      alert("Please enter quantity for the item you want to add in cart!");
    } else {
      var CStr = ColorArr.join(">>>>");
      var SStr = SizeArr.join(">>>>");
      var QStr = QtyArr.join(">>>>");
      $('#ItemDesc').val(ItemName);
      $('#ColorName').val(CStr);
      $('#Size').val(SStr);
      $('#Qty').val(QStr);
      $('#tempform').submit();
    }
    /*var btn = $(this).closest('td');
     var ColorName = btn.prev('td').prev('td').prev('td').prev('td').prev('td').prev('td').prev('td').prev('td').text();
     var Size = btn.prev('td').prev('td').prev('td').prev('td').prev('td').prev('td').prev('td').text();
     var Qty = btn.prev('td').find('input').val();
     if (Qty == '' || Qty == 0) {
     alert("Please enter quantity");
     } else {
     $('#ItemDesc').val(ItemName);
     $('#ColorName').val(ColorName);
     $('#Size').val(Size);
     $('#Qty').val(Qty);
     $('#tempform').submit();
     }*/
  });
  $('.add-each-frame-image').on('click', function (e) {
    e.preventDefault();
    var qty = $(this).closest('td').prev('td').find("input").val();
    $('#gridtable tbody tr').each((tr_idx, tr) => {
      $(tr).children('td').each((td_idx, td) => {
        if (td_idx == 7) {
          $(td).find('input').val(qty);
        }
      });
    });
  });
  $('.del-item').on('click', function (e) {
    e.preventDefault();
    var TempOrderID = $(this).attr('TempOrderID');
    $(this).closest('tr').remove();
    $.ajax({
      type: "POST",
      url: "ajax.php",
      dataType: "JSON",
      data: {TempOrderID: TempOrderID},
      success: function (data) {
        if (data.msg == 'success') {
          //$(this).closest('tr').remove();
          alert("Item has been removed from the cart!!!");
        }
      }
    });
  });
</script>