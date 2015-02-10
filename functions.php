<?php // Example 26-1: functions.php
  $dbhost  = 'localhost';       // Unlikely to require changing
  $dbname  = 'oliva';           // Modify these...
  $dbuser  = 'oliva';           // ...variables according
  $dbpass  = 'oliva';           // ...to your installation
  $appname = "Social Network";  // ...and preference

  $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ($connection->connect_error) die($connection->connect_error);

  function createTable($name, $query)
  {
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
  }

  function queryMysql($query)
  {
    global $connection;
    $result = $connection->query($query);
    if (!$result) die($connection->error);
    return $result;
  }

  function destroySession()
  {
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
      setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
  }

  function sanitizeString($var)
  {
    global $connection;
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $connection->real_escape_string($var);
  }

  function showProfile($user)
  {
    if (file_exists("$user.jpg"))
      echo "<img src='$user.jpg' style='float:left;'>";

    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if ($result->num_rows)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);
      echo stripslashes($row['text']) . "<br style='clear:left;'><br>";
    }
  }

  function youtubeEmbed($url)
  {
    return "<iframe width='560' height='315' src='" . $url . "' frameborder='0' allowfullscreen></iframe>";
  }

  function soundcloudEmbed($url)
  {
    $getValues=file_get_contents('http://soundcloud.com/oembed?format=js&url='.$url.'&iframe=true');
    $decodeiFrame=substr($getValues, 1, -2);
    $jsonObj = json_decode($decodeiFrame);
    return str_replace('height="400"', 'height="140"', $jsonObj->html);
  }
?>
