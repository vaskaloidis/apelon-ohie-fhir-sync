<?php 

session_start();

// Dependencies
use Apelon\Sync\ihrisSync,
  Apelon\Sync\rmapSync,
  Apelon\Object\rmFacilityTypeConfig,
  Apelon\Util\Gui;
require __DIR__ . '/vendor/autoload.php';

// Config File
require './etc/config.values.php';
$url = $site_url . "index.php"; // URL

// User Interface
$gui = new Gui($url);

// Authenticated
$logged_in = false;
$apelon_user = false;
if(isset($_SESSION)) {
  if(in_array($_SESSION["user"], $apelon_sync_users) {
    $logged_in = true;
    $apelon_user = $_SESSION["user"];
  }
}
$gui->loadView('head');
$gui->loadCss('login');
$gui->loadView('navbar');
?>

<?php 
if(isset($_POST)) {
  if(isset($_POST['login'])) {
    if(isset($_POST['username']) && isset($_POST['password'])) {
      if(in_array($_POST['username'], $apelon_sync_users)) {
        if(md5($_POST['passsword']) == $apelon_sync_users[$_POST['username']]) {
          $gui->alertSuccess("You have logged in succesfully");
        } else {
          $gui->alert("You have entered an incorrect password. Login Failed.");
        }
      } else {
        $gui->alert("That username does not exist in our system. Login Failed.");
      }
    } else {
      $gui->alert("Please enter both a username and password");  
    }
  }
} else { ?>

  <form action ='login.php' method='POST' class="form-signin">
    <h2 class="form-signin-heading">Please sign in</h2>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
    <div class="checkbox">
      <label>
        <input type="checkbox" value="remember-me"> Remember me
      </label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  </form>

<?php } ?>