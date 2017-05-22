<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
        
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['State'][1]; ?>").addClass("act");
                
        jQuery.validator.addMethod( 'allowCharacter', function(value) {
            if(value != '')
                return /^[a-zA-Z -]*$/.test(value);
            else
                return true;

        });  
            
        $("#frmRegions").validate({
            rules: {
                region_name:{
                    required:true,
                    remote:{
                        url: "<?php echo $this->config->item("admin_url") . 'state/check_region_exists/' . $edit_id; ?>",
                        type: "post",
                        data: {
                            "region_name": function(){ return $("#state_name").val(); },
                            "country_id": function(){ return $("#country_id").val(); }
                        }
                    },
                    allowCharacter:true
                },
                country_id:{
                    required:true
                }                                            
            },
            messages:{
                region_name:{
                    required:"Please enter a region name.",
                    remote: "The region name already exists.",
                    allowCharacter: "Only characters, dashes & spaces are allowed."
                },
                country_id:{
                    required:"Please select country."
                }
                                            
            }
        });
                            
                            
        $.session.set("addedit",1);
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> State</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item("admin_url"); ?>dashboard">Home</a> </li>
            <li><a href="<?php echo $this->config->item("admin_url"); ?>regions">Regions</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "Add"); ?></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="error jx_indicator_error">* indicates required field.</div>
        <div class="panel panel-default" align="left" style="border:0px;">
            <div class="panel-body" >
                <div class="dialog1">
                    <form id="frmRegions" action="<?php echo $this->config->item("admin_url"); ?>regions/addedit/<?php echo $edit_id; ?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control valid" name="country_id" id="country_id" aria-invalid="false" style="width:200px;">
                                    <option value="" >Select Country</option>
                                    <?php  foreach ($countriesData as $res) {
                                        print_r($countriesData);
                                        ?>
                                        <option <?php echo ($res['cou_id'] == $formData['cou_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $res['cou_id']; ?>"><?php echo htmlentities(stripslashes($res['cou_name'])); ?></option>
                                    <?php }
                                    ?> 
                                </select>
                                <span class="error"><?php echo form_error('cou_id'); ?></span>
                            </div>
                        </div>   


                        <div class="form-group">
                            <label class="col-sm-3 control-label">Region Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" value="<?php echo set_value('state_name', stripslashes($formData['state_name'])); ?>" id="region_name" name="region_name" class="form-control" maxlength="50" />
                                <span class="error"><?php echo form_error('state_name'); ?></span>
                            </div>
                        </div>

                        <!--<div class="form-group">
                                        <label class="col-sm-3 control-label">Status<span class="error" ></span></label>
                                        <div class="col-sm-6">
                                                        <select class="form-control valid" name="status" id="status" aria-invalid="false" style="width:200px;">
                                                                 <option value="1" <?php //echo ($formData['status'] == '1' ? "selected='selected'" : "");     ?>>Active</option>
                                                                 <option value="0" <?php //echo ($formData['status'] == '0' ? "selected='selected'" : "");     ?>>Inactive</option>
                                                        </select>
                                        </div>
                        </div>-->      



                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo $this->config->item("admin_url"); ?>state'"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>					
    </div>
</div>