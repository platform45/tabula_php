<script src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
    
	
    $(document).ready(function(){
        <?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Loyalty Setting'][1]; ?>").addClass("act");
        jQuery.validator.addMethod( 'allowCharacter', function(value) {
            if(value != '')
                return /^[a-zA-Z -]*$/.test(value);
            else
                return true;
        });   
                            
<?php $arr = $this->session->userdata('menu') ?>
       
        $("#setting").validate({
                rules: {
                    setting_name:{
                        required:true
                    },
                    setting_value:{
                        required:true,
                        number: true
                    },
                    setting_parameter:{
                        required:true,
                        number: true
                    },
                                    },
                messages: {
                    setting_name:{
                        required:"Please enter setting name."
                    },
                    setting_value:{
                        required:"Please select setting value."
                    },
                    setting_parameter:{
                        required:"Please enter setting parameter."
                    },
                }
            });
      
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title">Loyalty Setting</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li class="active">
                Loyalty Setting
            </li>
        </ul>
    </div>
     
<div class="main-content">
       <div class="error " style="">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-7" style="margin-bottom: 16px;">* indicates required field.</div>
                    </div>
        <div class="panel panel-default" align="left" style="border:0px;">
            <div class="panel-body" >
                <div class="dialog1">
                        <form id="setting" action="<?php echo base_url(); ?>admin/setting/addedit/1" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        
            <!--     <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id;?>"/> -->

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" value="<?php echo set_value('setting_name',$formData['setting_name']);?>" name="setting_name" id="setting_name" class="form-control" maxlength="50"/>
                                <span class="error"><?php echo form_error('setting_name'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Value(R)<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" value="<?php echo set_value('setting_value',$formData['setting_value']);?>" id="setting_value" name="setting_value" class="form-control" maxlength="50"/>
                                <span class="error"><?php echo form_error('setting_value'); ?></span>
                            </div>
                        </div>
                        <div class="form-group" id="country_code">
                            <label class="col-sm-3 control-label">Parameter(%)<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" id="setting_parameter" name="setting_parameter"  value="<?php echo set_value('setting_parameter',$formData['setting_parameter']);?>" class="form-control" maxlength="10"/>
                                <span class="error"><?php echo form_error('setting_parameter'); ?></span>
                            </div>
                        </div>

                        <div class="form-group" id="image_preview">
                            <div class="col-sm-offset-3 col-sm-6">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="Update" class="btn btn-primary"/>
                                <input type="button" value="Cancel" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo $this->config->item("admin_url"); ?>'"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>                  
    </div>
</div>
