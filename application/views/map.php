<form name="__map">
<?php echo $map['js']; ?>
<?php echo $map['html']; ?>
</form>
<style type="text/css">
#mapFooter {
	height: 30px;
}

#dblClick-categ {
	float: left; font-size: small; width: 33%; vertical-align: middle;
}

#mrkShown-categ {
	float: left; font-size: small; width: 33%; vertical-align: middle;
}

#tglCategs {
	float: left; text-align: right; font-size: small; width: 33%; vertical-align: middle;
}

#dblClick-nocateg {
	float: left; text-align: center; font-size: small; width: 50%; vertical-align: middle;
}

#mrkShown-nocateg {
	float: left; text-align: center; font-size: small; width: 50%; vertical-align: middle;
}
</style>
<?php
	$categ = "no";
	if( isset($categories) ) {
		$categ = "";
	}
?>
	<div id="mapFooter">
		<div id="dblClick-<?php echo $categ?>categ">
			Double-click to add a Marker<br>(you must be logged in for that)
		</div>
		<div id="mrkShown-<?php echo $categ?>categ">
			Markers shown: <span id="markersShown" style="display: inline-block; width: 50px;">All</span> <input style="font-size: small;" type="button" value="Toggle" onClick="toggleMarkers();">
		</div>
<?php
	if( isset($categories) ) {
?>
		<div id="tglCategs">
		<?php
			foreach ($categories as $cat_id => $cat) {
				echo "<input type=checkbox checked=checked name=cat".$cat_id." value=".$cat_id." onClick='showHideMarkers(cat_".$cat_id."_markers, this);'><img src='".base_url()."icons/".$cat['icon']."'> ";
			}
		?>
		</div>
<?php
	}
?>
	</div>
<script type="application/javascript">
	function showHideMarkers(markers, checkbox) {
		for(var i=0; i<markers.length; i++) {
			marker = markers[i];
			marker.setVisible( checkbox.checked );
		}
	}

	var label = document.getElementById('markersShown'); 
	var markersShown = 'all';

	function toggleMarkers() {
		switch( markersShown ) {
			case 'all':
				for(var i=0; i<markers_not_owned.length; i++) {
					markers_not_owned[i].setVisible( false );
				}
				markersShown = 'user';
				label.innerHTML = 'User'; // TODO lang
				break;

			case 'user':
				for(var i=0; i<markers_not_owned.length; i++) {
					markers_not_owned[i].setVisible( true );
				}
				for(var i=0; i<markers_owned.length; i++) {
					markers_owned[i].setVisible( false );
				}
				label.innerHTML = 'Others';
				markersShown = 'others';
				break;

			case 'others':
				for(var i=0; i<markers_owned.length; i++) {
					markers_owned[i].setVisible( true );
				}
				label.innerHTML = 'All';
				markersShown = 'all';
				break;
		}
	}
</script>