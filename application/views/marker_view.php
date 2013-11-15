<body>
<h5><?php echo $name; ?></h5>
<?php
	if( isset($category) ) {
?>
<h6>(<?php echo $category['name'];?>)</h6>
<?php
	}
?>
<p><?php echo $description; ?></p>
	   
<div id="images"></div>
<p>
<a href="<?php echo base_url()?>map">Back to the Map</a>
</p>
</body>
<script>
$( document ).ready(function() {
	$(".fancybox").fancybox();
	refresh_marker_images(<?php echo $id;?>);
});
</script>
</html>