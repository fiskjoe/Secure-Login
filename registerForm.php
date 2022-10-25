<?php
session_start();
echo "<link rel='stylesheet' href='registerFormStyles.css'>";
$_SESSION['token'] = md5(uniqid(mt_rand(), true));
echo "<h1> Please register your details below: </h1>";

echo "<form action='registerFormCheck.php' method='POST'>";
echo "<p>";
echo "<label for='a'> Forename: </label>";
echo "<input id='a' name='txtForename' type='text' maxlength='35'/>";
echo "</p> <p>";
echo "<label for='b'> Surname: </label>";
echo "<input id='b' name='txtSurname' type='text' maxlength='35'/>";
echo "</p> <p>";
echo "<label for='c'> Username: </label>";
echo "<input id='c' name='txtUsername' type='text' maxlength='20'/>";
echo "</p> <p>";
echo "<label for='d'> Email Address: </label>";
echo "<input id='d' name='txtEmail' type='text' maxlength='255'/>";
echo "</p> <p>";
echo "<label for='e'> Phone Number: </label>";
echo "<input id='e' name='txtPhoneNo' type='text' maxlength='20'/>";
echo "</p> <p>";
echo "<label for='f'> Password: </label>";
echo "<input id='f' name='txtPassword1' type='password' maxlength='255'/>";
echo "</p> <p>";
echo "<label for='g'> Confirm Password: </label>";
echo "<input id='g' name='txtPassword2' type='password' maxlength='35'/>";
echo "</p>";
echo "<input type='hidden' name='csrf_token' value=".$_SESSION['token'].">";
echo "<input type='submit' value='Register'>";
echo "</form>";

echo "Passwords must be at least 8 characters long and contain at least one upper case letter, one lower case letter, one number and one special character. </br>";
echo "Already have an account? Click <a href='loginForm.php'> HERE </a>";

?>



























