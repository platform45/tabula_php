<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>lib/bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="<?php echo $this->config->item('assets');?>lib/font-awesome/css/font-awesome.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/theme.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/premium.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/toast/toastmessage.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/jquery.datetimepicker.css">





<script src="<?php echo $this->config->item('assets');?>lib/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('assets');?>lib/jquery.validate.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('assets');?>lib/jQuery-Knob/js/jquery.knob.js" type="text/javascript"></script>
<script src="<?php echo $this->config->item('assets');?>lib/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo $this->config->item('assets');?>lib/toastmessage.js"></script>
<script src="<?php echo $this->config->item('assets');?>lib/jquery.session.js"></script>
<script src="<?php echo $this->config->item('assets');?>lib/jquery.datetimepicker.js"></script>
<script src="<?php echo $this->config->item('assets');?>lib/Chart.js"></script>

<link rel="stylesheet" href="<?php echo $this->config->item('assets');?>stylesheets/bootstrap-multiselect.css" type="text/css">
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/bootstrap-multiselect-collapsible-groups.js"></script>
     
     <style>
     .multiselect{
        position: relative;
     }
.multiselect-selected-text {
  display: block;
  position: relative;
  width: 100%;
  text-align: left;
}
.multiselect .caret{
    display: inline-block;
    float: right;
    margin-top: -2px;
    position: absolute;
    right: 10px;
    text-align: right;
    top: 50%;
    width: auto;
  }
     </style>

	
	<script type="text/javascript">
        $(function() {
            var uls = $('.sidebar-nav > ul > *').clone();
            uls.addClass('visible-xs');
            $('#main-menu').append(uls.clone());
        });


			
		
		$(document).ready(function() {


			 //Multi selected dropdown script Initialization for division
                         $('#div_id').multiselect({
                               numberDisplayed: 1,
                               enableFiltering: true,
                               includeSelectAllOption: true,
                               maxHeight: 400,
                               dropUp: true

                           });


                        //Multi selected dropdown script Initialization for Industries
                         $('#ind_id').multiselect({
                               numberDisplayed: 1,
                               enableFiltering: true,
                               includeSelectAllOption: true,
                               maxHeight: 400,
                               dropUp: true
                          });

                           //Multi selected dropdown script Initialization for solutions
                          $('#sol_id').multiselect({
                               numberDisplayed: 1,
                               enableFiltering: true,
                               includeSelectAllOption: true,
                               maxHeight: 400,
                               dropUp: true
                          });
                         
                        

			//$().toastmessage('showSuccessToast', "dfksdfldskjf");
			
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
		
		<input type="hidden" id="success_msg" name="success_msg" value="<?php echo isset($success)?  $success:'';?>"/>
		<input type="hidden" id="error_msg" name="error_msg" value="<?php echo isset($error)?  $error:'';?>"/>
		
	<style type="text/css">
        #line-chart {
            height:300px;
            width:800px;
            margin: 0px auto;
            margin-top: 1em;
        }
        .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover { 
            color: #fff;
        }
        
    </style>	