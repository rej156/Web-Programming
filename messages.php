<?php // Example 26-11: messages.php
  require_once 'header.php';

  date_default_timezone_set('GMT');

  if (!$loggedin) die();

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  if (isset($_POST['text']))
  {
    $text = sanitizeString($_POST['text']);

    if ($text != "")
    {
      $pm   = substr(sanitizeString($_POST['pm']),0,1);
      $time = time();
      queryMysql("INSERT INTO messages VALUES(NULL, '$user',
        '$view', '$pm', $time, '$text')");
    }
  }

  if ($view != "")
  {
    if ($view == $user) $name1 = $name2 = "Your";
    else
    {
      $name1 = "<a href='members.php?view=$view'>$view</a>'s";
      $name2 = "$view's";
    }

    echo "<div class='main'><h3>$name1 Messages</h3>";
    showProfile($view);

    echo <<<_END
      <form method='post' action='messages.php?view=$view'>
      Type here to leave a message:<br>
      <textarea name='text' cols='40' rows='3'></textarea><br>
      Public<input type='radio' name='pm' value='0' checked='checked'>
      Private<input type='radio' name='pm' value='1'>
      <input type='submit' value='Post Message'></form><br>
_END;

    if (isset($_GET['erase']))
    {
      $erase = sanitizeString($_GET['erase']);
      queryMysql("DELETE FROM messages WHERE id=$erase AND recip='$user'");
    }

    $query  = "SELECT * FROM messages WHERE recip='$view' ORDER BY time DESC";
    $result = queryMysql($query);
    $num    = $result->num_rows;
    $youtube_pattern = '/(http:|https:)?\/\/(www\.)?(youtube.com|youtu.be)\/(watch)?(\?v=)?(\S+)?/';
    $soundcloud_pattern = '/((https:\/\/)|(http:\/\/)|(www.)|(\s))+(soundcloud.com\/)+[a-zA-Z0-9\-\.]+(\/)+[a-zA-Z0-9\-\.]+/';
    for ($j = 0 ; $j < $num ; ++$j)
    {
      $row = $result->fetch_array(MYSQLI_ASSOC);

      if ($row['pm'] == 0 || $row['auth'] == $user || $row['recip'] == $user)
      {
        echo date('M jS \'y g:ia:', $row['time']);
        echo " <a href='messages.php?view=" . $row['auth'] . "'>" . $row['auth']. "</a> ";

          /* For now https://www.youtube.com/embed/r2YAOeuz64U links work on
             local development environments due to CORS
             Until hosted on webprojects before submission, watch links will
             trigger console log errors.
           */
        if ($row['pm'] == 0 && preg_match($youtube_pattern, $row['message']))
          echo "posted a youtube link! " . youtubeEmbed($row['message']);
        elseif (preg_match($youtube_pattern, $row['message']))
          echo "sent a private youtube link! " . youtubeEmbed($row['message']);
        elseif ($row['pm'] == 0 && preg_match($soundcloud_pattern, $row['message']))
          echo "sent a soundcloud link!" . soundcloudEmbed($row['message']);
        elseif (preg_match($soundcloud_pattern, $row['message']))
          echo "sent a private soundcloud link! " . soundcloudEmbed($row['message']);
        elseif ($row['pm'] == 0)
          echo "wrote: &quot;" . $row['message'] . "&quot; ";
        else
          echo "whispered: <span class='whisper'>&quot;" .
            $row['message']. "&quot;</span> ";

        if ($row['recip'] == $user)
          echo "[<a href='messages.php?view=$view" .
               "&erase=" . $row['id'] . "'>erase</a>]";

        echo "<br>";
      }
    }
  }

  if (!$num) echo "<br><span class='info'>No messages yet</span><br><br>";

  echo "<br><a class='button' href='messages.php?view=$view'>Refresh messages</a></div><br>";
?>
  </body>
</html>
