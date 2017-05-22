<div id="loginModal" class="modal fade bs-example-modal-lg search-poup" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <a href="#" class="loginclosefield" data-dismiss="modal" aria-label="Close"><span class="uic-close"></span></a>
    <div class="modal-dialog modal-lg" role="document">
        <div class="loginpopup">
            <div class="modal-body">
                <div class="row">
                    <?php $attributes = array('class' => 'form-horizontal', 'id' => 'login_form'); ?>
                    <!--                    --><?php //echo form_open_multipart('front/home/login', $attributes) ?>
                    <form id="login_form">
                        <div class="col-sm-12">
                            <h3 class="popupheading">Welcome to Tabula</h3>
                        </div>
                        <div class="col-sm-12 textfield">
                            <input type="text" id="user_type" name="user_type" value="2" hidden>
                        </div>

                        <div class="col-sm-12 ">
                            <div class="textfield">
                                <input type="text" id="user_email" class="form-control" name="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="textfield">
                                <input type="password" id="user_password" class="form-control" name="password" placeholder="Password">
                            </div>
                        </div>

                        <div class="col-sm-12 " id="error_message_for_user_login" style="color: red;">
                        </div>

                        <div class="col-sm-12 login-button-outer">
                            <button id="login_form_submit" type="button" class="login-button">Sign In</button>
                        </div>
                    </form>
                    <!--                    --><?php //echo form_close(); ?>
                    <div class="col-sm-12 text-right padding-top-10">
                        <a href="javascript:void(0);" id="forget_pass">Forgot Password?</a>
                    </div>
                    <?php $attributes = array('class' => 'form-horizontal', 'id' => 'forget_password'); ?>
                    <?php echo form_open_multipart('front/home/forget_password', $attributes) ?>
					<form class='form-horizontal' id="forget_password">
						<div id="open_form" style="display:none" class="col-sm-12 ">
							<div class="textfield">
								<input type="email" class="form-control" id="forget_pass_email" name="email" placeholder="Email">
							</div>
							<div class="col-sm-12 " id="error_message_for_forget_pass" style="color: red;font-size: 12px;font-weight: 600;padding: 0;">
							</div>
							<div class="col-sm-12 login-button-outer">
								<button type="button" id="forget_pass_form_submit" class="login-button">Send</button>
							</div>
						</div>
					</form>
                   <!-- <?php echo form_close(); ?>-->
                    <div class="col-sm-12 text-center padding-top-20">
                        New to Tabula? <a href="javascript:void(0);" id="sign_up_modal">Create an account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="signupModal" class="modal fade bs-example-modal-lg search-poup" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel">
    <a href="#" class="loginclosefield" data-dismiss="modal" aria-label="Close"><span class="uic-close"></span></a>
    <div class="modal-dialog modal-lg" role="document">
        <div class="loginpopup">
            <div class="modal-body">
                <div class="row">
                    <?php $attributes = array('class' => 'form-horizontal', 'id' => 'sign_form'); ?>
                    <?php echo form_open_multipart('front/home/registration', $attributes) ?>
                    <div class="col-sm-12">
                        <h3 class="popupheading">Welcome to Tabula</h3>
                    </div>

                    <div class="tabContainer">

                        <div class="tab-content ">
                            <div class="tab-pane active" id="guest_tab">

                                <div class="col-sm-12">
                                    <div class="fileUpload btn btn-primary">
                                        <span>Upload</span>
                                        <input type="file" class="upload" name="profile_image">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="textfield">
                                        <input type="text" class="form-control" name="first_name"
                                               placeholder="Full Name">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="textfield">
                                        <input type="text" class="form-control" id="email_address" name="email_address"
                                               placeholder="Email Address">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="textfield">
                                        <input type="password" class="form-control" id="password" name="password"
                                               placeholder="Password">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="textfield">
                                        <input type="password" class="form-control" name="conf_password"
                                               id="confirm_password" placeholder="Confirm Password">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="textfield">
                                        <input type="text" class="form-control" id="contact_number"
                                               name="contact_number" placeholder="Contact Number">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="textfield">
                                        <input type="text" class="form-control" id="dob" name="dob"
                                               placeholder="Date of Birth">
                                    </div>
                                </div>

                                <div class="col-sm-12 textfield">
                                    <select class="form-control" name="gender" id="gender">
                                        <option value="">Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
									<div id="sign_up_gender_error_placement"></div>
                                </div>
                                <div class="col-sm-12 textfield">
                                    <select class="form-control" name="country" id="country">
                                        <option value="47">South Africa</option>
                                    </select>
									<div id="sign_up_country_error_placement"></div>
                                </div>
                                <div class="col-sm-12 textfield">
                                    <select class="form-control" name="state" id="state">
                                        <option value="">Select State</option>
                                        <?php foreach ($states as $state) {
                                            ?>
                                            <option value="<?php echo $state->state_id ?>"><?php echo $state->state_name; ?></option>
                                        <?php } ?>
                                    </select>
									<div id="sign_up_state_error_placement"></div>
                                </div>
                                <div class="col-sm-12 textfield">
                                    <select class="form-control" name="city" id="city">
                                        <option value="">Select City</option>
                                        <!--                                        <option value="">Ciasdfdfty</option>-->
                                    </select>
									<div id="sign_up_city_error_placement"></div>
                                </div>
                                <div class="col-sm-12 padding-top-20">
                                    <label class="control control--radio">
                                        I Accept & Agree to <a href="<?php echo base_url(); ?>terms-and-condition" target="_blank">Terms & Conditions</a>
                                        <input type="checkbox" name="radio">
                                        <div class="control__indicator"></div>
                                    </label>
                                </div>

                                <div class="col-sm-12 login-button-outer">
                                    <input type="hidden" class="form-control" id="base_url"
                                           value="<?php echo base_url(); ?>" placeholder="">
                                    <input type="hidden" class="form-control" id="user_type" name="user_type" value="2"
                                           placeholder="Date of Birth">
                                    <input type="submit" name="submit" id="sign_up_submit" class="login-button" value="Sign Up"/>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


