<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
<?php header("Cache-Control: no-store, no-cache, must-revalidate"); ?>
    <link rel="icon" href="favicon.ico">

    <!--    <title>Tabula</title>-->
    <link rel="icon" href="<?php echo $this->config->item('assets');?>images/favicon-tab.ico" type="image/x-icon">
    <link href="<?php echo $this->config->item('front_assets'); ?>css/style.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('front_assets'); ?>css/custom.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('front_assets'); ?>css/slick.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('front_assets'); ?>css/bootstrap-select.css" rel="stylesheet">
    <link href="<?php echo $this->config->item('front_assets'); ?>css/bootstrap-notifications.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">

    <link rel="stylesheet" href="<?php echo $this->config->item('front_assets'); ?>css/jquery.fancybox.css?v=2.1.5"
          type="text/css" media="screen"/>
    <link rel="stylesheet"
          href="<?php echo $this->config->item('front_assets'); ?>css/helpers/jquery.fancybox-buttons.css?v=1.0.5"
          type="text/css" media="screen"/>
    <link rel="stylesheet" type="text/css"
          href="<?php echo $this->config->item('assets'); ?>stylesheets/toast/toastmessage.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo $this->config->item('front_assets'); ?>css/slick-theme.css"/>
    <link rel="stylesheet" type="text/css"
          href="<?php echo $this->config->item('front_assets'); ?>css/loading.min.css"/>
    <link href="<?php echo $this->config->item('front_assets'); ?>css/slider.css" rel="stylesheet">
	
	<!--<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css"> -->
	<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('front_assets'); ?>css/bootstrap-multiselect.css">


<!--toast-container toast-position-top-right-->
    <style>
		.toast-type-error, .toast-type-success{
			font-family: TabulaBook !important;
		}
        .user_favourite {
            background-color: red !important;
        }
        #loadMore {
            color: #444;
            border: 1px solid #CCC;
            background: #DDD;
            box-shadow: 0 0 5px -1px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            vertical-align: middle;
            max-width: 100px;
            padding: 5px;
            text-align: center;
            margin: 0 auto !important;
            margin-top: 10px !important;
        }
        #profile_confirm_booking_load_more, #profile_history_booking_load_more, #reviewLoadMore{
            color: #444;
            border: 1px solid #CCC;
            background: #DDD;
            box-shadow: 0 0 5px -1px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            vertical-align: middle;
            max-width: 100px;
            padding: 5px;
            text-align: center;
            margin: 0 auto !important;
            margin-top: 10px !important;
            margin-bottom: 10px !important;
        }
		#after_booking_detials_modal_btn{
			color: white;
			background-color: #e41444;
			border-radius: 30px;
			width: 20%;
		}
        .restaurant_filter_load_more {
            padding: 5px;
            text-align: center;
            background: #e41444 !important;
            color: white !important;
            border-radius: 30px;
        }
        #load_image{
            padding: 5px;
            text-align: center;
        }
        .date {
            margin-top: 0px !important;
        }
        .read_more {
            float: right;
            padding: 5px;
            margin: 5px;
        }
        .review_error_placement {
            color: red;
        }
        .slider-selection {
            background: #BABABA;
        }
        #user_review_panel{
            margin: 0 auto;
            width: 70%;
        }
        .slider-horizontal{
            width: 100% !important;
        }
        .a_link_disabled {
            pointer-events: none;
            cursor: default;
        }
        #share_link_btn, .histroy_list_btn, .booking_list_btn{
            pointer-events: none;
            cursor: default;
        }
        .notificationDiv{
            float: right;
        }
        #btn_review_submit, #btn_review_cancel{
            background-color: #e41444  !important;
            border-color: #e41444  !important;
        }
        .ratting{
            width: 61px !important;
            text-align: right !important;
        }
        #close_social_icons{
            color: #e41444;
        }
        header .login_btn_content a.welcomeuser{
            padding-left: 2px !important;
        }
        #header_profile_logo{
            height: 35px !important;
            width: 40px !important;
            border-radius: 70px !important;
			margin-left: 2px;
        }
        .no_gallery_images_msg
        {
            text-align: center;
            font-size: 22px !important;
            opacity: 0.8 !important;
            margin-bottom: 15px !important;
        }
        .profile_restaurant_image{
            max-height: 106px !important;
            width: 154px;
        }
		
		#booking_number_of_people{
			border-top: none;
			border-left: none;
			padding-left: 0;
			padding-right: 0;
			left: 0;
			right: 0;
			color: #000;
			font-size: 18px;
			height: 30px;
			background-color: transparent;
			box-shadow: none;
			-moz-border-radius: 0;
			-webkit-border-radius: 0;
			border-radius: 0;
			border-right: none;
			border-bottom-style: dotted;
			border-bottom-color: gray
		}
		#front_booking_error_div{
			font-size: 18px;
			color: red;
			padding: 0;
		}
		#front_next_four_available_time_slots, #last_minute_cancellation_div{
			padding: 0;
			margin-left: -15px;
		}
		#front_available_time_slot_div button{
			background-color: #e41444;
			color: white;
		}
		#after_booking_detials_modal{
			text-align: center;
		}
		.booking_cancel_text{
			text-align: right;
			font-weight: 600;
			letter-spacing: 2px;
			width: 0px !important;
			color: #e41444 !important;
		}
		#sign_up_gender_error_placement, #sign_up_city_error_placement, #sign_up_state_error_placement, #sign_up_country_error_placement{
			color: #ff0000;
			font-size: 12px;
			font-weight: 600;
		}
		a.contact_mail_underline:hover {
			text-decoration: underline !important;
		}
		select.selectpicker:hover {
			color: black !important;
		}
    </style>

    <!--     <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
         <script language="javascript" type="text/javascript">
           var map;
           var geocoder;
            function InitializeMap() {
                console.log("Initialize");
                var latlng = new google.maps.LatLng(-34.397, 150.644);
                var myOptions =
                {
                    zoom: 8,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    disableDefaultUI: true
                };
                map = new google.maps.Map(document.getElementById("map"), myOptions);
            }
    //         window.onload = InitializeMap;
        </script>-->

