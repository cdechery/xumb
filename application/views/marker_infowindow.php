<?php
	$path = $dist['upload']['path'];
	$width = "250";
	if( isset($data['images']) && count($data['images'])>2 ) {
		$width = "320";
	}

	$desc = $data['description'];
	if( strlen($desc)>110 ) {
		$desc = substr($desc, 0, 106)."<a href='#'>...</a>";
	}
?>
<div style="width: <?php echo $width;?>px; text-align: left;">
<?php
	if( isset($data['category']) ) {
?>
<b><?php echo $data['category']['name'];?></b><br>
<?php
	}
?>
<?php echo $data['name'];?><br>
<?php echo wordwrap($desc, 39); ?><br>
<?php
	foreach ($data['images'] as $img) {
		$thumb = $img->filename = thumb_filename($img->filename, "100");
?>
	<img src="<?php echo base_url().$path.$thumb; ?>">  
<?php
	}

	if( $login_data["logged_in"] && $login_data["user_id"]==$data["user_id"] ) {
		echo "<br>[<a href=\"./map/modify_marker/".$data['id']."\">Modify</a>] | ";
		echo "[<a href=\"#\" class=\"delete_marker_link\" data-marker_id=\"".$data['id']."\">Delete</a>]";
	} else {
		echo "<br>[<a href=\"./map/show_marker/".$data['id']."\">More</a>]";
	}
?>
  
</div>
