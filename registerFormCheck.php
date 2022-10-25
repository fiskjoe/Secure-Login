<?php
 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\Exception;
 
 session_start();
 //mysql server information
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

 // Copy all of the data from the form into variables
 $forename = $_POST['txtForename'];
 $surname = $_POST['txtSurname'];
 $username = $_POST['txtUsername'];
 $email = $_POST['txtEmail'];
 $phoneNo = $_POST['txtPhoneNo'];
 $password1 = $_POST['txtPassword1'];
 $password2 = $_POST['txtPassword2'];

 // Create a variable to indicate if an error has occurred or not, 0=false and 1=true. 
 $errorOccurred = 0;

 // Make sure that all text boxes were not blank.
 if ($forename == "" or $surname == "" or $username == "" or $email == "" or $phoneNo == "" or $password1 == "" OR $password2 == "") {
	echo "Please fill in all fields of the registration form. <br/>";
	$errorOccurred = 1;
 }
 
 //check passwords match
 if ($password1 != $password2) {
	echo "Passwords do not match. <br/>";
	$errorOccurred = 1;
 }
 
 //validate inputs 
 if (!filter_var($_POST['txtEmail'], FILTER_VALIDATE_EMAIL)) {
	echo "Invalid email address. <br/>";
	$errorOccurred = 1;
 } 
 
 if (preg_match('/^[a-zA-Z0-9]+$/', $username) == 0) {
    exit('Username is not valid! Must only contain letters and numbers <br/>');
	$errorOccurred = 1;
 }
 
 //check phone number is valid
 $phoneNo = filter_var($phoneNo, FILTER_SANITIZE_NUMBER_INT);
 $phoneNo = str_replace("-", "", $phoneNo);
 if (strlen($phoneNo) < 10 or strlen($phoneNo) > 14) {
	echo "Invalid Phone Number. <br/>";
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
 
 //sanitize strings to ensure no html tags
 $forename = filter_var($forename, FILTER_SANITIZE_STRING);
 $surname = filter_var($surname, FILTER_SANITIZE_STRING);
 
 //check if username/email have been used, if not insert data to SystemUser
 if (($query = $connection -> prepare("SELECT ID, Username, Email FROM SystemUser WHERE Username = ? OR Email = ?")) and ($errorOccurred == 0)) {
	$query -> bind_param('ss', $username, $email);
	$query -> execute();
	$query -> store_result();
	if ($query -> num_rows == 0) {
		if ($iquery = $connection -> prepare("INSERT INTO SystemUser (Username, Password, Forename, Surname, Email, Phone, Activation) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
			$password = password_hash($password1, PASSWORD_DEFAULT);
			$activation = uniqid();
			$iquery -> bind_param('sssssss', $username, $password, $forename, $surname, $email, $phoneNo, $activation);
			$iquery -> execute();
			$iquery -> close();
			
			//account verification
			echo "Please follow the instructions in your email to activate your account. Don't forget to check your spam if you can't see it!";	
			
			$activation_link = "https://users.sussex.ac.uk/~jf439@sussex.ac.uk/loginApplication/activation.php?email=" . $email . "&code=" . $activation;
			$body = "<p> Click on the link to activate your account: <a href='" . $activation_link . "'>" . $activation_link . "</a></p>";
			$subject = "Account Activation - noreply";

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
				$mail -> addAddress($email, $username);
				
				//Content
				$mail -> isHTML(true);
				$mail -> Subject = $subject;
				$mail -> Body = $body;
				$mail -> send();
			} catch (Exception $e) {
				echo "Message could not be sent. Error: ". $mail->ErrorInfo;
			}	
		} else {
			echo "Insert query failed";
		}
	} else {
		$query -> bind_result($id, $un, $em);
		while ($query -> fetch()) {
			if ($un === $username) {
				echo "Username already in use";
			} else {
				echo "Email already in use";
			}
		}
	}
	$query -> close();
 } else {
	 echo "<br>";
	 echo "<br>";
	 echo "<a href='registerForm.php'> BACK TO SIGNUP </a>";
 }
 
?>
