<title>Tabula: Profile</title>

<?php
function readMore($content, $link, $var, $id, $limit)
{
    $content = substr($content, 0, $limit);
    $content = substr($content, 0, strrpos($content, ' '));
    $content = $content . " <a href='$link?$var=$id'>Read More...</a>";
    return $content;
}

/*
* Kaustubh Bhujbal
* Profile related views
* 2 Feb 2017
*/
?>

<style>
    .share {
        position: relative;
        display: inline-block;
    }

    .tooltiptext ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .tooltiptext. ul li a {
        display: block;
        padding: 8px;
    }

    .tooltiptext ul li {
        display: inline;
        float: left;
    }

    .share .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: #290302;

        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

    / Position the tooltip / position: absolute;
        z-index: 1;
    }

    .share:hover .tooltiptext {
        visibility: visible;
    }
</style>
<section class="innercontain profilepage">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 profiltab">
                <div class="row">
                    <div class="col-sm-3 col-md-2">
                        <div class="profiledetail">
						    <div class="fileUpload btn btn-primary">
                                <span>Upload</span>
                                <div class="profileImgHover"></div>
								<?php $attributes = array('class' => 'form-horizontal', 'id' => 'profile_upload_image'); ?>
								<?php echo form_open_multipart('front/profile/upload_profile_image', $attributes) ?>
									<!--<form id="profile_upload_image" method="post" enctype="multipart/form-data" action='<?php echo base_url();?>front/profile/upload_profile_image'> -->
									<input type="file" class="upload" name="profile_image" id="profile_image">
								<?php echo form_close(); ?>
                                <a href="javascript:void(0)"><img src="<?php echo $this->session->userdata('user_image'); ?>" class="img-responsive" style="cursor:default"></a>
                            </div>
                            <!--<a href="javascript:void(0)"><img src="<?php echo $this->session->userdata('user_image'); ?>" class="img-responsive" style="cursor:default"></a>-->
                            <h2><?php echo $this->session->userdata('user_first_name'); ?></h2>
                            <!--<p>121 Points</p>-->
                        </div>
                        <ul style="margin-bottom: 15px;" class="nav nav-tabs hidden-xs" id="campTab">
                            <li class="active">
                                <a data-toggle="tab" id="bookingConfimtab" href="#tab1" aria-expanded="false"
                                   class="js-tabcollapse-panel-heading" data-parent="">
                                    Bookings
                                </a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="#tab2" id="reviewTab" aria-expanded="false"
                                   class="js-tabcollapse-panel-heading" data-parent="">
                                    Reviews
                                    <input type="hidden" id="ReviewOffset" value="0">
                                </a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" id="historytab" href="#tab3" aria-expanded="false"
                                   class="js-tabcollapse-panel-heading" data-parent="">
                                    History
                                </a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" id="your_profile" href="#tab4" aria-expanded="false"
                                   class="js-tabcollapse-panel-heading" data-parent="">
                                    Your Details
                                </a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="#tab5" id="wishlisttab" aria-expanded="false"
                                   class="js-tabcollapse-panel-heading" data-parent="">
                                    Wishlist
                                    <input type="hidden" id="wishlistOffset" value="0">
                                </a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="#tab6" aria-expanded="false" id="suggestiontab"
                                   class="js-tabcollapse-panel-heading" data-parent="">
                                    Suggestions
                                    <input type="hidden" id="suggestionOffset" value="0">
                                </a>
                            </li>
                            <li class="">
                                <a data-toggle="tab" href="" aria-expanded="false" id="signouttab"
                                   class="js-tabcollapse-panel-heading" data-parent="">
                                    Signout
                                </a>
                            </li>
                        </ul>
                        <div id="campTab-accordion" class="panel-group visible-xs"></div>
                    </div>

                    <!--                            Booking part start-->
                    <div class="col-sm-9 col-md-10">
                        <div class="tab-content" id="campTabContent">
                            <div id="tab1" class="tab-pane fade active in">
                                <input type="text" id="profile_confirm_booking_offset" value="0" hidden></input>
                                <div class="containbox bookinglist mr-top-0" id="confirm_booking_list_div">

                                </div>
                            </div>
