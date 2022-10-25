<?php
 session_start();
 // Server and db connection
 $mysql_host="krier.uscs.susx.ac.uk";
 $mysql_database="G6077_jf439";  
 $mysql_user="jf439";  
 $mysql_password="Mysql_494214";  

 $csrf_token = $_POST['csrf_token'];
 if ((!isset($_SESSION['token'])) or ($_POST['csrf_token']=="") or ($csrf_token != $_SESSION['token'])) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
    exit;
 }

 // connect to the server
 $connection = new mysqli($mysql_host, $mysql_user,$mysql_password, $mysql_database);
 if (mysqli_connect_errno()) {
	 exit("Failed to connect to MySQL: " . mysqli_connect_error());
 }
 
 $password1 = $_POST["txtPassword1"];
 $password2 = $_POST["txtPassword2"];
 $token = $_POST["txtToken"];
 $email = $_POST["txtEmail"];
 
 $errorOccurred = 0;
 
 //check passwords match
 if ($password1 != $password2) {
	echo "passwords do not match.";
	$errorOccurred = 1;
 }
 
 //check password is valid
 $uppercase = preg_match('@[A-Z]@', $password1);
 $lowercase = preg_match('@[a-z]@', $password1);
 $number    = preg_match('@[0-9]@', $password1);
 $specialChars = preg_match('@[^\w]@', $password1);
 if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password1) < 8) {
	echo "Passwords must be at least 8 characters long and contain at least one upper case letter, one lower case letter, one number and one special character. <br/>";
	$errorOccurred = 1;
 }
 
 if ($query = $connection -> prepare("SELECT Token FROM RecoverPass WHERE Email = ?") and ($errorOccurred==0)) {
	$query -> bind_param('s', $email);
	$query -> execute();
	$query -> store_result();
	$query -> bind_result($token_hash);
	$pass_changed = 0;
	while ($query -> fetch()) {
		if (password_verify($token, $token_hash)) {
			if ($uquery = $connection -> prepare("UPDATE SystemUser SET Password = ? WHERE Email = ?")) {
				$password_hash = password_hash($password1, PASSWORD_DEFAULT);
				$uquery -> bind_param('ss', $password_hash, $email);
				$uquery -> execute();
				$pass_changed = 1;
				if ($dquery = $connection -> prepare("DELETE FROM RecoverPass WHERE Token = ?")) {
					$dquery -> bind_param('s', $token_hash);
					$dquery -> execute();
					$dquery -> close();
				} 
			} else {
				echo "update query failed";
			}
		}
	}	
	if ($pass_changed == 0) {
		echo "Password was not changed.";
	} else {
		echo "Password has been successfully updated. Login <a href='loginForm.php'> HERE </a>";
	}
 } 

	
?>