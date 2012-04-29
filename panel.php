<?
require_once('include/session.php');
require_once('include/sql.php');
require_once('include/config.php');
require_once('include/mixpanel_core.php');

check_session();

$events = Array();

$user->get_events();
while(($event = $user->next_event()) != NULL) {
  $events[] = $event;
}

if(isset($_GET['i'])) {
   $property_id = $_GET['i'];
   $sql = new sql($mixpanel);
   $property = new mixpanel_property($sql);
   if(!$property->load($property_id)) {
     unset($property_id);
   }
}

include('views/header.php');
include('views/panel.php');
?>