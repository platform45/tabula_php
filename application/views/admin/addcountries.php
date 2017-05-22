<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script> 
<script type="text/javascript">
        
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Country'][1]; ?>").addClass("act");
        jQuery.validator.addMethod( 'allowCharacter', function(value) {
            if(value != '')
                return /^[a-zA-Z -]*$/.test(value);
            else
                return true;
        });  
           
        //Validate countries   
        $("#frmCountries").validate({
            rules: {
                country_name:{
                    required:true,
                    remote:{
                        url: "<?php echo $this->config->item("admin_url") . 'countries/check_country_exists/' . $edit_id; ?>",
                        type: "post",
                        data: {
                            "country_name": function(){ return $("#country_name").val(); }
                        }
                    },
                    allowCharacter:true
                },               
                country_abbrivation:{
                    required:true,
                    remote:{
                        url: "<?php echo $this->config->item("admin_url") . 'countries/check_abbr_exists/' . $edit_id; ?>",
                        type: "post",
                        data: {
                            "country_abbrivation": function(){ return $("#country_abbrivation").val(); }
                        }
                    },
                    allowCharacter:true
                },
                country_code : {
<?php if ($formData['country_code'] == "") { ?>
                        required:true,
<?php } ?>
                    accept:'jpg,jpeg,png,gif'
                },		
            },
            messages:{
                country_name:{
                    required:"Please enter a country name.",
                    remote: "The country name already exists.",
                    allowCharacter: "Only characters, dashes & spaces are allowed."
                },
                                           
                country_abbrivation:{
                    required:"Please enter country abbreviation.",
                    remote: "The country abbreviation already exists.",
                    allowCharacter: "Only characters, dashes & spaces are allowed."
                },country_code:{
                    required:"Please enter country code.",
                    remote: "The country code already exists.",
                    allowCharacter: "Only characters, dashes & spaces are allowed."
                }
            }
        });
                            
        $.session.set("addedit",1);
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Country</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item("admin_url"); ?>dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo $this->config->item("admin_url"); ?>countries">Countries</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "Add"); ?></li>
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
                    <form id="frmCountries" action="<?php echo $this->config->item("admin_url"); ?>countries/addedit/<?php echo $edit_id; ?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" value="<?php echo set_value('country_name', stripslashes($formData['country_name'])); ?>" id="country_name" name="country_name" class="form-control" maxlength="50"/>
                                <span class="error"><?php echo form_error('country_name'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country Abbreviation<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" value="<?php echo set_value('cou_abbreviation', stripslashes($formData['country_abbrivation'])); ?>" id="country_abbrivation" name="country_abbrivation" class="form-control" maxlength="8"/>
                                <span class="error"><?php echo form_error('cou_abbreviation'); ?></span>
                            </div>
                        </div>
                        <div class="form-group" id="country_code">
                            <label class="col-sm-3 control-label">Country Code<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" id="country_code" name="country_code"  value="<?php echo set_value('country_code', stripslashes($formData['country_code'])); ?>" class="form-control" />
                                <span style="font-size:12px; display: block;"></span>
                            </div>
                        </div>

                        <div class="form-group" id="image_preview">
                            <div class="col-sm-offset-3 col-sm-6">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo $this->config->item("admin_url"); ?>countries'"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>					
    </div>
</div>