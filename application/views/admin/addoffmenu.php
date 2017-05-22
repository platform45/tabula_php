<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/extra_method.js" ></script>
<script type="text/javascript">
        
        $(document).ready(function(){
            <?php $arr = $this->session->userdata('menu');
            ?>
            $(".sidebar-nav #menu<?php echo $arr['Off Menu'][1];?>").addClass("act");
                /*
			* Purpose: Initialise Tinymce editor.
			* Date: 7 Oct 2014
			* Input Parameter:
			*            None
			*  Output Parameter:
			*            None
			*/
			tinymce.init({
				selector: "textarea#txtmaincontent",
				fontsize_formats: "8pt 10pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 30pt 32pt 34pt 36pt 38pt 40pt 42pt 44pt 46pt 48pt 50pt 52pt 54pt 56pt 58pt 60pt 62pt 64pt 66pt 68pt 70pt 72pt 74pt 76pt 78pt 80pt",
				theme: "modern",
				plugins: [
					 "advlist autolink colorpicker link image lists charmap print preview hr anchor pagebreak",
					 "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
					 "save table contextmenu directionality emoticons template paste textcolor responsivefilemanager"
				],
				toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons", 
				toolbar2: "fontsizeselect | fontselect ",
				theme_advanced_buttons: "forecolor backcolor",
				directionality : 'ltr',
				relative_urls: false,
				remove_script_host: false,
				force_br_newlines : true,
				force_p_newlines : false,
				browser_spellcheck : true,
				height : 400,
				forced_root_block : false,
				external_filemanager_path:"<?php echo $this->config->item('assets');?>lib/tinymce/filemanager/",
				filemanager_title:"Responsive Filemanager" ,
				external_plugins: { "filemanager" : "<?php echo $this->config->item('assets');?>lib/tinymce/filemanager/plugin.js",
									"nanospell": "<?php echo $this->config->item('assets');?>lib/tinymce/nanospell/plugin.js"
								  },
				nanospell_server: "php",
				nanospell_dictionary: "en"

			 });
            
            
                $.session.set("addedit",1);
                $('#frmOffMenu').validate({
                        rules:{
                            txtmaincontent:{
                                    required:true
                            },
                            txtheadertitle:{
                                    required:true
                            },
                            txtsubtitle:{
                                    required:true
                            },
                            txtbrowsertitle:{
                                    required:true
                            },
                            txturlname:{
                                    required:true
                            },
                            txtmetadescription:{
                                    required:true
                            },
                            txtkeywords:{
                                    required:true
                            }
                        },
                        messages:{
                                txtmaincontent:{
                                        required:"Please enter Main content."
                                },
                                txtheadertitle:{
                                        required:"Please enter Header title."
                                },
                                txtsubtitle:{
                                        required:"Please enter Sub title."
                                },
                                txtbrowsertitle:{
                                        required:"Please enter Browser title."
                                },
                                txturlname:{
                                        required:"Please enter Url."
                                },
                                txtmetadescription:{
                                        required:"Please enter Meta description."
                                },
                                txtkeywords:{
                                        required:"Please enter Keywords."
                                }
                        }
                });
                
                
                $("#btn-danger1").click(function(){
				var formData = new FormData($('form#tab1')[0]);
                                
                                $.ajax({
					type:'POST',
					url:'<?php echo base_url();?>offmenu/delete_header_image/<?php echo $formData['off_menu_id'];?>',
					data: formData,
					success:function(data, textStatus, jqXHR){
						
                                                if(data==1)
						{
							$("#preview").attr("src","<?php echo '../../../assets/upload/No_Image.jpg';?>");
							$().toastmessage('showSuccessToast', "Image removed successfully");
                                                        $(".delete_button").css("display","none");
						}
						else
						{
							$().toastmessage('showErrorToast', "No Image to delete.");
						}
					},
					cache: false,
					contentType: false,
					processData: false
				
				});
                            });
                
                
                
                <?php if($formData['off_header_img']==""){ ?>  
                               $(".delete_button").css("display","none");
		<?php  } ?>	
                
        });
