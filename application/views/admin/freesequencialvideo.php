<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
		  
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu') ?>
        $(".sidebar-nav #menu<?php echo $arr['Channel'][1] ?>").addClass("act");

        $.session.set("addedit",1);
          
          
        $('#btn-add').click(function(){
            $('#select_from option:selected').each( function() {
                $('#select-to').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
                $(this).remove();
            });
        });
        $('#btn-remove').click(function(){
            $('#select-to option:selected').each( function() {
                $('#select_from').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
                $(this).remove();
            });
        });
        function moveSelected(select, up) {
            var $select = $(select);
            var $selected = $(":selected", $select);
            if (!up) {
                $selected = $($selected.get().reverse());
            }
            $selected.each(function () {
                var $this = $(this);
                if (up) {
                    var $before = $this.prev();
                    if ($before.length > 0 && !$before.is(":selected")) {
                        $this.insertBefore($before);
                    }
                } else {
                    var $after = $this.next();
                    if ($after.length > 0 && !$after.is(":selected")) {
                        $this.insertAfter($after);
                    }
                }
            });
        }
        $.fn.moveSelectedUp = function () {
            return this.each(function () {
                moveSelected(this, true);
            });
        };
        $.fn.moveSelectedDown = function () {
            return this.each(function () {
                moveSelected(this, false);
            });
        };
        
        $("#up").click(function(){
            $("select").moveSelectedUp();
        });
        $("#down").click(function(){
            $("select").moveSelectedDown();
        });
        $("#category_id").change(function(){
            var cat_id = $(this).val();
            var channel_id = $("#channel_id").val();
            var arr = [];
            $("#select-to > option").each(function(){
             arr.push(this.value);
             
            });
             $.ajax({
                url:'<?php echo base_url();?>admin/channel/getVideo',
                type:"POST",
                data:{cat_id : cat_id,channel_id:channel_id},
                success:function(data){
                     
                    $("#select_from").html(data);
                     var frmarr = [];
                    $("#select_from > option").each(function(){   
                        
                         if($.inArray( this.value, arr )!=-1)
                             {
                                 $("#select_from option[value='"+this.value+"']").remove();
                             }
                        
                     });
                  
                }
                });
        });
        $("#submit").click(function(){                     
         $('#select-to option').prop('selected', true);
          var count = $("#select-to :selected").length;
          if(count > 10 || count >= 1)
           return true;
           else
               {
                $("#select_error").html("Please select free videos as required.");
                return false;
               }
        });
    });
</script>
<div class="content">
    <div class="header">
       <h1 class="page-title"> Add free videos</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard">Home</a> </li>
            <li >Add free videos</li>

        </ul>
    </div>
    <div class="main-content">
      
        <div class="panel panel-default" align="left" style="border:0px;">

            <div class="panel-body" >
                <div class="">
                    <form id="select" action="<?php echo base_url()?>admin/channel/sequentialVideo" method="post">
                        <input type="hidden" value="<?php echo $channel_id ?>" name="channel_id" id="channel_id" >
                        <fieldset>
                            <div class="col-md-5 col-sm-4 col-xs-12">
                                  <select class="form-control valid" name="category_id" id="category_id" aria-invalid="false">
                                    <option value="" >Select category</option>
                                    <?php if ($categoriesData)
                                        foreach ($categoriesData as $cat) {
                                            ?>
                                     <option  value="<?php echo $cat['cat_id']; ?>"><?php echo $cat['category_name']; ?></option>
                                            <?php }
                                    ?>    

                                </select>
                                <select class="form-control" name="selectfrom" id="select_from" multiple >
                                     <?php if ($videoData) {
                                        foreach ($videoData as $vid) { 
                                            
                                            if(!in_array($vid['vid_id'],$allVideoData)) {
                                            ?>
                                         <option value="<?php echo $vid['vid_id']  ?>"><?php echo $vid['video_title']  ?></option>
                                    <?php } } } ?>
                                </select>
                            	</div>
                                <div class="col-md-1 col-sm-2 col-xs-12 btn_align_v text-center">
                                <a href="JavaScript:void(0);" id="btn-add" class="add_icon">&nbsp;</a>
                                <a href="JavaScript:void(0);" id="btn-remove" class="remove_icon">&nbsp;</a>
                                </div>
                            
                            <div class="col-md-5 col-sm-4 col-xs-12">
                                <select class="form-control" name="selectto[]" id="select-to" multiple size="6">
                                  
                                <?php if(@$freeVideosData) { foreach($freeVideosData as $freevideo){ ?>

                                <option value="<?php echo $freevideo['vid_id']  ?>"><?php echo $freevideo['video_title']  ?></option> 

                                <?php  } }?>
                                </select>
                                 <div id="select_note" class="">Note : Select minimum 1 and maximum 10 videos.</div>
                                <div id="select_error" class="error"></div>
                       			</div>
                             <div class="col-md-1 col-sm-2 btn_align_v1 col-xs-12">
                            <a href="JavaScript:void(0);" id="up" class="up_icon">&nbsp;</a>
                            <a href="JavaScript:void(0);" id="down" class="down_icon">&nbsp;</a>
                            </div>
                           
                        </fieldset>
                         <div class="form-group">
                            <div class="col-sm-11 vbtn_align">
                                    <input type="submit" id="submit" value="<?php echo ($channel_id ? "Save" : "Save");?>" class="btn btn-primary"/>
                                    <input type="button" value="Cancel" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url();?>admin/channel'"/>
                            </div>
                            </div>
                    </form>

                </div>
            </div>
        </div>					

    </div>
</div>
</div>