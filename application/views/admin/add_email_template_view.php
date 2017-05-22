<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui.js"></script>

<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css"/>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js"></script>

<script type="text/javascript">
    /*
     * Purpose: Initialise Tinymce editor.
     * Date: 7 Oct 2014
     * Input Parameter:
     *            None
     *  Output Parameter:
     *            None
     */
    <?php $arr = $this->session->userdata('menu');?>
    $(".sidebar-nav #menu<?php echo $arr['Email Templates'][1]?>").addClass("act");
    tinymce.init({
        selector: "textarea#email_body",
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
        directionality: 'ltr',
        relative_urls: false,
        remove_script_host: false,
        force_br_newlines: true,
        force_p_newlines: false,
        browser_spellcheck: true,
        content_css: "<?php echo $this->config->item('assets');?>front/css/editor_style.css",
        height: 400,

        forced_root_block: false,
        external_filemanager_path: "<?php echo $this->config->item('assets');?>lib/tinymce/filemanager/",
        filemanager_title: "Responsive Filemanager",
        external_plugins: {
            "filemanager": "<?php echo $this->config->item('assets');?>lib/tinymce/filemanager/plugin.js",
            "nanospell": "<?php echo $this->config->item('assets');?>lib/tinymce/nanospell/plugin.js"
        },
        nanospell_server: "php",
        nanospell_dictionary: "en"

    });


    $(document).ready(function () { <?php $arr = $this->session->userdata('menu') ?>

        $.session.set("addedit", 1);

        $.validator.addMethod("validEmail", function (value, element) {
            if (value == '')
                return true;
            var temp1;
            temp1 = true;
            var ind = value.indexOf('@');
            var str2 = value.substr(ind + 1);
            var str3 = str2.substr(0, str2.indexOf('.'));
            if (str3.lastIndexOf('-') == (str3.length - 1) || (str3.indexOf('-') != str3.lastIndexOf('-')))
                return false;
            var str1 = value.substr(0, ind);
            if ((str1.lastIndexOf('_') == (str1.length - 1)) || (str1.lastIndexOf('.') == (str1.length - 1)) || (str1.lastIndexOf('-') == (str1.length - 1)))
                return false;
            str = /(^[a-zA-Z0-9]+[\._-]{0,1})+([a-zA-Z0-9]+[_]{0,1})*@([a-zA-Z0-9]+[-]{0,1})+(\.[a-zA-Z0-9]+)*(\.[a-zA-Z]{2,3})$/;
            temp1 = str.test(value);
            return temp1;
        }, "Please enter valid email.");

        $('#frmContent').validate({
            rules: {
                email_from: {
                    required: true,
                    validEmail: true
                },
                email_name: {
                    required: true,
                    characterCheck: true
                },
                email_subject: {
                    required: true

                },
                email_body: {
                    required: true

                }
            },
            messages: {
                email_from: {
                    required: "Please enter from email."
                },
                email_name: {
                    required: "Please enter email title."
                },
                email_subject: {
                    required: "Please enter email subject."
                },
                email_body: {
                    required: "Please enter email body."
                }
            }
        });

    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo $title; ?></h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item('base_url'); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a>
            </li>
            <li><a href="<?php echo base_url() . 'admin/emailtemplate'; ?>"><?php echo $sParentTitle; ?></a></li>
            <li class="active"><?php echo $title; ?></li>
        </ul>
    </div>

    <div class="main-content">
        <div style="" class="error ">
            <label class="col-sm-2 control-label"></label>
            <div style="margin-bottom: 16px;" class="col-sm-7">* indicates required field.</div>
        </div>
        <div class="panel panel-default" align="left" style="border:0px;">
            <div class="panel-body">
                <div class="dialog1">
                    <form id="frmContent"
                          action="<?php echo $this->config->item('base_url'); ?>admin/emailtemplate/addedit<?php echo(!empty($aFormData['email_id']) ? '/' . $aFormData['email_id'] : ''); ?>"
                          method="POST" enctype="multipart/form-data" class="form-horizontal">
                        <input type="hidden" id="hidEmtID" name="hidEmtID" value="<?php echo $aFormData['email_id']; ?>">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email From<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <input type="email"
                                       value="<?php echo set_value('email_from', isset($aFormData['email_from']) ? $aFormData['email_from'] : ''); ?>"
                                       class="form-control email required" id="email_from" name="email_from">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email Title<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <input type="text"
                                       value="<?php echo set_value('email_name', isset($aFormData['email_name']) ? $aFormData['email_name'] : ''); ?>"
                                       class="form-control" id="email_name" name="email_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email Subject<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <input type="text"
                                       value="<?php echo set_value('email_subject', isset($aFormData['email_subject']) ? $aFormData['email_subject'] : ''); ?>"
                                       class="form-control" id="email_subject" name="email_subject">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email Body<span class="error"></span></label>
                            <span class="error"><?php echo form_error('email_body'); ?></span>
                            <div class="col-sm-9">
                                <textarea value="" rows="3" id="email_body" name="email_body"
                                          class="form-control"><?php echo set_value('email_body', isset($aFormData['email_body']) ? $aFormData['email_body'] : ''); ?></textarea>
                            </div>
                        </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <input style="margin-left: 187px;" type="submit" id="en_submit" name="en_submit"
                               value="<?php echo (empty($aFormData['email_id'])) ? ' Add ' : 'Update' ?>"
                               class="btn btn-primary"/>
                        <input type="button" id="submit" value="Cancel" class="btn btn-primary"
                               onclick="javascript:window.location.href='<?php echo base_url('admin/emailtemplate') ?>'"/>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
