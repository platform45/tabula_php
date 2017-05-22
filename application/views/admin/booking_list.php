<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js"></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.8.7.custom.min.js"></script>
<script src="<?php echo $this->config->item('assets'); ?>lib/datetimpicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">

<style>
    /*.dataTables_length, .dataTables_filter {*/
        /*display: none;*/
    /*}*/
    #booking_details_div, #custom_filter {
        margin-top: 5px;
    }

</style>
<script type="text/javascript">
    var oTable = $('#booking_details').DataTable();

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
        <?php $arr = $this->session->userdata('menu'); ?>
        $(".sidebar-nav #menu<?php echo $arr['Booking List'][1]; ?>").addClass("act");

        $("#booking_date").datepicker({
            dateFormat: 'dd-mm-yy',
            timeFormat: 'hh:mm TT',
            use24hours: false,
            changeMonth: true,
            changeYear: true
        });

        //Get today's booking start
        var todayDate = new Date();
        var twoDigitMonth = ((todayDate.getMonth().length + 1) === 1) ? (todayDate.getMonth() + 1) : (todayDate.getMonth() + 1);
        var currentDate = todayDate.getDate() + "-" + twoDigitMonth + "-" + todayDate.getFullYear();


        if($.session.get("currentDate") != currentDate)
        {
            $("#booking_date").val($.session.get("currentDate"));
        }
        if($.session.get("currentDate") === undefined || $.session.get("currentDate") === null)
        {
            $.session.set("currentDate", currentDate);
            $("#booking_date").val(currentDate);
        }
        load_booking_data(currentDate);
        //Get today's booking end

        $("#booking_date").on('change', function () {
            var date = this.value;
            $.session.set("currentDate", date);
            load_booking_data(date);
        });


        $("#filter").keypress(function(e){
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if(keycode==13)
            {
                if($('#filter').val() != '')
                {
                    var test = $('#filter').val();
                    oTable
                        .columns( 2 )
                        .search( $('#filter').val() )
                        .draw();
                    $.session.set("slider_search",test);

                }
                check_pagination();
            }
        });

        $('#show').click(function(){
            if($('#filter').val() != '')
            {
                var test = $('#filter').val();
                oTable = $('#booking_details').DataTable();
                oTable
                    .columns(2)
                    .search(test)
                    .draw();
                $.session.set("slider_search",test);
            }
            else {
                $('#clear').click();
            }
            check_pagination();
        });

        if($.session.get("addedit")==1)
        {
            if($.session.get("slider_search"))
            {
                $("#filter").val($.session.get("slider_search"));
                $.session.set("is_table_status",0);
                $('#show').click();
            }
            else
            {
                $.session.set("addedit",0);
            }
            check_pagination();
        }
        else if($.session.get("is_table_status")==1)
        {
            $.session.set("is_table_status",0);
            $('#clear').click();
        }
        else
        {
            $.session.set("page_number",0);
            $.session.set("sort_column",0);
            $.session.set("sort_order","asc");
        }

        $('#clear').click( function ()
        {
            $.session.remove('slider_search');
            $('#filter').val('');
            $.session.remove("sort_column");
            $.session.remove("sort_order");
            $.session.set("page_number",0);

            $.session.set("currentDate", currentDate);

            location.reload();
        });


    });

    function check_pagination()
    {
        var page_info = oTable.page.info();
        var page_length = page_info.pages;
        if(parseInt($.session.get("sort_column")) && $.session.get("sort_order"))
        {
            oTable
                .order([parseInt($.session.get("sort_column")),$.session.get("sort_order")])
                .draw(false);
        }
        if(parseInt($.session.get('page_number')) <= page_length)
            oTable.page(parseInt($.session.get('page_number'))).draw(false);
    }

    function load_booking_data(date) {
        var table = $('#booking_details').DataTable();
        table.destroy();

        date = $.session.get("currentDate");
        console.log("current date" + date);

        oTable = $('#booking_details').DataTable({
            "columns":[
                { "bSortable": false, "sClass": "text-center" },
                { "bSortable": false,  "sClass": "text-center" },
                { "bSortable": false, "sClass": "text-center" },
                { "bSortable": false,  "sClass": "text-center" },
                { "bSortable": false,  "sClass": "text-center" },
                { "bsearchable": false, "bSortable": false, "sClass": "text-center" },
                { "bsearchable": false, "bSortable": false, "sClass": "text-center" }
            ],
            "ajax": {
                "url" : "<?php echo $this->config->item("admin_url");?>booking/get_booking_list",
                'type': 'POST',
                'dataType': 'json',
                'data': {
                    'date': date
                },
                beforeSend: function( ) {
                    $(".loading").show();
                },
                complete: function(data ) {
                    console.log(data);
                    $(".loading").hide();
                    $('[data-toggle="tooltip"]').tooltip();
                }
            },
            "dom" : '<tlip>',
            "iDisplayLength":25,
            "lengthMenu": [ 25, 50, 75, 100 ],
            "processing": true,
            "serverSide": true
        });

        $('#booking_details tr').removeClass('sorting_asc');
    }

    $('body').on('mouseenter', '.tdHover', function () {
        if ($(this).attr('data-toggle') != 'popover') {
            $(this).popover({
                container: 'body',
                placement: 'right',
                trigger: 'hover'
            }).popover('show');
        }
    });
</script>

<div class="content">

    <div class="header">
        <h1 class="page-title">Booking List</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
            <li class="active">Booking List</li>
        </ul>
    </div>

    <div class="main-content">
        <div class="row">
            <div class="col-md-10 col-lg-10">
                <div class="col-md-2 col-lg-2">
                    <label class="control-label">Select Date</label>
                </div>

                <div class="col-md-4 col-lg-4 input-group">
                    <input type="text" id="booking_date" value="<?php echo date('d-m-y'); ?>" class="form-control"
                           name="booking_date" readonly>
                    <label class="input-group-addon btn" for="booking_date">
                        <span class="fa fa-calendar open-datetimepicker"></span>
                    </label>
                </div>
            </div>

        </div>

        <div class="col-md-12" id="custom_filter">
            <input type="text" class="form-control" style="width:20%;display:inline;" id="filter" name="filter" placeholder="Search Guest" value="<?php echo set_value('filter'); ?>"/>
            <input type="button" class="btn btn-primary" style="margin-left:10px" id="show" name="show" value="Show"/>
            <input type="button" class="btn btn-primary" id="clear" name="clear" style="margin-left:10px" value="Clear" />
            <a href="<?php echo base_url(); ?>admin/booking/addedit" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Booking </a>
        </div>

        <div class="col-md-12 col-lg-12" id="booking_details_div">
            <table id="booking_details" class="display cell-border" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>SEQ. NO.</th>
                    <th>TIME</th>
                    <th>GUEST NAME</th>
                    <th>TOTAL GUEST</th>
                    <th>TABLES</th>
                    <th>BOOKING BY</th>
                    <th>EDIT</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div style="clear: both"></div>
    </div>
</div>