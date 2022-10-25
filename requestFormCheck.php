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

 $image_name = $_FILES['image']['name'];
 $target_dir = "eval_images/";
 $target_file = $target_dir . basename($_FILES['image']['name']);
 
 $comment = $_POST['txtComment'];
 $contact = $_POST['contactPreference'];
 
 //sanitize form inputs of html tags
 $comment = filter_var($comment, FILTER_SANITIZE_STRING);
 
 $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
 $valid_files = array("jpg","jpeg","png");
 
 if (file_exists($target_file)) {
	$new_image_name = uniqid();
	$image_name = $new_image_name . "." . $file_type;
	$target_file = $target_dir . $new_image_name . "." . $file_type;
 }
 
 if (in_array($file_type, $valid_files)) {
	 if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
		echo "The file has been uploaded, and your information has been added to the directory <br/>";
		echo "<a href='homeForm.php'> HOME </a>";
		if ($iquery = $connection -> prepare("INSERT INTO EvaluationRequests (UserID, Comment, Image, Contact) VALUES (?, ?, ?, ?)")) {
			$iquery -> bind_param('isss', $_SESSION['id'], $comment, $image_name, $contact);
			$iquery -> execute();
			$iquery -> close();	
		} else {
			echo "Insert query failed";
		}
	 } else {
		echo "Image was not uploaded.";
	 }
 } else {
	 echo "Invalid file type, must be .jpg, .jpeg or .png";
 }
 
 
?>