<?php

// GET MAPCODE

$mapcode = $_GET['mapcode'];
$mapcode = str_replace('@','',$mapcode);
echo "<div class=\"table\">";
$sql = "SELECT * FROM posts WHERE mapcode = '$mapcode'";
$result = $link->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo "<div class=\"cell\"><span><a href=\"./?mapcode=$row[mapcode]\"><span class=\"mapcode\">@$row[mapcode]</span></a> by <a href=\"./?name=$row[name]\"><span class=\"name\">$row[name]</span></a></span><span style=\"float:right;\"><a href=\"./?id=$row[id]\">#$row[id]</a></span><br><a href=\"./?id=$row[id]\"><img src=\"$row[image]\" style=\"margin: 10px 0px;\"></a><br><span class=\"cellbot\">";
		$sql2 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $row['id'];
		$result2 = $link->query($sql2);
		if ($result2->num_rows > 0) {
			while($row = $result2->fetch_assoc()) {
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

?>