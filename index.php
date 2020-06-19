<?php

require 'header.php';

if(isset($_GET['logout'])) { 
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
		setcookie("loggedin", 1, time() + (86400 * 30), "/buildlibrary"); // 86400 = 1 day
		setcookie("loggedin_id", $row['id'], time() + (86400 * 30), "/buildlibrary"); // 86400 = 1 day
		setcookie("loggedin_username", $row['name'], time() + (86400 * 30), "/buildlibrary"); // 86400 = 1 day
		echo "you are logged in! welcome " . $row['name'];
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
				<h1 class="shimmer"><b>THE BUILD LIBRARY</b></h1> <?php if(isset($_COOKIE[loggedin_username])) { echo "<span>you are logged in as $_COOKIE[loggedin_username] <a href=\"./upload\">[upload]</a> <a href=\"./?logout\">[logout]</a></span>"; } else { echo '<form action="./" method="post"><input type="text" class="login" placeholder="username" name="loginname"><input type="password" class="login" placeholder="password" name="loginpassword"><input type="submit" class="login" value="login"><small><a href="http://niclasjensen.dk/buildlibrary/register/">[register]</a></small></form>'; } ?><form><input type="text" class="button" placeholder="search"></form>
			</div>
			<div class="body">				
				<?php 

				if(isset($_GET["id"])) {

				?>

				<div class="centered">
					<?php

					$sql = "SELECT * FROM posts WHERE id='" . $_GET["id"] . "'";
					$result = mysqli_query($link, $sql);
					$column = mysqli_fetch_assoc($result);

					if ($column['name'] == "Tactcat" || $column['name'] == "Cassoulet") {
						$column['name'] = "<font color=\"pink\">" . $column['name'] . "</font>";
					}

					echo "<font color=\"lime\">@" . $column['mapcode'] . "</font> by " . $column['name'] . " | ";
					$sql1 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $column['id'];
					$result1 = $link->query($sql1);
					if ($result1->num_rows > 0) {
						while($row1 = $result1->fetch_assoc()) {
							echo "#" . $row1['name'] . " ";
						}
					}
					echo "<br><br>";
					echo "<img src=\"". $column['image'] . "\"><br><br>";
					echo "<font color=\"yellow\">\"" . $column['description'] . "\"</font><br><br>";

					?>

				<?php

				} else {

				?>
				<h2>Latest</h2>
				<div class="centered">
					<?php

					$sql = "SELECT * FROM posts ORDER BY id DESC LIMIT 1";
					$result = mysqli_query($link, $sql);
					$column = mysqli_fetch_assoc($result);



					if ($column['name'] == "Tactcat" || $column['name'] == "Cassoulet") {
						$column['name'] = "<font color=\"pink\">" . $column['name'] . "</font>";
					}

					echo "<font color=\"lime\">@" . $column['mapcode'] . "</font> by " . $column['name'] . " | ";
					$sql4 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $column['id'];
					$result4 = $link->query($sql4);
					if ($result4->num_rows > 0) {
						while($row4 = $result4->fetch_assoc()) {
							echo "#" . $row4['name'] . " ";
						}
					}
					echo "<br><br>";
					echo "<img src=\"". $column['image'] . "\"><br><br>";
					echo "<font color=\"yellow\">\"" . $column['description'] . "\"</font>";

					?>
				</div>
				
				<div class="table">
					<?php

					$sql2 = "SELECT * FROM posts ORDER BY id DESC"; // LIMIT 1,9
					$result2 = $link->query($sql2);

					if ($result2->num_rows > 0) {
						while($row = $result2->fetch_assoc()) {
							if ($row['name'] == "Tactcat" || $row['name'] == "Cassoulet") {
								$row['name'] = "<font color=\"pink\">" . $row['name'] . "</font>";
							}
							echo "<div class=\"cell\"><a href=\"./?id=$row[id]\" style=\"color: #eee;\"><span><font color=\"lime\">@$row[mapcode]</font> by $row[name]</span><span style=\"float:right;\">#$row[id]</span><br><img src=\"$row[image]\" style=\"margin: 10px 0px;\"></a><br><span class=\"cellbot\">";
							$sql3 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $row['id'];
							$result3 = $link->query($sql3);
							if ($result3->num_rows > 0) {
								while($row2 = $result3->fetch_assoc()) {
									echo "#" . $row2['name'] . " ";
								}
							}
							echo "</span></div>";
						}
					} else {
						echo "0 results";
					}

					echo "</div>";
					echo "<div class=\"centered\"><-- 1 2 3 --></div>";

					mysqli_close($link);

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