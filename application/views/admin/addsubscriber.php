<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets_admin');?>css/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets_admin');?>css/added_style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets_admin');?>css/jquery-ui.css">
<script type="text/javascript" src="<?php echo $this->config->item('assets_admin');?>js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets_admin');?>js/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets_admin');?>js/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets_admin');?>js/extra_method.js" ></script>
<script type="text/javascript" src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script type="text/javascript">

        $(document).ready(function(){
            
                <?php $arr = $this->session->userdata('menu');?>
				      $(".sidebar-nav #menu<?php echo $arr['Subscriber Management'][1]; ?>").addClass("act");
                $.session.set("addedit",1);
                    
                $.validator.addMethod( 'validemail', function(value) {
                        return /^([\w\-\.]+)@((\[([0-9]{1,3}\.){3}[0-9]{1,3}\])|(([\w\-]+\.)+)([a-zA-Z]{2,4}))$/.test(value);
                },"Please enter valid email address.");   
                
                $('#locum_form').validate({
                        rules:{
                            
                            sub_user_mail:{
                                required:true,
                                validemail:true,
                                remote: {
                                    url: "<?php echo base_url() ?>admin/subscriber/checkemail/",
                                    type: "post",
                                    data: {
                                        sub_mail: function() {
                                            return $( "#sub_user_mail" ).val();
                                        },
                                        edit_id: function() {
                                            return $( "#edit_id" ).val();
                                        }
                                    }
                                }
                            }
                        },
                        messages:{
                            sub_user_mail:{
                                    required:"Please enter your email address.",
                                    remote: "This email is already registered."
                            }
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
                <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add");?> Subscriber</h1>
                <ul class="breadcrumb">
                       <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
                        <li><a href="<?php echo base_url(); ?>admin/subscriber"> Subscriber</a> </li>
                        <li class="active"><?php echo ($edit_id ? "Edit" : "Add");?> Subscriber</li>
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
                        <form id="locum_form" action="<?php echo base_url(); ?>admin/subscriber/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                            <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id;?>"/>
                            
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Subscriber email<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                        <input type="text" value="<?php echo set_value('sub_user_mail',  $formData['sub_user_mail']);?>" id="sub_user_mail" name="sub_user_mail" class="form-control" maxlength="40" />
                                        <span class="error"><?php echo form_error('sub_user_mail');?></span>
                                    </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/subscriber'"/>
                            </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>					
        </div>
</div>