<html>
    <head>
        <title>Safyre</title>
        <script type="text/javascript" src="<?php echo $this->config->item('front_assets');?>js/jquery-1.11.1.min.js" ></script>
        <script type="text/javascript" src="<?php echo $this->config->item('front_assets');?>js/jquery.validate.js" ></script>
        <script type="text/javascript" src="<?php echo $this->config->item('front_assets');?>js/extra_method.js" ></script>
        <script type="text/javascript">
            $(document).ready(function(){
                
                $('#frmChangePassword').validate({
                        rules:{
                            txtpassword:{
                                    required:true,
                                    minlength:6
                                },
                                txtcnf_password:{
                                        required:true,
                                        equalTo: "#txtpassword"
                                }
                        },
                        messages:{
                                txtpassword:{
                                        required:"Please enter Password.",
                                        minlength:"Please enter password atleast 6 characters long."
                                },
                                txtcnf_password:{
                                        required:"Please enter Confirm Password.",
                                        equalTo: "Confirm password and password should be same."
                                }
                        }
                });
                
            });
        </script>
        
    </head>
    
    <style>
        table{
            margin: 0 auto;
            border:1px solid;
            font-family: sans-serif;
        }
        .error{
            color: red;
            font-size: 12px;
            margin: 2px 0 0 0;
        }
        td.headertop{
            height: 200px;
        }
        div.headerbg{
            height: 100%;
        }
        div.logo{
            padding-top: 45px;
            text-align: center;
            background: #000;
        }
        div.headerinner{
            height: 100px;
            margin: 10px auto 0;
            display: block;
            position: relative;
            width: 100px;
        }
        div.header-bottom{
            margin-bottom: 65px;
        }
        div.headerinner span {
            color: #ffffff;
            display: block;
            left: 15px;
            margin: 26px 0 0;
            position: absolute;
            text-align: center;
            top: 0;
        }        
    </style>
    <body>
        <form id="frmChangePassword" action="" method="post">
            <input type="hidden" name="hidUserID" value="<?php echo $aUserDetail['user_id']; ?>">
            
            <table cellspacing="0" cellpadding="0" style="max-width: 900px;width: 100%;">
                <tr>
                    <td colspan="2" class="headertop">
                        <div class="headerbg">
                            <div class="logo"><img src="<?php echo $this->config->item('assets');?>images/logo.png" height="100" /></div>
                            <div class="headerinner">
                                <span>Change <br/> password</span>
<!--                                <img src="<?php echo $this->config->item('front_assets');?>images/blackcircle.png" height="100" />-->
                            </div>
                        </div>
                        <div class="header-bottom"></div>
                    </td>
                </tr>
                <tr><td colspan="2" align="center"><p><?php echo $message; ?></p></td></tr>
                <tr>
                    <td width="20%" style="padding-left: 20px;"><label>New password:</label></td>
                    <td>
                        <input type="password" name="txtpassword" id="txtpassword">
                        
                    </td>
                </tr>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        <label id="txtpassword-error" class="error" for="txtpassword"></label>
                    </td>
                </tr>
                <tr>
                    <td width="20%" style="padding-left: 20px;"><label>Confirm password:</label></td>
                    <td>
                        <input type="password" name="txtcnf_password" id="txtcnf_password">
                    </td>
                </tr>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        <label id="txtcnf_password-error" class="error" for="txtcnf_password"></label>
                    </td>
                </tr>
                <tr><td>&nbsp;</td><td><input type="submit" name="command" value="Save"></td></tr>
                <tr>
                    <td colspan="2" style="height:30px;">&nbsp;</td>
                </tr>
                <tr>
                    <td bgcolor="#3C3E3F" colspan="2" style="height:30px;"></td>
                </tr>
            </table>
        </form>
    </body>
</html>