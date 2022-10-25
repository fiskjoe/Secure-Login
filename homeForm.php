<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header("Location: loginForm.php");
	exit();
}

echo "<link rel='stylesheet' href='registerFormStyles.css'>";

echo "<h1> Homepage </h1>";
echo htmlspecialchars("Welcome to the website " . $_SESSION["name"] . ".");

echo "<br> <a href='requestForm.php'> REQUEST EVALUATION </a>";
if ($_SESSION["isAdmin"] == 1) {	
	echo "<br> <a href='evaluationList.php'> EVALUATIONS LIST </a>";
}
echo "<br> <a href='logout.php'> LOGOUT </a>";
?>