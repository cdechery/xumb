<style type="text/css">
#loginbox { 
	background: #ededed; 
	margin: 20px auto; 
	padding: 20px; 
	width: 240px; 
}
</style>
<div align=center>
	<h5 style="color: red;"><?php echo $msg?></h5>
</div>
<div id="loginbox">
	<form method="post" action="<?php echo base_url()?>login/verify">
	<h3>Login</h3>
<p>Username: <input type="text" name="login" id="login" /></p>
<p>Password: <input type="password" name="password" id="password"/></p>
<p><input type="submit" value="Go"/></p>
<p><a class="various" href="<?php echo base_url()?>user/reset_password" data-fancybox-type="iframe">Forgot your password?</a></p>
</form>
</div>
<script>
$(document).ready(function() {
	$(".various").fancybox({
		maxWidth	: 300,
		maxHeight	: 100,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
})
</script>
