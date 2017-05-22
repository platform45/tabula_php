<?php
/*
* Programmer Name:SK
* Purpose: The index page header of all pages after login
* Date: 18-12-2014
* Dependancy: None
*/
?>
<!doctype html>
<?php
header( "Cache-Control: no-store, no-cache, must-revalidate" );
?>
<html lang="en">
<head>
<meta charset="utf-8">

<link rel="shortcut icon" href="<?php echo $this->config->item('assets');?>images/icon_aoe.ico" type="image/x-icon">
<link rel="icon" href="<?php echo $this->config->item('assets');?>images/favicon-tab.ico" type="image/x-icon">
<title><?php echo $this->config->item('site_name'); ?></title>
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<?php $this->load->view( 'admin/header' );?>

<script type="text/javascript">
$(function() {
$(".knob").knob();
});
</script>
<script src="<?php echo $this->config->item('assets');?>lib/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('assets');?>stylesheets/jquery.dataTables.min.css" type="text/css"/>	

<meta content="Facebook Developers" property="og:site_name">
<meta content="Social Plugins - Documentation - Facebook for Developers" property="og:title">
<meta content="article" property="og:type">
<meta content="https://developers.facebook.com/docs/plugins" property="og:url">
<meta content="https://fbstatic-a.akamaihd.net/rsrc.php/v2/yb/r/nrpLiSINthw.png" property="og:image">
<meta content="en_US" property="og:locale">
<meta content="Social plugins let you see what your friends have liked, commented on or shared on sites across the web." property="og:description">
 

</head>
	<body class=" theme-blue">
		<?php $this->load->view( 'admin/navigator' );?>
		<?php $this->load->view( 'admin/menu' );?>