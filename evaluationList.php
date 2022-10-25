<?php
 session_start();
 echo "<link rel='stylesheet' href='registerFormStyles.css'>";
 if (!isset($_SESSION['loggedin'])) {
	header("Location: loginForm.php");
	exit();
 }
 
 $mysql_host="krier.uscs.susx.ac.uk";
 $mysql_database="G6077_jf439";  
 $mysql_user="jf439";  
 $mysql_password="Mysql_494214";  

 // connect to the server
 $connection = new mysqli($mysql_host, $mysql_user,$mysql_password, $mysql_database);
 if (mysqli_connect_errno()) {
	 exit("Failed to connect to MySQL: " . mysqli_connect_error());
 }
 
 
 if ($_SESSION["isAdmin"] != 1) {	
	echo "Access denied";
 } else {
	echo "<h1> List of Evaluation Requests </h1>";
 
	if (($query = $connection -> prepare("SELECT RequestID, UserID, Comment, Image, Contact FROM EvaluationRequests"))) {
		$query -> execute();
		$query -> store_result();
		$query -> bind_result($reqID, $userID, $comment, $image, $contact);
		
		echo "<listings>";
		echo "<listing>";
		echo "<item> Comment </item>";
		echo "<item> Image </item>";
		echo "<item> Contact </item>";
		echo "<item> UserID </item>";
		echo "</listing>";
		while ($query -> fetch()) {
			echo "<listing>";
			echo "<item> <textarea id='c' rows='4' cols='50' readonly='' style='resize:none'> " . htmlspecialchars($comment) . "</textarea> </item>";
			echo "<item> <a  target='_blank' rel='noreferrer noopener' href='eval_images/".$image."'>" . $image . "</a> </item>";
			echo "<item> " . $contact . " </item>";
			echo "<item> " . $userID . " </item>";
			echo "</listing>";
		}
		echo "</listings>";
	} else {
		echo "query failed";
	}

	echo "<br> <a href='homeForm.php'> HOME </a>"; 
 }
 
?>