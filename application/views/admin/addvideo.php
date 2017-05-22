<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
	  
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu') ?>
            $(".sidebar-nav #menu<?php echo $arr['Video'][1] ?>").addClass("act");

            $.session.set("addedit",1);
            
            $('#frmSlider').validate({
                ignore: "hidden",
                rules:{
                    channel_id: { 
                        required: true
                      
                    }, 
                    category_id: { 
                        required: true                       
                    }, 
                    video_title: { 
                        required: true,
                        remote:{
                            url: "<?php echo base_url()?>admin/video/check_title_exists/<?php echo $edit_id ?>",
                            type: "post",
                            data: {
                                "title": function(){ return $("#video_title").val(); },
                                "channel_id": function(){ return $("#channel_id").val(); },
                                "category_id": function(){ return $("#category_id").val(); }
                            }
                            }
                    }, 
                    video_desc: { 
                        required: true                       
                    }, 
                    video_type:{
                        required:true
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
                        category_id:{
                            required:"Please select category."
                        },
                        video_title:{
                            required:"Please enter video name.",
                            remote:"Video already exist."
                            
                    
                        },
                        video_desc:{
                            required:"Please enter brief detail."
                        },
                        video:{
                        required:"Please select video.",
                        accept: "Extension should be mp4."
                    },
                    video_type:{
                        required:"Please select video type."
                    },
                    }
                });
                
                $("#channel_id").change(function(){
                 var channel_id = $(this).val();
                 $.ajax({
                    url: "<?php echo base_url() ?>admin/video/getcategories",                   
                    type: "POST",
                    data:{channel_id:channel_id},
                    success: function(data){
                       
                        $("#category_id").html(data);
                    }
                    });
                });
                $("#category_id").change(function(){
                              
                    $("#video_title").removeAttr( "readonly" );
              
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
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Video</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard">Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/video">Video</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "add"); ?></li>
        </ul>
    </div>
    <div class="main-content">

        <div class="error">* indicates required field.</div>
        <div class="panel panel-default" align="left" style="border:0px;">

            <div class="panel-body" >
                <div class="dialog1">
                    <form id="frmSlider" action="<?php echo base_url(); ?>admin/video/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="old_img" name="old_img" value="<?php echo $formData['video']?>"/>
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                       
                       
                       
                       
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Channel<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control valid" name="channel_id" id="channel_id" aria-invalid="false">
                                    <option value="" >Select channel</option>
                                    <?php if ($channelArray)
                                        foreach ($channelArray as $channel) {
                                            ?>
                                     <option <?php echo ($channel['channel_id'] == $formData['channel_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $channel['channel_id']; ?>"><?php echo $channel['channel_title']; ?></option>
                                            <?php }
                                    ?>    

                                </select>
                            </div>
                        </div>
                        
                         <div class="form-group">
                            <label class="col-sm-3 control-label">Category<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control valid" name="category_id" id="category_id" aria-invalid="false">
                                    <option value="" >Select category</option>
                                    <?php if ($categoryArray)
                                        foreach ($categoryArray as $cat) {
                                            ?>
                                     <option <?php echo ($cat['cat_id'] == $formData['category_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $cat['cat_id']; ?>"><?php echo $cat['category_name']; ?></option>
                                            <?php }
                                    ?>    

                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Video Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input maxlength="50"  <?php if(!$edit_id) echo "readonly"; ?> type="text" value="<?php echo set_value('video_title', $formData['video_title']); ?>" id="video_title" name="video_title" class="form-control" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Brief <span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <textarea maxlength="300" id="video_desc" name="video_desc" class="form-control" ><?php echo set_value('video_desc', $formData['video_desc']); ?></textarea>
                            </div>
                        </div>
                        
                         <div class="form-group">
                            <label class="col-sm-3 control-label">Video Type <span class="error" >*</span></label>
                            <div class="col-sm-6">
                              <input type="radio" value="0" checked="checked" name="video_type" > Free 
                              <input type="radio" value="1" <?php if( $formData['video_type']==1) { echo 'checked="checked"';} ?> name="video_type" > Paid
                            </div>
                        </div>
                      
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Video<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="file"  id="image" name="video"  value=""/>
                            </div>
                        </div>
                         <?php if($formData['video']) { ?>
                        <div class="form-group" >
                            <div class="col-sm-offset-3 col-sm-6">
                              
<!--                                 <video width="200" height="200" controls>
                                    <source src="<?php echo $this->config->item('assets').'/video/'.$formData['video'] ?>" >
                                    Your browser does not support the video tag.
                                  </video>    -->
                                  <iframe src="<?php echo $this->config->item('assets').'/video/'.$formData['video'] ?>"></iframe>
                               <div>(mp4 format is supported only)</div>
                            </div>
                          
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <progress style="display:none;"></progress>
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/video'"/>
                            </div>
                    </form>
                </div>
            </div>
        </div>					

    </div>
</div>
</div>