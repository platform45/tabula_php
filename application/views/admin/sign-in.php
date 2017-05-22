<?php header("Cache-Control: no-store, no-cache, must-revalidate"); ?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="<?php echo $this->config->item('assets'); ?>images/favicon-tab.ico">
        <title>Sign In</title>
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
    </head>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
    <script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
    <script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#txtUsername").focus();

            $('#LoginForm').validate({
                rules:{
                    txtUsername:{
                        required:true
                    },
                    txtPassword:{
                        required:true
                    },
                    txtSecureCode:{
                        required:true
                    }
                },
                messages:{
                    txtUsername:{
                        required:"Please enter username / email address."
                    },
                    txtPassword:{
                        required:"Please enter password."
                    },
                    txtSecureCode:{
                        required:"Please enter secure code."
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
            <div class="navbar-collapse collapse" style="height: 1px;">
            </div>

            <div class="header-logo" style="text-align:center; margin:10px 0" width="200px" height="180px">
                <img width="200" src="<?php echo $this->config->item('assets'); ?>images/logo.png" alt=""/>
            </div>
            <div class="dialog">
                <div class="panel panel-default">
                    <p class="panel-heading no-collapse">Sign In</p>
                    <div class="panel-body">
                        <?php $attribute = array('id' => 'LoginForm'); ?>
                        <?php echo form_open('admin/admin/verifylogin', $attribute); ?>
                        <div class="form-group">
                            <label>Username / Email Address</label>
                            <input type="text" class="form-control span12" id="txtUsername" name="txtUsername" style="height:10%">
                            <div class="error">
                                <?php echo form_error('txtUsername'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control span12 form-control" id="txtPassword" name="txtPassword" style="height:10%">
                            <input type="hidden" id="pswd" />
                            <div class="error">
                                <?php echo form_error('txtPassword'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Secure Code</label>
                          <a href="<?php echo base_url(); ?>admin" >  <img src="<?php echo $this->config->item('assets'); ?>images/refresh.png" id="refreshCaptcha" name="refreshCaptcha" height="25px" title="Refresh Captcha"></a>
                            <div class="captch-div">
                                <span class="captcha-image"></span>
                                <input type="text" placeholder="Secure code"  id="txtSecureCode" name="txtSecureCode" class="form-control" style="width: 57%;"/>
                            </div>
                            <div class="error">
                                <?php echo form_error('txtSecureCode'); ?>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary pull-right" value="Login" />
                        <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('admin/footer'); ?>
    </body>
</html>
