<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.qtip.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/added_style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/multiselect.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/custome.css">
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('assets'); ?>stylesheets/jquery.timepicker.css">
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,400italic&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>

<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/datetimpicker.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/modernizr-2.6.2-respond-1.1.0.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.validate.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery-ui-1.10.4.custom.min.js" ></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.qtip.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/superfish.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.timepicker.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/jquery.formstyler.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('assets'); ?>lib/extra_method.js" ></script>

<style>
    #divLargerImage
    {
        /*display: none;*/
        /*width: 250px;*/
        /*height: 250px;*/
        /*position: absolute;*/
        /*top: 35%;*/
        /*left: 35%;*/
        /*z-index: 99;*/
        bottom: auto;
        display: none;
        height: auto;
        left: 50%;
        position: absolute;
        right: auto;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 600px;
        z-index: 99;
    }

    #divOverlay
    {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        background-color: #CCC;
        opacity: 0.5;
        width: 100%;
        height: 100%;
        z-index: 98;
    }
    #divLargerImage img{
        height: auto !important;
        width: 100%; !important;
    }
	 .open_close_select_option{
        min-width: 70px !important;
    }
</style>

<script type="text/javascript">

  $(document).ready(function(){
    <?php $arr = $this->session->userdata('menu');
    ?>

    /*$('input.timepicker1').timepicker({ timeFormat: 'HH:mm:ss',
      maxHour: 23,
      maxMinutes: 30,
      <?php if (!$edit_id) { ?>
        defaultTime: '09:00:00',
        <?php } ?>
        interval: 30 }); 

    $('input.timepicker2').timepicker({ timeFormat: 'HH:mm:ss',
      maxHour: 23,
      maxMinutes: 30,
      <?php if (!$edit_id) { ?>
        defaultTime: '20:00:00',
        <?php } ?>
        interval: 30 }); */

    $("#alert_button").click( function(e)
    {
      e.preventDefault();

      jAlert('Please Select Time ', 'Alert Dialog');
    });

    $("#new_password").qtip();
    $("#conf_password").qtip();

    $('#cusine').multiselect({
      numberDisplayed: 0,
      buttonWidth: '70%',
      includeSelectAllOption: true,
      nonSelectedText :'Select Cuisine'
    });
    $('#ambience').multiselect({
      numberDisplayed: 0,
      buttonWidth: '70%',
      includeSelectAllOption: true,
      nonSelectedText :'Select Amenities'
    });
    $('#dietary').multiselect({
      numberDisplayed: 0,
      buttonWidth: '70%',
      includeSelectAllOption: true,
      nonSelectedText :'Select Dietary Preference'
    });

    <?php if(!$edit_id) 
    { ?>
     $("#region_id").html('<option value="">Select State</option>');
     $("#city_id").html('<option value="">Select City</option>');
     $.post("<?php echo $this->config->item("admin_url") . 'cities/get_region_by_country'; ?>",
      {country_id: $("#country_id").val()},
      function(option_list){
        if( option_list != 'false' )
          $("#region_id").html(option_list);
        else
          $("#region_id").html('<option value="">Select State</option>');
        $("#region_id").trigger("change");
      });
     <?php } ?> 

     $("#country_id").change(function(){
      $.post("<?php echo $this->config->item("admin_url") . 'cities/get_region_by_country'; ?>",
        {country_id: $("#country_id").val()},
        function(option_list){
          $("#region_id").html(option_list);
          $("#region_id").trigger("change")
        });
    })
     $("#region_id").change(function(){
      $.post("<?php echo $this->config->item("admin_url") . 'cities/get_city_by_region'; ?>",
        {region_id: $("#region_id").val()},
        function(option_list){
          $("#city_id").html(option_list);
          $("#city_id").trigger("change")
        });
    })

     jQuery.validator.addMethod( 'emailAddress', function(value) {
      return /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,100}|[0-9]{1,3})(\]?)$/.test(value);
    },"Please enter a valid email address.");

     jQuery.validator.addMethod( 'passwordMatch', function(value) {
      if(value != '')
        return /^.*(?=.{6,20})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_~\-!@#\$%\^&\*\(\)]).*$/.test(value);
      else
        return true;
    });

     $.validator.addMethod('validateCheckBoxes', function (value) {
      return $('.validateCheckBoxes:checked').size() > 0; }, 'Please select days.');

     var checkboxes = $('.validateCheckBoxes');
     var checkbox_names = $.map(checkboxes, function(e,i) { return $(e).attr("name")}).join(" ");

     $('#user_form').validate({
      groups: { checks: checkbox_names },
      ignore:"",
      errorElement: "div",
      rules:{
        txtrname:{
          required:true
        },
        resownername:{
          required:true
        },
        'cusine[]':{
          required:true
        },
        'ambience[]':{
          required:true
        },
        'dietary[]':{
          required:true
        },
        avgspend:{
          required:true,
          number: true
        },

        latitude:{
          required:true
        },
        longitude:{
          required:true
        },
        description:{
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
        address:{
          required:true
        },
        txtemail:{
          required:true,
          emailAddress:true,
          
        },
        txtcontact:{
          required:true,
          minlength:10,
          maxlength:10,
          number: true
        },
        new_password:{
          <?php if (!$edit_id) { ?>
            required:true,
            <?php } ?>
            minlength:6,
            passwordMatch:true
          },
          conf_password:{
            <?php if (!$edit_id) { ?>
              required:true,
              <?php } ?>
              equalTo: "#new_password"
            },
            image : {
              <?php if (!$edit_id) { ?>
                required:true,
                <?php } ?>
                accept:'jpg,jpeg'
              }
            },
            messages:{
              txtrname:{
                required:"Please enter restaurant name."
              },
              resownername:{
                required:"Please enter restaurant owner name."
              },
              'cusine[]':{
                required:"Please select cuisine title.",
              },
              'ambience[]':{
                required:"Please select amenities title."
              },
              'dietary[]':{
                required:"Please select dietary preference title."
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
              address:{
                required:"Please enter address."
              },
              txtemail:{
                required:"Please enter email.",
                email:"Please enter a valid email."
              },
              txtcontact:{
                required:"Please enter contact no."
              },
              avgspend:{
                required:"Please enter average spend amount."
              },
              bankname:{
                required:"Please enter bank name."
              },
              bankaccnum:{
                required:"Please enter bank account number."
              },
              bankbrnum:{
                required:"Please enter bank branch number."
              },
              bankaccholdername:{
                required:"Please enter bank account holder name."
              },
              latitude:{
                required:"Please enter latitude."
              },
              longitude:{
                required:"Please enter longitude."
              },
              description:{
                required:"Please enter restaurant description."
              },
              new_password:{
                <?php if (!$edit_id) { ?>
                  required:"Please enter new password.",
                  <?php } ?>

                  minlength:"Please enter password atleast 6 characters long.",
                  passwordMatch:"Password Invalid. Password must contain : 6 characters ( 1 Upper, 1 lower, 1 number and 1 symbol.)"
                },
                conf_password:{
                  <?php if (!$edit_id) { ?>
                    required:"Please enter confirm password.",
                    <?php } ?>
                    equalTo: "Please enter same password again."
                  },
                  image:{
                    <?php if (!$edit_id) { ?>
                      required:"Please select an image to upload.",
                      <?php } ?>
                      accept: "Extension should be jpg,jpeg."
                    }
                  },
                  errorPlacement: function(error, element) {
                    error.appendTo($('#' + element.attr('id')).parent());
                    if (element.attr("type") == "checkbox")
                      error.insertAfter('#note');


                  }

                });

      $('a img').click(function () {
          var $img = $(this);
          $('#divLargerImage').html($img.clone()).add($('#divOverlay')).fadeIn();
          $("html, body").animate({
              scrollTop: $('#divLargerImage').offset().top
          }, 2000);
      });

      $('#divLargerImage').add($('#divOverlay')).click(function () {
          $('#divLargerImage').add($('#divOverlay')).fadeOut(function () {
              $('#divLargerImage').empty();
          });
      });

});

