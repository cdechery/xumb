<?php
	if (isset($files) && count($files)) {
		$path = $dist['upload']['path'];
?>
<?php
		foreach ($files as $file) {
			$thumb = thumb_filename($file->filename, 150);
?>
<div style="display: inline-block;" align="center">
	<a class="fancybox" href="<?php echo base_url().$path.$file->filename; ?>" rel="galery1" title="<?php echo $file->description; ?>"><img src="<?php echo base_url().$path.$thumb; ?>"></a><br>
	[<a href="#" class="delete_file_link" data-file_id="<?php echo $file->id; ?>">X</a>]
	<strong><?php echo $file->description; ?></strong>
</div>
<?php
		} // foreach
?>
<?php
	} else {
?>
   <p>No images</p>
<?php
	} //else
?>