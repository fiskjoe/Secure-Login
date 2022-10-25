<?php
session_start();
$_SESSION['token'] = md5(uniqid(mt_rand(), true));

if (!isset($_SESSION['loggedin'])) {
	header("Location: loginForm.php");
	exit();
}

echo "<h1> Request Evaluation </h1>";

echo "<form action='requestFormCheck.php' enctype='multipart/form-data' method='POST'>";
echo "<p>";
echo "<label for='a'> Comments: Please enter the details of your antique and your request </label>";
echo "</p> <p>";
echo "<textarea id='a' name='txtComment' rows='10', cols='60' maxlength='2000'></textarea>";
echo "</p> <p>";
echo "<label for='b'> Image: </label>";
echo "<input type='file' name='image'></input>";
echo "</p> <p>";
echo "<label for='b'> Contact Preference: </label>";
echo "<select name='contactPreference'>";
echo "<option value=''>Select...</option>";
echo "<option value='phone'>Phone</option>";
echo "<option value='email'>Email</option>";
echo "</select>";
echo "</p>";
echo "<input type='hidden' name='csrf_token' value=".$_SESSION['token'].">";
echo "<input type='submit' value='Submit'>";
echo "</form>";

echo "<br> <a href='homeForm.php'> HOME </a>";
?>