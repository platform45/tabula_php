<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
		  
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['Subadmin'][1]; ?>").addClass("act");
        $.session.set("addedit",1);
        $("#new_password").qtip();
        $("#conf_password").qtip();
					
        jQuery.validator.addMethod( 'passwordMatch', function(value) {
            if(value != '')
                return /^.*(?=.{6,20})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_~\-!@#\$%\^&\*\(\)]).*$/.test(value);
            else
                return true;

        });
                                                
      	jQuery.validator.addMethod( 'emailAddress', function(value) {
            return /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,100}|[0-9]{1,3})(\]?)$/.test(value);
        },"Please enter a valid email address.");


        $('#user_form').validate({
            rules:{
                txtusername:{
                    required:true,
                    remote:{
                        url: "<?php echo base_url() ?>admin/subadmin/check_username_exists/<?php echo $edit_id ?>",
                        type: "post",
                        data: {
                            "title": function(){ return $("#txtusername").val(); }
                        }
                       
                    }
                },
                txtfname:{
                    required:true
                },
            
                'chkaccess[]':{
                    required:true
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
                txtusername:{
                    required:"Please enter username.",
                    remote:"Username already exist."
                },
                txtfname:{
                    required:"Please enter first name."
                },
              					
                'chkaccess[]':{
                    required:"Please select atleast one checkbox."
                },
                txtemail:{
                    required:"Please enter email.",
                    email:"Please enter a valid email.",
                    remote:"Email already exist."
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
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Subadmin</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/subadmin">Subadmin</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "Add"); ?> Subadmin</li>
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

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Username<span class="error" >*</span></label>
                            <div class="col-sm-6"><input type="text" <?php echo ($edit_id > 0 ? "readonly='readonly'" : ""); ?> value="<?php echo trim(set_value('txtusername', $formData['txtusername'])); ?>" id="txtusername" name="txtusername" class="form-control" maxlength="40" />
                                <span class="error"><?php echo form_error('sch_email'); ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">First Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo trim(set_value('user_first_name', $formData['txtfname'])); ?>" id="txtfname" name="txtfname" class="form-control" maxlength="40"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Last Name<span class="error" ></span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo trim(set_value('user_last_name', $formData['txtlname'])); ?>" id="txtlname" name="txtlname" class="form-control" maxlength="40"  />
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-sm-3 control-label">Email Address<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" <?php echo ($edit_id > 0 ? "readonly='readonly'" : ""); ?>  value="<?php echo set_value('user_email', $formData['txtemail']); ?>" id="txtemail" name="txtemail" class="form-control" maxlength="40" />
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="col-sm-3 control-label">New Password<span class="error" ><?php echo ($edit_id > 0 ? "" : "*") ?></span></label>
                            <div class="col-sm-6">
                                <input id="new_password" name="new_password" value="" type="password" class="form-control" title="Password must contain : 6 characters( 1 Upper, 1 lower, 1 number and 1 symbol)"/>
                                <?php if ($edit_id) { ?><span>(Enter only if you want to change.)</span><?php } ?>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="col-sm-3 control-label">Confirm Password<span class="error" ><?php echo ($edit_id > 0 ? "" : "*") ?></span></label>
                            <div class="col-sm-6">
                                <input id="conf_password" name="conf_password" value="" type="password" class="form-control" title="Password must contain : 6 characters( 1 Upper, 1 lower, 1 number and 1 symbol)"/>
                            </div>
                        </div>

                        <div class="form-group" id="access">
                            <label class=" control-label col-sm-3">Access<span class="error" >*</span></label>
                            <div class="col-sm-6">

                                <?php
                                if ($allMenu) {
                                    foreach ($allMenu as $optM) {
                                        $checked = "";
                                        if (!empty($menuAccess))
                                            if (in_array($optM['opt_optionid'], $menuAccess)) {
                                                $checked = 'checked=""';
                                            }
                                        ?>


                                        <label for="chkaccess_<?php echo $optM['opt_optionid']; ?>" class="">
                                            <input class="mycheckbox" <?php echo $checked; ?> style="width:10px;height:10px;margin-top: none;margin-right: 7px;" id="chkaccess_<?php echo $optM['opt_optionid']; ?>" name="chkaccess[]" value="<?php echo $optM['opt_optionid']; ?>" type="checkbox">
                                            <?php echo $optM['opt_option_name']; ?>
                                        </label><br>

                                        <?php
                                    }
                                }
                                ?>
                                <label id="chkaccess[]-error" class="error" for="chkaccess[]"></label>
                            </div>
                        </div>
                        <div class="form-group" id="image">
                            <label class="col-sm-3 control-label">Image<span class="error" >*</span></label>
                            <div class="col-sm-6"><input type="file"  id="image" name="image"  value=""/></div>
                        </div>
                        <div class="form-group" id="image_preview">
                            <div class="col-sm-offset-3 col-sm-6">
                                <img id="preview" name="preview" width="100" src="<?php if ($formData['new_img']) echo $this->config->item('assets') . 'upload/subadmin/' . $formData['new_img']; else echo $this->config->item('assets') . 'upload/adminuser/No_Image.jpg'; ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label></label>
                            <progress style="display:none;"></progress>
                            <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/subadmin'"/>
                            </div>
                        </div>
                    </form>	
                </div>				

            </div>
        </div>
    </div>
</div>