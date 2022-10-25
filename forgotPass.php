<?php
session_start();
echo "<link rel='stylesheet' href='registerFormStyles.css'>";
$_SESSION['token'] = md5(uniqid(mt_rand(), true));

echo "<h1> Enter email to reset password </h1>";

echo "<form action='forgotPassCheck.php' method='POST'>";
echo "<p>";
echo "<label for='a'> Email: </label>";
echo "<input id='a' name='txtEmail' type='text' maxlength='255' />";
echo "</p>";
echo "<input type='hidden' name='csrf_token' value=".$_SESSION['token'].">";
echo "<input type='submit' value='Send Email'>";
echo "</form>";

echo "<a href='loginForm.php'> BACK TO LOGIN </a>";


?>