<?php
 session_start();
 // Server and db connection
 // qW2bp4hG5&31v6jVOeTd
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


 // values come from user, through webform
 $username =$_POST['txtUsername'];
 $password = $_POST['txtPassword'];

 // Check connection
 if ($connection->connect_error) {
	die ("Connection failed" .$conn->connect_error);
 }

 //delete records on login table that are over 10 mins old
 if ($dquery = $connection -> prepare("DELETE FROM LoginAttempts WHERE TIMESTAMPDIFF(MINUTE,Timestamp,NOW()) > 9")) {
	$dquery -> execute();
	$dquery -> close();
 } else {
	 echo "delete query failed";
 }
 
 $ip = $_SERVER["REMOTE_ADDR"];
 
 //see how many login attempts have come from this ip in the last 10 mins
 if ($cquery = $connection -> prepare("SELECT COUNT(*) FROM LoginAttempts WHERE address LIKE ?")) {
	$cquery -> bind_param('s', $ip);
	$cquery -> execute();
	$cquery -> bind_result($count);
	$cquery -> fetch();
	$cquery -> close();
 } else {
	echo "count query failed";
 }
 
 $captcha_verified = 0;
 if (!empty($_POST['g-recaptcha-response'])) {
	$secret = '6Lf_vYwdAAAAAH5wAKybJ_dO2qDbJ4MpdZlt_T5v';
	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
	$responseData = json_decode($verifyResponse);
	if($responseData->success) {
		$captcha_verified = 1;
	} 
 }
 
 // query if login is correct
 if ($captcha_verified == 1) {
	 if ($count < 3) {
		if ($query = $connection -> prepare("SELECT ID, Password, Activation, Admin FROM SystemUser WHERE Username = ?")) {
			$query -> bind_param('s', $username);
			$query -> execute();
			$query -> store_result();	
			if ($query -> num_rows > 0) {
				//acount with username is found
				$query -> bind_result($id, $pass, $activation, $admin);
				$query -> fetch();
				if (password_verify($password, $pass)) {
					if ($activation == "active") {			
						session_regenerate_id();
						$_SESSION['loggedin'] = TRUE;
						$_SESSION['name'] = $username;
						$_SESSION['id'] = $id;
						$_SESSION['isAdmin'] = $admin;
						header("Location: homeForm.php");
					} else {
						echo "Your account is not activated. Please check your emails";
					}
				} else {
					echo "Incorrect username/password";
				}
			} else {
				echo "Incorrect username/password";
			}
			$query -> close(); 
		} else {
		 echo "SQL Statement invalid";
		}
		if (!isset($_SESSION['loggedin'])) {
			if ($iquery = $connection -> prepare("INSERT INTO LoginAttempts (Address) VALUES (?)")) {
				$iquery -> bind_param('s', $ip);
				$iquery -> execute();
				$iquery -> close();
				echo "<br>";
				echo "Failed attempts: " . ($count+1) . " (max 3 attempts per 10 mins)";
			} else {
				echo "insert query failed";
			}
			echo "<br>";
			echo "<a href='loginForm.php'> BACK TO LOGIN </a>";
		}
	 } else {
		echo "<br> Too many failed login attempts in the last 10 mins - please try again later";
	 }
 } else {
	 echo "Must complete captcha. </br>";
	 echo "<a href='loginForm.php'> BACK TO LOGIN </a>";
 }	 
 

 

?>
