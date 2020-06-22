<?php

require 'header.php';

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

if(isset($_GET['logout'])) {
	setcookie("loggedin", "", time() - 3600);
	setcookie("loggedin_id", "", time() - 3600);
	setcookie("loggedin_username", "", time() - 3600);
	setcookie("session_id", "", time() - 3600);
	header("Refresh:0; url=" . $site_url);
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
		setcookie("loggedin", 1, $cookietime, $site_path);
		setcookie("loggedin_id", $row['id'], $cookietime, $site_path);
		setcookie("loggedin_username", $row['name'], $cookietime, $site_path);
		$session_id = hash('sha256', $row['name'] . getUserIpAddr() . rand());
		setcookie("session_id", $session_id, $cookietime, $site_path);
		echo "you are logged in! welcome " . $row['name'];
		$sql = "UPDATE users SET session_id = '$session_id' WHERE id='$row[id]'";
		if(mysqli_query($link, $sql)) {
			echo "<br>session id set!";
			header("Refresh:0; url=" . $site_url);
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
			<?php
			include 'htmlheader.php';
			?>
			<div class="body">
				<?php
				if (isset($_GET['id'])) {
					include 'getid.php';
				} elseif (isset($_GET['tag_id']) || isset($_GET['tag']) || isset($_GET['name']) || isset($_GET['mapcode'])) {
					echo "<div class=\"centered\">";
					$sql = "SELECT *, p.name AS p_name, p.id AS p_id FROM posts p";  // resolve ambiguous column names 'name' and 'id'
					if (isset($_GET['tag_id']) || isset($_GET['tag'])) {
						$sql .= " INNER JOIN post_tags pt ON p.id = pt.post_id";
						if (isset($_GET['tag']))
							$sql .= " INNER JOIN tags t ON pt.tag_id = t.id";
					}
					$sql_conditions = [];
					if (isset($_GET["id"]))
						$sql_conditions[] = "id=" . $_GET["id"];
					if (isset($_GET['tag_id']))
						$sql_conditions[] = "tag_id =" . $_GET['tag_id'];
					if (isset($_GET['tag']))
						$sql_conditions[] = "t.name = '" . $_GET['tag'] . "'";
					if (isset($_GET['name']))
						$sql_conditions[] = "p.name = '" . $_GET['name'] . "'";
					if (isset($_GET['mapcode']))
						$sql_conditions[] = "mapcode = '" . $_GET['mapcode'] . "'";
					
					$sql .= " WHERE " . implode(" AND ", $sql_conditions);
					
					$result = $link->query($sql);
					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							echo "<div class=\"cell\"><span><a href=\"./?mapcode=$row[mapcode]\"><span class=\"mapcode\">@$row[mapcode]</span></a> by <a href=\"./?name=$row[name]\"><span class=\"name\">$row[p_name]</span></a></span><span style=\"float:right;\"><a href=\"./?id=$row[p_id]\">#$row[p_id]</a></span><br><a href=\"./?id=$row[p_id]\"><img src=\"$row[image]\" style=\"margin: 10px 0px;\"></a><br><span class=\"cellbot\">";
							$sql2 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $row['p_id'];
							$result2 = $link->query($sql2);
							if ($result2->num_rows > 0) {
								while($row2 = $result2->fetch_assoc()) {
									echo "<a href=\"./?tag=$row2[name]\"><span class=\"tag\">#$row2[name]</span></a> ";
								}
							}
							echo "</span></div>";
						}
					} else {
						echo "0 results";
					}
					echo "</div>";
					echo "<div class=\"centered\"><-- 1 2 3 --></div>";
				} else {

					echo "<div class=\"centered\">";

					$sql = "SELECT * FROM posts ORDER BY id DESC LIMIT 1";
					$result = mysqli_query($link, $sql);
					$column = mysqli_fetch_assoc($result);

					echo "<a href=\"./?id=$column[id]\">#$column[id]</a> | <a href=\"./?mapcode=$column[mapcode]\"><span class=\"mapcode\">@$column[mapcode]</span></a> by <a href=\"./?name=$column[name]\"><span class=\"name\">$column[name]</span></a> | ";
					$sql2 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $column['id'];
					$result2 = $link->query($sql2);
					if ($result2->num_rows > 0) {
						while($row = $result2->fetch_assoc()) {
							echo "<a href=\"./?tag=$row[name]\"><span class=\"tag\">#$row[name]</span></a> ";
						}
					}
					echo "<br><br>";
					echo "<img src=\"". $column['image'] . "\"><br><br>";
					echo "\"" . $column['description'] . "\"<br><br><br>";

					echo "</div>";
					echo "<div class=\"table\">";

					$sql3 = "SELECT * FROM posts ORDER BY id DESC LIMIT 1,99999"; // LIMIT 1,9
					$result3 = $link->query($sql3);

					if ($result3->num_rows > 0) {
						while($row = $result3->fetch_assoc()) {
							echo "<div class=\"cell\"><span><a href=\"./?mapcode=$row[mapcode]\"><span class=\"mapcode\">@$row[mapcode]</span></a> by <a href=\"./?name=$row[name]\"><span class=\"name\">$row[name]</span></a></span><span style=\"float:right;\"><a href=\"./?id=$row[id]\">#$row[id]</a></span><br><a href=\"./?id=$row[id]\"><img src=\"$row[image]\" style=\"margin: 10px 0px;\"></a><br><span class=\"cellbot\">";
							$sql4 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $row['id'];
							$result4 = $link->query($sql4);
							if ($result4->num_rows > 0) {
								while($row = $result4->fetch_assoc()) {
									echo "<a href=\"./?tag=$row[name]\"><span class=\"tag\">#$row[name]</span></a> ";

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
