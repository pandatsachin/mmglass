<?php
include("includes/config.php");
if (!isset($_SESSION['User'])) {
    header("Location:index.php");
}
include("includes/header.php");
?>

<body style="align-items: baseline;">
    <?php
//    include("includes/userlinks.php");
      if (isset($_POST['save'])) {
        $name = $_POST['name'];
        $date = $_POST['date'];
        $address = $_POST['address'];
        $apt = $_POST['apt'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip = $_POST['zip'];
        $technician = $_POST['technician'];
        $front_door = $_POST['front_door'];
        $lami = $_POST['lami'];
        $Plexi = $_POST['Plexi'];
        $pw = $_POST['pw'];
        $size = $_POST['size'];
        $x = $_POST['x'];
        $vest_door = $_POST['vest_door'];
        $notes = $_POST['notes'];
        $side_light = $_POST['side_light'];
        $kick_panel = $_POST['kick_panel'];
        $hallway = $_POST['hallway'];
        $floor = $_POST['floor'];
        $pw_1 = $_POST['pw_1'];
        $rw = $_POST['rw'];
        $cg = $_POST['cg'];
        $size_1 = $_POST['size_1'];
        $skylight = $_POST['skylight'];
        $x_1 = $_POST['x_1'];
        $notes_1 = $_POST['notes_1'];
        $kit = $_POST['kit'];
        $br_1 = $_POST['br_1'];
        $top = $_POST['top'];
        $bath = $_POST['bath'];
        $lr = $_POST['lr'];
        $bottom = $_POST['bottom'];
        $clear_glass = $_POST['clear_glass'];
        $size_2 = $_POST['size_2'];
        $x_2 = $_POST['x_2'];
        $iu = $_POST['iu'];
        $locks = $_POST['locks'];
        $shoes = $_POST['shoes'];
        $tl = $_POST['tl'];
        $pivot = $_POST['pivot'];
        $caps = $_POST['caps'];
        $rb = $_POST['rb'];
        $bb = $_POST['bb'];
        $notes_2 = $_POST['notes_2'];
            echo $name . '<br>' . $notes_2 . '<br>' . $bb . '<br>' . $date . '<br>' . $medicaid_id_already . '<br>' . $medicaid_id_number . '<br>' . $married_status;
//echo $last_name;
//echo $date;
        exit();
    }
    ?>
    <main role="main" class="container jobs_page" style="margin-top: 35px;">
        <h1 style="text-align: center;">Job Form</h1>

        <form name="" method="post" action="" class="job_form" id="job_form">
            <div class="form-group row">
                <div class="col-sm-2">Name</div>
                <div class="col-sm-4"> <input type="text" name="name" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-2"> <input value="" name="date" type="date" id="date" class="form-control"> </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2">Address</div>
                <div class="col-sm-4"> <input type="text" name="address" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-2">APT#</div>
                <div class="col-sm-2"><input type="text" name="apt" class="form-control" value="" placeholder=""></div>
            </div>
            <div class="form-group row">
                <div class="col-sm-1">City</div>
                <div class="col-sm-2"> <input type="text" name="city" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">State</div>
                <div class="col-sm-2"><input type="text" name="state" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">Zip</div>
                <div class="col-sm-2"> <input type="text" name="zip" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">Technician</div>
                <div class="col-sm-2"><input type="text" name="technician" class="form-control" value="" placeholder=""></div>
            </div>
            <hr style=" background-color: black;">
            <div class="form-group row">
                <div class="col-sm-2"><input type="checkbox" name="front_door" value="">
                    <label for="">Front Door</label>
                </div>
                <div class="col-sm-1">Lami</div>
                <div class="col-sm-1"><input type="text" name="lami" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">Plexi</div>
                <div class="col-sm-1"><input type="text" name="Plexi" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">PW</div>
                <div class="col-sm-1"><input type="text" name="pw" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">Size</div>
                <div class="col-sm-1"><input type="text" name="size" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">X</div>
                <div class="col-sm-1"><input type="text" name="x" class="form-control" value="" placeholder=""></div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2"><input type="checkbox" name="vest_door" value="">
                    <label for="">Vest. Door</label>
                </div>
                <div class="col-sm-1">Notes</div>
                <div class="col-sm-5"><input type="text" name="notes" class="form-control" value="" placeholder=""></div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2"><input type="checkbox" name="side_light" value="">
                    <label for="">Side Light</label>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2"><input type="checkbox" name="kick_panel" value="">
                    <label for="">Kick Panel</label>
                </div>
            </div>
            <hr style=" background-color: black;">

            <div class="form-group row">
                <div class="col-sm-2"><input type="checkbox" name="hallway" value="">
                    <label for="">Hallway</label>
                </div>
                <div class="col-sm-1">Floor</div>
                <div class="col-sm-1"><input type="text" name="floor" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">PW</div>
                <div class="col-sm-1"><input type="text" name="pw_1" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">RW</div>
                <div class="col-sm-1"><input type="text" name="rw" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">CG</div>
                <div class="col-sm-1"><input type="text" name="cg" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">Size</div>
                <div class="col-sm-1"><input type="text" name="size_1" class="form-control" value="" placeholder=""></div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2"><input type="checkbox" name="skylight" value="">
                    <label for="">Skylight</label>
                </div>
                <div class="col-sm-1">X</div>
                <div class="col-sm-1"><input type="text" name="x_1" class="form-control" value="" placeholder=""></div>
                <div class="col-sm-1">Notes</div>
                <div class="col-sm-4"><input type="text" name="notes_1" class="form-control" value="" placeholder=""></div>
            </div>
            <hr style=" background-color: black;">
            <div class="form-group row">
                <div class="col-sm-4">
                    <div class="form-group row">
                        <div class="col-sm-4"><input type="checkbox" name="kit" value="">
                            <label for="">KIT</label>
                        </div>
                        <div class="col-sm-4"><input type="checkbox" name="br_1" value="">
                            <label for="">BR</label>
                        </div>
                        <div class="col-sm-4"><input type="checkbox" name="top" value="">
                            <label for="">TOP</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4"><input type="checkbox" name="bath" value="">
                            <label for="">BATH</label>
                        </div>
                        <div class="col-sm-4"><input type="checkbox" name="lr" value="">
                            <label for="">LR</label>
                        </div>
                        <div class="col-sm-4"><input type="checkbox" name="bottom" value="">
                            <label for="">Bottom</label>
                        </div>
                    </div>
                    
                </div>
                <div class="col-sm-8">
                    <div class="form-group row">
                        <div class="col-sm-1">Clear Glass</div>
                        <div class="col-sm-2"><input type="text" name="clear_glass" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">Size</div>
                        <div class="col-sm-2"><input type="text" name="size_2" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">X</div>
                        <div class="col-sm-2"><input type="text" name="x_2" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">IU</div>
                        <div class="col-sm-2"><input type="text" name="iu" class="form-control" value="" placeholder=""></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-1">Locks</div>
                        <div class="col-sm-2"><input type="text" name="locks" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">Shoes</div>
                        <div class="col-sm-2"><input type="text" name="shoes" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">TL</div>
                        <div class="col-sm-2"><input type="text" name="tl" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">Pivot</div>
                        <div class="col-sm-2"><input type="text" name="pivot" class="form-control" value="" placeholder=""></div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-1">Caps</div>
                        <div class="col-sm-2"><input type="text" name="caps" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">RB</div>
                        <div class="col-sm-2"><input type="text" name="rb" class="form-control" value="" placeholder=""></div>
                        <div class="col-sm-1">BB</div>
                        <div class="col-sm-2"><input type="text" name="bb" class="form-control" value="" placeholder=""></div>
                    </div>  
                    <div class="form-group row">
                        <div class="col-sm-1">Notes</div>
                        <div class="col-sm-5"><input type="text" name="notes_2" class="form-control" value="" placeholder=""></div>
                    </div>  
                </div>
            </div>
            <hr style=" background-color: black;">
            <button type="submit" name="save" class="btn btn-primary">save</button>   
        </form>
    </main>
</body>
<?php
include("includes/footer.php");
?>