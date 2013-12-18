<html>
<head>
	<title>Xumb Update Tool</title>
	 <link type="text/css" rel="stylesheet" href="<?php echo base_url()?>css/bootstrap.min.css">
</head>
<body style="margin: 20px">
	<h1>Xumb Update Tool</h1>
<?php
	if( empty($step) ) {
		$step = "auth";
	}

	if( empty($msg) ) {
		$msg = "";
	}

	if( $step=="auth" ) {
?>	
	<form name="xmbupd" method="POST" action="<?php echo base_url()?>update_tool/confirm_settings">
		<h5 style="color: red;"><?php echo $msg?></h5>
		<input type="hidden" name="action" value="go">
		Please provide the password to procceed:
		<input type="password" name="password" size="8">
		<input type="submit" value=" >> ">
	</form>
<?php

	} else if( $step=="confirm" ) { // step = auth
?>	
	<form method="post" action="<?php echo base_url()?>update_tool/do_update">
	<div style="margin: 20px">
	<h4>Please confirm the settings:</h4>
	<?php echo $settings?>
	<br>If everything is correct, just click the button below to begin the Update.
	If not, correct your <i>dist.php</i> file and refresh (F5) this page before procceding.
	<br><br>
	<input type="submit" value="Do Update!">
	</div>
	</form>
<?php
	} else if ( $step=="do_update" ) {
?>
		<div style="margin: 20px">
		<?php echo $output?>
		</div>
<?php
	}
?>
</body>
</html>
