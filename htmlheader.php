<div class="header">
	<a href="<?php echo $site_url;?>"><h1 class="shimmer"><b><span style="color: rgba(233, 196, 106, 0.9);">THE </span><span style="color: rgba(244, 162, 97, 0.9);">BUILD </span><span style="color: rgba(231, 111, 81, 0.9);">LIBRARY</span></b></h1></a>
	<?php if (isset($_COOKIE[loggedin]) && compareSessID($_COOKIE[loggedin_username],$_COOKIE[session_id]) === true) { ?>
    	<div>you are logged in as <a href="<?php echo $site_url;?>/?name=<?php echo $_COOKIE[loggedin_username];?>"><font color="#F4A261"><?php echo $_COOKIE[loggedin_username];?></font></a>
    	<small><a href="./upload">[upload]</a> <a href="<?php echo $site_url;?>/?logout">[logout]</a></small></div>
	<?php } else { ?>
    	<form action="<?php echo $site_url;?>" method="post">
    	    <input type="text" class="login" placeholder="username" name="loginname"><input type="password" class="login" placeholder="password" name="loginpassword">
    	    <input type="submit" class="login" value="login">
    	    <small><a href="<?php echo $site_url;?>/register/">[register]</a><br>
    	    <a href="<?php echo $site_url;?>/upload/">[upload]</a></small>
    	</form>

	<?php } ?>
	<form><input type="text" class="button" placeholder="search"></form>
</div>
