<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu');
?>
        $(".sidebar-nav #menu<?php echo $arr['FAQ'][1]; ?>").addClass("act");
        /*
         * Purpose: Initialise Tinymce editor.
         * Date: 14 Sept 2016
         * Input Parameter:
         *            None
         *  Output Parameter:
         *            None
         */
                       
    
        tinymce.init({
            selector: "textarea#txtanswer_en",
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

        });
            
            
        var validator = $("#frmOffMenu").submit(function() {
            // update underlying textarea before submit validation
            tinyMCE.triggerSave();
        }).validate({
            ignore: "",
            rules: {
                txtanswer_en:{
                    required:true
                },
                txtquestion_en:{
                    required:true
                }
            },
            messages:{
                txtanswer_en:{
                    required:"Please enter answer."
                },
                txtquestion_en:{
                    required:"Please enter question."
                }
            },
            errorPlacement: function(label, element) {
                // position error label after generated textarea
                if (element.is("textarea")) {
                    label.insertAfter(element.next());
                } else {
                    label.insertAfter(element)
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
                }
            }
        }
                            
            
        $.session.set("addedit",1);
               
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Create"); ?> FAQ</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard">Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/faq">FAQ</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "Create"); ?> FAQ</li>
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
                    <?php $attributes = array('class' => 'form-horizontal', 'id' => 'frmOffMenu'); ?>
                    <?php echo form_open_multipart('admin/faq/addedit/' . @$edit_id, $attributes); ?>
                    <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Question<span class="error" >*</span></label>
                        <div class="col-sm-6">
                            <input type="text" maxlength="200" value="<?php echo set_value('txtquestion_en', $formData['faq_question']); ?>" id="txtquestion_en" name="txtquestion_en" class="form-control" />
                            <span class="error"><?php echo form_error('txtquestion_en'); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Answer<span class="error" >*</span></label>
                        <div class="col-sm-9">
                            <textarea id="txtanswer_en" rows="3" name="txtanswer_en" class="form-control" /><?php echo set_value('txtanswer_en', $formData['faq_answer']); ?></textarea>
                            <span class="error"><?php echo form_error('txtanswer_en'); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-6">
                            <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                            <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/faq'"/>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>					
    </div>
</div>