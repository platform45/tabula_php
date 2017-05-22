<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/extra_method.js" ></script>
<script type="text/javascript">
tinymce.init({
				selector: "textarea#en_txtContent",
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
                                content_css : "<?php echo $this->config->item('assets');?>front/css/editor_style.css",
				height : 400,
                                
				forced_root_block : false,
				external_filemanager_path:"<?php echo $this->config->item('assets');?>lib/tinymce/filemanager/",
				filemanager_title:"Responsive Filemanager" ,
				external_plugins: { "filemanager" : "<?php echo $this->config->item('assets');?>lib/tinymce/filemanager/plugin.js",
									"nanospell": "<?php echo $this->config->item('assets');?>lib/tinymce/nanospell/plugin.js"
								  },
				nanospell_server: "php",
				nanospell_dictionary: "en",
                                // update validation status on change
                                onchange_callback: function(editor) {
                                        tinyMCE.triggerSave();
                                        $("#" + editor.id).valid();
                                }

			 });
			

$(document).ready(function(){
    
    $( "#txtdate" ).datetimepicker({
        format:'m/d/Y',
        timepicker:false,
        closeOnDateSelect:true
    });
    
    
    <?php $arr = $this->session->userdata('menu')?>
        $(".sidebar-nav #menu<?php echo $arr['News'][1]?>").addClass("act");

            $.session.set("addedit",1);
        
        
        $(function() {
		var validator = $("#frmSlider").submit(function() {
			// update underlying textarea before submit validation
			tinyMCE.triggerSave();
		}).validate({
			ignore: "",
			rules: {
				txttitle:{
                                    required:true,
                                    remote:{
                                        url: "<?php echo base_url().'newsevents/check_title_exists/'.$edit_id;?>",
                                        type: "post",
                                        data: {
                                            "title": function(){ return $("#txttitle").val(); }
                                        }
                                    }
                                },
                                en_txtContent:{
                                        required:true

                                },
                                txtbrief: {
                                        required:true
                                },
                                image:{
                                    <?php if(!$edit_id){?>
                                    required:true,
                                    <?php }?>
                                    accept:'jpg,jpeg,bit,gif'
                                },
                                txtdate:{
                                          required: true,
                                           date: true
                                }
			},
			errorPlacement: function(label, element) {
				// position error label after generated textarea
				if (element.is("textarea")) {
					label.insertAfter(element.next());
				} else {
					label.insertAfter(element)
				}
			}, messages:{
                        txttitle:{
                            required:"Please enter title.",
                            remote : "News title already exist."
                        },
                        en_txtContent:{
			  required:"Please enter content."
			},
                        txtbrief: {
                                required:"Please entet news brief."
                        },
                        image:{
                            required:"Please select image.",
                            accept: "Extension should be jpg,jpeg or gif."
                        },
                        txtdate:{
                            required: "Please select new date",
                            date: "Please enter valid date"
                        } 
                }
		});
		validator.focusInvalid = function() {
			// put focus on tinymce on submit validation
			if (this.settings.focusInvalid) {
				try {
					var toFocus = $(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
					if (toFocus.is("textarea")) {
						tinyMCE.get(toFocus.attr("id")).focus();
					} else {
						toFocus.filter(":visible").focus();
					}
				} catch (e) {
					// ignore IE throwing errors when focusing hidden elements
				}
			}
		}
	});
        
        
        
        
        
        
        
        
        /*$('#frmSlider').validate({
                ignore: ":hidden:not(#en_txtContent)",
                rules:{
                        txttitle:{
                            required:true,
                            remote:{
                                url: "<?php echo base_url().'newsevents/check_title_exists/'.$edit_id;?>",
                                type: "post",
                                data: {
                                    "title": function(){ return $("#txttitle").val(); }
                                }
                            }
                        },
                        en_txtContent:{
				required:true
                                                   
			}
                },
                messages:{
                        txttitle:{
                            required:"Please enter title.",
                            remote : "News + Events title already exist."
                        },
                        en_txtContent:{
			  required:"Please enter content."
			}
                }
        });*/			
    });
</script>
        <div class="content">
                <div class="header">
                        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add");?> News</h1>
                        <ul class="breadcrumb">
                                <li><a href="<?php echo base_url();?>admin/dashboard">Home</a> </li>
                                <li><a href="<?php echo base_url();?>newsevents">News</a></li>
                                <li class="active"><?php echo ($edit_id ? "Edit" : "add");?></li>
                        </ul>
                </div>
                <div class="main-content">
				<div class="error">* indicates required field.</div>
					<div class="panel panel-default" align="left" style="border:0px;">
                    
                                        <div class="panel-body" >
                                        <div class="dialog1">
					<form id="frmSlider" action="<?php echo base_url();?>newsevents/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                                                <input type="hidden" id="old_img" name="old_img" value="<?php echo $formData['image'];?>"/>
						<input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id;?>"/>
						<div class="form-group">
								<label class="col-sm-3 control-label">Date<span class="error" >*</span></label>
                                <div class="col-sm-6">
								<input type="text" value="<?php echo set_value('txtdate',$formData['txtdate']);?>" id="txtdate" name="txtdate" class="form-control" />
                                </div>
						</div>
                                                
                                                <div class="form-group">
								<label class="col-sm-3 control-label">Title<span class="error" >*</span></label>
                                <div class="col-sm-6">
								<input type="text" value="<?php echo set_value('txttitle',$formData['txttitle']);?>" id="txttitle" name="txttitle" class="form-control" />
                                </div>
						</div>
                                                <div class="form-group">
                                                        <label class="col-sm-3 control-label">Brief <span class="error">*</span></label>
                                                        <div class="col-sm-6">
                                                            <textarea class="form-control" name="txtbrief" id="txtbrief" maxlength="300"><?php echo set_value('txtbrief',$formData['txtbrief']);?></textarea>
                                                            <label id="txtbrief-error" class="error" for="txtbrief"></label>
                                                        </div>
                                                        
						</div>
                                                
                                                <div class="form-group">
								<label class="col-sm-3 control-label">Image<span class="error">*</span></label>
                                <div class="col-sm-6">
								<input type="file" value="" name="image" id="image">
                                </div>
						</div>
                                                
                                                
                                                <div class="form-group" >
                                                    <div class="col-sm-offset-3 col-sm-6">
                                                        <img id="preview" name="preview" width="100" src="<?php if($formData['image']) echo $this->config->item('assets').'upload/newsevents/'.$formData['image']; else echo $this->config->item('assets').'upload/No_Image.jpg';?>"/>
                                                        <div>(jpg,jpeg and bitmap images are allowed only.)</div>
                                                    </div>
						</div>
                                                
                                                <div id="content_data4" class="form-group">
								<label class="col-sm-3 control-label">Meta Keywords:</label>
								<div class="col-sm-6">
									<textarea style="resize: none;" id="metakeywords" maxlength="200" class="form-control" name="metakeywords"><?php echo set_value('metakeywords',$formData['metakeywords']);?></textarea>
								</div>
                                                                
                                                </div>
                                                <div id="content_data5" class="form-group">
								<label class="col-sm-3 control-label">Meta Description:</label>
								<div class="col-sm-6">
									<textarea style="resize: none;" id="metatag" maxlength="200" class="form-control" name="metatag"><?php echo set_value('metatag',$formData['metatag']);?></textarea>
								</div>
                                                </div>
                                                
                                                <div class="form-group">
                                                <label class="col-sm-3 control-label">Content<span class="error" >*</span></label>
                                                <span class="error"><?php echo form_error('en_txtContent');?></span>
                                                <div class="col-sm-9">
                                                <textarea value="" rows="3" id="en_txtContent" name="en_txtContent" class="form-control"><?php echo set_value('en_txtContent',isset($formData['txtdesc'])?strip_slashes($formData['txtdesc']):'');?></textarea>
                                                <label id="txttitle-error" class="error" for="en_txtContent"></label>
                                                </div>
                                                
                                        </div>
                                                
						
						
						<div class="form-group">
                        	<div class="col-sm-offset-3 col-sm-6">
							<progress style="display:none;"></progress>
								<input type="submit" value="<?php echo ($edit_id ? "Update" : "Save");?>" class="btn btn-primary"/>
								<input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url();?>newsevents'"/>
						</div>
					</form>
                                </div>
                                </div>
			</div>					

            </div>
    </div>
    </div>