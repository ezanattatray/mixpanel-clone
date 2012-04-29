<?
function random_string($length)
{
  $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";

  $str = "";
  for($i = 0; $i < $length; $i++) {
    $str .= $alphabet[rand(0, strlen($alphabet)-1)];
  }

  return $str;
}
?>