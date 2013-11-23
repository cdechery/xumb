<body>
<?php
	$name = $surname = $lat = $long = $email = "";
	$city = $country = $zip_code = "";
	$id = $login = $avatar = $action = "";

	if( !empty($data) ) {
		extract($data);
	}

	$actions = array("insert"=>xlabel('insert'), "update"=>xlabel('update'));
	if( empty($avatar) ) {
		$avatar = "images/default_avatar.gif";
	} else {
		$avatar = $dist['upload']['path'].$avatar;
	}

	$login_disabled = "";
	if( $action=="update" ) {
		$login_disabled = "disabled";
	}
?>

<table cellpadding=5 cellspacing=5 border=0>
	<tr>
		<td style="vertical-align:text-top;">
		<img id="user_avatar" src="<?php echo base_url() . $avatar;?>"/><br>
<?php
	if( $action=="update" ) {
?>
	    <form method="post" action="<?php echo base_url();?>upload/upload_avatar" id="upload_avatar" enctype="multipart/form-data">
		<input type="hidden" name="user_id" id="user_id" value="<?php echo $id; ?>">
	    <input type="hidden" name="thumbs" id="thumbs" value="100|200"/>
		<input type="file" id="userfile" name="userfile" style="display: none;" />
		<input type="button" value="Browse ..." onclick="document.getElementById('userfile').click();" />

	      <br><input type="submit" name="Upload" id="submit" value="<?php echo xlabel('upload')?>" />
	   </form>
<?php
	}
?>
		</td>
		<td>
		<form method="POST" action="<?php echo base_url()?>user/<?php echo $action; ?>" id="user_<?php echo $action?>" onSubmit="clearInlineLabels(this);">
		<input type="hidden" name="id" value="<?php echo $id; ?>">
		<input type="hidden" name="lat" value="<?php echo $lat; ?>">
		<input type="hidden" name="long" value="<?php echo $long; ?>">

		<input type="text" name="login" value="<?php echo $login; ?>" size="50" <?php echo $login_disabled; ?> title="Username"/><br>
		<input type="text" name="name" value="<?php echo $name; ?>" size="50" title="Name" /><br>
		<input type="text" name="surname" value="<?php echo $surname; ?>" size="50" title="Surname"/><br>
		<input type="text" name="email" value="<?php echo $email; ?>" size="50" title="Email" /><br>
		Password<br>
		<input type="password" name="password" value="" size="10"><br>
		Repeat the password<br>
		<input type="password" name="password_2" value="" size="10" /><br>
		<p>---------------------------</p>
		<input type="text" name="city" value="<?php echo $city; ?>" size="50" title="City"/><br>
		<input type="text" name="country" value="<?php echo $country; ?>" size="20" title="Country" /><br>
		<input type="text" name="zip_code" value="<?php echo $zip_code; ?>" size="20" title="ZIP Code" />

		<div><input type="submit" value="<?php echo $actions[ $action ]; ?>"/></div>
		</form>
		<td>
		</td>
	</tr>
</table>
<a href="<?php echo base_url()?>map">Back to the Map</a>
</p>
<script>
$( document ).ready(function() {
	processInLineLabels();
});
</script>
