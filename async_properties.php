<?
if(!isset($_GET['i'])) exit();

require_once('include/mixpanel_core.php');
require_once('include/sql.php');
require_once('include/config.php');

$event_id = $_GET['i'];

$sql = new sql($mixpanel);

$event = new mixpanel_event($sql);
if($event->load($event_id) == False) {
  echo "Error";
  exit();
}
  
$event->get_properties();
#print_r($event);

$html = "<ul class=\"horiz\">";

while(($property = $event->next_property()) != NULL) {
  $html .= "<li><a href=\"panel.php?i=".$property->id."\">";
  $html .= $property->name;
  $html .= "</a></li> ";
}

$html .= "</ul>";

echo $html;
?>