</script>
<div class="content">
        <div class="header">
                <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Create");?> Off Menu</h1>
                <ul class="breadcrumb">
                        <li><a href="<?php echo base_url();?>admin/dashboard">Home</a> </li>
                        <li><a href="<?php echo base_url();?>offmenu">Off Menu</a></li>
                        <li class="active"><?php echo ($edit_id ? "Edit" : "Create");?></li>
                </ul>

        </div>
        <div class="main-content">
                <div class="error">* indicates required field.</div>
                    <div class="panel panel-default" align="left" style="border:0px;">
                    <div class="panel-body" >
                    <div class="dialog1">
                    <form id="frmOffMenu" action="<?php echo base_url();?>offmenu/addedit/<?php echo $edit_id;?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
                            <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id;?>"/>
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Header Title<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('txtheadertitle',$formData['off_headertitle']);?>" id="txtheadertitle" name="txtheadertitle" class="form-control" />
                                    <span class="error"><?php echo form_error('txtheadertitle');?></span>
                                    </div>
                            </div>                            
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Sub Title<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('txtsubtitle',$formData['off_subtitle']);?>" id="txtsubtitle" name="txtsubtitle" class="form-control" />
                                    <span class="error"><?php echo form_error('txtsubtitle');?></span>
                                    </div>
                            </div>                            
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Browser Title<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('txtbrowsertitle',$formData['off_browsertitle']);?>" id="txtbrowsertitle" name="txtbrowsertitle" class="form-control" />
                                    <span class="error"><?php echo form_error('txtbrowsertitle');?></span>
                                    </div>
                            </div>                            
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Page Url<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                    <label style="width:250px;"><?php echo $this->config->item('front_url').'page/';?></label>
                                    <input type="text" value="<?php echo set_value('txturlname',$formData['off_urlname']);?>" id="txturlname" name="txturlname" class="form-control" />
                                    <span class="error"><?php echo form_error('txturlname');?></span>
                                    </div>
                            </div>  
                            <div class="form-group"  >
                                                <label class="col-sm-3 control-label">Background Image<span class="error" ></span></label>
                                                <div class="col-sm-6">
								<input type="file"  id="image" name="image"  value=""/>
                                </div>
                                        </div>  
                                                <?php //echo $formData['image']; ?>
                                            <div class="form-group" >
                                                    <div class="col-sm-offset-3 col-sm-6">
                                                        <img id="preview" name="preview" width="100" src="<?php if($formData['off_header_img']) echo $this->config->item('assets').'upload/header/'.$formData['off_header_img']; else echo $this->config->item('assets').'upload/No_Image.jpg'; ?>"/>
                                                        <a href="#myModal" title="Delete Image" class="delete_button" id="<?php if(isset($formData['off_menu_id'])) echo $formData['off_menu_id']; ?>"  role="button" data-toggle="modal"><i class="fa fa-times-circle-o fa-2x" ></i></a>
                                                        <div>(jpg,jpeg,png and bitmap images are allowed only.)</div>
                                                    </div>
						</div>
                            
                            
                            
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Meta description<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('txtmetadescription',$formData['off_metadescription']);?>" id="txtmetadescription" name="txtmetadescription" class="form-control" />
                                    <span class="error"><?php echo form_error('txtmetadescription');?></span>
                                    </div>
                            </div>                            
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Keywords<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('txtkeywords',$formData['off_keywords']);?>" id="txtkeywords" name="txtkeywords" class="form-control" />
                                    <span class="error"><?php echo form_error('txtkeywords');?></span>
                                    </div>
                            </div>
                            
                            
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Content<span class="error" >*</span></label>
                                    <div class="col-sm-9">
                                        <textarea id="txtmaincontent" rows="3" name="txtmaincontent" class="form-control" /><?php echo set_value('txtmaincontent',  strip_slashes($formData['off_maincontent']));?></textarea>
                                    <span class="error"><?php echo form_error('txtmaincontent');?></span>
                                    </div>
                            </div>
                            <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                    <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save");?>" class="btn btn-primary"/>
                                    <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url();?>offmenu'"/>
                            </div>
                            </div>
                            
                            
                    </form>
                    </div>
                    </div>
            </div>					
        </div>
</div>

<div class="modal small fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
							<h3 id="myModalLabel">Delete Confirmation</h3>
						</div>
						<div class="modal-body">
							<p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete this Image?<br>This cannot be undone.</p>
						</div>
						<div class="modal-footer">
							<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
							<button class="btn btn-primary" id="btn-danger1" data-dismiss="modal">Delete</button>
						</div>
					  </div>
					</div>
</div>