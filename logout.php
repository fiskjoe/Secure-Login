<?php
Session_start();
if (!isset($_SESSION['loggedin'])) {
	header("Location: loginForm.php");
	exit();
}
Session_destroy();
header("Location: loginForm.php");
?>