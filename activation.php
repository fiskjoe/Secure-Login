<?php
 session_start();
 // Server and db connection
 $mysql_host="krier.uscs.susx.ac.uk";
 $mysql_database="G6077_jf439";  
 $mysql_user="jf439";  
 $mysql_password="Mysql_494214";  

 // connect to the server
 $connection = new mysqli($mysql_host, $mysql_user,$mysql_password, $mysql_database);
 if (mysqli_connect_errno()) {
	 exit("Failed to connect to MySQL: " . mysqli_connect_error());
 }
 
 $email = $_GET["email"];
 $activation_code = $_GET["code"];
 
 if (($email != "") and ($activation_code != "")) {
	if ($query = $connection -> prepare("SELECT * FROM SystemUser WHERE Email = ? AND Activation = ?")) {
		$query -> bind_param('ss', $email, $activation_code);
		$query -> execute();
		$query -> store_result();
		if ($query -> num_rows > 0) {
			if ($query = $connection -> prepare("UPDATE SystemUser SET Activation = ? WHERE Email = ? AND Activation = ?")) {
				$activated = "active";
				$query -> bind_param('sss', $activated, $email, $activation_code);
				$query -> execute();
				echo "Your account has now been activated! Log in <a href='loginForm.php'> HERE </a>";
			} else {
				echo "Update query failed";
			}
		} else {
			echo "Account is already activated or does not exist.";
		}
	} else {
		echo "Select query failed";
	}
 }
?>