function loadCloseTimeSlots(selectedValue,day) {
	console.log("selectedValue = " + selectedValue);
	console.log("day = " + day);
        $.ajax({
            url: '<?php echo base_url(); ?>admin/restaurant_login/get_closing_slots',
            type: "POST",
            data: {
                "slot_id" : selectedValue
            },
            dataType: 'json',
            success: function(data){
				console.log(data);
                if( data.success == 1 )
                {
                    var closing_time_slots = data.closing_time_slots;
                    var option = '';
                    $.each(closing_time_slots, function(key, value) {
                        option += '<option value="'+ value['time_slot'] + '">' + value['time_slot'] + '</option>';
                    });
                    $('#closed_to_' + day).html(option);
                }
                else
                {
                    alert("No time available");
                }
            }
        });
    }

</script>


<div class="content">
  <div class="header">
    <h1 class="page-title"><?php echo ($edit_id ? "Edit" : "Add"); ?> Profile</h1>
    <ul class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>admin/dashboard"><i class="fa fa-home"></i> Home</a> </li>
      <li class="active"><?php echo ($edit_id ? "Edit" : "Add"); ?> Profile</li>
    </ul>
  </div>
  <div class="main-content">

    <div class="panel panel-default" align="left" style="border:0px; margin:0px;">
      <div class="panel-body" >
        <div class="dialog1">
          <div class="error " style="">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-7" style="margin-bottom: 16px;">* indicates required field.</div>
          </div>
          <form id="user_form" action="" method="POST" enctype="multipart/form-data" class="form-horizontal">
            <input type="hidden" id="new_img" name="new_img" value="<?php echo set_value('new_img'); ?>"/>
            <input type="hidden" id="hero_image" name="hero_image" value="<?php echo set_value('hero_image'); ?>"/>
            <input type="hidden" id="edit_id" name="edit_id" value="<?php echo $edit_id; ?>"/>

            <div class="form-group">
              <label class="col-sm-3 control-label">Name of Restaurant<span class="error" >*</span></label>
              <div class="col-sm-7">
                <input type="text" value="<?php echo set_value('user_first_name', $formData['txtrname']); ?>" id="txtrname" name="txtrname" class="form-control" maxlength="40" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">Cuisine Title<span class="error" >*</span></label>
              <div class="col-sm-7 cusine" >
                <select class="form-control" name="cusine[]" id="cusine" class="form-control" aria-invalid="false" width="auto"  multiple>
                  <?php foreach ($cuisineData as $res) {
                    ?>
                    <option  value="<?php echo $res['cuisine_id']; ?>"  <?php
                      if (in_array($res['cuisine_id'], $aCuisine)) {
                        echo "selected = 'selected'";
                      }
                      ?> ><?php echo htmlentities(stripslashes($res['cuisine_name'])); ?></option>
                      <?php }
                      ?>
                    </select>
                  </div>
                </div><input type="hidden" name="user_id" id="user_id" />
                <div class="form-group">
                  <label class="col-sm-3 control-label">Amenities Title<span class="error" >*</span></label>
                  <div class="col-sm-7 ambience">
                    <select name="ambience[]" id="ambience"   multiple="multiple" class="form-control" aria-invalid="false">
                      <?php foreach ($ambienceData as $res) {
                        ?>
                        <option  value="<?php echo $res['ambience_id']; ?>"  <?php
                          if (in_array($res['ambience_id'], $aAmbience)) {
                            echo "selected = 'selected'";
                          }
                          ?>  ><?php echo htmlentities(stripslashes($res['ambience_name'])); ?></option>
                          <?php }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label">Dietary Preference Title<span class="error" >*</span></label>
                      <div class="col-sm-7 dietary">
                        <select name="dietary[]" id="dietary"   multiple="multiple" class="form-control"  aria-invalid="false">
                          <?php foreach ($dietaryData as $res) {
                            ?>
                            <option  value="<?php echo $res['diet_id']; ?>"  <?php
                              if (in_array($res['diet_id'], $aDietary)) {
                                echo "selected = 'selected'";
                              }
                              ?>  ><?php echo htmlentities(stripslashes($res['diet_preference'])); ?></option>
                              <?php }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Email Address<span class="error" >*</span></label>
                          <div class="col-sm-7">
                            <input type="text" <?php echo ($edit_id > 0 ? "readonly='readonly'" : ""); ?>  value="<?php echo set_value('user_email', $formData['txtemail']); ?>" id="txtemail" name="txtemail" class="form-control" maxlength="40" />
                          </div>
                        </div>
                        <div class="form-group" >
                          <label class="col-sm-3 control-label">Password<span class="error" ><?php echo ($edit_id > 0 ? "" : "*") ?></span></label>
                          <div class="col-sm-7">
                            <input id="new_password" name="new_password" value="" type="password" class="form-control" title="Password must contain : 6 characters ( 1 Upper, 1 lower, 1 number and 1 symbol." />
                            <?php if ($edit_id) { ?><span>(Enter only if you want to change.)</span><?php } ?>
                          </div>
                        </div>
                        <div class="form-group" >
                          <label class="col-sm-3 control-label">Confirm Password<span class="error" ><?php echo ($edit_id > 0 ? "" : "*") ?></span></label>
                          <div class="col-sm-7">
                            <input id="conf_password" name="conf_password" value="" type="password" class="form-control" title="Password must contain : 6 characters ( 1 Upper, 1 lower, 1 number and 1 symbol." />
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Name of Contact Person<span class="error" >*</span></label>
                          <div class="col-sm-7">
                            <input type="text" value="<?php echo set_value('restaurant_owner_name', $formData['resownername']); ?>" id="resownername" name="resownername" class="form-control" maxlength="40" />
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label">Contact Number<span class="error" >*</span></label>
                          <div class="col-sm-7">
                            <input type="text" value="<?php echo set_value('user_contact', $formData['txtcontact']); ?>" id="txtcontact" name="txtcontact" class="form-control" maxlength="40" />
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Country<span class="error" >*</span></label>
                          <div class="col-sm-7">
                            <select class="form-control" name="country_id" id="country_id" aria-invalid="false" disabled="true">
                              <option value="" >Select Country</option>
                              <?php foreach ($countriesData as $res) {
                                ?>
                                <option <?php echo ($res['cou_id'] == '47' ? "selected='selected'" : ""); ?>  value="<?php echo $res['cou_id']; ?>"><?php echo htmlentities(stripslashes($res['cou_name'])); ?></option>
                                <?php }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-sm-3 control-label">State<span class="error" >*</span></label>
                            <div class="col-sm-7">
                              <select class="form-control" name="region_id" id="region_id" aria-invalid="false" >
                                <option value="" >Select State</option>
                                <?php foreach ($regionsData as $res) {
                                  ?>
                                  <option <?php echo ($res['region_id'] == $formData['region_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $res['region_id']; ?>"><?php  echo htmlentities(stripslashes($res['region_name'])); ?></option>
                                  <?php }
                                  ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">City<span class="error" >*</span></label>
                              <div class="col-sm-7">
                                <select class="form-control" name="city_id" id="city_id" aria-invalid="false" >
                                  <option value="" >Select City</option>
                                  <?php foreach ($citiesData as $res1) {
                                    ?>
                                    <option <?php echo ($res1['city_id'] == $formData['city_id'] ? "selected='selected'" : ""); ?>  value="<?php echo $res1['city_id']; ?>"><?php echo htmlentities(stripslashes($res1['city_name'])); ?></option>
                                    <?php }
                                    ?>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group">
                                <label class="col-sm-3 control-label">Physical Address<span class="error" >*</span></label>
                                <div class="col-sm-7">
                                  <input type="text" maxlength="100" value="<?php echo trim(set_value('street_address1', $formData['address'])); ?>" id="address" name="address" class="form-control" />
                                </div>
                              </div>

<!-- 
                              <div class="form-group">
                                <label class="col-sm-3 control-label">Average Spend (R)<span class="error" >*</span></label>
                                <div class="col-sm-7">
                                  <input type="text" value="<?php echo set_value('average_spend', $formData['avgspend']); ?>" id="avgspend" name="avgspend" class="form-control" maxlength="10"  />
                                </div>
                              </div> -->

                              <div class="form-group">
                              <label class="col-sm-3 control-label">Average Spend (R)<span class="error" >*</span></label>
                              <div class="col-sm-7">
                              <?php //print_r($formData['avgspend']);die; ?>
                                <select class="form-control" name="avgspend" id="avgspend" aria-invalid="false" >
                                  <option value="" >Select Average Spend</option>
                                 <option value="1" <?php echo $formData['avgspend'] == 1 ? "selected='selected'" : ""; ?> >R0-R100 – 1 R </option>     
                                 <option value="2" <?php echo $formData['avgspend'] == 2 ? "selected='selected'" : ""; ?> >R101 - R200 – 2 R  </option>     
                                 <option value="3" <?php echo $formData['avgspend'] == 3 ? "selected='selected'" : ""; ?> >R201 - R400 – 3 R  </option>     
                                 <option value="4" <?php echo $formData['avgspend'] == 4 ? "selected='selected'" : ""; ?> >R401 - R600 – 4 R  </option>    
                                 <option value="5" <?php echo $formData['avgspend'] == 5 ? "selected='selected'" : ""; ?> >R601 – R800 – 5 R  </option>   
                                 <option value="6" <?php echo $formData['avgspend'] == 6 ? "selected='selected'" : ""; ?> >R801 - R1000 – 6 R </option>  
                                 </select>
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="col-sm-3 control-label">Description<span class="error" >*</span></label>
                                <div class="col-sm-7">
                                  <input type="text" value="<?php echo trim(set_value('user_description', $formData['description'])); ?>" id="description" name="description" class="form-control" maxlength="350" />
                                </div>
                              </div>
							  
							  <div class="form-group">
								  <label class="col-sm-3 control-label">Restaurant Web Domain</label>
								  <div class="col-sm-7">
									<input type="text" value="<?php echo set_value('web_domain', $formData['web_domain']); ?>" id="txtrwebdomain" name="txtrwebdomain" class="form-control" maxlength="50" />
								  </div>
							</div>

                              <?php
                              if (empty($edit_id)) {
                                $restaurantTimeData = array("value" => "1");
                              }
                            if ($restaurantTimeData) {
                            ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Restaurant Timings<span
                                            class="error">*</span></label>
                                <div class="col-sm-7">
                                    <div>
                                        <input type="checkbox" <?php
                                        if ($edit_id) {
                                            foreach ($restaurantTimeData as $row) {
                                                if ($row['open_close_day'] == '2' && $row['open_close_status'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?> class="validateCheckBoxes" value="2" id="day_2" name="day_2"
                                                style="width:10px;height:10px;margin-top: none;margin-right: 7px;"/>
                                        MONDAY
                                        <div style=" float:right;" id="timing2">
                                            <label><b>Open From:&nbsp </b></label>

                                            <select name="open_from_2" class="open_close_select_option" id="open_from_2" aria-invalid="false" onchange="loadCloseTimeSlots(this.value,2)">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
														$monday_flag = 0;
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '2' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['open_time_from'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
																$monday_flag = 1;
                                                            }
                                                        }
                                                        ?>
														<?php if($monday_flag !=1) { ?>
															<option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
														<?php } else {
                                                        $monday_flag = 0;
                                                         }
                                                    } else { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                    <?php }
                                                endforeach; ?>
                                            </select>

                                            &nbsp &nbsp to &nbsp &nbsp
                                            <select name="closed_to_2" class="open_close_select_option" id="closed_to_2" aria-invalid="false">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '2' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['close_time_to'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>

                                    <div><input type="checkbox" <?php
                                        if ($edit_id) {
                                            foreach ($restaurantTimeData as $row) {
                                                if ($row['open_close_day'] == '3' && $row['open_close_status'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?> class="validateCheckBoxes" value="3" id="day_3" name="day_3"
                                                style="width:10px;height:10px;margin-top: none;margin-right: 7px;"/>
                                        TUESDAY
                                        <div style=" float:right;" id="timing3">
                                            <label><b>Open From:&nbsp </b></label>

                                            <select name="open_from_3" class="open_close_select_option" id="open_from_3" aria-invalid="false" onchange="loadCloseTimeSlots(this.value,3)">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
														$tuestday_flag = 0;
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '3' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['open_time_from'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
																$tuestday_flag = 1;
                                                            }
                                                        }
                                                        ?>
														<?php if($tuestday_flag !=1) { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
														<?php }else  {
															$tuestday_flag = 0;
														} 
                                                        
                                                    } else { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                    <?php }
                                                endforeach; ?>
                                            </select>

                                            &nbsp &nbsp to &nbsp &nbsp
                                            <select name="closed_to_3" class="open_close_select_option" id="closed_to_3" aria-invalid="false">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '3' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['close_time_to'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>

                                    <div><input type="checkbox" <?php
                                        if ($edit_id) {
                                            foreach ($restaurantTimeData as $row) {
                                                if ($row['open_close_day'] == '4' && $row['open_close_status'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?> class="validateCheckBoxes" value="4" id="day_4" name="day_4"
                                                style="width:10px;height:10px;margin-top: none;margin-right: 7px;"/>
                                        WEDNESDAY
                                        <div style=" float:right;" id="timing4">
                                            <label><b>Open From:&nbsp </b></label>

                                            <select name="open_from_4" class="open_close_select_option" id="open_from_4" aria-invalid="false" onchange="loadCloseTimeSlots(this.value,4)">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
														$wednesday_flag = 0;
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '4' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['open_time_from'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
																$wednesday_flag = 1;
                                                            }
                                                        }
                                                        ?>
														<?php if($wednesday_flag != 1){ ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
														<?php } else {
															$wednesday_flag = 0;
														}
                                                        
                                                    } else { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                    <?php }
                                                endforeach; ?>
                                            </select>

                                            &nbsp &nbsp to &nbsp &nbsp
                                            <select name="closed_to_4" class="open_close_select_option" id="closed_to_4" aria-invalid="false">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '4' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['close_time_to'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>

                                    <div><input type="checkbox" <?php
                                        if ($edit_id) {
                                            foreach ($restaurantTimeData as $row) {
                                                if ($row['open_close_day'] == '5' && $row['open_close_status'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?> class="validateCheckBoxes" value="5" id="day_5" name="day_5"
                                                style="width:10px;height:10px;margin-top: none;margin-right: 7px;"/>
                                        THURSDAY
                                        <div style=" float:right;" id="timing5">
                                            <label><b>Open From:&nbsp </b></label>

                                            <select name="open_from_5" class="open_close_select_option" id="open_from_5" aria-invalid="false" onchange="loadCloseTimeSlots(this.value,5)">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
														$thursday_flag = 0;
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '5' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['open_time_from'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php $thursday_flag = 1;
                                                            }
                                                        }
                                                        ?>
														<?php if($thursday_flag != 1) {?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                        <?php } else { $thursday_flag = 0; }
                                                    } else { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                    <?php }
                                                endforeach; ?>
                                            </select>

                                            &nbsp &nbsp to &nbsp &nbsp
                                            <select name="closed_to_5" class="open_close_select_option" id="closed_to_5" aria-invalid="false">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '5' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['close_time_to'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>

                                    <div><input type="checkbox" <?php
                                        if ($edit_id) {
                                            foreach ($restaurantTimeData as $row) {
                                                if ($row['open_close_day'] == '6' && $row['open_close_status'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?> class="validateCheckBoxes" value="6" id="day_6" name="day_6"
                                                style="width:10px;height:10px;margin-top: none;margin-right: 7px;"/>
                                        FRIDAY
                                        <div style=" float:right;" id="timing6">
                                            <label><b>Open From:&nbsp </b></label>

                                            <select name="open_from_6" class="open_close_select_option" id="open_from_6" aria-invalid="false" onchange="loadCloseTimeSlots(this.value,6)">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
														$firday_flag = 0;
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '6' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['open_time_from'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php $firday_flag = 1;
                                                            }
                                                        }
                                                        ?>
														<?php if($firday_flag != 1) {?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                        <?php } else{ $firday_flag = 0; }
                                                    } else { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                    <?php }
                                                endforeach; ?>
                                            </select>

                                            &nbsp &nbsp to &nbsp &nbsp
                                            <select name="closed_to_6" class="open_close_select_option" id="closed_to_6" aria-invalid="false">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '6' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['close_time_to'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>

                                    <div><input type="checkbox" <?php
                                        if ($edit_id) {
                                            foreach ($restaurantTimeData as $row) {
                                                if ($row['open_close_day'] == '7' && $row['open_close_status'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?> class="validateCheckBoxes" value="7" id="day_7" name="day_7"
                                                style="width:10px;height:10px;margin-top: none;margin-right: 7px;"/>
                                        SATURDAY
                                        <div style=" float:right;" id="timing7">
                                            <label><b>Open From:&nbsp </b></label>

                                            <select name="open_from_7" class="open_close_select_option" id="open_from_7" aria-invalid="false" onchange="loadCloseTimeSlots(this.value,7)">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
														$saturday_flag = 0;
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '7' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['open_time_from'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php $saturday_flag = 1;
                                                            }
                                                        }
                                                        ?>
														<?php if($saturday_flag != 1) { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                        <?php } else { $saturday_flag = 0; }
                                                    } else { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                    <?php }
                                                endforeach; ?>
                                            </select>

                                            &nbsp &nbsp to &nbsp &nbsp
                                            <select name="closed_to_7" class="open_close_select_option" id="closed_to_7" aria-invalid="false">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '7' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['close_time_to'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>

                                    <div><input type="checkbox" <?php
                                        if ($edit_id) {
                                            foreach ($restaurantTimeData as $row) {
                                                if ($row['open_close_day'] == '1' && $row['open_close_status'] == '1') {
                                                    echo 'checked="checked"';
                                                }
                                            }
                                        }
                                        ?> class="validateCheckBoxes" value="1" id="day_1" name="day_1"
                                                style="width:10px;height:10px;margin-top: none;margin-right: 7px;"/>
                                        SUNDAY
                                        <div style=" float:right;" id="timing1">
                                            <label><b>Open From:&nbsp </b></label>

                                            <select name="open_from_1" class="open_close_select_option" id="open_from_1" aria-invalid="false" onchange="loadCloseTimeSlots(this.value,1)">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
														$sunday_flag = 0;
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '1' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['open_time_from'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php $sunday_flag = 1;
                                                            }
                                                        }
                                                        ?>
														<?php if($sunday_flag != 1) { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                        <?php } else { $sunday_flag = 0; }
                                                    } else { ?>
                                                        <option value="<?php echo $slots->time_slot; ?>"> <?php echo $slots->time_slot; ?> </option>
                                                    <?php }
                                                endforeach; ?>
                                            </select>

                                            &nbsp &nbsp to &nbsp &nbsp
                                            <select name="closed_to_1" class="open_close_select_option" id="closed_to_1" aria-invalid="false">
                                                <option value="">--:--</option>
                                                <?php foreach ($time_slots as $slots) :
                                                    if ($edit_id) {
                                                        foreach ($restaurantTimeData as $row) {
                                                            if ($row['open_close_day'] == '1' && $row['open_close_status'] == '1' && date('H:i', strtotime($row['close_time_to'])) == date('H:i', strtotime($slots->time_slot))) { ?>
                                                                <option value="<?php echo $slots->time_slot; ?>" selected> <?php echo $slots->time_slot; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>

                                    <p id="note"><b>Note:</b> If your restaurant is opened 24 hours use time format
                                        00:00 to 23:30</p>

                                </div>
                            </div>
                        <?php } ?>>
								   
								   
								   
								   
                                   <div class="form-group" >
                                    <label class="col-sm-3 control-label">Profile image<span class="error" >*</span></label>
                                    <div class="col-sm-7"><input type="file"  id="image" name="image"  value=""/></div>
                                  </div>
                                  <div class="form-group" id="image_preview">
                                    <div class="col-sm-offset-3 col-sm-7">
                                      <img id="preview" name="preview" width="150" height="150" src="<?php if ($formData['new_img']) echo base_url() . MEMBER_IMAGE_PATH . $formData['new_img']; else echo $this->config->item('assets') . 'upload/adminuser/restaurant.jpg'; ?>"/>
                                      <p><b>Note:</b> Image should be jpg, jpeg.</p>
                                    </div>
                                  </div>
                                  <div class="form-group" >
                                    <label class="col-sm-3 control-label">Restaurant Hero Image<span class="error" >*</span></label>
                                    <div class="col-sm-7"><input type="file"  id="h_image" name="h_image"  value=""/></div>
                                  </div>
                                  <div class="form-group" id="image_preview">
                                    <div class="col-sm-offset-3 col-sm-7">
                                      <img id="preview" name="preview" width="150" height="130" src="<?php if ($formData['hero_image']) echo base_url() . MEMBER_IMAGE_PATH . $formData['hero_image']; else echo $this->config->item('assets') . 'upload/adminuser/restaurant.jpg'; ?>"/>
                                      <p><b>Note:</b> Image should be jpg, jpeg.</p>
                                    </div>
                                  </div>


                                  <div class="form-group" >
                                      <label class="col-sm-3 control-label">Restaurant Floor Plan</label>
                                      <div class="col-sm-7"><input type="file"  id="f_image" name="f_image"  value=""/></div>
                                  </div>

                                  <div class="form-group" id="image_preview">
                                      <div class="col-sm-offset-3 col-sm-7">
                                          <a href="#">
                                          <img id="preview" name="preview" width="150" height="130" src="<?php if ($formData['floor_image']) echo base_url() . MEMBER_IMAGE_PATH . $formData['floor_image']; else echo $this->config->item('assets') . 'upload/adminuser/restaurant.jpg'; ?>"/>
                                          </a>
                                          <p><b>Note:</b> Image should be png, jpg, jpeg.</p>
                                      </div>
                                  </div>

                                  <div class="form-group">
                                    <label></label>
                                    <progress style="display:none;"></progress>
                                    <div class="col-sm-offset-3 col-sm-7">
                                      <input type="submit" value="<?php echo ($edit_id ? "Update" : "Save"); ?>" class="btn btn-primary" id="btnSubmit"  />
                                      <input type="button" value="Cancel" style="margin-left:20px" class="btn btn-primary" onclick="javascript:window.location.href='<?php echo base_url(); ?>admin/restaurant'"/>
                                    </div>
                                  </div>
                                </form>
                              </div>

                            </div>
                          </div>
                        </div>

    <div id="divLargerImage"></div>
    <div id="divOverlay"></div>
                      </div>
					  