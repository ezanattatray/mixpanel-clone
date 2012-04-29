<?
require_once("include/sql.php");
require_once("include/util.php");
require_once("include/config.php");

class mixpanel_user {
  var $sql, $id, $api_key, $email, $password;
  function __construct($sql)
  {
    $this->sql = $sql;
  }

  // load a user from the database
  public function load($id)
  {
    $user = $this->sql->query_row("SELECT api_key, email, password FROM users WHERE id = " . $this->sql->sanitize($id, 0));
    if($this->sql->rows == 1) {
      $this->id       = $id;
      $this->api_key  = $user['api_key'];
      $this->email    = $user['email'];
      $this->password = $user['password'];
      return True;
    } else {
      return False; // no user has this id
    }
  }

  public function load_by_api_key($api_key)
  {
    $user = $this->sql->query_row("SELECT id, email, password FROM users WHERE api_key = '" . $this->sql->sanitize($api_key) . "'");
    if($this->sql->rows == 1) {
      $this->id       = $user['id'];
      $this->email    = $user['email'];
      $this->password = $user['password'];
      $this->api_key  = $api_key;
      return True;
    } else {
      return False;
    }
  }

  // insert a new user into the database
  public function create($email, $password)
  {
    $api_key = $this->build_unique_api_key();

    $this->sql->sql_insert("INSERT INTO users (api_key, email, password) VALUES ('$api_key', '" . $this->sql->sanitize($email) . "', '" . sha1($password . $mixpanel['salt']) . "')");
    if($this->sql->a_rows == 1) {
      $this->id       = $sql->a_id; // get last inserted id from mysql. if only it supported RETURNING like pgsql ;_;
      $this->api_key  = $api_key;
      $this->email    = $email;
      $this->password = $password;
      return True;
    } else {
      return False;
    }
  }

  // returns True (casted) if the api key already exists
  function check_api_key($key)
  {
    $this->sql->query_item("SELECT api_key FROM users WHERE api_key = '$key'"); // doesnt need to be sanitized, user input will never reach this query
    return(!$this->sql->rows > 0);
  }
  
  function build_unique_api_key()
  {
    do {
      $key = random_string(32);
    } while(!$this->check_api_key($key));

    return $key;
  }

  function get_events()
  {
    $this->sql->sql_query("SELECT id, name FROM events WHERE user_id = $this->id");
  }

  public function next_event()
  {
    $event_ary = $this->sql->next_row();
    if($event_ary == NULL) return NULL;
    $event = new mixpanel_event($sql);
    // manually populating each property object for performance reasons
    $event->id      = $event_ary['id'];
    $event->user_id = $this->id;
    $event->name    = $event_ary['name'];
    return $event;
  }
}

class mixpanel_auth
{
  var $sql, $email, $password;

  function __construct($sql)
  {
    $this->sql = $sql;
  }

  // check if username exists, returns true if the email exists
  public function check_email($email)
  {
    $this->sql->query_item("SELECT email FROM users WHERE email = '" . $this->sql->sanitize($email) . "'");
    return($this->sql->rows > 0);
  }

  // checks if login pair is valid, returns user id on success and False on failure
  // IMPORTANT! when checking the return value, check the type! use !== and ===, because PHP will cast 0 to false, making it impossible for uid 0 to login!
  public function login($email, $password)
  {
    $id = $this->sql->query_item("SELECT id FROM users WHERE email = '" . $this->sql->sanitize($email) . "' AND password = '" . sha1($password) . $mixpanel['salt'] . "'");
    if($this->sql->rows > 0)
      return $id;
    else
      return False;
  }
}

class mixpanel_event
{
  var $sql, $id, $user_id, $name;

  function __construct($sql)
  {
    $this->sql = $sql;
  }

  public function create($user, $name)
  {
    $this->sql->sql_insert("INSERT INTO events (user_id, name) VALUES (" . $user->id . ", '" . $this->sql->sanitize($name) . "')");
    if($this->sql->a_rows == 1) {
      $this->id      = $this->sql->a_id;
      $this->user_id = $user->id;
      $this->name    = $name;
      return True;
    } else {
      return False;
    }
  }

  public function load($id)
  {
    $event = $this->sql->query_row("SELECT user_id, name FROM events WHERE id = " . $this->sql->sanitize($id, 1));
    if($this->sql->rows == 1) {
      $this->id      = $id;
      $this->user_id = $event['user_id'];
      $this->name    = $event['name'];
      return True;
    } else {
      return False;
    }
  }

