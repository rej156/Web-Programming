<?php // Example 26-8: profile.php
  require_once 'header.php';

  if (!$loggedin) die();

  echo "<div class='main'><h3>Your Images</h3>";
  echo "<script src=\"photoscript.js\"></script>";

  $result = queryMysql("SELECT photoID FROM photos WHERE user='$user'");
  $dir = "photos/";

  

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		echo "<img src=" . $dir . $row['photoID'] . ".jpg width=\"300\"> <br>";
	}
  
  
	

  echo <<<_END
	
    <form method='post' action='photo_upload.php' enctype='multipart/form-data'>
    <h3>upload new image</h3>
    Image: <input type='file' name='fileToUpload' id='fileToUpload'>
    <input type='submit' value='Upload Image'>
    </form>
    </div><br>
_END;
?>
  </body>
</html>
