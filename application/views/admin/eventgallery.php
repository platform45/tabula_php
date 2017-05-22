<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/extra_method.js" ></script>
<script type="text/javascript" >
$(document).ready(function() {
        <?php $arr = $this->session->userdata('menu')?>
        $(".sidebar-nav #menu<?php echo $arr['Event'][1]?>").addClass("act");
        
         $.session.set("addedit",1);

        /*
        * Programmer Name: 
        * Purpose: DataTable initialisation.
        * Date: 02 june 2015
        * Dependency: portfoliogallery.php
        */
            var oTable = $('#test').DataTable({
                "columns":[
                        null,
                       
                        { "bsearchable": false, "bSortable": false },
                        { "bsearchable": false, "bSortable": false }	
                ],
                "dom" : '<tlip>',
                "iDisplayLength":25,
                "lengthMenu": [ 25, 50, 75, 100 ]

            });


        $("#test_paginate").on("click",function(){info = oTable.page.info(); $.session.set('page_number',info.page);});
        $("#test").on("click",function(){var order = oTable.order(); $.session.set('sort_column',order[0][0]); $.session.set('sort_order',order[0][1]);});

       

        /*
        * Programmer Name: 
        * Purpose: To display confirmation dialog before deletion and passing the id to the confirm delete dialog.
        * Date: 2 june 2015
        * Dependency: admin.php
        */
        $("#test").on("click",".delete_button",function() {
            var id = this.id;

            // Assign the id to a custom attribute called data-id and language id
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("aria-hidden",false);
        });

        /*
        * Programmer Name:
        * Purpose: To redirect to the controller for deletion.
        * Date: 2 june 2015
        * Dependency: portfoliogallery.php
        */
        $("#btn-danger1").click(function(){
                
                 $.ajax({
                        url:'<?php echo base_url();?>admin/eventgallery/delete_image',
                        type:"POST",
                        data:{"event_id": $("#myModal").attr("data-id") },
                        success:function(){
                                   
                                    $().toastmessage('showSuccessToast', "Record Deleted successfully.");
                                    window.location.reload();
                        }


                });	
        }); 
        
         $('#frmportfolio').validate({
                       rules:{
                               image:{
                                   required:true,
                                   accept:'jpg,jpeg'
                               }
                       },
                       messages:{
                               image:{
                                   required:"Please select image.",
                                   accept:'jpg and jpeg images are only allowed'
                                  
                               }
                       }
               });
       
});
       			
    
</script>
<div class="content">
            <div class="header">
                    <h1 class="page-title">Event images</h1>
                            <ul class="breadcrumb">
                                    <li><a href="<?php echo base_url();?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
                                    <li><a href="<?php echo base_url();?>admin/event">Event</a></li>
                                    <li class="active"> images</li>                            </ul>
                                    <span class="col-sm-offset-3 "style="color: black;" ><strong>Event : <?php echo $pm_title;?></strong></span>
            </div>
            <div class="main-content">
                
                       
                    <div id="custom_filter"> 
                       <form id="frmportfolio" action="<?php echo base_url();?>admin/eventgallery/add/<?php echo $event_id?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
                          <div class="form-group">
			    <label class="col-sm-3 control-label">Add Image<span class="error" >*</span></label>
                          
                                <div class="col-sm-6">
                                    <input type="file"  id="image" name="image"  value=""/>
                                    <span></span>
                                    <br>
                                   <span>Note: .jpg and .jpeg images are allowed only.<br></span>
                                </div>
                            
			</div>
                           
                           <div class="form-group">
                        	<div class="col-sm-offset-3 col-sm-6">
                                    <progress style="display:none;"></progress>
                                     <input type="submit" value="Save" class="btn btn-primary"/>
                                     <input type="button" value="Back" class="btn btn-primary" onclick="javascript:window.location.href = '<?php echo base_url();?>admin/event'"/>
				</div>
                           </div>
                       </form>
                           
                    </div>

            <br/>
            
		<div class="table-responsive">
                    <table class="display hover cell-border table" id="test">
                        <thead>
                            <tr class="table_th_tr">
                                <th class="row-col_1">SEQUENCE</th>
                                <th class="row-col_3">IMAGE</th>
                                
                                <th class="row-col_1">DELETE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;?>
                            <?php 
                            if($galleryData)
                            foreach($galleryData as $rec):
                                $path = $rec['image'];
                                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                                        $fileName = basename($path, ".".$ext); 
                                        $thumb_img = $fileName."_thumb.".$ext;
                                ?>
                            <tr style="text-align:center;">
                                <td> <?php echo $i++;?></td>
                                 <td> <img width="100px" id="preview" name="preview" src="<?php if($rec['image']) echo $this->config->item('assets').'upload/eventgallery/'.$thumb_img; else echo $this->config->item('assets').'upload/No_Image_thumb.jpg';?>"/> </td>
                                  
                                <td><a href="#myModal" class="delete_button" id="<?php echo $rec['id'];?>" role="button" data-toggle="modal" title="Delete"><i class="fa fa-times-circle-o fa-2x" ></i></a></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                    <div class="modal small fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                    <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                                            <h3 id="myModalLabel">Delete Confirmation</h3>
                                    </div>
                                    <div class="modal-body">
                                            <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete?<br>This cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer">
                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                                            <button class="btn btn-primary" id="btn-danger1" data-dismiss="modal">Delete</button>
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>
</div>