<script src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script type="text/javascript">
    /*
     * Purpose: Initialise Tinymce editor.
     * Date: 02 Sept 2016
     * Input Parameter:
     *            None
     *  Output Parameter:
     *            
     *
     *            None
     */
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
        content_css : "<?php echo $this->config->item('assets'); ?>front/css/editor_style.css",
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

$(document).ready(function(){
    
    
    <?php $arr = $this->session->userdata('menu') ?>
    $(".sidebar-nav #menu<?php echo $arr['Content Menu'][1] ?>").addClass("act");
    $(function() {
        var validator = $("#frmContent").submit(function() {
            tinyMCE.triggerSave();
        }).validate({
            ignore: "",
            rules: {
                txtwebpageurl:{
                    required:true
                },
                pdffile:{
                    <?php if (!empty($edit_id)) { ?>
                        required:true,
                        <?php } ?>
                        accept:'pdf'
                    },
                    en_txtbrowsertitle:{
                        required:true
                    },
                    en_txtpagetitle:{
                        required:true
                    },
                    
                    en_txtContent:{
                        required:true
                    },
                },
                messages: {
                    webpageurl:{
                        required:"Please enter web page url."
                    },
                    pdffile:{
                        required:"Please select pdf file.",
                        accept:"Please select proper pdf file."
                    },
                    en_txtbrowsertitle:{
                        required:"Please enter browser title."
                    },
                    en_txtpagetitle:{
                        required:"Please enter page title."
                    },
                    en_txtpageurl:{
                        <?php if ($edit_menu_id == 143) { ?>
                            required:"Please enter learn more url.",
                            <?php } else { ?>
                                required:"Please enter page url.",
                                <?php } ?>
                                remote:"This url is already taken."
                            },
                            en_txtContent:{
                                required:"Please enter content."
                            },
                            image:{
                                required: "Please upload an image.",
                                accept: "Extension should be jpg,jpeg or gif."
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
        });
        // onload setting
        $("#rad_pdf_tab").hide();
        $("#rad_url_tab").hide();
        $('input[type=radio][name=content_radio]').change(function() {
            if (this.value == 'content') {
                $("#rad_content_tab").show();
                $("#rad_pdf_tab").hide();
                $("#rad_url_tab").hide();
            }
            else if (this.value == 'pdf') {
                $("#rad_content_tab").hide();
                $("#rad_pdf_tab").show();
                $("#rad_url_tab").hide();
            }
            else{
                $("#rad_content_tab").hide();
                $("#rad_pdf_tab").hide();
                $("#rad_url_tab").show();
            }
        });
        
        $("#en_txtpageurl").blur();
        
        
        var contentType = '<?php echo $content->cont_content_type; ?>';
        if(contentType == 1)
            $("#rad_content").trigger("click");
        else if(contentType == 2)
            $("#rad_pdf").trigger("click");
        else if(contentType == 3)
            $("#rad_url").trigger("click");
        
        $(".delete_button").click(function() {
            var id = this.id;
            var lang_id = $(".delete_button").attr("lang_id");
            
            // Assign the id to a custom attribute called data-id and language id
            $("#myModal").attr("data-id", id);
            $("#myModal").attr("lang_id", lang_id);
            $("#myModal").attr("aria-hidden",false);
            
        });
        
        $("#btn-danger1").click(function(){
            var formData = new FormData($('form#tab1')[0]);
            
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>admin/cmsmenu/delete_header_image/<?php echo $content->cont_contentid; ?>',
                data: formData,
                success:function(data, textStatus, jqXHR){
                  
                    if(data==1)
                    {
                        $("#preview").attr("src","<?php echo $this->config->item('assets') . 'upload/No_Image.jpg'; ?>");
                        $().toastmessage('showSuccessToast', "Image removed successfully");
                        $(".delete_button").css("display","none");
                        $("#hidd_old_image").val("");
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

<?php if ($content->cont_header_img == "") { ?>  
    $(".delete_button").css("display","none");
    <?php } ?>	      
});
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title">Edit Content</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/cmsmenu">Content Menu</a></li>
            <li class="active">
                Edit Content
            </li>
        </ul>
    </div>
    <?php
    $hide = "";
    $hidehome = "";
    $hidefooter = "";
    $hideForLandingPage = "";
    switch ($edit_menu_id) {
        case 1:
        $hide = "style= 'display: none;'";
        $hidehome = "style= 'display: none;'";
        break;
        case 2:
        $hide = "style= 'display: none;'";
        break;
        case 3:
        $hide = "style= 'display: none;'";
        break;
        case 4:
        $hide = "style= 'display: none;'";
        break;
        case 92:
        $hide = "style= 'display: none;'";
        break;
        case 93:
        $hide = "style= 'display: none;'";
        $hidefooter = "style= 'display: none;'";
        break;
        case 97:
        $hide = "style= 'display: none;'";
        break;
        case 143:
        $hideForLandingPage = "style= 'display: none;'";
        break;
        default:
        $hide = "";
        break;
    }
    ?>   
    <div class="main-content">
        <div class="error " style="">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-6" style="margin-bottom: 16px;">* indicates required field.</div>
        </div> 
        <div class="panel panel-default" align="left" style="border:0px;">
            <div class="panel-body" >
                <div class="dialog1">
                    <form id="frmContent" action="<?php echo base_url(); ?>admin/cmsmenu/content<?php echo (!empty($edit_menu_id) ? '/' . $edit_menu_id : ''); ?>" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="hidd_edit_menu_id" name="hidd_edit_menu_id" value="<?php echo $edit_menu_id; ?>" >
                        <input type="hidden" id="hidd_old_image" name="hidd_old_image" value="<?php echo $content->cont_header_img; ?>" >
                        <div class="form-group" >
                            <label class="col-sm-2 control-label">Menu Title<span class="error" ></span></label>
                            <div class="col-sm-6">
                                <label class="control-label"><?php echo (isset($content->mnu_menu_name) ? $content->mnu_menu_name : ''); ?></label>
                            </div>
                        </div>
                        <div class="form-group" style="display:none" <?php echo $hidefooter; ?> <?php echo $hideForLandingPage; ?> >
                            <label class="col-sm-2 control-label">Content Type<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input checked="checked" type="radio" value="content" class="form-control-radio" id="rad_content" name="content_radio">
                                <label for="rad_content">Content</label>
                                <input type="radio" value="pdf" class="form-control-radio" id="rad_pdf" name="content_radio">
                                <label for="rad_pdf">Pdf</label>
                                <input type="radio" value="url" class="form-control-radio" id="rad_url" name="content_radio">
                                <label for="rad_url">Url</label>
                            </div>
                        </div>
                        <div id="rad_content_tab" >
<!--                            <div class="form-group" <?php echo $hidehome; ?> <?php echo $hidefooter; ?> <?php echo $hideForLandingPage; ?>>
                                <label class="col-sm-3 control-label">Browser Title<span class="error" >*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('en_txtbrowsertitle', isset($content->cont_browser_title) ? $content->cont_browser_title : ''); ?>" class="form-control" id="en_txtbrowsertitle" name="en_txtbrowsertitle">
                                    <span class="error"><?php echo form_error('en_txtbrowsertitle'); ?></span>
                                </div>
                            </div>
                            <div class="form-group" <?php echo $hidehome; ?> <?php echo $hidefooter; ?>  <?php echo $hideForLandingPage; ?>>
                                <label class="col-sm-3 control-label">Page Title<span class="error" >*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('en_txtpagetitle', isset($content->cont_page_title) ? $content->cont_page_title : ''); ?>" class="form-control" id="en_txtpagetitle" name="en_txtpagetitle">
                                    <span class="error"><?php echo form_error('en_txtpagetitle'); ?></span>
                                </div>
                            </div>

                            <div class="form-group" <?php echo $hide; ?> >
                                <?php if ($edit_menu_id == 143) { ?>
                                    <label class="col-sm-3 control-label">Learn More URL<span class="error" >*</span></label>
                                <?php } else { ?>
                                    <label class="col-sm-3 control-label">Page URL<span class="error" >*</span></label>
                                <?php } ?>
                                <div class="col-sm-6">
                                    <label style="width:250px;" <?php echo $edit_menu_id == 143 ? "class='hide'" : ""; ?>><?php echo $this->config->item('front_url'); ?>content/</label>
                                    <input type="text" value="<?php echo set_value('en_txtpageurl', isset($content->cont_url_name) ? $content->cont_url_name : ''); ?>" class="form-control form-url" id="en_txtpageurl" name="en_txtpageurl" >
                                    <span class="error"><?php echo form_error('en_txtpageurl'); ?></span>
                                </div>
                            </div>

                            <div style="display:none" class="form-group" <?php echo $hidehome; ?> <?php echo $hidefooter; ?> >
                                <label class="col-sm-3 control-label">Background Image<?php if ($edit_menu_id == 143) { ?><span class="error" >*</span><?php } ?></label>
                                <div class="col-sm-6">
                                    <input type="file"  id="image" name="image"  value=""/>

                                </div>
                            </div>  
                            <div style="display:none" class="form-group"  <?php echo $hidehome; ?> <?php echo $hidefooter; ?> >
                                <div class="col-sm-offset-3 col-sm-6">
                                    <img width=100 id="preview" name="preview" width="100" src="<?php if ($content->cont_header_img) echo $this->config->item('assets') . 'upload/header/' . $content->cont_header_img; else echo $this->config->item('assets') . 'upload/No_Image.jpg'; ?>"/>
                                    <a href="#myModal" title="Delete Image" class="delete_button" id="<?php echo $content->cont_contentid; ?>"  role="button" data-toggle="modal"><i class="fa fa-times-circle-o fa-2x" ></i></a>
                                    <?php if ($edit_menu_id == 143) { ?>
                                        <div>Image size should be 700px X 660px.<br/>(jpg,jpeg and bitmap images are allowed only.)</div>
                                    <?php } else { ?>
                                        <div>Image size should be 1284px X 247px.<br/>(jpg,jpeg and bitmap images are allowed only.)</div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group" <?php echo $hidefooter; ?> <?php echo $hideForLandingPage; ?>>
                                <label class="col-sm-3 control-label">Meta Description</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="en_txtmetadescription" name="en_txtmetadescription"><?php echo set_value('en_txtmetadescription', isset($content->cont_meta_description) ? $content->cont_meta_description : ''); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group" <?php echo $hidefooter; ?> <?php echo $hideForLandingPage; ?>>
                                <label class="col-sm-3 control-label">Keywords</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="en_txtkeywords" name="en_txtkeywords"><?php echo set_value('en_txtkeywords', isset($content->cont_keywords) ? $content->cont_keywords : ''); ?></textarea>
                                </div>
                            </div>-->
                            <div class="form-group" <?php echo $hidehome; ?> <?php echo $hideForLandingPage; ?>>
                                <label class="col-sm-2 control-label">Content<span class="error" >*</span></label>

                                <div class="col-sm-9">
                                    <textarea value="" rows="3" id="en_txtContent" name="en_txtContent" class="form-control"><?php echo set_value('en_txtContent', isset($content->cont_content) ? strip_slashes($content->cont_content) : ''); ?></textarea>
                                    <span class="error"><?php echo form_error('en_txtContent'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-6">
                                <input type="submit" id="en_submit" name="en_submit" value="Update" class="btn btn-primary" />
                                <input type="button" id="submit" value="Cancel" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/cmsmenu'"/>
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