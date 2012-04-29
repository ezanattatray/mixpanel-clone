
<?php
class sql {
  // a class for handling unparameterized sql queries 
  var $config, $dblink, $dbuser, $dbpass, $dbname, $dbhost;
  var $a_rows, $rows, $result, $data;
  var $r_id;
 
  function __construct($conf) {
    $this->config = $conf;
    $this->dbuser = $conf['dbuser'];
    $this->dbpass = $conf['dbpass'];
    $this->dbname = $conf['dbname'];
 
    $this->dblink = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpass) or die("Can not connect: " . mysql_error());
    @mysql_select_db($this->dbname, $this->dblink) or die("Can not select database: " . mysql_error());
  }

  // query and fetch a single field
  public function query_item($query) {
    $this->result = @mysql_query($query, $this->dblink);
    $this->data   = @mysql_fetch_row($this->result);
    $this->rows   = @mysql_num_rows($this->result);
    echo @mysql_error($this->dblink);
    return($this->data[0]);
  }

  // query and fetch a row
  public function query_row($query)
  {
    $this->result = @mysql_query($query, $this->dblink);
    $this->data   = @mysql_fetch_assoc($this->result);
    $this->rows   = @mysql_num_rows($this->result);
    return($this->data);
  }
 
  // perform a sql query
  public function sql_query($query)
  {
    $this->result = @mysql_query($query, $this->dblink);
    $this->rows   = @mysql_num_rows($this->result);
    echo @mysql_error($this->dblink);
  }

  // fetch next row
  public function next_row()
  {
    $this->data = @mysql_fetch_assoc($this->result);
    return($this->data);
  }

  // insert a row
  public function sql_insert($query)
  {
    $this->result = @mysql_query($query);
    $this->a_rows = @mysql_affected_rows($this->dblink);
    $this->a_id   = @mysql_insert_id($this->dblink);
    echo @mysql_error($this->dblink);
  }

  // fetch a row
  public function sql_fetch($row) {
    @mysql_data_seek($this->result, $row);
    echo @mysql_error($this->dblink);
    $this->data   = @mysql_fetch_assoc($this->result);
    return($this->data);
  }

  public function sanitize($var, $type=1) {
    switch($type) {
    case 0:
      return ((int) $var);
    case 1:
      return (mysql_real_escape_string($var));
    default:
      switch(gettype($var)){
      case "integer":
	return ((int) $var);
      case "string":
	return (mysql_real_escape_string($var));
      default:					
	return "Fuck you!\n";
      }
    }
  }
}
?>
