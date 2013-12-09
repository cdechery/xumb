<body>
<?php
	$name = $lat = $long = $id = $description = "";
	
	$name = $data['name'];
	$lat = $data['latitude'];
	$long = $data['longitude'];
	$id = $data['id'];
	$description = $data['description'];
?>
<form action="<?php echo base_url()?>map/update_marker" id="update_marker">
<h5>Name</h5>
<input type="text" name="name" value="<?php echo $name; ?>" size="50" />
<input type="hidden" name="latitude" value="<?php echo $lat; ?>">
<input type="hidden" name="longitude" value="<?php echo $long; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<h5>Details</h5>
<textarea name="description"><?php echo $description; ?></textarea>

<div><input type="submit" value="<?php echo xlabel('update')?>" /> <input type="button" class="delete_marker_btn" data-marker_id="<?php echo $id; ?>" value="Remover" /></div>
</form>
	   
	   <h5>Upload Image</h5>
	   <form method="post" action="<?php echo base_url()?>image/upload_marker_image"
	   		id="upload_marker_image" enctype="multipart/form-data" onSubmit="clearInlineLabels(this)";>
		<input type="hidden" name="marker_id" id="marker_id" value="<?php echo $id; ?>">
	    <input type="hidden" name="thumbs" id="thumbs" value="<?php echo implode('|',$dist['image_settings']['thumb_sizes'])?>"/>
	      <input type="text" name="title" id="title" value="" title="Name is optional"/><br>
	      <input type="file" name="userfile" id="userfile" size="20" title="File"/><br>
	 
	      <input type="submit" name="submit" id="submit" value="<?php echo xlabel('upload')?>" />
	   </form>
	   <h5>Images</h5>
	   <div id="images"></div>
	   <p>
<a href="<?php echo base_url()?>map">Back to the Map</a>
</p>
</body>
<script>
$( document ).ready(function() {
	$(".fancybox").fancybox();
	refresh_marker_images(<?php echo $data['id'];?>);
	processInLineLabels();
});
</script>
</html>