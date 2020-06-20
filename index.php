<?php

function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

require 'header.php';

function compareSessID($funcname, $funcid) {
	global $link;
	$sql = "SELECT * FROM users WHERE name='$funcname'";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($result);
	if ($funcid == $row[session_id]) {
		return true;
	} else {
		return false;
	}
}

if(!empty($_GET['logout'])) { 
	setcookie("loggedin", "", time() - 3600);
	setcookie("loggedin_id", "", time() - 3600);
	setcookie("loggedin_username", "", time() - 3600);
	header("http://niclasjensen.dk/buildlibrary/");
} elseif(isset($_POST['loginname'])) {

	$loginname = $_POST['loginname'];
	$loginpassword = $_POST['loginpassword'];

	if($link === false) {
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	$sql = "SELECT * FROM users WHERE name='$loginname'";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($result);
	$hash = $row['password'];

	if (password_verify($loginpassword, $hash)) {
		$cookietime = time() + 86400 * 30; // 86400 = 1 day
		setcookie("loggedin", 1, $cookietime, "/buildlibrary");
		setcookie("loggedin_id", $row['id'], $cookietime, "/buildlibrary");
		setcookie("loggedin_username", $row['name'], $cookietime, "/buildlibrary");
		$session_id = hash('sha256', $row['name'] . getUserIpAddr() . rand());
		setcookie("session_id", $session_id, $cookietime, "/buildlibrary");
		echo "you are logged in! welcome " . $row['name'];
		$sql = "UPDATE users SET session_id = '$session_id' WHERE id='$row[id]'";
		if(mysqli_query($link, $sql)) {
			echo "<br>session id set!";
		} else {
			echo "<br>session id failed :(";
		}
	} else {
		echo "wrong password! hacker??";
	}

}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>TFM Builds Library</title>
		<link rel="stylesheet" href="style.css">
		<link rel="stylesheet" href="shimmer.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<a href="http://niclasjensen.dk/buildlibrary/"><h1 class="shimmer"><b>THE BUILD LIBRARY</b></h1></a> <?php if(isset($_COOKIE[loggedin]) && compareSessID($_COOKIE[loggedin_username],$_COOKIE[session_id]) === true) { echo "<span>you are logged in as $_COOKIE[loggedin_username] <a href=\"./upload\">[upload]</a> <a href=\"./?logout\">[logout]</a></span>"; } else { echo '<form action="./" method="post"><input type="text" class="login" placeholder="username" name="loginname"><input type="password" class="login" placeholder="password" name="loginpassword"><input type="submit" class="login" value="login"><small><a href="http://niclasjensen.dk/buildlibrary/register/">[register]</a></small></form>'; } ?><form><input type="text" class="button" placeholder="search"></form>
			</div>
			<div class="body">				
				<?php 

				if(isset($_GET["id"])) {

					include "getid.php";

				} elseif (isset($_GET['tag_id'])) {

					include "gettagid.php";

				} elseif (isset($_GET['tag'])) {

					include "gettag.php";

				} elseif (isset($_GET['name'])) {

					include "getname.php";

				} elseif (isset($_GET['mapcode'])) {

					include "getmapcode.php";

				} else {

					echo "<h2 style=\"margin: 5px;\">Latest</h2>";
					echo "<div class=\"centered\">";

					$sql = "SELECT * FROM posts ORDER BY id DESC LIMIT 1";
					$result = mysqli_query($link, $sql);
					$column = mysqli_fetch_assoc($result);

					echo "<a href=\"./?id=$column[id]\">#$column[id]</a> | <a href=\"./?mapcode=$column[mapcode]\"><font color=\"lime\">@$column[mapcode]</font></a> by <a href=\"./?name=$column[name]\">$column[name]</a> | ";
					$sql2 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $column['id'];
					$result2 = $link->query($sql2);
					if ($result2->num_rows > 0) {
						while($row = $result2->fetch_assoc()) {
							echo "<a href=\"./?tag=$row[name]\">#$row[name]</a> ";
						}
					}
					echo "<br><br>";
					echo "<img src=\"". $column['image'] . "\"><br><br>";
					echo "<font color=\"yellow\">\"" . $column['description'] . "\"</font><br><br>";

					echo "</div>";
					echo "<div class=\"table\">";

					$sql3 = "SELECT * FROM posts ORDER BY id DESC LIMIT 1,99999"; // LIMIT 1,9
					$result3 = $link->query($sql3);

					if ($result3->num_rows > 0) {
						while($row = $result3->fetch_assoc()) {
							echo "<div class=\"cell\"><span><a href=\"./?mapcode=$row[mapcode]\"><font color=\"lime\">@$row[mapcode]</font></a> by <a href=\"./?name=$row[name]\">$row[name]</a></span><span style=\"float:right;\"><a href=\"./?id=$row[id]\">#$row[id]</a></span><br><a href=\"./?id=$row[id]\"><img src=\"$row[image]\" style=\"margin: 10px 0px;\"></a><br><span class=\"cellbot\">";
							$sql4 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $row['id'];
							$result4 = $link->query($sql4);
							if ($result4->num_rows > 0) {
								while($row = $result4->fetch_assoc()) {
									echo "<a href=\"./?tag=$row[name]\">#$row[name]</a> ";

								}
							}
							echo "</span></div>";
						}
					} else {
						echo "0 results";
					}

					echo "</div>";
					echo "<div class=\"centered\"><-- 1 2 3 --></div>";

				}

					?>
			</div>
			<div class="footer">
				<span>(github link)</span>
				<span>fuck copyright 2020</span>
			</div>
		</div>
	</body>
</html>

<?php

mysqli_close($link);

?>