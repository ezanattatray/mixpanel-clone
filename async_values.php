<?
if(!isset($_GET['i'])) exit();

require_once('include/mixpanel_core.php');
require_once('include/sql.php');
require_once('include/config.php');

$property_id = $_GET['i'];

$sql = new sql($mixpanel);

$property = new mixpanel_property($sql);
if($property->load($property_id) == False) {
  echo "Error";
  exit();
}

$property->get_values();

$last_value = "";

while(($value = $property->next_value()) != NULL) {
  if($last_text != $value->text) {
    echo "\n" . $value->text;
  }

  echo ",".$value->timestamp;

  $last_text = $value->text;
}

?>