<!--                            Booking part end-->



                            <div id="tab2" class="tab-pane fade">
                                <div class="containbox bookinglist reviewsbox mr-top-0">
                                </div>
                                <div class="row containbox ">
									<div class='restaurant_filter_load_more search_btn' id='reviewLoadMore' hidden>View More</div>
							    </div>
                            </div>

<!--                            History tab start-->
                            <div id="tab3" class="tab-pane fade">
                                <input type="text" id="profile_history_booking_offset" value="0" hidden></input>
                                <div class="containbox bookinglist mr-top-0" id="history_booking_list_div">

                                </div>
                            </div>
<!--                            History tab end-->

                            <div id="tab5" class="tab-pane fade">
                                <div class="row containbox bookinglist wishlist mr-top-0">
                                </div>
                                <div class="row containbox ">
                                    <center><a href="javascript:void(0)" id="wishlistloadMore" role="button"
                                               class="btn btn-danger" style="display:none;">Load More</a></center>
                                    <p class="clearfix">&nbsp;</p>
                                </div>
                            </div>
                            <div id="tab6" class="tab-pane fade">
                                <div class="row containbox bookinglist mr-top-0 suggestionprofile">
                                </div>
                                <div class="row containbox ">
                                    <center><a href="javascript:void(0)" id="suggestionloadMore" role="button"
                                               class="btn btn-danger" style="display:none;">Load More</a></center>
                                    <p class="clearfix">&nbsp;</p>
                                </div>
                            </div>
                            <div id="tab4" class="tab-pane fade">
                                <form id="profile_form" method="POST" name="profile_form" enctype="multipart/form-data">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                           value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    <input type="hidden" name="userId" id="userId"
                                           value="<?php echo $this->session->userdata('user_id'); ?>">
                                    <div class="containbox bookinglist profiledetail mr-top-0">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="textfield">
                                                    <input type="text" class="form-control" placeholder="Full Name"
                                                           id="full_name" name="full_name" value="">
													<div id="user_name_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="textfield">
                                                    <input type="text" class="form-control" placeholder="Email Address"
                                                           maxlength="40" id="email" name="email" value="" readonly/>
                                                </div>
												<div id="email_error_placement">
													</div>
													
                                            </div>
                                        </div>
                                        <div class="row">
										
                                            <div class="col-sm-6">
                                                <div class="textfield">
                                                    <input type="password" class="form-control" autocomplete="off"
                                                           placeholder="Password: Enter only if you want to change" id="password" name="password"
                                                           value="">
													<div id="password_error_placement">
													</div>
													
												</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="textfield">
                                                    <input type="password" class="form-control" autocomplete="off"
                                                           placeholder="Confirm Password" id="conf_password"
                                                           name="conf_password" value="">
													<div id="conf_pass_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="textfield">
                                                    <input type="text" class="form-control" placeholder="Contact Number"
                                                           id="contact" name="contact" value="">
													<div id="contact_number_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class='input-group date' id='dob_profile_datepicker'>
                                                        <input type='text' class="form-control"
                                                               placeholder="Date of Birth" id="dob_profile"
                                                               name="dob_profile" value=""/>
														<span class="input-group-addon">
															<span class="droparrow"></span>
														</span>
                                                    </div>
													<div id="profile_dob_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <select class="form-control" id="gender_profile" name="gender">
                                                        <option value="">Gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
													<div id="gender_profile_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <select class="form-control" id="country" name="country">
                                                        <option value="47">South Africa</option>
                                                    </select>
													<div id="country_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <select class="form-control" id="state" name="state">
                                                        <?php foreach ($states as $state) { ?>
                                                            <option value="<?php echo $state->state_id ?>"><?php echo $state->state_name; ?></option>
                                                        <?php } ?>
                                                    </select>
													<div id="state_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <select class="form-control" id="city" name="city">
                                                        <option value="">City</option>
                                                    </select>
													<div id="city_profile_error_placement">
													</div>
													
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 padding-top-20">
                                                <label class="control control--radio">
                                                    Push Notifications
                                                    <input type="checkbox" name="radio" id="notification_flag">
                                                    <div class="control__indicator"></div>
                                                </label>
                                            </div>
											
                                        </div>
										
										<div class="row">
                                            <div class="col-sm-12 login-button-outer padding-bottom-20">
                                                <button type="submit" class="login-button" id="button-profile-save">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
										
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
</body>
</html>