</head>
<body>

<input type="text" id="base_url" value="<?php echo base_url(); ?>" hidden></input>
<input type="text" id="logged_in_user_id" value="<?php echo $this->session->userdata('user_id'); ?>" hidden></input>

<header>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-1 col-sm-3 col-md-4 col1">
                <button type="button" class="navbar-toggle collapsed menu_icon" data-toggle="collapse"
                        data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="col-xs-5 col-sm-4 col-md-4 col2">
                <a href="<?php echo base_url(); ?>home" class="logo"><img
                            src="<?php echo $this->config->item('front_assets'); ?>images/logo.png"
                            class="img-responsive"></a>
            </div>
            <?php if (!$this->session->userdata('user_id')) : ?>
                <div class="col-xs-6 col-sm-5 col-md-4 col3">
                    <div class="login_btn_content">
                        <a class="search-button filter-btn" id="sign_in_btn" data-target="#loginModal" data-toggle="modal">Sign in</a>
                        <a class="search-button filter-btn" id="sign_up_btn" data-target="#signupModal" data-toggle="modal">Sign Up</a>
                    </div>
                </div>

            <?php else : ?>
<?php 
 $notifications = $this->notificationmodel->front_get_notification_records($this->session->userdata('user_id'));
 $noticount_count = $this->notificationmodel->get_total_notification_records($this->session->userdata('user_id'));
?>
                <div class="col-xs-6 col-sm-5 col-md-4 col3">

                    <ul class="nav navbar-nav notificationsdropdown">
                        <li class="dropdown dropdown-notifications">
                            <a class="dropdown-toggle" id="dLabel" href="/page.html" data-target="#" data-toggle="dropdown" role="button">
                                <i class="glyphicon glyphicon-bell notification-icon" data-count="<?php echo !empty($noticount_count) ? $noticount_count : 0; ?>"></i>
                            </a>

                            <div class="dropdown-container" aria-labelledby="dLabel" role="menu">

                                <ul class="dropdown-menu">
								
								<?php if($noticount_count > 0) {
									foreach($notifications as $notification) { ?>
										<li class="notification">
											<div class="media">
												<div class="media-body">
													<strong class="notification-title"><?php print_r($notification['notification_message']); ?></strong>
													
													<div class="notification-meta">
														<small class="timestamp"><?php print_r($notification['notification_date']); ?></small>
													</div>
												</div>
											</div>
										</li>
								<?php } 
								} else { ?>
									<li class="notification" id="no_notification_found">
											<div class="media">
												<div class="media-body">
													<div>
													<strong class="notification-title" style="text-align: center;">No notification found.</strong>
													</div>
													<div class="notification_img">
													<img src="<?php echo base_url() ?>assets/images/Notification-Bell.jpg">
													</div>
													
												</div>
											</div>
										</li>
									
								<?php  }?>
                                    
                                </ul>
                            </div><!-- /dropdown-container -->
                        </li><!-- /dropdown -->
                    </ul>


                    <div class="login_btn_content login_responsive">

                        <a class="welcomeuser" href="<?php echo base_url(); ?>profile">
<!--                            <i class="fa fa-user" aria-hidden="true"></i>-->
                            <img src="<?php echo $this->session->userdata('user_image'); ?>" id="header_profile_logo">
                            <?php echo $this->session->userdata('user_first_name'); ?>
                        </a>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
	
    <div class="navbar_content collapsed" style="display:none;">
        <a href="<?php echo base_url(); ?>home" class="logo_submenu"><img
                    src="<?php echo $this->config->item('front_assets'); ?>images/tabula_grey.png"
                    class="img-responsive"></a>
        <div class="container">
            <div class="row">
                <div class="col-md-4"><a href="<?php echo base_url(); ?>home">Home</a>
                    <a href="<?php echo base_url(); ?>search">Search Restaurants</a>
                    <a href="<?php echo base_url(); ?>news">News</a>
                </div>
                <div class="col-md-4"><a href="<?php echo base_url(); ?>about-us">About Tabula</a>
                    <a href="<?php echo base_url(); ?>terms-and-condition">Terms & conditions</a>
                    <a href="<?php echo base_url(); ?>contact-us">Contact</a>
                </div>
                <div class="col-md-4">
                    <a href="javascript:void(0)"><span class="uic-fb"></span></a>
                    <a href="javascript:void(0)"><span class="uic-tw"></span></a>
                    <a href="javascript:void(0)"><span class="uic-insta"></span></a>
                </div>
            </div>
        </div>
    </div>
</header>

