<?
require_once("include/config.php");
require_once("include/sql.php");
require_once("include/mixpanel_core.php");

session_start();

if(isset($_SESSION["uid"])) {
  $sql = new sql($mixpanel);
  $user = new mixpanel_user($sql);
  $user->load($_SESSION["uid"]);
}

function check_session()
{
  if(!isset($_SESSION['uid'])) {
    header('Location: login.php');
    exit();
  }
}

function is_logged_in()
{
  return isset($_SESSION['uid']);
}

function logout()
{
  unset($_SESSION['uid']);
}

?>
