<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.8.7.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<script type="text/javascript">
    $(document).ready(function(){

<?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Table Management'][1]; ?>").addClass("act");

        $('#restaurant_table_form').validate({
            rules:{
                txt_table_name:{
                    required: true
                },
                txt_table_capacity:{
                    required: true,
                    number: true
                }
            },
            messages:{
                txt_table_name:{
                    required:"Please enter table name."
                },
                txt_table_capacity:{
                    required:"Please enter table capacity."
                }
            }
        });

    });
</script>

<div class="content">

    <div class="header">
        <h1 class="page-title"><?php echo ( $table_id ? "Edit" : "Add" ); ?> Table</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/users">Restaurant Tables</a></li>
            <li class="active"><?php echo ( $table_id ? "Edit" : "Add" ); ?></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="panel panel-default" align="left" style="border:0px; margin:0px;">
            <div class="panel-body">
                <div class="dialog1">
                    <div class="error" style="">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-7" style="margin-bottom: 16px;">* indicates required field.</div>
                    </div>

                    <form id="restaurant_table_form" action="" method="POST" class="form-horizontal">
                        <input type="hidden" id="table_id" name="table_id" value="<?php echo $table_id; ?>"/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Table Name<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo set_value('table_name', $form_data['txt_table_name']); ?>" id="txt_table_name" name="txt_table_name" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Table Capacity<span class="error" >*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="<?php echo set_value('table_capacity', $form_data['txt_table_capacity']); ?>" id="txt_table_capacity" name="txt_table_capacity" class="form-control" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label></label>
                            <progress style="display:none;"></progress>
                            <div class="col-sm-offset-3 col-sm-6">
                                <input type="submit" value="<?php echo ( $table_id ? "Update" : "Save" ); ?>" class="btn btn-primary"/>
                                <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/restaurant_table'"/>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

</div>