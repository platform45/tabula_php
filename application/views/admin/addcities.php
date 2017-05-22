<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
        
    $(document).ready(function(){
            
        /* Fillup Region According to select country */
        $("#country_id").change(function(){							
            $.post("<?php echo $this->config->item("admin_url") . 'cities/get_region_id_by_country'; ?>", 
            {country_id: $("#country_id").val()},
            function(option_list){										
                $("#region_id").html(option_list);
            });
        })		
        jQuery.validator.addMethod( 'allowCharacter', function(value) {
            if(value != '')
                return /^[a-zA-Z -]*$/.test(value);
            else
                return true;

        });  
						
						
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['City'][1]; ?>").addClass("act");
          
        $("#frmCities").validate({
            rules: {
                city_name:{
                    required:true,
                    remote:{
                        url: "<?php echo $this->config->item("admin_url") . 'cities/check_city_exists/' . $edit_id; ?>",
                        type: "post",
                        data: {
                            "country_id": function(){ return $("#country_id").val(); },
                            "region_id": function(){ return $("#region_id").val(); },
                            "city_name": function(){ return $("#city_name").val(); }																															
                        }
                    },
                    allowCharacter:true
                },
                country_id:{
                    required:true
                },
                region_id:{
                    required:true
                }
            },
            messages:{
                city_name:{
                    required:"Please enter a city name.",
                    remote: "The city name already exists.",
                    allowCharacter: "Only characters, dashes & spaces are allowed."
                },
                country_id:{
                    required:"Please select country."
                },
                region_id:{
                    required:"Please select state."
                }
            }
        });
        $.session.set("addedit",1);
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> City</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item("admin_url"); ?>dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo $this->config->item("admin_url"); ?>cities">Cities</a></li>
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
                    <form id="frmCities" action="<?php echo $this->config->item("admin_url"); ?>cities/addedit/<?php echo $edit_id; ?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control valid" name="country_id" id="country_id" aria-invalid="false" style="width:200px;">
                                    <option value="" >Select Country</option>
                                    <?php foreach ($countriesData as $res) {
                                        ?>
                                        <option <?php echo ($res['cou_id'] == $formData['country_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $res['cou_id']; ?>"><?php echo htmlentities(stripslashes($res['cou_name'])); ?></option>
                                    <?php }
                                    ?> 
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">State<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control valid" name="region_id" id="region_id" aria-invalid="false" style="width:200px;">
                                    <option value="" >Select State</option>
                                    <?php foreach ($regionsData as $res) {
                                        ?>
                                        <option <?php echo ($res['region_id'] == $formData['region_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $res['region_id']; ?>"><?php echo htmlentities(stripslashes($res['region_name'])); ?></option>
                                    <?php }
                                    ?> 
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">City<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" style="max-width:200px" value="<?php echo set_value('city_name', stripslashes($formData['city_name'])); ?>" id="city_name" name="city_name" class="form-control" maxlength="50"/>
                                <span class="error"><?php echo form_error('city_name'); ?></span>
                            </div>
                        </div>		
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo $this->config->item("admin_url"); ?>cities'"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>					
    </div>
</div>