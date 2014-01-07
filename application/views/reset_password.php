<head>
<script type="text/javascript">
// this javascript code below is used to make this view 'inherit'
// the CSS styles from the parent window, since this is being
// loaded via fancybox into an inframe
window.onload = function() {
    if (parent) {
        var oHead = document.getElementsByTagName("head")[0];
        var arrStyleSheets = parent.document.getElementsByTagName("link");
        for (var i = 0; i < arrStyleSheets.length; i++){    
            oHead.appendChild(arrStyleSheets[i].cloneNode(true));
        }            
    }    
}
</script>
<style type="text/css">
.error {
	color: red;
	font-weight:bold;
}
.success {
	color: blue;
	font-weight:bold;
}
.form {
	color: black;
}
</style>
</head>
<div style="text-align: middle; margin: 10px">
<div class="<?php echo $status?>"><?php echo $msg?></div><br>
<form method="post" action="<?php echo base_url()?>user/reset_password">
	<input type="hidden" name="action" value="<?php echo $action?>">
<?php
	if( $status!="success" ) {
?>
	<input type="text" name="email"> <input type="submit" value="Submit">
<?php
	} //if
?>
</form>
</div>
