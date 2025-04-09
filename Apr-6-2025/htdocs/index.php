<?php
include("includes/config.php");
$msg = '';
if (isset($_SESSION['User']) && $_SESSION['User']['SalesmanID'] != '') {
    header("Location:home.php");
}
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $qry = "select * from WSalesmanTable where Name='" . $username . "' AND SalesmanCode='" . $password . "'";
    $result = $conn->query($qry);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $_SESSION['User']['SalesmanID'] = trim($row['SalesmanID']);
            $_SESSION['User']['Name'] = trim($row['Name']);
            $_SESSION['User']['SalesmanCode'] = trim($row['SalesmanCode']);
            $_SESSION['User']['SalesmanEmail'] = trim($row['SalesmanEmail']);
            header("Location:home.php");
        }
    } else {
        $msg = '<h5 class="h5 alert alert-danger">Username/Password combination is wrong</h5>';
    }
}
?>
<?php
include("includes/header.php");
?>
<body class="text-center">
    <form class="form-signin" name="login" action="" method="post">
        <!--<img class="mb-4" src="qb.gif" alt="" width="72" height="72">-->
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <?php echo $msg; ?>
        <label for="username" class="sr-only">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
        <label for="password" class="sr-only">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>    
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Sign in</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2021-2022</p>
    </form>
</body>
<?php
include("includes/footer.php");
?>