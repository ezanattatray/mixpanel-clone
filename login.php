<?
require_once("include/config.php");
require_once("include/sql.php");
require_once("include/mixpanel_core.php");
require_once("include/session.php");

if(is_logged_in()) {
  header('Location: panel.php');
  exit();
}

$sql = new sql($mixpanel);

$messages = Array();

if($_SERVER['REQUEST_METHOD'] == "POST") { // check if for is submitted
  if($_POST['email'] != "" && $_POST['password'] != "") {
    $auth = new mixpanel_auth($sql);
    if(!$auth->check_email($_POST['email'])) {
      $messages['error'] = "There is no account with that email address";
    } else {
      $login = $auth->login($_POST['email'], $_POST['password']);
      if($login !== False) { // check the type too ;)
	session_start();
	$_SESSION['uid'] = $login;
	header('Location: panel.php');
      } else {
	$messages['error'] = "Login failed!";
      }
    }
  } else {
    $messages['error'] = "Please fill out all of the required fields";
  }
}

include("views/header.php");
include('views/login.php');
?>
