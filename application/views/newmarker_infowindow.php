<div style="width: 250px; text-align: left;">
	<form name="newMark" id="newMark" method=post action="<?php echo base_url();?>map/new_marker" onSubmit="clearInlineLabels();">
	<input type=hidden name=latitude value="<?php echo $lat;?>"><input type=hidden name=longitude value="<?php echo $long;?>">
	Create a new marker here?
	<input id="input_text" type="text" id="name" name="name" value="" size="20" title="Name is optional"/><br>
<?php
	if( isset($categories) ) {
		echo "	<select name=cat>";
		foreach($categories as $id => $cat) {
			echo "		<option value=".$id.">".$cat['name']."</option>";
?>
<?php
		} //for
		echo "	</select>";
	} else {
		echo "<input type=hidden name=cat value=".$default_category.">";
	}
?>
	<input type="submit" value="Yes">
	<input type="button" value="No" onClick="newMarker.setMap(null);">
	</form>
</div>