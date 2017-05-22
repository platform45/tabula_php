<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.8.7.custom.min.js"></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/datetimpicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">

<script type="text/javascript">
    $(document).ready(function(){
<?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Restaurant'][1]; ?>").addClass("act");

        $( "#booking_date" ).datepicker({ dateFormat: 'dd-mm-yy',timeFormat: 'hh:mm TT', use24hours: false, changeMonth:true, changeYear:true});

        // Assign time slot and table to modal
        $(document).on('click', '.booking_link', function()
        {
            var is_reserved = 0;
            $("#booking_code").val('');

            if( $(this).parent('td').attr('class') == "reserverd" )
            {
                is_reserved = 1;
                $("#myModalLabel").html('Release Table');
                $(".modal-body .error-text").html('<i class="fa fa-warning modal-icon"></i>Are you sure you want to release the table?<div class="clearfix"></div>');
                $("#btn-reserve").html('Release');
            }
            else
            {
                is_reserved = 0;
                $("#myModalLabel").html('Reserve Table');
                $(".modal-body .error-text").html('<i class="fa fa-warning modal-icon"></i>Are you sure you want to reserve the table?<div class="clearfix"></div>');
                $("#btn-reserve").html('Reserve');
            }

            $('#booking_is_reserved').val( is_reserved );
            $('#booking_table_date').val( $("#booking_date").val() );
            $('#booking_table').val( $(this).attr("data-table") );
            $('#booking_time_slot').val( $(this).attr("data-time") );
        });

        // Reserve table
   
        // Load table booking as per date
        $("#btn-show").click(function(){
            var date = $("#booking_date").val();
            var user_id = $("#user_id").val();
            $.ajax({
                url:'<?php echo base_url(); ?>admin/booking_view/load_booking_table/',
                type:"POST",
                data:{ "date": date,user_id:user_id },
                dataType: 'json',
                success:function( data )
                {
                    if( data.success == 0 )
                    {
                        $().toastmessage('showErrorToast', data.message);
                    }
                    else
                    {
                        $("#restaurant_table_booking").replaceWith( data.view );

                        var expireDateStr = date;
                        var expireDateArr = expireDateStr.split("-");
                        var expireDate = new Date(expireDateArr[2], expireDateArr[1]-1, expireDateArr[0]);
                        var todayDate = new Date();
                        todayDate.setHours(0,0,0,0);

                        if (todayDate > expireDate)
                        {
                            $("td a").attr("data-toggle", '');
                        }

                        $().toastmessage('showSuccessToast', data.message);
                    }
                }
            });
        });

    });
</script>

<div class="content">

    <div class="header">
        <h1 class="page-title">Restaurant Bookings</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
            <li><a href="<?php echo base_url(); ?>admin/restaurant"> Restaurant</a> </li>
            <li class="active">Bookings</li>
        </ul>
    </div>

    <div class="main-content">
        <div class="panel panel-default" align="left" style="border:0px; margin:0px;">

            <div class="panel-body">
                <div class="dialog1">
                    <form id="booking_date_form" action="" method="POST" class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Booking Date</label>
                            <div class="col-sm-6">
                                <?php
                                $timestamp = strtotime($date_selected);
                                $newDate = date('d-m-Y', $timestamp);
                                ?>
                                <input type="text" value="<?php echo set_value('booking_date', $newDate); ?>" id="booking_date" name="booking_date" class="form-control" readonly/>
                            </div>
                            <div class="col-sm-3">
                                <input type="button" value="Show" class="btn btn-primary" id="btn-show"/>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>

        <div class="indicators">
            <span class="reserved">
                Reserved
            </span>
            <span class="available">
                Available
            </span>
        </div>

        <div class="table-responsive custom-resonsive">
            <?php echo $this->load->view('admin/booking_time_table_view1'); ?>
        </div>

        <div class="modal small fade" id="booking_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                        <h3 id="myModalLabel">Reserve Table</h3>
                    </div>
                    <div class="modal-body">
                        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to reserve the table?<div class="clearfix"></div></p>

                        <form id="booking_form" action="" method="POST" class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-5 control-label">Booking Code</label>
                                <div class="col-sm-5">
                                    <input type="text" id="booking_code" name="booking_code" class="form-control"/>
                                </div>
                            </div>
                            <input type="hidden" id="booking_is_reserved" name="booking_is_reserved"/>
                            <input type="hidden" id="booking_table_date" name="booking_table_date"/>
                            <input type="hidden" id="booking_time_slot" name="booking_time_slot"/>
                            <input type="hidden" id="booking_table" name="booking_table"/>
                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id ?>"/>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                        <button class="btn btn-primary" id="btn-reserve" data-dismiss="modal">Reserve</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>