<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.qtip.js"></script>
<script type="text/javascript">

        $(document).ready(function(){
                <?php $arr = $this->session->userdata('menu');
                    ?>
                    $(".sidebar-nav #menu<?php echo $arr['Role'][1];?>").addClass("act");
                        $.session.set("addedit",1);
                $('#role_form').validate({
                        rules:{
                            txtroletitle:{
                                    required:true
                            },
                            'chkaccess[]':{
                                    required:true
                            }
                        },
                        messages:{
                                txtroletitle:{
                                        required:"Please enter role title."
                                },
                                    'chkaccess[]':{
                                        required:"Check at least one access link."
                                }
                        }
                });
        });
</script>
<div class="content">
        <div class="header">
                <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add");?> Role</h1>
                <ul class="breadcrumb">
                        <li><a href="<?php echo base_url();?>admin/dashboard">Home</a> </li>
                        <li><a href="<?php echo base_url();?>admin/role">Role</a></li>
                        <li class="active"><?php echo ($edit_id ? "Edit" : "Add");?></li>
                </ul>

        </div>
        <div class="main-content">
                <div class="error">* indicates required field.</div>
                    <div class="panel panel-default" align="left" style="border:0px;">
                    <div class="panel-body" >
                    <div class="dialog1">
                    <form id="role_form" action="<?php echo base_url();?>admin/role/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                            <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id;?>"/>
                            <div class="form-group">
                                    <label class="col-sm-3 control-label">Role Title<span class="error" >*</span></label>
                                    <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('user_first_name',$formData['txtroletitle']);?>" id="txtroletitle" name="txtroletitle" class="form-control" />
                                    </div>
                            </div>
                            <div class="form-group">
                                   <label class="col-sm-3 control-label">Description<span class="error" ></span></label>
                                   <div class="col-sm-6">
                                    <input type="text" value="<?php echo set_value('user_last_name',$formData['txtrolediscription']);?>" id="txtrolediscription" name="txtrolediscription" class="form-control" />
                                    </div>
                            </div>
                             <?php if($edit_id!=3)
                                    { ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Access<span class="error" >*</span></label>
                                <div class="col-sm-6">
                                   <?php 
                                     if($allMenu){
                                        foreach($allMenu as $optM){
                                            $checked = "";
                                            if(!empty($menuAccess))
                                            if(in_array($optM['opt_optionid'], $menuAccess))
                                            {
                                                $checked = 'checked=""';
                                            }
                                            ?>

                                        <div>
                                            <label for="chkaccess_<?php echo $optM['opt_optionid'];?>">
                                                <input <?php echo $checked;?> style="width:10px;height:10px;margin-top: none;margin-right: 7px;" id="chkaccess_<?php echo $optM['opt_optionid'];?>" name="chkaccess[]" value="<?php echo $optM['opt_optionid'];?>" type="checkbox">
                                                <?php echo $optM['opt_option_name'];?>
                                            </label>
                                        </div>
                                            <?php
                                        }
                                    }
                                    }
                                    ?>
                                <label id="chkaccess[]-error" class="error" for="chkaccess[]"></label>
                                </div>

                            </div>
                            <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                    <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save");?>" class="btn btn-primary"/>
                                    <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url();?>admin/role'"/>
                            </div>
                            </div>
                    </form>
                    </div>
                    </div>
            </div>					
        </div>
</div>