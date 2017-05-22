<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript" src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
            
<?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Newsletter Management'][1]; ?>").addClass("act");
        $.session.set("addedit",1);
                    
        /*
         * Purpose: Initialise Tinymce editor.
         * Date: 7 Oct 2014
         * Input Parameter:
         *            None
         *  Output Parameter:
         *            None
         */
        tinymce.init({
            selector: "textarea#newsletter_content",
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
            external_filemanager_path:"<?php echo $this->config->item('assets'); ?>lib/tinymce/filemanager/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "<?php echo $this->config->item('assets'); ?>lib/tinymce/filemanager/plugin.js",
                "nanospell": "<?php echo $this->config->item('assets'); ?>lib/tinymce/nanospell/plugin.js"
            },
            nanospell_server: "php",
            nanospell_dictionary: "en"

        });
                
        $("form input[type=submit]").click(function() {
            $("input[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });
                
        var validator = $("#newsletter_form").submit(function() {
            // update underlying textarea before submit validation
                        
                                      
            tinyMCE.triggerSave();
        }).validate({
            ignore: "",
            rules: {
                newsletter_title:{
                    required:true
                },
                newsletter_content:{
                    required:true
                },
                'chbemail[]':{
                    required: function(){
                        var val = $("input[type=submit][clicked=true]").val();
                        if(val == 'Send'){
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                }
            },
            messages:{
                newsletter_title:{
                    required:"Please enter title."
                },
                newsletter_content:{
                    required:"Please enter description."
                },
                'chbemail[]':{
                    required:"Please select at least one email."
                }
            },
            errorPlacement: function(label, element) {
                // position error label after generated textarea
                if (element.is("textarea")) {
                    label.insertAfter("#lblNewsLetter");
                } else if(element.attr("name") == "chbemail[]") {
                    label.insertAfter("#lblSelectAll")
                } else  {
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
                    // ignore IE throwing errors when focusing hidden elements
                }
            }
        }
       
        $("#selectAll").change(function(){
            if(this.checked){
              $(".chbClass").each(function(){
                this.checked=true;
              })              
            }else{
              $(".chbClass").each(function(){
                this.checked=false;
              })              
            }
          });

          $(".chbClass").click(function () {
            if ($(this).is(":checked")){
              var isAllChecked = 0;
              $(".chbClass").each(function(){
                if(!this.checked)
                   isAllChecked = 1;
              })              
              if(isAllChecked == 0){ $("#selectAll").prop("checked", true); }     
            }else {
              $("#selectAll").prop("checked", false);
            }
          });



                
    });
</script>
<style>
    .col-sm-2{
        width:21%!important;
    }
    .col-sm-offset-mod{
        margin-left: 21%!important;
    }
    .main-content .panel {
        margin-bottom: 5px !important;
    }
</style>

<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Create"); ?> Newsletter</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard">Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/newsletter">Newsletter</a></li>
            <li class="active"><?php echo ($edit_id ? "Edit" : "Create"); ?> Newsletter</li>
        </ul>

    </div>
    <div class="main-content">
        <div class="error">* indicates required field.</div>

        <div class="panel panel-default" align="left" style="border:0px;">
            <div class="panel-body" >
                <div class="dialog1">
                    <form id="newsletter_form" action="<?php echo base_url() . 'admin/newsletter/addedit/' . $edit_id; ?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>

                        <div class="form-group">
                            <label class="col-sm-1 control-label" style="text-align: left;">Title<span class="error" >*</span></label>
                            <div class="col-sm-11">
                                <input type="text" value="<?php echo trim(set_value('newsletter_title', $formData['newsletter_title'])); ?>" id="newsletter_title" name="newsletter_title" class="form-control" maxlength="50"  />
                                <span class="error"><?php echo form_error('newsletter_title'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label id="lblNewsLetter" class="col-sm-1 control-label">Description<span class="error" >*</span></label>
                            <div class="col-sm-11">
                                <textarea class="form-control" id="newsletter_content" name="newsletter_content"><?php echo set_value('newsletter_content', stripslashes($formData['newsletter_content'])); ?></textarea>
                                <span class="error"><?php echo form_error('newsletter_content'); ?></span>
                                <label id="newsletter_content-error" class="error" for="newsletter_content"></label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div id="lblSelectAll" class="col-sm-12">
                                    <input id="selectAll" type="checkbox" style="float: left;width: auto;" value="1" name="selectAll">
                                    <label  for="selectAll"  style="margin-left: 5px;">Select all</label>
                                </div>
                                <div class="col-sm-12" style="border-top: 1px solid #ccc;padding: 15px 0;margin-top: 10px;">
                                    <?php if ($subscriberData): ?>
                                        <?php foreach ($subscriberData as $value): ?>
                                            <div class="col-sm-4" >
                                                <input type="checkbox" class="chbClass" id="chbemail<?php echo $value['sub_id'] ?>" name="chbemail[]" value="<?php echo $value['sub_id'] ?>" style="float: left;width: auto;"  maxlength="40" />
                                                <label  style="margin-left: 5px;" for="chbemail<?php echo $value['sub_id'] ?>"><?php echo stripslashes($value['sub_email']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-6">
                                <input type="submit" name="action" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="submit" name="action" value="Send" class="btn btn-primary" style="margin-left:10px"/>
                                <input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/newsletter'"/>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>					
    </div>
</div>