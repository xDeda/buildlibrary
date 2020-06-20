<?php

echo "<div class=\"table\">";
$sql = "SELECT post_id FROM post_tags WHERE tag_id = " . $_GET['tag_id'];
$result = $link->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		
		$sql2 = "SELECT * FROM posts WHERE id = " . $row['post_id'];
		$result2 = $link->query($sql2);
		if ($result2->num_rows > 0) {
			while($row2 = $result2->fetch_assoc()) {
				echo "<div class=\"cell\"><span><a href=\"./?mapcode=$row2[mapcode]\"><font color=\"lime\">@$row2[mapcode]</font></a> by <a href=\"./?name=$row2[name]\">$row2[name]</a></span><span style=\"float:right;\"><a href=\"./?id=$row2[id]\">#$row2[id]</a></span><br><a href=\"./?id=$row2[id]\"><img src=\"$row2[image]\" style=\"margin: 10px 0px;\"></a><br><span class=\"cellbot\">";
				$sql3 = "SELECT name FROM tags t INNER JOIN post_tags pt ON t.id = pt.tag_id WHERE pt.post_id = " . $row2['id'];
				$result3 = $link->query($sql3);
				if ($result3->num_rows > 0) {
					while($row3 = $result3->fetch_assoc()) {
						echo "<a href=\"./?tag=$row3[name]\">#$row3[name]</a> ";
					}
				}
				echo "</span></div>";
			}
		}
	}
} else {
	echo "0 results";
}
echo "</div>";
echo "</div>";
echo "<div class=\"centered\"><-- 1 2 3 --></div>";

?>