<?php header("Cache-Control: no-store, no-cache, must-revalidate"); ?>
<script src="<?php echo $this->config->item('assets_admin'); ?>js/jquery.datetimepicker.js"></script>
<script src="<?php echo $this->config->item('assets_admin'); ?>js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('assets_admin'); ?>css/jquery-ui.css">
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<style>

.circle {
	padding: 10px !important;
	margin: 5px !important;
	background: #5f2800;
	color: white;
	-moz-border-radius: 50px;
	-webkit-border-radius: 50px;
	border-radius: 50px;
}
.circle:hover { 
    color: white !important;
}

</style>

<div class="content">
  <div class="header">
    <h1 class="page-title">Dashboard</h1>
    <ul class="breadcrumb">
      <li><a class="active" href="<?php echo $this->config->item('admin_base_url'); ?>dashboard"><i class="fa fa-home"></i> Home</a> </li>
      <script type="text/javascript" src="<?php echo $this->config->item('base_url'); ?>assets/admin/js/Chart.js"></script>
    </ul>
  </div>
  <div class="main-content">
    <div align="center" class="form-group"> </div>
    <?php
    $selectqueryUserstotal = "SELECT COUNT(*) AS cnt FROM tab_usermst WHERE is_deleted='0' AND user_type='2'";
    $rqueryuserstotal = $this->db->query($selectqueryUserstotal);
    $cntAlluserstotal = $rqueryuserstotal->row()->cnt;

    $selectqueryUsersActive = "SELECT COUNT(*) AS cnt FROM tab_usermst WHERE is_deleted='0' AND user_type='2' AND user_status='1'";
    $rqueryUsersActive = $this->db->query($selectqueryUsersActive);
    $cntAllUsersActive = $rqueryUsersActive->row()->cnt;

    $selectqueryUsersInactive = "SELECT COUNT(*) AS cnt FROM tab_usermst WHERE is_deleted='0' AND user_type='2' AND user_status='0'";
    $rqueryUsersInactive = $this->db->query($selectqueryUsersInactive);
    $cntAllUsersInactive = $rqueryUsersInactive->row()->cnt;
    ?>
    <div class="form-group">
      <div class="row">
        <div class="count-box chartleft col-md-12" style="border-color: #000;border-image: none;border-width: 1px;box-shadow: 2px 3px 7px #bababa;margin-bottom: 20px;margin-left: 38px;padding: 15px 0 11px 12px;width: 90%;">
        <div id="position">
          
		  <div class="col-md-3 pending-order">
			<label style="color:#03765B;font-weight: bold;font-size: 13px;">Total App Users: 
				<a id="lnkPendingOrder" class="pending-ord-link circle" href="<?php echo base_url(); ?>admin/users">
					<?php echo $cntAlluserstotal ?>
				</a>
			</label>
		  </div>
          
		  <div class="col-md-3 active-chef">
			<label style="color:#03765B;font-weight: bold;font-size: 13px;">Total Active App Users: 
				<a id="lnkPendingOrder" class="pending-ord-link circle" href="<?php echo base_url(); ?>admin/active_users">
					<?php echo $cntAllUsersActive ?>
				</a>
			</label>
		  </div>
          
		  <div class="col-md-3 active-chef">
			<label style="color:#03765B;font-weight: bold;font-size: 13px;margin-right: -219px;">Total Inactive App Users: 
				<a id="lnkPendingOrder" class="pending-ord-link circle" href="<?php echo base_url(); ?>admin/inactive_users">
					<?php echo $cntAllUsersInactive ?>
				</a>
			</label>
		  </div>
		  
		  
        </div>
        </div>
        <div id="SaleByChef" class="chart-box chartleft col-md-6">&nbsp;</div>
        <div id="SaleByMenu" class="chart-box chartright col-md-6">&nbsp;</div>
      </div>
    </div>        


    <div><h4>Restaurant Statistics</h4></div>
    <canvas id="canvas" height="450" width="1200"></canvas>

    <script>
      $(document).ready(function(){   
        var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
        var barChartData = {
          labels : ["Total Restaurants","Active Restaurants","Inactive Restaurants"],
          datasets : [
          {
                            //fillColor : "rgb(3,118,91)", //0,100,0,0.6
                            //strokeColor : "rgb(3,118,91)",//0,100,0,0.8
                            //highlightFill: "rgb(128,0,0)",//0,100,0,0.75
                            //highlightStroke: "rgb(3,118,91)",//0,100,0,1
                            data : [<?php echo $total_count; ?>,<?php echo $total_active_users_count; ?>,<?php echo $total_count - $total_active_users_count; ?>  ]
                          }
                          ]
                        }
                        Chart.types.Bar.extend({
                          name: "BarAlt",
                          draw: function(){
                            this.options.barValueSpacing = this.chart.width / 3;
                            Chart.types.Bar.prototype.draw.apply(this, arguments);
                          }
                        });
                        window.onload = function(){
                          var ctx = document.getElementById("canvas").getContext("2d");
                          window.myBar = new Chart(ctx).Bar(barChartData, {
                            responsive : true
                          }); 
						  
						  
							myBar.datasets[0].bars[0].fillColor = "#483D8B";
							myBar.datasets[0].bars[0].highlightFill = "#483D8B";							
							myBar.datasets[0].bars[1].fillColor = "#008080";	
							myBar.datasets[0].bars[1].highlightFill = "#008080";	
							myBar.datasets[0].bars[2].fillColor = "#800000";	
							myBar.datasets[0].bars[2].highlightFill = "#800000";							
							myBar.update();
                        }
                      });

</script>

</div>
</div>


