<?php
session_start();
$_SESSION['token'] = md5(uniqid(mt_rand(), true));
echo "<link rel='stylesheet' href='registerFormStyles.css'>";
echo "<script src='https://www.google.com/recaptcha/api.js' async defer ></script>";

echo "<h1> Lovejoy's Antique Evaluation </h1>";

echo "<form action='loginFormCheck.php' method='POST'>";
echo "<p>";
echo "<label for='a'> Username: </label>";
echo "<input id='a' name='txtUsername' type='text' maxlength='20'/>";
echo "</p> <p>";
echo "<label for='b'> Password: </label>";
echo "<input id='b' name='txtPassword' type='password' maxlength='255'/>";
echo "</p> <p>";
echo "<div id='captcha' class='g-recaptcha' data-sitekey='6Lf_vYwdAAAAAIkwKm_s1ONJS0sJZTfxnZ4PuSdM'></div>";
echo "</p>";
echo "<input type='hidden' name='csrf_token' value=".$_SESSION['token'].">";
echo "<input type='submit' value='Login'>";
echo "</form>";

echo "Not registered yet? Click <a href='registerForm.php'> HERE </a>";
echo "<br>";
echo "Forgotten password? Click <a href='forgotPass.php'> HERE </a>";


?>

