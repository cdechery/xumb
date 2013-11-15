<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; <?php echo $this->config->item('charset');?>">
<?php
	if( !isset($title) ) {
		echo "ERROR: Title not defined!";
		return;
	}
	
	if( !isset($min_template) ) {
		$min_template = "basic";
	}
?>
<script type="application/javascript" src="<?php echo base_url();?>javascript"></script>
<script type="application/javascript" src="<?php echo base_url();?>min/g=<?php echo $min_template;?>_js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>min/g=<?php echo $min_template;?>_css"/>
<style>
body {
	background-color:#787977;font-size:14px;margin:0px;
}
.corners {
	padding:15px; line-height:18px; -moz-border-radius:10px; -khtml-border-radius:10px; -webkit-border-radius:10px; border-radius:10px; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; -khtml-box-sizing:content-box; box-sizing:content-box
}
#body_container {
	margin:20px 0px 0px;width:800px;background-color:#fff;padding:20px;
}
.credits {
	margin:0px;width:800px;text-align:right;padding:5px 0px;color:#ccc;font-size:11px;top:30px;float:bottom;
}
.credits a {
	color:#ccc; font-size:12px;border:0px;text-decoration:none;
}
</style>
<title><?php echo $title; ?></title>
</head>
<body>
	<div align="center">
	<div class="corners" id="body_container" align="left">
	<h2><a href="<?php echo base_url()?>">Xumb v0.6</a></h2>
	<?php
		$user_name = "None";
		$signup_link = " | <a href='".base_url()."login'>Login</a>";
		$signup_link .= " | <a href='".base_url()."user/new_user'>Sign up</a>";


		if( $login_data["logged_in"] ) {
			$user_name = "<a href='".base_url()."user/modify'>". $login_data["name"]."</a> ";
			$user_name .= "[<a href='".base_url()."user/logout'>Logout</a>]";
			$signup_link = "";
		}
	?>
	<div align="right">User: <?php echo $user_name;?><?php echo $signup_link;?></div>
