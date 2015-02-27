<?php
require_once 'header.php';
require_once 'functions.php';
  if (!$loggedin) die();

  
 

  
if(isset($_FILES["fileToUpload"]["type"]))
{
$validextensions = array("jpeg", "jpg");
$temporary = explode(".", $_FILES["fileToUpload"]["name"]);
$file_extension = "jpg";
if ((($_FILES["fileToUpload"]["type"] == "image/jpg") || ($_FILES["fileToUpload"]["type"] == "image/jpeg")
) && ($_FILES["fileToUpload"]["size"] < 1000000)//Approx. 100kb files can be uploaded.
&& in_array($file_extension, $validextensions)) {

if ($_FILES["fileToUpload"]["error"] > 0)
{
echo "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br/><br/>";
}
else
{
if (file_exists("upload/" . $_FILES["fileToUpload"]["name"])) {
echo $_FILES["fileToUpload"]["name"] . " <span id='invalid'><b>already exists.</b></span> ";
}
else
{
  queryMysql("INSERT INTO photos (user) VALUES ('$user')");
  $fileNumber = queryMysql("SELECT * FROM photos ORDER BY photoID DESC LIMIT 1");
 if($fileNumber->num_rows){
	$fileNumber = $fileNumber->fetch_array(MYSQLI_ASSOC);
	
	$fileNumber = $fileNumber['photoID'];

  }
$sourcePath = $_FILES['fileToUpload']['tmp_name'];
$targetPath = "photos/".$fileNumber.".jpg";
echo $targetPath;
move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file
echo "<span id='success'>Image Uploaded Successfully...!!</span><br/>";
echo "<meta http-equiv=\"refresh\" content=\"0;URL=photos.php\" />";
}
}
}
else
{
echo "<span id='invalid'>***Invalid file Size or Type***<span>";
}
}

?>

 