<?
require_once("include/config.php");
require_once("include/sql.php");
require_once("include/mixpanel_core.php");

$sql = new sql($mixpanel);

$messages = Array();

if($_SERVER['REQUEST_METHOD'] == "POST") { // check if for is submitted
  if($_POST['email'] != "" && $_POST['password'] != "") {
    $auth = new mixpanel_auth($sql);
    if(!$auth->check_email($_POST['email'])) { // if the email does not exist...
      $user = new mixpanel_user($sql);
      if($user->create($_POST['email'], $_POST['password']))
	$messages['success'] = "Success! Your API key is $user->api_key";
      else
	$messages['error'] = "Sorry, there was an error during registration";
    } else {
      $messages['error'] = "That email is already in use";
    }
  } else {
    $messages['error'] = "Please fill out all of the required fields";
  }
}

include("views/header.php");
include("views/register.php");
?>
