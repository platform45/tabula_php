<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/multiselect.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/custome.css">
<link rel="stylesheet" type="text/css"
      href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.timepicker.css">
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,400italic&subset=latin,cyrillic-ext' rel='stylesheet'
      type='text/css'>

<script type="text/javascript"
        src="<?php echo $this->config->item('assets'); ?>lib/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js"></script>
<script type="text/javascript"
        src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/superfish.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.timepicker.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.formstyler.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js"></script>

<style>
    #tableErrorAfterPlacement{
        color: red;
        padding: 1px;
    }
</style>
<script type="text/javascript">

    $(document).ready(function () {
        <?php $arr = $this->session->userdata('menu');
        ?>

        $(".user_input_hidden_div").hide();

        $("#booking_date").datepicker({
            dateFormat: 'dd-mm-yy',
            timeFormat: 'hh:mm TT',
            minDate: new Date(),
            use24hours: false,
            changeMonth: true,
            changeYear: true
        });

        $('#booking_table').multiselect({
            numberDisplayed: 0,
            buttonWidth: '100%',
            includeSelectAllOption: true,
            nonSelectedText: 'Select Table'
        });

        $("#date_of_birth").datepicker({
            dateFormat: 'dd-mm-yy',
            timeFormat: 'hh:mm TT',
            maxDate: 'now',
            use24hours: false,
            changeMonth: true,
            changeYear: true
        });

        $(".sidebar-nav #menu<?php echo $arr['Booking List'][1]; ?>").addClass("act");
        $.session.set("addedit", 1);



        jQuery.validator.addMethod( 'emailAddress', function(value) {
            return /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,100}|[0-9]{1,3})(\]?)$/.test(value);
        },"Please enter a valid email address.");

        jQuery.validator.addMethod( 'contactNumber', function(value) {
            return /^([0-9])[+()0-9]+$/.test(value);
        },"Please enter a valid phone number.");

        $('#booking_form').validate({
            ignore: ":not(:visible)",
            errorElement: "div",
            rules: {
                no_of_guest: {
                    required: true,
                    digits: true,
                    min: 1,
                    max: 20
                },
                booking_date: {
                    required: true
                },
                booking_time: {
                    required: true
                },
                'booking_table[]': {
                    required: true
                },
                date_of_birth:{
                    required:true
                },
                gender:{
                    required:true
                },
                country_id:{
                    required:true
                },
                region_id:{
                    required:true
                },
                city_id:{
                    required:true
                },
                user_name:{
                    required:true
                },
                user_email:{
                    required:true,
                    emailAddress:true
                },
                txtcontact:{
                    required:true,
                    minlength:10,
                    maxlength: 16,
                    contactNumber: true
                }
            },
            messages: {
                no_of_guest: {
                    required: "Please enter number of guest.",
                    digits: "Please enter number only",
                    min: "Minumum No. of guest should be 1",
                    max: "Maximum No. of guest should be 20"
                },
                booking_date: {
                    required: "Please select date."
                },
                booking_time: {
                    required: "Please select time."
                },
                'booking_table[]': {
                    required: "Please select table(s)."
                },
                date_of_birth:{
                    required:"Please select date of birth."
                },
                gender:{
                    required:"Please select gender."
                },
                country_id:{
                    required:"Please select country."
                },
                region_id:{
                    required:"Please select state."
                },
                city_id:{
                    required:"Please select city."
                },
                user_name:{
                    required:"Please enter user name."
                },
                txtcontact:{
                    required:"Please enter user contact number.",
                    minlength:"Please enter atleast 10 characters"
                },
                user_email:{
                    required:"Please enter user email.",
                    email:"Please enter a valid email."
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "date_of_birth" ) {
                    error.insertAfter("#DOBErrorPlacement");
                }
                else if(element.attr("name") == "booking_date"){
                    error.insertAfter("#bookingDateErrorAfterPlacement");
                }
                else if(element.attr("name") == "gender"){
                    error.insertAfter("#genderErrorAfterPlacement");
                }
                else if(element.attr("name") == "booking_table[]"){
                    error.insertAfter("#tableErrorAfterPlacement");
                }
                else {
                    error.insertAfter(element);
                }
            }

        });


        $("#booking_date").on('change', function () {
            var date = this.value;
            $("#booking_time").html('<option value="">--:--</option>');
            $("#booking_table option").remove();
            $("#booking_table").multiselect('rebuild');
            $.ajax({
                url: '<?php echo base_url(); ?>admin/booking/get_time_slot',
                type: "POST",
                data: {
                    "date": date
                },
                dataType: 'json',
                success: function (data) {
                    var options = '<option value="">--:--</option>';
                    $.each(data.time_slots, function (key, value) {
                        options += "<option value='" + value.slot_id + "'>" + value.time_slot + "</option>";
                    });

                    $("#booking_time").html(options);
                }
            });
        });

        $("#booking_time").on('change', function () {
            var slot_id = this.value;
            var date = $("#booking_date").val();
            $.ajax({
                url: '<?php echo base_url(); ?>admin/booking/get_table_list',
                type: "POST",
                data: {
                    "date": date,
                    "slot_id": slot_id
                },
                dataType: 'json',
                success: function (data) {
                    if (data.result.is_table_list == 1) {
                        $("#next_four_available_time_slots").hide();
                        var table_list = data.result.response.table_list;
                        var options = '';
                        $.each(table_list, function (key, value) {
                            options += "<option data-table_capacity='" + value.table_capacity + "' value='" + value.table_id + "'>" + value.table_name + " (" + value.table_capacity + ")</option>";
                        });
                        $("#booking_table").html(options);
                        $("#booking_table").multiselect('rebuild');
                    }
                    else {
                        $("#booking_table option").remove();
                        $("#booking_table").multiselect('rebuild');
                        var available_time_slots = data.result.response.time_slot;
                        if (available_time_slots.length > 0) {
                            var button = '';
                            $.each(available_time_slots, function (key, value) {
                                button += '<button name="name" value="' + value.slot_id + '" type="button" class="btn btn-primary" disabled>' + value.time_slot + '</button> &nbsp;';
                            });

                            $("#next_four_available_time_slots").show();
                            $("#available_time_slot_div").html(button);
                        }
                        else {
                            alert("Table not available for selected time slot.");
                        }
                    }

                }
            });
        });

        $("#booking_form").submit(function (event) {
            var total_selected_guest = 0;
            $("#booking_table :selected").each(function () {
                total_selected_guest = total_selected_guest + parseInt($(this).attr('data-table_capacity'));
            });

            if(total_selected_guest <= 0){
                $("#tableErrorAfterPlacement").html("Please select Table(s)");
                event.preventDefault();
            }else {
                var inputed_no_of_guest = $("#no_of_guest").val();
                if (inputed_no_of_guest > total_selected_guest) {
                    $("#tableErrorAfterPlacement").html("Guest number should be less than or equal to table capacity.");
                    event.preventDefault();
                }
            }
        });


        //Load all region
        $.post("<?php echo $this->config->item("admin_url") . 'cities/get_region_by_country'; ?>",
            {country_id: $("#country_id").val()},
            function (option_list) {
                if (option_list != 'false')
                    $("#region_id").html(option_list);
                else
                    $("#region_id").html('<option value="">Select State</option>');
                $("#region_id").trigger("change");
            });

        //Load all cities on change region
        $("#region_id").change(function(){
            $.post("<?php echo $this->config->item("admin_url") . 'cities/get_city_by_region'; ?>",
                {region_id: $("#region_id").val()},
                function(option_list){
                    if( option_list != 'false' )
                        $("#city_id").html(option_list);
                    else
                        $("#city_id").html('<option value="">Select City</option>');
                });
        });

        $("#check_user_exist").click(function () {
            $("#btnSubmit").attr("disabled", false);
            var email = $("#user_email").val();
            if(email){
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/booking/check_for_user_exist',
                    type: "POST",
                    data: {
                        "email": email
                    },
                    dataType: 'json',
                    success: function (data) {
                        $("#user_name").val('');
                        $("#user_name").attr("readonly",false);
                        if(data.success == 1)
                        {
                            alert("User found. You can proceed further.");
                            $("#user_name").val(data.result.user_first_name);
                            $("#user_name").attr("readonly",true);
                            $(".user_input_hidden_div").hide();
                            $("input[name=user_id]").attr('value',data.result.user_id);
                            $("input[name=is_user_fields]").attr('value','0');
                        }
                        else {
                            $(".user_input_hidden_div").show();
                            $("input[name = is_user_fields]").attr('value','1');
                        }
                    }
                });
            }
            else {
                alert("Please enter user email.");
            }
        });
    });
