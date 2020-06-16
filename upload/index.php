<?php
function mynl2br($text) {
	return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
}

if ($_POST['url'] != "") {

	$url = $_POST['url'];
	if (isset($_SESSION['loggedin'])) {
		$name = $_SESSION['loggedin_name'];
		$author_id = $_SESSION['loggedin_id'];
	} else {
		$name = mynl2br(addslashes(strip_tags($_POST['name'], '<br>')));
	}
	$mapcode = mynl2br(addslashes(strip_tags($_POST['mapcode'], '<br>')));
	$mapcode = str_replace('@', '', $mapcode);
	$mapcode = intval($mapcode);
	$description = mynl2br(addslashes(strip_tags($_POST['description'], '<br>')));
	$tags = mynl2br(addslashes(strip_tags($_POST['tags'], '<br>')));
	
	$servername = "localhost";
	$dbusername = "dbusername";
	$dbpassword = "dbpassword";
	$dbname = "buildlibrary";

	$link = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

	if($link === false) {
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	if (isset($_SESSION['loggedin'])) {
		$sql = "INSERT INTO posts (id, name, description, image, postdate, mapcode, author_id) VALUES (NULL, '$name', '$description', '$url', NULL, '$mapcode', '$author_id')";
	} else {
		$sql = "INSERT INTO posts (id, name, description, image, postdate, mapcode, author_id) VALUES (NULL, '$name', '$description', '$url', NULL, '$mapcode', NULL)";
	}

	if(mysqli_query($link, $sql)) {
		$last_id = mysqli_insert_id($link);
		echo "build upload successful. id: " . $last_id . "<br>";
	} else {
		echo "ERROR: could not execute $sql. " . mysqli_error($link) . "<br>";
	}

	$tags = str_replace(' ', '', $tags);
	$tags = explode(",", $tags);

	foreach($tags as $tag) {
	 	$sql = "SELECT * FROM tags WHERE name='$tag'";
	 	$result = mysqli_query($link, $sql);
	 	$exists = mysqli_num_rows($result);
	 	if(mysqli_num_rows($result) > 0) {
	 		$exists = "yes";
	 	} else {
	 		$exists = "no";
	 	}
	 	$rows = mysqli_fetch_array($result);
	 	if ($exists == "yes") {
	 		$sql = "INSERT INTO post_tags (post_id, tag_id) VALUES ('$last_id', '$rows[0]')";
	 		if(mysqli_query($link, $sql)) {
	 			print("$rows[1] exists! id: $rows[0]<br>");
	 		} else {
	 			print("something on the moon went wrong: " . mysqli_error($link) . "<br>");
	 		}
	 	} else {
	 		$sql = "INSERT INTO tags (id, name) VALUES (NULL, '$tag')";
	 		if(mysqli_query($link, $sql)) {
	 			$last_tag_id = mysqli_insert_id($link);
	 			print("$tag didn't exist. it does now! id: $last_tag_id<br><br>");
	 			$sql = "INSERT INTO post_tags (post_id, tag_id) VALUES ('$last_id', '$last_tag_id')";
	 			if(mysqli_query($link, $sql)) {
	 				print("$tag exists! id: $last_tag_id<br>");
	 			} else {
	 				print("something on venus went wrong: " . mysqli_error($link) . "<br>");
	 			}
	 		} else {
	 			print("something somewhere went wrong: " . mysqli_error($link) . "<br>");
	 		}
	 	}
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
				<h1>THE BUILD LIBRARY</h1> <form><input type="text" class="vcentered button" size="27" placeholder="search?! but this is upload"></form>
			</div>
			<div class="body">
				<form action="./" method="post" enctype="multipart/form-data">
					<table style="width:55%">
						<tr>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
						</tr>
						<tr>
							<td colspan="10"><input type="text" name="url" id="url" placeholder="image url" class="button" autocomplete="off"></td>
						</tr>
						<tr>
							<td colspan="10" style="padding: 10px">
								<span id="preview" style="display: block">pewview</span>
							</td>
						</tr>
						<tr>
							<td colspan="5"><input type="text" name="name" placeholder="your name" class="button" autocomplete="off"></td>
							<td colspan="5"><input type="text" name="mapcode" placeholder="map code" class="button" autocomplete="off"></td>
						</tr>
						<tr>
							<td colspan="10">
								<input type="text" name="description" placeholder="description (optional)" class="button" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td colspan="10">
								<input type="text" name="tags" placeholder="tags separated by comma (optional)" class="button" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td colspan="10">
								<input type="submit" value="upload build" name="submit" class="button">
							</td>
						</tr>
						<tr>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
							<td colspan="1"></td>
						</tr>
					</table>
				</form>
			</div>
			<div class="footer">
				<span>(github link)</span>
				<span>fuck copyright 2020</span>
			</div>
	</body>
</html>

<script>
	function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('#preview').html('<img src=\"'+ e.target.result + '\">');
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}

$("#file").change(function(){
	readURL(this);
	$("#url").val("");
});

$("#url").change(function(){
	$('#preview').html('<img src=\"'+ $("#url").val() + '\">');
	$("#file").val("");
});

</script>