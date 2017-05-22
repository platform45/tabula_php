<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
		  
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu');  ?>
            $(".sidebar-nav #menu<?php echo $arr['Category'][1] ?>").addClass("act");
            
            $.session.set("addedit",1);
            
            $('#frmSlider').validate({
                rules:{
                    channel_id:{
                        required:true
                    },
                    txttitle:{
                        required:true,
                        remote:{
                        url: "<?php echo base_url()?>admin/category/check_title_exists/<?php echo $edit_id ?>",
                        type: "post",
                        data: {
                            "title": function(){ return $("#txttitle").val(); },
                            "channel_id": function(){ return $("#channel_id").val(); }
                        }
                    }
                    },
                    txtdesc:{
                        required:true
                    },
                    icon:{
<?php if (!$edit_id) { ?>
                                                    required:true,
<?php } ?>
                                                accept:'jpg,jpeg,bit,gif'
                                            }
                                        },
                                        messages:{
                                            channel_id:{
                                                required:"Please enter channel."
                                            },
                                            txttitle:{
                                                required:"Please enter category name.",
                                                remote:"Category name already exist"
                                            },
                                             txtdesc:{
                                                required:"Please enter category description."
                                            },
                                            image:{
                                                required:"Please select image.",
                                                accept: "Extension should be jpg,jpeg or gif."
                                            }
                                        }
                                    });	
                                    
                                     $("#channel_id").change(function(){
                                    $("#txttitle").removeAttr( "readonly" )
                                        });
                                });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Category</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard">Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/category">Category</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "add"); ?></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="error">* indicates required field.</div>
        <div class="panel panel-default" align="left" style="border:0px;">

            <div class="panel-body" >
                <div class="dialog1">
                    <form id="frmSlider" action="<?php echo base_url(); ?>admin/category/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                      
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Channel Title<span class="error" >*</span></label>
                            <div class="col-sm-6">
                            <select class="form-control" id="channel_id" name="channel_id">
                               <option value="">Select </option>
                               <?php foreach($getChannel as $channel) { 
                                  
                                   
                                   ?>
                              
                               <option <?php if($channel['channel_id']==$formData['channel_id']) echo "selected='selected'"?> value="<?php echo $channel['channel_id'] ?>"><?php echo $channel['channel_title'] ?></option>
                           <?php } ?>
                           </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" <?php if(!$edit_id) echo "readonly"; ?> value="<?php echo set_value('txttitle', $formData['txttitle']); ?>" id="txttitle" name="txttitle" class="form-control" />
                            </div>
                        </div>
                        
                         <div class="form-group">
                            <label class="col-sm-3 control-label">Category description<span class="error" >*</span></label>
                            <div class="col-sm-6">
                               
                                <textarea class="form-control" id="txtdesc" name="txtdesc"><?php echo $formData['txtdesc'] ?></textarea>
                            
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category Icon<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="file"  id="image" name="icon"  value=""/>
                            </div>
                        </div>
                        <div class="form-group" >
                            <div class="col-sm-offset-3 col-sm-6">
                                <img id="preview" name="preview" width="100" src="<?php if ($formData['category_icon']) echo $this->config->item('assets') . 'upload/category_icon/' . $formData['category_icon']; else echo $this->config->item('assets') . 'upload/No_Image.jpg'; ?>"/>
                                <div>(jpg,jpeg and bitmap images are allowed only.)</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <progress style="display:none;"></progress>
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/category'"/>
                            </div>
                    </form>
                </div>
            </div>
        </div>					

    </div>
</div>
</div>