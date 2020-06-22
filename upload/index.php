<?php

require '../header.php';

function mynl2br($text) {
	return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
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

if ($_POST['url'] != "") {

	$url = $_POST['url'];
	$logged = 0;
	if (isset($_COOKIE[loggedin]) && compareSessID($_COOKIE[loggedin_username],$_COOKIE[session_id]) === true) {
		$logged = 1;
		$name = $_COOKIE[loggedin_username];
		$author_id = $_COOKIE[session_id];
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

	if($logged == 1) {
		$sql = "INSERT INTO posts (id, name, description, image, postdate, mapcode, author_id) VALUES (NULL, '$_COOKIE[loggedin_username]', '$description', '$url', NULL, '$mapcode', '$_COOKIE[loggedin_id]')";
	} else {
		$sql = "INSERT INTO posts (id, name, description, image, postdate, mapcode, author_id) VALUES (NULL, 'Guest $name', '$description', '$url', NULL, '$mapcode', NULL)";
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
		<link rel="stylesheet" href="../shimmer.css">
		<link href="./dropzone.css" rel="stylesheet" media="screen">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.7/cropper.min.css" rel="stylesheet">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.7/cropper.min.js"></script>
	</head>
	<body>
		<div class="container">
			<?php
			include '../htmlheader.php';
			?>
			<div class="body">
				<form action="./" method="post" id="upload-form" enctype="multipart/form-data">
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
							        <input value="confirm crop" id="crop-btn" type="button" class="button-free">
							        <input value="undo crop" id="uncrop-btn" type="button" class="button-free">
							    </div>
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
								<input type="submit" value="upload build" id="upload-btn" class="button">
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
    preview: '#preview',
    //initialAspectRatio: 16 / 9,
    autoCropArea: 1,
    viewMode: 1,
});
var prev_imgs = [];
var curr_img = null;
var uploading = false;

setDropzoneCallback(eventFileChosen);

$('#urlpaste').change(function(){
    eventExternalImage($('#urlpaste').val());
});

$('#crop-btn').click(function() {
    cropper.getCroppedCanvas({imageSmoothingEnabled: false, imageSmoothingQuality: 'high'}).toBlob(function(blob) {
        cropper.replace(URL.createObjectURL(blob));
        prev_imgs.push(curr_img);
        curr_img = blob;
    });
});
$('#uncrop-btn').click(function() {
    if (prev_imgs.length > 0) {
        curr_img = prev_imgs.pop();
        cropper.replace(URL.createObjectURL(curr_img));
    }
});

$('#upload-form').submit(function(e) {
    e.preventDefault();
    if (uploading) return;
    doImageUpload(curr_img);
    $('#upload-btn').val("uploading image...")
    $('#upload-btn').prop('disabled', true);
    uploading = true;
});

function eventImgurUploaded(res) {
    if (res.success === true) {
        $("#url").val(res.data.link);
        $('#urlpaste').val(res.data.link);
        document.getElementById("upload-form").submit();
    } else {
        $('#upload-btn').val("something on the moon went wrong... try again")
        $('#upload-btn').prop('disabled', false);
        uploading = false;
    }
}

function eventFileChosen(file) {
	if (file.type.match(/image/) && file.type !== 'image/svg+xml') {
		var reader = new FileReader();

		reader.onload = function(e) {
		    var ev = e || event;
		    cropper.replace(ev.target.result);
            $('.dropzone').hide();
            $('.croparea').show();
            
		}
        curr_img = file;
		reader.readAsDataURL(file);
	}
}
// TODO: will break with discord.. the external image must allow CORS.. 
function eventExternalImage(url) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'blob';
    
    xhr.onload = function(e) {
        if (this.status !== 200) {
            $("#url").val(url);
            return;
        }
        $('.dropzone').hide();
        $('.croparea').show();
        curr_img = this.response;
        cropper.replace(URL.createObjectURL(curr_img));
    };
    
    xhr.send();
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

function doImageUpload(blob) {
    var fd = new FormData();
    fd.append('image', blob);

    imgurPost("https://api.imgur.com/3/image", fd, function(data) {
        eventImgurUploaded(data);
    });
}

</script>