<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">		  
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu') ?>
        $(".sidebar-nav #menu<?php echo $arr['Cuisine'][1] ?>").addClass("act");

        $.session.set("addedit",1);
        $('#frmSlider').validate({
            rules:{
                txttitle:{
                    required:true,
                    remote:{
                        url: "<?php echo base_url() ?>admin/cuisine/check_cuisine_exist/<?php echo $edit_id ?>",
                        type: "post",
                        data: {
                            "title": function(){ return $("#txttitle").val(); }
                        }
                    }
                }
            },
            messages:{
                txttitle:{
                    required:"Please enter cuisine title.",
                     remote:"Cuisine already exist."
                }
                
            }
        });
    

    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Cuisine</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/cuisine">Cuisine</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit Cuisine" : "Add Cuisine"); ?></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="error " style="">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-6" style="margin-bottom: 16px;">* indicates required field.</div>
        </div>   

        <div class="panel panel-default" align="left" style="border:0px;">
            <div class="panel-body" >
                <div class="dialog1">
                    <form id="frmSlider" action="<?php echo base_url(); ?>admin/cuisine/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">

                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-left" >Cuisine Title<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo trim(set_value('txttitle', $formData['txttitle'])); ?>" id="txttitle" name="txttitle" class="form-control" maxlength="40"/>
                            </div>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label class="col-sm-3 control-label ">URL <span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="http://safyre.com" id="txturl" name="txturl" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <progress style="display:none;"></progress>
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/cuisine'"/>
                            </div>
                    </form>
                </div>
            </div>
        </div>					

    </div>
</div>
</div>