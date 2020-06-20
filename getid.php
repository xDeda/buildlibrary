<?php

echo "<div class=\"centered\">";
$sql = "SELECT * FROM posts WHERE id=" . $_GET["id"];
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

?>