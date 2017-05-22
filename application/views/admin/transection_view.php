<link rel="stylesheet" href="<?php echo $this->config->item('assets_admin');?>css/jquery-ui.css">
<script src="<?php echo $this->config->item('assets_admin');?>js/jquery.validate.js"></script>
<script src="<?php echo $this->config->item('assets_admin');?>js/additional-methods.min.js"></script>
<script src="<?php echo $this->config->item('assets_admin');?>js/jquery-ui.js"></script>
<script src="<?php echo $this->config->item('assets_admin');?>js/page/order.js"></script>
<script type="text/javascript" >
    $(document).ready(function() {
        <?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #order").addClass("act");
        
    });
</script>
<div class="content">
    <div class="header">
        <h1 class="page-title"></h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->item('admin_base_url'); ?>dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>/admin/transaction">Transaction</a></li>
            <li class="active">Transaction History</li>
        </ul>
    </div>

    <div class="main-content order-detail">
        <div class="error">* indicates required field.</div>
        <div class="panel panel-default" align="left" style="border:0px;">
            <div class="panel-body" >
                <div class="dialog1">
                   
                        
                        <div class="page-header">
                            <h3>Transection History</h3>
                        </div>
                        
                    <div class="row" style="line-height: 4;">
                        <?php 
                        if($transectionData->is_yearly == 1)
                        {
                           $package =  "Yearly";
                        }
                        else if($transectionData->is_monthly==1)
                        {
                            $package =  "Monthly";    
                        }
                        else
                        {
                            $package =  "Per Video";  
                        }
                        ?>
                         <div class="col-md-12 control-label"> 
                        <div class="col-md-3"> Username</div>
                        <div class="col-md-6"> <?php echo $transectionData->user_first_name?$transectionData->user_first_name." ".$transectionData->user_last_name :"--" ?></div>
                        </div > 
                        <div class="col-md-12 control-label"> 
                        <div class="col-md-3"> Transaction Id</div>
                        <div class="col-md-6"> # <?php echo $transectionData->transect_id ?></div>
                        </div > 
                         <div class=" col-md-12 control-label"> 
                        <div class="col-md-3"> Transaction number</div>
                        <div class="col-md-6">  <?php echo $transectionData->transection_no ?></div>
                        </div >
                        <div class=" col-md-12 control-label"> 
                        <div class="col-md-3"> Transaction Date</div>
                        <div class="col-md-6"> <?php echo $transectionData->date ?></div>
                        </div >
                        <div class=" col-md-12 control-label"> 
                        <div class="col-md-3"> Amount</div>
                        <div class="col-md-6"> <?php echo $transectionData->amount ?></div>
                        </div >
                        <div class=" col-md-12 control-label"> 
                            <div class="col-md-3"> Subscription package</div>
                            <div class="col-md-6"> <?php echo $package ?></div>
                        </div>
                         <div class=" col-md-12 control-label"> 
                            <div class="col-md-3"> Transection By</div>
                            <div class="col-md-6"> <?php echo $transectionData->transection_by ?></div>
                        </div>
                        <div class=" col-md-12 control-label"> 
                        <div class="col-md-3"> Account Status</div>
                        <div class="col-md-6"> <?php echo $transectionData->account_status ?></div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <progress style="display:none;"></progress>                              
                                <input type="button" value="Back" style="margin-left:10px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/transaction'"/>
                            </div>
                        </div>
                   </div><!-- rad_content_tab -->
                        
                    
                   
                </div><!-- dialog1 -->
            </div><!-- panel-body -->
        </div><!-- panel panel-default -->
    </div><!-- main-content -->
</div><!-- content -->