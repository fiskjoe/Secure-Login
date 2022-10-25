<?php
 session_start();
 $_SESSION['token'] = md5(uniqid(mt_rand(), true));
 
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
 echo "<link rel='stylesheet' href='registerFormStyles.css'>";
 
 $email = $_GET["email"];
 $token = $_GET["code"];
 
 if (($email != "") and ($token != "")) {
	//delete all tokens over an hour old
	if ($dquery = $connection -> prepare("DELETE FROM RecoverPass WHERE TIMESTAMPDIFF(MINUTE,Timestamp,NOW()) > 59")) {
		$dquery -> execute();
		$dquery -> close();
	
		if ($query = $connection -> prepare("SELECT * FROM RecoverPass WHERE Email = ?")) {
			$query -> bind_param('s', $email);
			$query -> execute();
			$query -> store_result();
			if ($query -> num_rows > 0) {
				echo "<form action='resetPassCheck.php' method='POST'>";
				echo "<p>";
				echo "<label for='a'> Password: </label>";
				echo "<input id='a' name='txtPassword1' type='password' maxlength='255' />";
				echo "</p>";
				echo "<p>";
				echo "<label for='b'> Confirm Password: </label>";
				echo "<input id='b' name='txtPassword2' type='password' maxlength='255' />";
				echo "</p>";
				echo "<input type='hidden' name='txtToken' id='hiddenField1' value='" . $token . "' />";
				echo "<input type='hidden' name='txtEmail' id='hiddenField2' value='" . $email . "' />";
				echo "<input type='hidden' name='csrf_token' value=".$_SESSION['token'].">";
				echo "<input type='submit' value='Submit'>";
				echo "</form>";
			} else {
				echo "This account has not generated a password reset request";
			}
		} else {
			echo "Select query failed";
		}
	} else {
		echo "delete query failed";
	} 
 }
	
?>