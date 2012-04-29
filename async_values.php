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

$count = Array(); 

$times = Array(); // assoc array to keep track of junk

while(($value = $property->next_value()) != NULL) {
  if(!is_array($times[$value->text]))
    $times[$value->text] = Array();
  array_push($times[$value->text], $value->timestamp);
}

foreach($times as $text => $ary) {
  $line = "";
  $line .= "$text,";
  foreach($ary as $time) {
    $line .= "$time,";
  }

  echo substr($line, 0, -1); // trim last comma
  echo "\n";
}

?>