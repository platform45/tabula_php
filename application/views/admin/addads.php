<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
	  
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu') ?>
        $(".sidebar-nav #menu<?php echo $arr['Ad'][1] ?>").addClass("act");

        $.session.set("addedit",1);
            
        $('#frmSlider').validate({
            ignore: "hidden",
            rules:{
                channel_id: { 
                    required: true                      
                }, 
                    
                ad_title: { 
                    required: true,
                    remote:{
                        url: "<?php echo base_url() ?>admin/ads/check_title_exists/<?php echo $edit_id ?>",
                        type: "post",
                        data: {
                            "title": function(){ return $("#ad_title").val(); },
                            "channel_id": function(){ return $("#channel_id").val(); },
                                
                        }
                    }
                }, 
                ad_descirption: { 
                    required: true                       
                }, 
                    
                video:{
<?php if (!$edit_id) { ?>
                        required:true,
<?php } ?>
                    accept: "mp4"
                },
                   
            },                      
            messages:{                       
                channel_id:{
                    required:"Please select channel."

                },
                       
                ad_title:{
                    required:"Please enter ad name.",
                    remote:"Ad  already exits."
                },
                ad_descirption:{
                    required:"Please enter brief detail."
                },
                video:{
                    required:"Please select video.",
                    accept: "Extension should be mp4."
                },
                   
            }
        });
                
               
    });
</script>
<style>
    .multiselect-container.dropdown-menu {
        width: auto;
    }
</style>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Ad</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard">Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/ads">Ad</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "add"); ?></li>
        </ul>
    </div>
    <div class="main-content">

        <div class="error">* indicates required field.</div>
        <div class="panel panel-default" align="left" style="border:0px;">

            <div class="panel-body" >
                <div class="dialog1">
                    <form id="frmSlider" action="<?php echo base_url(); ?>admin/ads/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="old_img" name="old_img" value="<?php echo $formData['ad_video'] ?>"/>
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Channel<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control valid" name="channel_id" id="channel_id" aria-invalid="false">
                                    <option value="" >Select channel</option>
                                    <?php
                                    if ($channelArray)
                                        foreach ($channelArray as $channel) {
                                            ?>
                                            <option <?php echo ($channel['channel_id'] == $formData['channel_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $channel['channel_id']; ?>"><?php echo $channel['channel_title']; ?></option>
                                        <?php }
                                    ?>    

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Ad Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input maxlength="50" type="text" value="<?php echo set_value('ad_title', $formData['ad_title']); ?>" id="ad_title" name="ad_title" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Brief <span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <textarea maxlength="300" id="video_desc" name="ad_descirption" class="form-control" ><?php echo set_value('ad_descirption', $formData['ad_descirption']); ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Ad Video<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="file"  id="image" name="video"  value=""/>
                            </div>
                        </div>

                        <?php if ($formData['ad_video']) { ?>
                            <div class="form-group" >
                                <div class="col-sm-offset-3 col-sm-6">
                                    <!--                                  <video width="200" height="200" controls>
                                                                        <source src="<?php echo $this->config->item('assets') . '/ads/' . $formData['ad_video'] ?>" >
                                                                        Your browser does not support the video tag.
                                                                      </video>  -->
                                    <iframe src="<?php echo $this->config->item('assets') . '/ads/' . $formData['ad_video'] ?>"></iframe>
                                    <div>(mp4 format is supported only)</div>

                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <progress style="display:none;"></progress>
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/ads'"/>
                            </div>

                        </div>
                    </form>
                </div>
            </div>					

        </div>
    </div>
</div>