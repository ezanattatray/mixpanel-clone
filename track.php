<?
require_once("include/config.php");
require_once("include/sql.php");
require_once("include/mixpanel_core.php");

if(!isset($_GET['data'])) exit();

$data = $_GET['data'];

$json = json_decode(base64_decode($_GET['data']), true);

if($json == NULL) {
  echo "Malformed request"; // TODO: make this json
  exit();
}

$sql = new sql($mixpanel);

$user = new mixpanel_user($sql);

$token      = $json['properties']['token'];
$event_name = $json['event'];

if(!$user->load_by_api_key($token)) {
  echo "Invalid api key"; // TODO: make this json
  exit();
}

$event = new mixpanel_event($sql);
if(!$event->load_or_create($user, $event_name)) {
  echo "Server error";
  exit();
}

foreach($json['properties'] as $property_name => $value_text) {
  if($property_name == "token") break;

  $property = new mixpanel_property($sql);
  if($property->load_or_create($event, $property_name)) {
    //echo $event->id;
    $property_value = new mixpanel_value($sql);
    $property_value->create($property, $value_text);
  }
}

echo "Done"; // TODO: make this json
exit();
?>
