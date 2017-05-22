<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">		  
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu') ?>
        $(".sidebar-nav #menu<?php echo $arr['Slider'][1] ?>").addClass("act");

        $.session.set("addedit",1);
        $('#frmSlider').validate({
            rules:{
                txttitle:{
                    required:true
                },
                txturl:{
                    required:true,
                    url: true
                },
                image:{
<?php if (!$edit_id) { ?>
                        required:true,
<?php } ?>
                    accept:'jpg,jpeg,bit'
                }
            },
            messages:{
                txttitle:{
                    required:"Please enter title."
                },
                txturl:{
                    required:"Please enter URL."
                },
                image:{
                    required:"Please select image.",
                    accept: "Extension should be jpg,jpeg."
                }
            }
        });			
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Slider</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/slider">Slider</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit Slider" : "Add Slider"); ?></li>
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
                    <form id="frmSlider" action="<?php echo base_url(); ?>admin/slider/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="old_img" name="old_img" value="<?php echo $formData['image']; ?>"/>
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                        <div class="form-group">
                            <label class="col-sm-3 control-label text-left" >Title<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo set_value('txttitle', $formData['txttitle']); ?>" id="txttitle" name="txttitle" class="form-control" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label class="col-sm-3 control-label ">URL <span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="http://safyre.com" id="txturl" name="txturl" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Image<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="file"  id="image" name="image"  value=""/>

                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-sm-offset-3 col-sm-6">
                                <img id="preview" name="preview" width="200" height="150" src="<?php if ($formData['image']) echo $this->config->item('assets') . 'upload/slider/' . $formData['image']; else echo $this->config->item('assets') . 'upload/No_Image.jpg'; ?>"/>
                                <div><br/>(jpg, jpeg images are allowed only.)</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <progress style="display:none;"></progress>
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/slider'"/>
                            </div>
                    </form>
                </div>
            </div>
        </div>					

    </div>
</div>
</div>