  // loads an event or creates it if it doesnt exist
  public function load_or_create($user, $name)
  {
    $event = $this->sql->query_row("SELECT id FROM events WHERE name = '" . $this->sql->sanitize($name) . "' AND user_id = " . $user->id);
    if($this->sql->rows == 1) {
      $this->id      = $event['id'];
      $this->user_id = $user->id;
      $this->name    = $name;
      return True;
    } else {
      return $this->create($user, $name);
    }
  }

  public function get_properties()
  {
    $this->sql->sql_query("SELECT id, name FROM properties WHERE event_id = " . $this->sql->sanitize($this->id, 1));
  }

  public function next_property()
  {
    $property_ary = $this->sql->next_row();
    if($property_ary == NULL) return NULL;
    $property = new mixpanel_property($this->sql);
    // manually populating each property object for performance reasons
    $property->id       = $property_ary['id'];
    $property->event_id = $this->id;
    $property->name     = $property_ary['name'];
    return $property;
  }
}

class mixpanel_property
{
  var $sql, $id, $event_id, $name;

  function __construct($sql)
  {
    $this->sql = $sql;
  }

  public function create($event, $name)
  {
    $this->sql->sql_insert("INSERT INTO properties (event_id, name) VALUES (" . $event->id . ", '" . $this->sql->sanitize($name) . "')");
    if($this->sql->a_rows == 1) {
      $this->id = $this->sql->a_id;
      $this->event_id = $event->id;
      $this->name = $name;
      return True;
    } else {
      return False;
    }
  }

  public function load($id)
  {
    $property = $this->sql->query_row("SELECT event_id, name FROM properties WHERE id = " . $this->sql->sanitize($id));
    if($this->sql->rows == 1) {
      $this->id       = $id;
      $this->event_id = $property['event_id'];
      $this->name     = $property['name'];
      return True;
    } else {
      return False;
    }
  }

  // loads a property or creates it if it doesnt exist
  public function load_or_create($event, $name)
  {
    $property = $this->sql->query_row("SELECT id FROM properties WHERE name = '" . $this->sql->sanitize($name) . "' AND event_id = " . $event->id);
    if($this->sql->rows == 1) {
      $this->id       = $property['id'];
      $this->event_id = $event->id;
      $this->name     = $name;
      return True;
    } else {
      return $this->create($event, $name);
    }
  }

  public function get_values($offset=0)
  {
    $this->sql->sql_query("SELECT t1.text, t2.time FROM property_values AS t1 JOIN property_values_ts AS t2 ON t1.id = t2.value_id WHERE property_id = " . $this->sql->sanitize($this->id) . " ORDER BY t1.text, t2.time LIMIT 100 OFFSET " . $this->sql->sanitize($offset, 1));
  }

  public function next_value()
  {
    $value_ary = $this->sql->next_row();
    if($value_ary == NULL) return NULL;
    $value = new mixpanel_value($this->sql);
    // manually populating each property object for performance reasons
    $value->property_id = $this->id;
    $value->text        = $value_ary['text'];
    $value->timestamp   = $value_ary['time'];
    return $value;
  }
}

class mixpanel_value 
{
  var $sql, $id, $property_id, $text, $timestamp;

  function __construct($sql)
  {
    $this->sql = $sql;
  }

  function create($property, $text)
  {
    $value = $this->sql->query_row("SELECT id FROM property_values WHERE property_id = " . $this->sql->sanitize($property->id, 1) . " AND text = '" . $this->sql->sanitize($text) . "'");
    if($this->sql->rows > 0) {
      $this->id = $value['id'];
    } else {
      $this->sql->sql_insert("INSERT INTO property_values (property_id, text) VALUES (" . $property->id . ", '" . $this->sql->sanitize($text) . "')");
      if($this->sql->a_rows == 1) {
	$this->id = $this->sql->a_id;
	$this->property_id = $property->id;
	$this->text = $text;
      } else {
	return False;
      }
    }

    $this->sql->sql_insert("INSERT INTO property_values_ts (value_id) VALUES (".$this->sql->sanitize($this->id, 1).")");
    if($this->sql->a_rows == 1) {
      return True;
    } else {
      return False;
    }
  }
}
?>