<script language="Javascript">
	function fbAuth() {
		var  screenX    = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft,
		     screenY    = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop,
		     outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth,
		     outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22),
		     width    = 400,
		     height   = 200,
		     left     = parseInt(screenX + ((outerWidth - width) / 2), 10),
		     top      = parseInt(screenY + ((outerHeight - height) / 2.5), 10),
		     features = (
		        'width=' + width +
		        ',height=' + height +
		        ',left=' + left +
		        ',top=' + top
		      );
		
		newwindow=window.open('<?php echo $url; ?>','Login_by_facebook',features);
		if (window.focus) { newwindow.focus() }
	}
</script>
<a href="#" onClick="fbAuth(); return false;">Click here to login</a>