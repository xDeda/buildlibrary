<?php
function mynl2br($text) {
	return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
}

if ($_POST['username'] != "" && $_POST['password'] != "") {

	$username = $_POST['username'];
	$password = $_POST['password'];
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);
	
	$servername = "localhost";
	$dbusername = "dbusername";
	$dbpassword = "dbpassword";
	$dbname = "buildlibrary";

	$username = mynl2br($username);
	$username = addslashes($username);
	$username = strip_tags($username, '<br>');

	$link = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

	if($link === false) {
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	$sql = "INSERT INTO users (id, name, password) VALUES (NULL, '$username', '$hashed_password')";

	if(mysqli_query($link, $sql)) {
		$last_id = mysqli_insert_id($link);
		echo "registration successful. id: " . $last_id;
	} else {
		echo "ERROR: could not execute $sql. " . mysqli_error($link);
	}

	mysqli_close($link);

}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>TFM Builds Library</title>
		<link rel="stylesheet" href="../style.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<h1><a href="../">THE BUILD LIBRARY</a></h1> <form><input type="text" class="button" placeholder="search"></form>
			</div>
			<div class="body">
				<div style="width:25%;">
					<form action="./" method="post">
						username
						<input type="text" id="username" name="username" placeholder="username" class="button" autocomplete="off">
						password
						<input type="password" id="password" name="password" placeholder="password" class="button" autocomplete="off">
						<input type="submit" value="register!" class="button">
					</form>
				</div>
			</div>
			<div class="footer">
				<span>(github link)</span>
				<span>fuck copyright 2020</span>
			</div>
		</div>
	</body>
</html>