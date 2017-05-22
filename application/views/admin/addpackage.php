<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets');?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets');?>lib/extra_method.js" ></script>
<script type="text/javascript">
		  
$(document).ready(function(){
    <?php $arr = $this->session->userdata('menu')?>
        $(".sidebar-nav #menu<?php echo $arr['Package'][1]?>").addClass("act");

            $.session.set("addedit",1);
            
        $('#frmSlider').validate({
                rules:{
                        txtprice:{
                            required:true
                        }
                },
                messages:{
                        txtprice:{
                            required:"Please enter price."
                        }
                }
        });	
		
		 $("#txtprice").keydown(function (e) { 
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
		
    });

</script>
        <div class="content">
                <div class="header">
                        <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add");?> Package</h1>
                        <ul class="breadcrumb">
                                <li><a href="<?php echo base_url();?>admin/dashboard">Home</a> </li>
                                <li><a href="<?php echo base_url();?>admin/package">Package</a></li>
                                <li class="active"><?php echo ($edit_id ? "Edit" : "add");?></li>
                        </ul>
                </div>
                <div class="main-content">
				<div class="error">* indicates required field.</div>
					<div class="panel panel-default" align="left" style="border:0px;">
                    
                                        <div class="panel-body" >
                                        <div class="dialog1">
					<form id="frmSlider" action="<?php echo base_url();?>admin/package/addedit" method="POST" enctype="multipart/form-data" class="form-horizontal">
                                     
						<input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id;?>"/>
						<div class="form-group">
								<label class="col-sm-3 control-label">Title<span class="error" >*</span></label>
                                <div class="col-sm-6">
								<input type="text" value="<?php echo set_value('txttitle',$formData['txttitle']);?>" id="txttitle" name="txttitle" class="form-control" readonly />
                                </div>
						</div>
						<div class="form-group">
								<label class="col-sm-3 control-label">Price(₦)<span class="error" >*</span></label>
                                <div class="col-sm-6" id="staticParent">
								<input type="text" value="<?php echo set_value('txtprice',$formData['txtprice']);?>" id="txtprice" name="txtprice" class="form-control" />
                                </div>
						</div>
						<div class="form-group">
                        	<div class="col-sm-offset-3 col-sm-6">
							<progress style="display:none;"></progress>
								<input type="submit" value="<?php echo ($edit_id ? "Update" : "Save");?>" class="btn btn-primary"/>
								<input type="button" value="Cancel" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url();?>admin/package'"/>
						</div>
					</form>
                                </div>
                                </div>
			</div>					

            </div>
    </div>
    </div>