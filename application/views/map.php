<script type="text/javascript">
function showHideMarkers(markers, checkbox) {
	for(var i=0; i<markers.length; i++) {
		marker = markers[i];
		marker.setVisible( checkbox.checked );
	}
}
</script>
<form name="__map">
<?php echo $map['js']; ?>
<?php echo $map['html']; ?>
</form>
<?php
	if( isset($categories) ) {
?>
<div align="left" style="text-align: left; font-size: small;">
	Double-click to add a Marker (you must be logged for that)
</div>
<form>
<div align="right" style=" text-align: right">
<?php
	foreach ($categories as $cat_id => $cat) {
		echo "<input type=checkbox checked=checked name=cat".$cat_id." value=".$cat_id." onClick='showHideMarkers(cat_".$cat_id."_markers, this);'><img src='".base_url()."icons/".$cat['icon']."'> ";
	}
?>
</form>
</div>
<?php
	}
?>