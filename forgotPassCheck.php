<?php
 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;

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
 
 if (empty($_POST['txtEmail'])) {
	echo "no email entered. <a href='forgotPass.php'> GO BACK </a>";
 } else if (!filter_var($_POST['txtEmail'], FILTER_VALIDATE_EMAIL)) {
	echo "Invalid email address format. <a href='forgotPass.php'> GO BACK </a>";
 } else {
	$email = $_POST['txtEmail'];
 }
 
 if (isset($email)) {	
	if ($query = $connection -> prepare("SELECT * FROM SystemUser WHERE email = ?")) {
		$query -> bind_param('s', $email);
		$query -> execute();
		$query -> store_result();
		if ($query -> num_rows > 0) {
			$token = uniqid();		
			if ($iquery = $connection -> prepare("INSERT INTO RecoverPass (Email, Token) VALUES (?, ?)")) {
				$hash_token = password_hash($token, PASSWORD_DEFAULT);
				$iquery -> bind_param('ss', $email, $hash_token);
				$iquery -> execute();
				$iquery -> close();
			
				$activation_link = "https://users.sussex.ac.uk/~jf439@sussex.ac.uk/loginApplication/resetPass.php?email=" . $email . "&code=" . $token;
				$body = "<p> Click on the link to reset your password: <a href='" . $activation_link . "'>" . $activation_link . "</a></p>";
				$subject = "Password reset - noreply";

				require "Exception.php";
				require "PHPMailer.php";
				require "SMTP.php";
				$mail = new PHPMailer(true);
				try {
					//mail server settings
					$mail -> SMTPDebug = 0;
					$mail -> isSMTP();
					$mail -> Host = "smtp.gmail.com";
					$mail -> SMTPAuth = true;
					$mail -> SMTPAutoTLS = true;
					$mail -> Username = "lovejoyantiqueevaluation@gmail.com";
					$mail -> Password = "T9Eq!jP;<7mn\bGW";
					$mail -> SMTPSecure = "ssl";			
					$mail -> Port = 465;

					//Recipients
					$mail -> setFrom("lovejoyantiqueevaluation@gmail.com");
					$mail -> addAddress($email);

					//Content
					$mail -> isHTML(true);
					$mail -> Subject = $subject;
					$mail -> Body = $body;
					$mail -> send();
				} catch (Exception $e) {
					echo "Message could not be sent. Error: ". $mail->ErrorInfo;
				}			
				echo "Please follow the link in your emails to reset your password. It will be valid for 1 hour.";
				echo "<br>";
				echo "If you cant see an email, please try again: <a href='forgotPass.php'> GO BACK </a>";
			} else {
				echo "insert query failed";
			}
		} else {
			echo "No account exists with this email";
		}
	} else {
		echo "select query failed";
	}
 } 
 
?>