<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $this->config->item('site_name');?></title>
</head>
<body style="margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;color: #fff;">
<div style="width:100%;max-width:800px;margin:0 auto;position:relative;display:block;" id="warpper">
	<div style="width:100%;margin:0 ;float:left;" id="warpper_in"><!-- border:solid 1px #fab1b3; -->
    	<div class="top_header" style="width:100%;max-width:800px;height:100%;float:left;background: #000;text-align: center;">
            <img style="width:100%;max-width: 300px;float:none;height:auto;" src="<?php echo $this->config->item('front_assets');?>images/top-header.png" alt=""/>
        </div>
     	<div style="width:94%;float:left;padding:3% 0;word-break: break-all;color: #000;" class="mid_content">
            <table style="width: 100%; height: 100%;" border="0">
                <tbody>
                    <tr>
                        <td><strong>Dear {NAME},</strong></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Thank you for registration on Jinx Application.  Please see additional login information below:</td>
                    </tr>
                    <tr>
                        <td>&nbsp;
                            <table style="width: 100%; height: auto;" border="0">
                                <tbody>
                                    <tr>
                                        <td>Email id: {EMAILID}</td>
                                    </tr>
                                    <tr>
                                        <td>Password: {PASSWORD}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <p style="margin: 10px 0 0;">Thank you,</p>
                                            <p style="margin: 0;">Jinx Team</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="width:100%;max-width:800px;height:100%;float:left;height:70px;" class="fotter">
            <div style="color: #fff;font-family: Arial,Helvetica,sans-serif;font-size: 14px;font-weight: bold;height: 0;margin: 0;text-align: center;" class="txt1">
                <img style="width:100%;float:left;height:auto;" src="<?php echo $this->config->item('front_assets');?>images/fotter.png" alt=""/>
            </div>
      </div>
</div>
</body>
</html>