</script>


<div class="content">
    <div class="header">
        <h1 class="page-title"><?php echo($edit_id ? "Edit" : "Add"); ?> Booking</h1>
        <ul class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>admin/booking/booking_list">Booking List</a></li>
            <li class="active"><?php echo($edit_id ? "Edit" : "Add"); ?> Booking</li>
        </ul>
    </div>

    <div class="main-content">
        <div class="panel panel-default" align="left" style="border:0px; margin:0px;">
            <div class="panel-body">
                <div class="dialog1">
                    <div class="error " style="">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-7" style="margin-bottom: 16px;">* indicates required field.</div>
                    </div>
                </div>

                <form id="booking_form" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
                    <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Number Of Guest(s)<span class="error">*</span></label>
                        <div class="col-sm-7">
                            <input type="text"
                                   value="<?php echo trim(set_value('no_of_guest', $formData['no_of_guest'])); ?>"
                                   id="no_of_guest" name="no_of_guest" class="form-control" maxlength="2"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-md-7">
                                <label class="col-sm-5 control-label">Booking Date<span class="error">*</span></label>
                                <div class="col-sm-7">
                                    <div class="input-group" id="bookingDateErrorAfterPlacement">
                                        <input type="text"
                                               value="<?php echo trim(set_value('booking_date', $formData['booking_date'])); ?>"
                                               id="booking_date" class="form-control" name="booking_date" readonly>
                                        <label class="input-group-addon btn" for="booking_date">
                                            <span class="fa fa-calendar open-datetimepicker"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label class="col-sm-3 control-label">Time Slot<span class="error">*</span></label>
                                <div class="col-sm-4 booking_time_class">
                                    <select class="form-control" name="booking_time" id="booking_time"
                                            class="form-control"
                                            aria-invalid="false" width="auto">
                                        <option value="">--:--</option>
                                        <?php if ($edit_id) {
                                            foreach ($formData['time_slots'] as $time_slot) {
                                                if ($time_slot['status'] == SUCCESS) {
                                                    ?>
                                                    <option value="<?php echo $time_slot['slot_id']; ?>"
                                                            selected> <?php echo $time_slot['time_slot'] ?> </option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="<?php echo $time_slot['slot_id']; ?>"> <?php echo $time_slot['time_slot'] ?> </option>
                                                    <?php
                                                }

                                            }
                                            ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="next_four_available_time_slots" hidden>
                        <label class="col-sm-3 control-label">Next Available Time Slot(s)<span
                                    class="error">*</span></label>
                        <div class="col-sm-7" id="available_time_slot_div">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tables<span class="error">*</span></label>
                        <div class="col-sm-7 tables">
                            <select class="form-control" name="booking_table[]" id="booking_table" class="form-control" width="auto" multiple>
                                <?php if ($edit_id) {

                                    foreach ($formData['booking_table_list'] as $booking_table) {
                                        if ($booking_table['status'] == SUCCESS) {
                                            ?>
                                            <option data-table_capacity="<?php echo $booking_table['table_capacity'] ?>" value="<?php echo $booking_table['table_id']; ?>" selected> <?php echo $booking_table['table_name'] ?>(<?php echo $booking_table['table_capacity'] ?>) </option>
                                            <?php
                                        } else {
                                            ?>
                                            <option data-table_capacity="<?php echo $booking_table['table_capacity'] ?>" value="<?php echo $booking_table['table_id']; ?>"> <?php echo $booking_table['table_name'] ?>(<?php echo $booking_table['table_capacity'] ?>)
                                            </option>
                                            <?php
                                        }
                                    }
                                    ?>
                                <?php } ?>
                            </select>
                            <div id="tableErrorAfterPlacement"></div>
                        </div>
                    </div>

                    <?php if (!$edit_id) { ?>

                        <div class="dialog2 user_input_hidden_div">
                            <div class="error">
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-7" style="margin-bottom: 16px;">* Please fill required details of user.</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Customer Name<span class="error">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" value="" id="user_name" name="user_name" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Customer Email<span class="error">*</span></label>
                            <div class="col-sm-7">
                                <input type="text" value="" id="user_email" name="user_email" class="form-control"/>
                            </div>
                        </div>


                        <!--                    Start    hidden field of customer info.-->
                        <input type="hidden" value="" id="is_user_fields" name="is_user_fields" />
                        <input type="hidden" value="" id="user_id" name="user_id" />
                        <div class="form-group user_input_hidden_div">
                            <label class="col-sm-3 control-label">Date of Birth<span class="error">*</span></label>
                            <div class="col-sm-4">
                                <div class="input-group" id="DOBErrorPlacement">
                                    <input type="text" value="" id="date_of_birth" class="form-control"
                                           name="date_of_birth" readonly>
                                    <label class="input-group-addon btn" for="date_of_birth">
                                        <span class="fa fa-calendar open-datetimepicker"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group user_input_hidden_div">
                            <label class="col-sm-3 control-label">Gender<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <input type="radio" value="Male" id="gender" name="gender"/> Male
                                <input type="radio" value="Female" id="gender" name="gender"/> Female
                                <div id="genderErrorAfterPlacement"></div>
                            </div>
                        </div>

                        <div class="form-group user_input_hidden_div">
                            <label class="col-sm-3 control-label">Contact No.<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" value="" id="txtcontact" name="txtcontact" class="form-control"
                                       maxlength="50"/>
                            </div>
                        </div>

                        <div class="form-group user_input_hidden_div">
                            <label class="col-sm-3 control-label">Country<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="country_id" id="country_id" aria-invalid="false"
                                        readonly>
                                    <option value="">Select Country</option>
                                    <option value="47" selected>South Africa</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group user_input_hidden_div">
                            <label class="col-sm-3 control-label">State<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="region_id" id="region_id" aria-invalid="false">
                                    <option value="">Select State</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group user_input_hidden_div">
                            <label class="col-sm-3 control-label">City<span class="error">*</span></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="city_id" id="city_id" aria-invalid="false">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                        </div>


                        <!--                    End    hidden field of customer info.-->


                        <div class="form-group">
                            <label></label>
                            <div class="col-sm-offset-3 col-sm-7">
                                <input type="button" name="check_user_exist" value="Check For User" class="btn btn-primary"
                                       id="check_user_exist"/>
                            </div>
                        </div>

                    <?php } ?>

                    <div class="form-group">
                        <label></label>
                        <progress style="display:none;"></progress>
                        <div class="col-sm-offset-3 col-sm-7">
                            <input type="submit" value="<?php echo ($edit_id ? "Update" : "Book"); ?>" class="btn btn-primary" id="btnSubmit" <?php echo ($edit_id ? "" : "disabled"); ?>/>
                            <input type="button" name="cancel" value="Cancel" style="margin-left:20px" class="btn btn-primary"
                                   onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/booking/booking_list'"/>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>