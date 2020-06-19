<?php
function mynl2br($text) {
	return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
}

if ($_POST['url'] != "") {

	require '../header.php';

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
	$tags = str_replace('#', '', $tags);

	if($link === false) {
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	if(isset($_COOKIE[loggedin_username])) {
		$sql = "INSERT INTO posts (id, name, description, image, postdate, mapcode, author_id) VALUES (NULL, '$_COOKIE[loggedin_username]', '$description', '$url', NULL, '$mapcode', '$_COOKIE[loggedin_id]')";
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
		<link href="./dropzone.css" rel="stylesheet" media="screen">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.7/cropper.min.css" rel="stylesheet">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.7/cropper.min.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<h1><a href="../">THE BUILD LIBRARY</a></h1><?php if(isset($_COOKIE[loggedin_username])) { echo "<span>you are logged in as $_COOKIE[loggedin_username] <a href=\"http://niclasjensen.dk/buildlibrary/?logout\">[logout]</a></span>"; } else { echo '<form action="./" method="post"><input type="text" class="login" placeholder="username" name="loginname"><input type="password" class="login" placeholder="password" name="loginpassword"><input type="submit" class="login" value="login"><small><a href="http://niclasjensen.dk/buildlibrary/register/">[register]</a></small></form>'; } ?><form><input type="text" class="vcentered button" size="27" placeholder="search?! but this is upload"></form>
			</div>
			<div class="body">
				<form action="./" method="post" enctype="multipart/form-data">
					<table style="width:65%">
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
							<td colspan="10">
							    <div class="dropzone"></div>
							    <div class="croparea" style="display:none;">
							        <img id="cropper-img" style="max-width:100%;">
							        <input value="upload image" name="submit" id="crop-btn" type="button" class="button">
							    </div>
							    <div id="preview" style="display:none;"></div>
							</td>
						</tr>
						<tr>
							<td colspan="1">or</td>
							<td colspan="9"><input type="text" id="urlpaste" placeholder="image url" class="button" autocomplete="off"></td>
						</tr>
						<tr>
							<td colspan="5"><?php if(isset($_COOKIE[loggedin_username])) { echo '<input type="text" name="name" value="' . $_COOKIE[loggedin_username] . '" class="button" disabled>'; } else { echo '<input type="text" name="name" placeholder="your name" class="button" autocomplete="off">'; } ?></td>
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
					<input type="hidden" type="text" name="url" id="url">
				</form>
			</div>
			<div class="footer">
				<span>(github link)</span>
				<span>fuck copyright 2020</span>
			</div>
	</body>
</html>

<script src="./dropzone.js"></script>
<script>
const image = document.getElementById('cropper-img');
const cropper = new Cropper(image, {
    initialAspectRatio: 16 / 9,
});

setDropzoneCallback(eventFileChosen);

$('#urlpaste').change(function(){
	$('.dropzone').html('<img src="'+ $('#urlpaste').val() +'">');
	urlPaste = $('#urlpaste').val();
	$('#url').val(urlPaste);
});

function eventImgurUploaded(res) {
    if (res.success === true) {
        $('#preview').html('<img src=\"'+ res.data.link + '\">');
        $("#url").val(res.data.link);
        $("#urlpaste").val(res.data.link);
    } else {
        $('#preview').html('something on the moon went wrong');
    }
    $('.croparea').hide();
    $('#preview').show();
}

function eventFileChosen(file) {
	if (file.type.match(/image/) && file.type !== 'image/svg+xml') {
		var reader = new FileReader();

		reader.onload = function(e) {
		    var ev = e || event;
		    cropper.replace(ev.target.result);
            $('.dropzone').hide();
            $('.croparea').show();
            $('#crop-btn').click(function() {
                let img = cropper.getCroppedCanvas().toDataURL();
                doImageUpload(img);
            });
		}

		reader.readAsDataURL(file);
	}
}

var config = {
    clientid: '1d434f87f174fd8',  // your Imgur client ID from https://api.imgur.com/oauth2/addclient
};

function imgurPost(path, data, callback) {
    var xhttp = new XMLHttpRequest();

    xhttp.open('POST', path, true);
    xhttp.setRequestHeader('Authorization', 'Client-ID ' + config.clientid);
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status >= 200 && this.status < 300) {
                var response = '';
                try {
                    response = JSON.parse(this.responseText);
                } catch (err) {
                    response = this.responseText;
                }
                callback.call(window, response);
            } else {
                throw new Error(this.status + " - " + this.statusText);
            }
        }
    };
    xhttp.send(data);
    xhttp = null;
}

/* https://stackoverflow.com/a/38935990 */
function dataURItoFile(datauri, filename) {
    var arr = datauri.split(','),
        mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), 
        n = bstr.length, 
        u8arr = new Uint8Array(n);
        
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    
    return new File([u8arr], filename, {type:mime});
}
    
function doImageUpload(img_uri) {
    var fd = new FormData();
    fd.append('image', dataURItoFile(img_uri));

    /* some loading spinner here? idk */
    imgurPost("https://api.imgur.com/3/image", fd, function(data) {
         /* close some loading spinner here? idk */
        eventImgurUploaded(data);
    });
}

</script>