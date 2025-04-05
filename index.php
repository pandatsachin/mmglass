<?php
include("includes/config.php");
$msg = '';
if(isset($_SESSION['User']) && $_SESSION['User']['TechID'] != '') {
  header("Location:home.php");
}
if(isset($_POST['login'])) {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  $qry = "select * from TechTable where TechEmail='" . $username . "' AND TechPassword='" . $password . "' and Active=1";
  $result = $conn->query($qry);
  if($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $_SESSION['User']['TechID'] = $row['TechID'];
      $_SESSION['User']['TechEmail'] = $row['TechEmail'];
      $_SESSION['User']['TechName'] = $row['TechName'];
      $_SESSION['User']['TechPhone'] = $row['TechPhone'];
      $update_qry = "update TechTable set LastLoggedIn=now() where TechID=" . $row['TechID'];
      $conn->query($update_qry);
      header("Location:todayjobs.php");
    }
  } else {
    $msg = '<h5 class="h5 alert alert-danger">Tech Email/Tech Password combination is wrong</h5>';
  }
}
?>
<?php
include("includes/header.php");
?>
<body class="text-center">
  <form class="form-signin" name="login" action="" method="post">    
    <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
    <?php echo $msg; ?>
    <label for="username" class="sr-only">Tech Email</label>
    <input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
    <label for="password" class="sr-only">Tech Password</label>
    <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>    
    <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Sign in</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2021-2022</p>
  </form>
</body>
<?php
include("includes/footer.php");
?>