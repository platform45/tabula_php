<?php header("Cache-Control: no-store, no-cache, must-revalidate"); ?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="<?php echo $this->config->item('assets'); ?>images/favicon-tab.ico">
    <title>Reset Password</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <?php
      preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $captcha['image'], $result);
      $captcha_image = array_pop($result);
    ?>
    <style>
      .captcha-image {
        width: 150px;
        height: 35px;
        float: left;
        margin-right: 10px;
        margin-bottom: 10px;
        background: url('<?php echo $captcha_image ?>') no-repeat center center;
      }
    </style>

    <?php $this->load->view('admin/header'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
  </head>
  <script type="text/javascript">
    $(document).ready(function(){

      // Password Validation
      $.validator.addMethod("pwcheck", function(value) {
          return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]/.test(value)
      },"Password must contain atleast one number, upper case alphabet and special character.");

      $('#reset_password_form').validate({
        rules:{
          password_text: {
            required: true,
            minlength: 6,
            pwcheck: true
          },
          confirm_password_text: {
            required: true,
            equalTo: "#password_text"
          },
          secure_code_text: {
            required: true
          }
        },
        messages:{
          password_text:{
            required: "Please enter Password."
          },
          confirm_password_text:{
            required: "Please enter Confirm Password.",
            equalTo: "Confirm password and password should be same."
          },
          secure_code_text: {
            required: "Please enter secure code"
          }
        }
      });

    });
  </script>
  <body class=" theme-blue">
    <div class="page-wrap" style="background:#fff; min-height:600px;">
      <div class="navbar-header">
        <a class="" href="<?php echo base_url(); ?>admin/home"><span class="navbar-brand"></span></a>
      </div>
      <div class="navbar-collapse collapse" style="height: 1px;"></div>
      <div class="header-logo" style="text-align:center; margin:10px 0" width="200px" height="180px">
        <img width="200" src="<?php echo $this->config->item('assets'); ?>images/logo.png" alt=""/>
      </div>
      <div class="dialog">
        <div class="panel panel-default">
          <p class="panel-heading no-collapse">Reset Password</p>
            <div class="panel-body">
              <?php if( $error_message ) { echo "<div class='error_message'>$error_message</div>"; } ?>
              <?php if( $success_message ) { echo "<div class='success_message'>$success_message</div>"; } ?>
              <form id="reset_password_form" action="" method="post">
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" class="form-control span12 password_text_field" id="password_text" name="password_text" style="height:10%" autocomplete="off">
                  <div class="error">
                    <?php echo form_error('password_text'); ?>
                  </div>
                </div>

                <div class="form-group">
                  <label>Confirm Password</label>
                  <input type="password" class="form-control span12 password_text_field" id="confirm_password_text" name="confirm_password_text" style="height:10%" autocomplete="off">
                  <div class="error">
                    <?php echo form_error('confirm_password_text'); ?>
                  </div>
                </div>

                <div class="form-group">
                  <label>Secure Code</label>
                  <a href="javascript:void(0);" onclick="window.location.reload();">
                    <img src="<?php echo $this->config->item('assets'); ?>images/refresh.png" id="refreshCaptcha" name="refreshCaptcha" height="25px" title="Refresh Captcha">
                  </a>
                  <div class="captch-div">
                    <span class="captcha-image"></span>
                    <input type="text" placeholder="Secure code"  id="secure_code_text" name="secure_code_text" class="form-control" style="width: 57%;"/>
                  </div>
                  <div class="error">
                    <?php echo form_error('secure_code_text'); ?>
                  </div>
                </div>

                <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">
                <input type="submit" class="btn btn-primary pull-right" value="Reset" />

                <div class="clearfix"></div>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php $this->load->view('admin/footer'); ?>
  </body>
</html>