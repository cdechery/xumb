<?php
	$thumb = thumb_filename($image->filename, "200");
	$path = $dist['upload']['path'];
?>
<div style="display: inline-block;" align="center">
	<a href="<?php echo base_url().$path.$image->filename; ?>" class="fancybox" rel="galery1" title="<?php echo $image->description; ?>"><img src="<?php echo base_url().$path.$thumb; ?>"></a><br>
	[<a href="#" class="delete_file_link" data-file_id="<?php echo $image->id; ?>">X</a>]
	<strong><?php echo $image->description; ?></strong>
</div>&nbsp;