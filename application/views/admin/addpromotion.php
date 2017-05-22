<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.8.7.custom.min.js"></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/datetimpicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">

<script type="text/javascript">
          
    $(document).ready(function(){

        /*	tinymce.init({
                                selector: "textarea#txtsubtitle",
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
                                forced_root_block : '',
                                external_filemanager_path:"<?php echo $this->config->item('assets'); ?>lib/tinymce/filemanager/",
                                filemanager_title:"Responsive Filemanager" ,
                                external_plugins: { "filemanager" : "<?php echo $this->config->item('assets'); ?>lib/tinymce/filemanager/plugin.js",
                                                                        "nanospell": "<?php echo $this->config->item('assets'); ?>lib/tinymce/nanospell/plugin.js"
                                                                  },
                                nanospell_server: "php",
                                nanospell_dictionary: "en",
                                onchange_callback: function(editor) {
                                        tinyMCE.triggerSave();
                                        $("#" + editor.id).valid();
                                }

                         });*/
			 
        $( "#txtdate" ).datepicker({ dateFormat: 'dd-mm-yy',timeFormat: 'hh:mm TT', use24hours: false, changeMonth:true, changeYear:true});
        /*$( "#txtdate" ).datetimepicker({
        format:'m/d/Y',
        timepicker:false,
        closeOnDateSelect:true
    });*/


<?php $arr = $this->session->userdata('menu') ?>
        $(".sidebar-nav #menu<?php echo $arr['Promotion'][1] ?>").addClass("act");

        $.session.set("addedit",1);
        // alert('hi');
		   
        $('#frmpromotion').validate({
            rules:{
                txttitle:{
                    required:true
                },
                txtsubtitle:{
                    required:true
                },
                image:{
<?php if (!$edit_id) { ?>
                        required:true,
<?php } ?>
                    accept:'jpg,jpeg,bit'
                },
                txtpdf:{
<?php if (!$edit_id) { ?>
                        required:true,
<?php } ?>
                    accept:'pdf'
                }
            },
            messages:{
                txttitle:{
                    required:"Please enter title."
                },
                txtsubtitle:{
                    required:"Please enter short description."
                },
                image:{
                    required:"Please select image.",
                    accept: "Allowed extensions are jpeg or jpg only."
                },
                txtpdf:{
                    required:"Please select pdf.",
                    accept: "Allowed extensions are pdf only."
                }
            }
        });        

    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Promotion</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/promotion">Promotion</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "Add"); ?> Promotion</li>
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
<!--<form id="frmSlider" action="<?php echo base_url(); ?>admin/slider/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">-->
                    <?php $attributes = array('class' => 'form-horizontal', 'id' => 'frmpromotion'); ?>
                    <?php echo form_open_multipart('admin/promotion/addedit', $attributes); ?>
                    <input type="hidden" id="old_img" name="old_img" value="<?php echo $formData['image']; ?>"/>
                    <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Title<span class="error" >*</span></label>
                        <div class="col-sm-6">
                            <input type="text" maxlength="40" value="<?php echo set_value('txttitle', trim($formData['txttitle'])); ?>" id="txttitle" name="txttitle" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Short Description<span class="error" >*</span></label>
                        <div class="col-sm-6">
                       <!--<input type="text" value="<?php echo set_value('txtsubtitle', $formData['txtsubtitle']); ?>" id="txtsubtitle" name="txtsubtitle" class="form-control" />-->
                            <textarea maxlength="300" id="txtsubtitle" name="txtsubtitle" class="form-control" ><?php echo set_value('txtsubtitle', trim($formData['txtsubtitle'])); ?></textarea>
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
                            <img id="preview" name="preview" width="100" src="<?php if ($formData['image']) echo $this->config->item('assets') . 'upload/promotion/' . $formData['image']; else echo $this->config->item('assets') . 'upload/No_Image.jpg'; ?>"/>
                            <div><br/>(jpg and jpeg images are allowed only.)</div>
                        </div>
                    </div>

                    <div class="form-group" id="divPDF">
                        <label class="col-sm-3 control-label">PDF <span class="error" >*</span></label>
                        <div class="col-sm-6">
                            <input type="file" value="" id="txtpdf" name="txtpdf" />
                            <a href="<?php echo $this->config->item('assets') . 'upload/promotion_pdf/' . $formData['txtpdf']; ?>" target="_blank"><?php echo $formData['txtpdf']; ?></a>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-6">
                            <progress style="display:none;"></progress>
                            <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                            <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/promotion'"/>
                        </div>
                        </form>
                    </div>
                </div>
            </div>                  

        </div>
    </div>
</div>