<?php header("Cache-Control: no-store, no-cache, must-revalidate");?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo (isset($description) ? htmlentities($description) : '');?>">
    <meta name="keyword" content="<?php echo (isset($keywords) ? htmlentities($keywords) : '');?>">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo $this->config->item('front_assets');?>images/logo_icon.ico">
    <title><?php echo (isset($pageTitle) ? htmlentities($pageTitle)." - Health Care Works" : 'Health Care Works');?></title>
    <!-- Core CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('admin_assets');?>stylesheets/jquery-ui.css">
    <link href="<?php echo $this->config->item('front_assets');?>css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('front_assets');?>css/style.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('front_assets');?>css/extra_added.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('front_assets');?>css/fancySelect.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $this->config->item('admin_assets');?>lib/multidate/mdp.css">
    <link rel="stylesheet" href="<?php echo $this->config->item('admin_assets');?>lib/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('admin_assets');?>stylesheets/toast/toastmessage.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
        <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <script src="<?php echo $this->config->item('front_assets');?>js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->item('admin_assets');?>lib/jquery-ui.js"></script>
    <script src="<?php echo $this->config->item('front_assets');?>js/bootstrap.js"></script>
    <script src="<?php echo $this->config->item('front_assets');?>js/fancySelect.js"></script>
    <script src="<?php echo $this->config->item('admin_assets');?>lib/toastmessage.js"></script>
    <script src="<?php echo $this->config->item('admin_assets');?>lib/multidate/jquery-ui.multidatespicker.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
			<?php if($this->session->userdata('toast_message'))
                                {
                                    $success =   $this->session->userdata('toast_message');
                                    $this->session->unset_userdata('toast_message');
                                }
                                if($this->session->userdata('toast_error_message'))
                                {
                                    $error =   $this->session->userdata('toast_error_message');
                                    $this->session->unset_userdata('toast_error_message');
                                }
			?>
			if($.trim($('#success_msg').val())!='')
			{
			  $().toastmessage('showSuccessToast', $('#success_msg').val());
			}
			if($.trim($('#error_msg').val())!='')
			{
			  $().toastmessage('showErrorToast', $('#error_msg').val());
			}
		});
    </script>
    
    

  </head>
<!-- NAVBAR
================================================== -->
  <body>
    <input type="hidden" id="success_msg" name="success_msg" value="<?php echo isset($success)?  $success:'';?>"/>
    <input type="hidden" id="error_msg" name="error_msg" value="<?php echo isset($error)?  $error:'';?>"/>
       <div class="container <?php if($this->uri->segment(2) == 'my-calendar' || $this->uri->segment(2) == 'schedule') { echo ''; }?>">