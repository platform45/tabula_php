<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/validation/user_validation.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript">
  var user_type = '';
  $(document).ready(function(){

    user_type = '<?php echo $this->session->userdata("user_type"); ?>';
    $("#new_password").qtip();
    $("#conf_password").qtip();


    jQuery.validator.addMethod( 'passwordMatch', function(value) {
      return /^.*(?=.{6,20})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_~\-!@#\$%\^&\*\(\)]).*$/.test(value);

    });

    $('#password_form').validate({
      rules:{
        old_password:{
          required:true,
          remote: {

            url:'<?php echo base_url(); ?>admin/user/check_old_password',
            type:'POST',
            data: {
              username: function(){
                return "<?php echo $this->session->userdata('user_username'); ?>";
              },
              password: function(){
                return $("#old_password").val();
              }
            }
          }
        },
        new_password:{
          required:true,
          minlength:6,
          passwordMatch:true
        },
        conf_password:{
          required:true,
          equalTo: "#new_password"
        }
      },
      messages:{
        old_password:{
          required:"Please enter your old password.",
          remote: "Invalid old password."
        },
        new_password:{
          required:"Please enter your new password.",
          minlength:"Please enter password atleast 6 characters long.",
          passwordMatch:"Please enter password as require format."
        },
        conf_password:{
          required:"Please confirm your new password.",
          equalTo: "New and confirm password must match."
        }
      }
    });
});
</script>
<div class="content">
  <div class="header">
    <?php    if( $this->session->userdata('user_type') == 3 ) ?><h1 class="page-title">My Account</h1><?php  ?>
    <ul class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
      <li class="active">My Account</li>
    </ul>
  </div>
  <div class="main-content">

    <ul class="nav nav-tabs">
      <li class="active"><a href="#home" data-toggle="tab">My Account</a></li>
      <li><a href="#profile" data-toggle="tab">Change Password</a></li>
    </ul>

    <div class="row">
      <br>
      <div id="myTabContent" class="tab-content">
        <div class="tab-pane active in" id="home">
          <div class="dialog1">
            <div class="error " style="">
              <label class="col-sm-3 control-label"></label>
              <div class="col-sm-7" style="margin-bottom: 16px;">* indicates required field.</div>
            </div>
            <div class="panel panel-default" align="left" style="border:0px;">
              <div class="panel-body" >

                <form id="user_form" action="<?php echo base_url(); ?>admin/user" method="POST" enctype="multipart/form-data"  class="form-horizontal" >
                  <input type="hidden" id="new_img" name="new_img" value="<?php echo $this->session->userdata('user_image'); ?>"/>

                  <?php $user_type = $this->session->userdata('user_type');
                  if ( $user_type != 3 ) { ?>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Username<span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input type="text" readonly value="<?php echo $this->session->userdata('user_username'); ?>" id="txtusername" name="txtusername" class="form-control" />
                    </div>
                  </div>
                  <?php } ?>
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo ( $user_type == 3 ? 'Restaurant Name' : 'First Name' ); ?><span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input type="text" maxlength="50" value="<?php echo htmlentities(trim($this->session->userdata('user_first_name'))); ?>" id="txtfname" name="txtfname" class="form-control" />
                    </div>
                  </div>
                  <?php if ( $user_type != 3 ) { ?>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Last Name<span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input type="text" maxlength="50" value="<?php echo htmlentities(trim($this->session->userdata('user_last_name'))); ?>" id="txtlname" name="txtlname" class="form-control" />
                    </div>
                  </div>
                  <?php } ?>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Email<span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input type="text" maxlength="50" value="<?php echo trim($this->session->userdata('user_email')); ?>" id="txtemail" name="txtemail" class="form-control" <?php echo ( $user_type == 3 ) ? "readonly" : ""; ?>/>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-3 control-label">Image<span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input type="file"  id="image" name="image"  value=""/>
                      <progress style="display:none;"></progress>
                    </div>
                  </div>
                  <div class="form-group" >
                    <div class="col-sm-offset-3 col-sm-6">
                      <?php
                      if($this->session->userdata('user_image')=="")
                      {
                        $image = $this->config->item('assets')."upload/adminuser/No_Image.jpg";
                      }
                      else
                      {
                        if( $this->session->userdata('user_type') == 3 )
                          $image = $this->config->item('assets').'upload/member/'.$this->session->userdata('user_image');
                        else
                          $image = $this->config->item('assets').'upload/adminuser/'.$this->session->userdata('user_image');
                      }
                      ?>
                      <img id="preview" name="preview" width="100" src="<?php echo $image ;?>"/>
                      <div> <p><b>Note:</b> Image should be jpg, jpeg.</p></div>
                    </div>
                  </div>
                  <div class="form-group" >
                    <div class="col-sm-offset-3 col-sm-6">
                      <input type="submit" value="Save" class="btn btn-primary"/>
                      <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/dashboard'"/>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="profile">

          <div class="dialog1">
            <div class="error " style="">
              <label class="col-sm-3 control-label"></label>
              <div class="col-sm-7" style="margin-bottom: 16px;">* indicates required field.</div>
            </div>
            <div class="panel panel-default" align="left" style="border:0px;">
              <div class="panel-body" >
                <form id="password_form" method="POST" action="<?php echo base_url(); ?>admin/user/reset_password"  class="form-horizontal" >
                  <div class="form-group" >
                    <label class="col-sm-3 control-label">Old Password<span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input id="old_password" name="old_password" value="" type="password" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group" >
                    <label class="col-sm-3 control-label">New Password<span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input id="new_password" name="new_password" value="" type="password" class="form-control" title="Password must contain : 6 characters( 1 Upper, 1 lower, 1 number and 1 symbol)"/>
                    </div>
                  </div>
                  <div class="form-group" >
                    <label class="col-sm-3 control-label">Confirm Password<span class="error" >*</span></label>
                    <div class="col-sm-6">
                      <input id="conf_password" name="conf_password" value="" type="password" class="form-control" title="Password must contain : 6 characters( 1 Upper, 1 lower, 1 number and 1 symbol)"/>
                    </div>
                  </div>
                  <div class="form-group" >
                    <div class="col-sm-offset-3 col-sm-6">
                      <input id="submit" name="submit" value="Reset Password" type="submit" class="btn btn-primary"/>
                      <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary " onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/dashboard'"/>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="btn-toolbar list-toolbar">
      </div>
    </div>
  </div>
</div>