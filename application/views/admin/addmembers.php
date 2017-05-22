<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.8.7.custom.min.js"></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/datetimpicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<script type="text/javascript">

    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu');
?>
        <?php if(!$edit_id) 
        { ?>
         $("#region_id").html('<option value="">Select State</option>');
            $("#city_id").html('<option value="">Select City</option>');
            $.post("<?php echo $this->config->item("admin_url") . 'cities/get_region_by_country'; ?>",
            {country_id: $("#country_id").val()},
            function(option_list){
                if( option_list != 'false' )
                    $("#region_id").html(option_list);
                else
                    $("#region_id").html('<option value="">Select State</option>');
                $("#region_id").trigger("change");
            });
       <?php } ?> 

        $(".sidebar-nav #menu<?php echo $arr['Guests'][1]; ?>").addClass("act");
        $.session.set("addedit",1);
        $("#new_password").qtip();
        $("#conf_password").qtip();

        $( "#date_of_birth" ).datepicker({ dateFormat: 'dd-mm-yy',timeFormat: 'hh:mm TT',  maxDate : 'now' ,use24hours: false, changeMonth:true, changeYear:true});

        $("#country_id").change(function(){
            $("#region_id").html('<option value="">Select State</option>');
            $("#city_id").html('<option value="">Select City</option>');
            $.post("<?php echo $this->config->item("admin_url") . 'cities/get_region_by_country'; ?>",
            {country_id: $("#country_id").val()},
            function(option_list){
                if( option_list != 'false' )
                    $("#region_id").html(option_list);
                else
                    $("#region_id").html('<option value="">Select State</option>');
                $("#region_id").trigger("change");
            });
        })

        $("#region_id").change(function(){
            $("#city_id").html('<option value="">Select City</option>');
            $.post("<?php echo $this->config->item("admin_url") . 'cities/get_city_by_region'; ?>",
            {region_id: $("#region_id").val()},
            function(option_list){
                if( option_list != 'false' )
                    $("#city_id").html(option_list);
                else
                    $("#city_id").html('<option value="">Select City</option>');
            });
        })

        jQuery.validator.addMethod( 'emailAddress', function(value) {
            return /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,100}|[0-9]{1,3})(\]?)$/.test(value);
        },"Please enter a valid email address.");

        jQuery.validator.addMethod( 'passwordMatch', function(value) {
            if(value != '')
                return /^.*(?=.{6,20})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_~\-!@#\$%\^&\*\(\)]).*$/.test(value);
            else
                return true;
        });

        $('#user_form').validate({
            rules:{
                txtfname:{
                    required:true
                },
                date_of_birth:{
                    required:true
                },
                gender:{
                    required:true
                },
                country_id:{
                    required:true
                },
                region_id:{
                    required:true
                },
                city_id:{
                    required:true
                },
                notify: {
                    required : true
                },
                mvg_points : {
                    required : true,
                    number : true
                },
                txtemail:{
                    required:true,
                    emailAddress:true,
                    remote:{
                        url: "<?php echo base_url() ?>admin/members/check_email_exist/<?php echo $edit_id ?>",
                        type: "post",
                        data: {
                            "title": function(){ return $("#txtemail").val(); }
                        }
                    }

                },
                txtcontact:{
                    required:true,
                    minlength:10,
                    maxlength:10,
                    number: true
                },
                new_password:{
<?php if (!$edit_id) { ?>
                        required:true,
<?php } ?>
                    minlength:6,
                    passwordMatch:true
                },
                conf_password:{
<?php if (!$edit_id) { ?>
                        required:true,
<?php } ?>
                    equalTo: "#new_password"
                },
                image : {
<?php if (!$edit_id) { ?>
                        required:true,
<?php } ?>
                    accept:'jpg,jpeg'
                }
            },
            messages:{
                txtfname:{
                    required:"Please enter first name."
                },
                date_of_birth:{
                    required:"Please select date of birth."
                },
                gender:{
                    required:"Please select gender."
                },
                country_id:{
                    required:"Please select country."
                },
                region_id:{
                    required:"Please select state."
                },
                city_id:{
                    required:"Please select city."
                },
                txtemail:{
                    required:"Please enter email.",
                    email:"Please enter a valid email.",
                    remote:"Email already exist."
                },
                notify: {
                    required : "Please set a notification"
                },
                mvg_points : {
                    required : "Please enter mvg points",
                    number : "Please enter a valid number."
                },
                txtcontact:{
                    required:"Please enter contact no."
                },
                new_password:{
<?php if (!$edit_id) { ?>
                        required:"Please enter new password.",
<?php } ?>
                    minlength:"Please enter password atleast 6 characters long.",
                    passwordMatch:"Password Invalid. Password must contain:6 characters ( 1 Upper, 1 lower, 1 number and 1 symbol)"
                },
                conf_password:{
<?php if (!$edit_id) { ?>
                        required:"Please enter confirm password.",
<?php } ?>
                    equalTo: "Please enter same password again."
                },
                image:{
<?php if (!$edit_id) { ?>
                        required:"Please select an image to upload.",
<?php } ?>
                    accept: "Extension should be jpg, jpeg."
                }
            }
        });


    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Guest</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/users">Guests</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "Add"); ?> Guest</li>
        </ul>
    </div>
    <div class="main-content">

        <div class="panel panel-default" align="left" style="border:0px; margin:0px;">
            <div class="panel-body" >
                <div class="dialog1">
                    <div class="error " style="">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-7" style="margin-bottom: 16px;">* indicates required field.</div>
                    </div>
                    <form id="user_form" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="new_img" name="new_img" value="<?php echo set_value('new_img'); ?>"/>
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                        <input type="hidden" id="loyalty_id" name="loyalty_id" value="<?php echo $loyalty_id; ?>"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo trim(set_value('user_first_name', $formData['txtfname'])); ?>" id="txtfname" name="txtfname" class="form-control" maxlength="40" />
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" <?php echo ($edit_id > 0 ? "readonly='readonly'" : ""); ?>  value="<?php echo set_value('user_email', $formData['txtemail']); ?>" id="txtemail" name="txtemail" class="form-control" maxlength="40" />
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="col-sm-3 control-label">New Password<span class="error" ><?php echo ($edit_id > 0 ? "" : "*") ?></span></label>
                            <div class="col-sm-6">
                                <input id="new_password" name="new_password" value="" type="password" class="form-control" title="Password must contain:6 characters ( 1 Upper, 1 lower, 1 number and 1 symbol)"/><!--<b>Note:</b>Password must contain : 6 characters( 1 Upper, 1 lower, 1 number and 1 symbol)-->
                                <?php if ($edit_id) { ?><span>(Enter only if you want to change.)</span><?php } ?>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="col-sm-3 control-label">Confirm Password<span class="error" ><?php echo ($edit_id > 0 ? "" : "*") ?></span></label>
                            <div class="col-sm-6">
                                <input id="conf_password" name="conf_password" value="" type="password" class="form-control" title="Password must contain : 6 characters( 1 Upper, 1 lower, 1 number and 1 symbol)"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Date of Birth<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <?php
                                $timestamp = strtotime($formData['date_of_birth']);
                                $newDate = date('d-m-Y', $timestamp);
                                ?>
                                <input type="text" value="<?php
                                if ($edit_id) {
                                    echo set_value('date_of_birth', $newDate);
                                }
                                ?>"    id="date_of_birth" name="date_of_birth" class="form-control" readonly/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Gender<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="radio" <?php if ($formData['gender'] == "Male") echo "checked"; ?> value="Male"  id="gender" name="gender"  /> Male
                                <input type="radio" <?php if ($formData['gender'] == "Female") echo "checked"; ?> value="Female"  id="gender" name="gender"   /> Female
                            </div>
                            <div class="col-sm-6"><label id="gender-error" class="error" for="gender"></label></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Contact No.<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo set_value('user_contact', $formData['txtcontact']); ?>" id="txtcontact" name="txtcontact" class="form-control" maxlength="50" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="country_id" id="country_id" aria-invalid="false" disabled="true" >
                                    <option value="" >Select Country</option>
                                    <?php foreach ($countriesData as $res) {
                                        ?>
                                        <option <?php echo ($res['cou_id'] == '47' ? "selected='selected'" : ""); ?>  value="<?php echo $res['cou_id']; ?>"><?php echo htmlentities(stripslashes($res['cou_name'])); ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">State<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="region_id" id="region_id" aria-invalid="false" >
                                    <option value="" >Select State</option>
                                    <?php 
                                    if (!empty($regionsData)) {
                                        foreach ($regionsData as $res) {
                                           ?>
                                            <option <?php echo ($res['region_id'] == $formData['region_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $res['region_id']; ?>"><?php  echo htmlentities(stripslashes($res['region_name'])); ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">City<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="city_id" id="city_id" aria-invalid="false" >
                                    <option value="" >Select City</option>
                                    <?php
                                    if (!empty($citiesData)) {
                                        foreach ($citiesData as $res1) {
                                            ?>
                                            <option <?php echo ($res1['city_id'] == $formData['city_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $res1['city_id']; ?>"><?php echo htmlentities(stripslashes($res1['city_name'])); ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="image">
                            <label class="col-sm-3 control-label">Profile Picture<span class="error" >*</span></label>
                            <div class="col-sm-6"><input type="file"  id="image" name="image"  value=""/></div>
                        </div>
                        <div class="form-group" id="image_preview">
                            <div class="col-sm-offset-3 col-sm-6">
                                <img id="preview" name="preview" width="100" height="100" src="<?php if ($formData['new_img']) echo $this->config->item('assets') . 'upload/member/' . $formData['new_img']; else echo $this->config->item('assets') . 'upload/adminuser/No-image.jpg'; ?>"/>
                                <p><b>Note:</b> Image should be jpg, jpeg.</p>
                            </div>
                        </div>
                          <?php if($edit_id) {
                         ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Push Notifications<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="radio" <?php if ($formData['notify'] == "1") echo "checked"; ?> value="1"  id="notify" name="notify"  /> On
                                <input type="radio" <?php if ($formData['notify'] == "0") echo "checked"; ?> value="0"  id="notify" name="notify"   /> Off
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">MVG Programme<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="radio" <?php if ($formData['mvg_points'] == "1") echo "checked"; ?> value="1"  id="mvg_points" name="mvg_points"  /> On
                                <input type="radio" <?php if ($formData['mvg_points'] == "0") echo "checked"; ?> value="0"  id="mvg_points" name="mvg_points"   /> Off
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label></label>
                            <progress style="display:none;"></progress>
                            <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/users'"/>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>