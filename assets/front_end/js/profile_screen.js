/*
 * Kaustubh Bhujbal
 * Profile related JS functions
 * 2 Feb 2017
 */
$(document).ready(function () {
    
	base_url = $("#base_url").val();
    
	setTimeout(function() {
        $("#bookingConfimtab").trigger('click');
    },10);
    
    /*$(".menu_icon").click(function(){
         $(".navbar_content").slideToggle();
         $(this).toggleClass('collapsed');
    });*/

    $(document).on('click', "#your_profile", function () {
        $.ajax({
            type: 'POST',
            url: base_url + 'front/profile/get_profile',
            data: {user_id: $("#userId").val()},
            success: function (result) {
                var parsed = jQuery.parseJSON(result);
				$.ajax({
                    type: 'POST',
                    url: base_url + 'front/profile/get_city_data',
                    data: {state_id: parsed.region_id},
                    success: function (cities) {
                        var response = jQuery.parseJSON(cities);
					    $.each(response, function (index, obj) {
							if(parsed.city_id == obj.city_id){
								$("#city").append('<option value="' + obj.city_id + '" selected>' + obj.city_name + '</option>');
							}
							else{
								$("#city").append('<option value="' + obj.city_id + '">' + obj.city_name + '</option>');
							}
                            
                        });
                        $("#city").selectpicker("refresh");
                    }
                });
				
				
                $("#full_name").val(parsed.user_first_name);
                $("#email").val(parsed.user_email);
                $("#contact").val(parsed.user_contact);
                $("#dob_profile").val(parsed.date_of_birth);
				//$("#dob_profile").datepicker("update", 'parsed.date_of_birt');
				//$("#gender_profile").val(parsed.gender);
			    //$("#gender_profile").next('div').find('span:first').html(parsed.gender);
				$('#gender_profile option[value='+parsed.gender+']').attr('selected','selected');
				
				
                $("#state").next('div').find('span:first').html(parsed.state_name);
                $("#city").val(parsed.city_id);
                if (parsed.notification_flag == "1") {
                    $("#notification_flag").prop('checked', true);
                }

                
                $("#gender_profile").selectpicker();
                $("#state").selectpicker();
                $("#country").selectpicker();
                $("#city").val(parsed.city_id);
            }
        });
    });

    jQuery.validator.addMethod('passwordMatch', function (value) {
        if (value != '')
            return /^.*(?=.{6,20})(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[_~\-!@#\$%\^&\*\(\)]).*$/.test(value);
        else
            return true;
    });
    $("#profile_form").validate({
        debug: true,
        rules: {
            full_name: {
                required: true
            },
            dob_profile: {
                required: true
            },
            gender: {
                required: true
            },
            country: {
                required: true
            },
            state: {
                required: true
            },
            city: {
                required: true
            },
            contact: {
                required: true,
                maxlength: 13,
                minlength: 10,
                number: true
            },
            password: {
                minlength: 6,
                passwordMatch: true
            },
            conf_password: {
                equalTo: "#password"
            }
        },
        messages: {
            full_name: {
                required: "Please enter full name."
            },
            dob_profile: {
                required: "Please select date of birth."
            },
            gender: {
                required: "Please select gender."
            },
            country: {
                required: "Please select country."
            },
            state: {
                required: "Please select province."
            },
            city: {
                required: "Please select city."
            },
            contact: {
                required: "Please enter contact no.",
                minlength: "Please enter valid mobile number, atleast 10 digit."
            },
            password: {
                minlength: "Please enter password atleast 6 characters long.",
                passwordMatch: "Password Invalid. Password must contain:6 characters ( 1 Upper, 1 lower, 1 number and 1 symbol)"
            },
            conf_password: {
                required: "Please enter confirm password.",
                equalTo: "Please enter same password again."
            }

        },
		errorPlacement: function(error, element){
			
			if(element.attr("name") == "full_name"){
				error.appendTo($('#user_name_error_placement'));
			}
			if(element.attr("name") == "dob_profile"){
				error.appendTo($('#profile_dob_error_placement'));
			}
			if(element.attr("name") == "gender"){
				error.appendTo($('#gender_profile_error_placement'));
			}
			if(element.attr("name") == "country"){
				error.appendTo($('#country_error_placement'));
			}
			if(element.attr("name") == "state"){
				error.appendTo($('#state_error_placement'));
			}
			if(element.attr("name") == "city"){
				error.appendTo($('#city_profile_error_placement'));
			}
			if(element.attr("name") == "contact"){
				error.appendTo($('#contact_number_error_placement'));
			}
			if(element.attr("name") == "password"){
				error.appendTo($('#password_error_placement'));
			}
			if(element.attr("name") == "conf_password"){
				error.appendTo($('#conf_pass_error_placement'));
			}
		},
        submitHandler: function (form) {
            // form.submit();
            var postData = $('#profile_form').serialize();
			$.ajax({
                type: 'POST',
                url: base_url + 'front/profile/update_profile',
                data: $('#profile_form').serialize(),
                success: function (result) {
                    alert('Profile updated successfully.');
                }
            });

        }
    });
    $.ajaxSetup({
        data: {
            csrf_test_name: $.cookie('csrf_cookie_name')
        }
    });

    // Get list of user wish list
    $(document).on('click', "#wishlisttab", function () {
		$("#wishlistOffset").val('0');
		$(".wishlist").html('');
        getwishlist();
    });

    $(document).on('click', "#wishlistloadMore", function () {
        getwishlist();
    });

    // Get list of user wish list
    $(document).on('click', "#suggestiontab", function () {
		$(".suggestionprofile").html('');
        getsuggestionlist();
    });

    $(document).on('click', "#suggestionloadMore", function () {
		getsuggestionlist();
    });

    $(document).on('click', "#reviewTab", function () {
		$("#ReviewOffset").val('0');
		$(".reviewsbox").html('');
        getReviewList();
    });

    $(document).on('click', "#reviewLoadMore", function () {
        getReviewList();
    });

    $(document).on('click', "#signouttab", function () {
        window.location = base_url+'front/home/logout';
    });


    $(document).on('click', ".favourite", function () {
        var resId = $(this).attr("data-id");
        $.ajax({
            type: 'POST',
            url: base_url + 'front/profile/restaurant_remove_wish_list',
            data: {restaurant_id: resId},
            success: function (result) {
                $("#listbox" + resId).css("display", "none");
            }
        });
    });

    $(document).on('click', "#historytab", function () {
        $("#history_booking_list_div").html('');
        $("#profile_history_booking_load_more").remove();
        $("#profile_history_booking_offset").val('0');
        getHistoryBookingList();
    });
	
	$(document).on('click', "#bookingConfimtab", function () {
		//$logged_in_user_id = $("#logged_in_user_id").val();
		// alert($logged_in_user_id);
		$("#confirm_booking_list_div").html('');
        $("#profile_confirm_booking_load_more").remove();
        $("#profile_confirm_booking_offset").val('0');
        getConfirmBookingList();
    });
	
	//To change profile pic
	$("#profile_image").on('change', function(){
		$("#profile_upload_image").submit();
		//event.preventDefault();
	});
});


//modify redirect with encoded data
function modify_booking_redirect(encoded_booking_id) {
    if (confirm("Do you want to modify your booking?")) {
        window.location = base_url+'profile/modify_booking/'+encoded_booking_id;
    }
}

//cancel confirm booking
function cancel_confirm_booking(booking_id, restaurant_id) {
    if (booking_id != 0) {
        if (confirm("Are you sure?")) {

            $.showLoading({
                name: 'circle-fade',
                callback: function () {
                }
            });

            $.ajax({
                url: base_url + 'front/profile/cancel_booking',
                data: {
                    booking_id: booking_id
                },
                dataType: 'json',
                success: function (result) {
					
					if (result.status == 1) {
                        alert(result.message);
                        $("#action_btns_" + booking_id).html('<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by you.</a>');
                    }
                    else {
						if(result.is_login == 0)
						{
							window.location = base_url+'home';
						}
						else
						{
						alert(result.message);	
						}
                    }

                }
            });

            $.hideLoading();
        }
    } else {
        alert("Sorry for inconvenience, We are unable to cancel you booking for now. Please try again after sometime.");
    }
}

//Call getConfirmBookingList function to get more list
function profile_confirm_booking_load_more() {
    $('#profile_confirm_booking_load_more').remove();
    $('#confirm_booking_list_div').after('<div id="load_image"><img src="' + base_url + 'assets/front_end/images/Loading_icon.gif"></img></div>');
    getConfirmBookingList();
}

//Get confirm booking list start
function getConfirmBookingList() {
	var offset = $("#profile_confirm_booking_offset").val();

    $.ajax({
        url: base_url + 'front/profile/get_confirm_booking_list',
        data: {
            offset: offset
        },
        dataType: 'json',
        success: function (result) {
            var profile_booking_html = '';
            if (result.total_records == 0) {
                profile_booking_html += '<div class="listing">' +
                    '<div class="rightside">' +
                    '<p>No Booking(s) found.</p>' +
                    '</div>' +
                    '</div>';

                $("#confirm_booking_list_div").html(profile_booking_html);
            }
            else {
                if (offset == 0) {
                    $("#confirm_booking_list_div").html('');
                    if (result.total_records > result.offset) {
                        $.each(result.booking_records, function (key, value) {
                            if (value.is_cancel == 1) {
                                profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '<div class="buttonbox">';
                                if (value.cancel_by == 2) {
                                    profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by you.</a>';
                                }
                                else if (value.cancel_by == 3){
                                    profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                                }
                                else
                                {
                                    profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Your booking is cancelled.</a>';
                                }
                                profile_booking_html +=
                                    '</div>' +
                                    '</div>';
                            }
                            else {
                                profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<input type="hidden" name="encoded_booking_id" value="' + ' + value.encoded_booking_id + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '<div class="buttonbox" id="action_btns_' + value.booking_id + '">' +
                                    '<a href="javascript:void(0)" class="listbutton green" onclick="modify_booking_redirect('+ value.booking_id +')">Modify</a>' +
                                    '<a href="javascript:void(0)" class="listbutton" onclick="cancel_confirm_booking(' + value.booking_id + ', ' + value.restaurant_id + ')">Cancel</a>' +
                                    '</div>' +
                                    '</div>';
                            }
                        });
                        $("input[id=profile_confirm_booking_offset]").val(result.offset);
                        $('#confirm_booking_list_div').after("<div class='restaurant_filter_load_more search_btn' id='profile_confirm_booking_load_more' onclick='profile_confirm_booking_load_more()'>View More</div>");
                        $("#confirm_booking_list_div").html(profile_booking_html);
                    }
                    else {
                        $.each(result.booking_records, function (key, value) {
                            if (value.is_cancel == 1) {
                                profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '<div class="buttonbox">';
                                if (value.cancel_by == 2) {
                                    profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by you.</a>';
                                }
                                else if (value.cancel_by == 3){
                                    profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                                }
                                else
                                {
                                    profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Your booking is cancelled.</a>';
                                }
                                profile_booking_html +=
                                    '</div>' +
                                    '</div>';
                            }
                            else {
                                profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<input type="hidden" name="encoded_booking_id" value="' + ' + value.encoded_booking_id + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '<div class="buttonbox" id="action_btns_' + value.booking_id + '">' +
                                    '<a href="javascript:void(0)" class="listbutton green" onclick="modify_booking_redirect('+ value.booking_id +')">Modify</a>' +
                                    '<a href="javascript:void(0)" class="listbutton" onclick="cancel_confirm_booking(' + value.booking_id + ', ' + value.restaurant_id + ')">Cancel</a>' +
                                    '</div>' +
                                    '</div>';
                            }


                        });
                        $("input[id=profile_confirm_booking_offset]").val(result.offset);
                        $("#profile_confirm_booking_load_more").remove();
                        $("#confirm_booking_list_div").append(profile_booking_html);
                    }

                }
                else if (result.total_records > result.offset) {
                    $("#confirm_booking_list_div .listing:last-child").css({"border-bottom": "1px solid #5b4640"});
                    $.each(result.booking_records, function (key, value) {
                        if (value.is_cancel == 1) {
                            profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '<div class="buttonbox">';
                            if (value.cancel_by == 2) {
                                profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by you.</a>';
                            }
                            else if (value.cancel_by == 3){
                                profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                            }
                            else
                            {
                                profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Your booking is cancelled.</a>';
                            }
                            profile_booking_html +=
                                '</div>' +
                                '</div>';
                        }
                        else {
                            profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<input type="hidden" name="encoded_booking_id" value="' + ' + value.encoded_booking_id + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '<div class="buttonbox" id="action_btns_' + value.booking_id + '">' +
                                '<a href="javascript:void(0)" class="listbutton green" onclick="modify_booking_redirect('+ value.booking_id +')">Modify</a>' +
                                '<a href="javascript:void(0)" class="listbutton" onclick="cancel_confirm_booking(' + value.booking_id + ', ' + value.restaurant_id + ')">Cancel</a>' +
                                '</div>' +
                                '</div>';
                        }

                    });
                    $("input[id=profile_confirm_booking_offset]").val(result.offset);
                    $('#confirm_booking_list_div').after("<div class='restaurant_filter_load_more search_btn' id='profile_confirm_booking_load_more' onclick='profile_confirm_booking_load_more()'>View More</div>");
                    $("#confirm_booking_list_div").append(profile_booking_html);
                }
                else {
                    $("#confirm_booking_list_div .listing:last-child").css({"border-bottom": "1px solid #5b4640"});
                    $.each(result.booking_records, function (key, value) {
                        if (value.is_cancel == 1) {
                            profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '<div class="buttonbox">';
                            if (value.cancel_by == 2) {
                                profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by you.</a>';
                            }
                            else if (value.cancel_by == 3){
                                profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                            }
                            else
                            {
                                profile_booking_html += '<a href="javascript:void(0)" class="booking_list_btn booking_cancel_text">Your booking is cancelled.</a>';
                            }
                            profile_booking_html +=
                                '</div>' +
                                '</div>';
                        }
                        else {
                            profile_booking_html += '<div class="listing" id="booking_confirm_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<input type="hidden" name="encoded_booking_id" value="' + ' + value.encoded_booking_id + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '<div class="buttonbox" id="action_btns_' + value.booking_id + '">' +
                                '<a href="javascript:void(0)" class="listbutton green" onclick="modify_booking_redirect('+ value.booking_id +')")">Modify</a>' +
                                '<a href="javascript:void(0)" class="listbutton" onclick="cancel_confirm_booking(' + value.booking_id + ', ' + value.restaurant_id + ')">Cancel</a>' +
                                '</div>' +
                                '</div>';
                        }

                    });
                    $("input[id=profile_confirm_booking_offset]").val(result.offset);
                    $("#profile_confirm_booking_load_more").remove();
                    $("#confirm_booking_list_div").append(profile_booking_html);
                }

                $("#load_image").remove();
            }
        }
    });
}
//Get confirm booking list end


//Call get History and cancel Booking List function to get more list
function profile_history_booking_load_more() {
    $('#profile_history_booking_load_more').remove();
    $('#history_booking_list_div').after('<div id="load_image"><img src="' + base_url + 'assets/front_end/images/Loading_icon.gif"></img></div>');
    getHistoryBookingList();
}

//Get history and cancel booking list start
function getHistoryBookingList() {

    var offset = $("#profile_history_booking_offset").val();
    $.ajax({
        url: base_url + 'front/profile/get_history_booking_list',
        data: {
            offset: offset
        },
        dataType: 'json',
        success: function (result) {
            var profile_history_html = '';
            if (result.total_records == 0) {
                profile_history_html += '<div class="listing">' +
                    '<div class="rightside">' +
                    '<p>No record(s) in  history</p>' +
                    '</div>' +
                    '</div>';

                $("#history_booking_list_div").html(profile_history_html);
            }
            else {
                if (offset == 0) {
                    $("#history_booking_list_div").html('');
                    if (result.total_records > result.offset) {
                        $.each(result.booking_records, function (key, value) {

                            if (value.is_cancel == 1) {
                                profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '<div class="buttonbox">';
                                if (value.cancel_by == 2) {
                                    profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by you.</a>';
                                }
                                if(value.cancel_by == 3){
                                    profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                                }
                                profile_history_html +=
                                    '</div>' +
                                    '</div>';
                            }
                            else {
                                profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '</div>';
                            }


                        });
                        $("input[id=profile_history_booking_offset]").val(result.offset);
                        $('#history_booking_list_div').after("<div class='restaurant_filter_load_more search_btn' id='profile_history_booking_load_more' onclick='profile_history_booking_load_more()'>View More</div>");
                        $("#history_booking_list_div").html(profile_history_html);
                    }
                    else {
                        $.each(result.booking_records, function (key, value) {
                            if (value.is_cancel == 1) {
                                profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '<div class="buttonbox">';
                                if (value.cancel_by == 2) {
                                    profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by you.</a>';
                                }
                                if(value.cancel_by == 3){
                                    profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                                }
                                profile_history_html +=
                                    '</div>' +
                                    '</div>';
                            }
                            else {
                                profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                    '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                    '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                    '<div class="rightside">' +
                                    '<h4>' + value.restaurant_name + '</h4>' +
                                    '<p>' + value.formatted_booking_date + '</p>' +
                                    '<p>Booking Code: ' + value.booking_code + '</p>' +
                                    '<p>Table for ' + value.number_of_guest + '</p>' +
                                    '</div>' +
                                    '</div>';
                            }

                        });
                        $("input[id=profile_history_booking_offset]").val(result.offset);
                        $("#profile_history_booking_load_more").remove();
                        $("#history_booking_list_div").append(profile_history_html);
                    }

                }
                else if (result.total_records > result.offset) {
                    $("#history_booking_list_div .listing:last-child").css({"border-bottom": "1px solid #5b4640"});
                    $.each(result.booking_records, function (key, value) {
                        if (value.is_cancel == 1) {
                            profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '<div class="buttonbox">';
                            if (value.cancel_by == 2) {
                                profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by you.</a>';
                            }
                            if(value.cancel_by == 3){
                                profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                            }
                            profile_history_html +=
                                '</div>' +
                                '</div>';
                        }
                        else {
                            profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '</div>';
                        }

                    });
                    $("input[id=profile_history_booking_offset]").val(result.offset);
                    $('#history_booking_list_div').after("<div class='restaurant_filter_load_more search_btn' id='profile_history_booking_load_more' onclick='profile_history_booking_load_more()'>View More</div>");
                    $("#history_booking_list_div").append(profile_history_html);
                }
                else {
                    $("#history_booking_list_div .listing:last-child").css({"border-bottom": "1px solid #5b4640"});
                    $.each(result.booking_records, function (key, value) {
                        if (value.is_cancel == 1 ) {
                            profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '<div class="buttonbox">';
                            if (value.cancel_by == 2) {
                                profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by you.</a>';
                            }
                            if(value.cancel_by == 3){
                                profile_history_html += '<a href="javascript:void(0)" class="histroy_list_btn booking_cancel_text">Cancelled by restaurant.</a>';
                            }
                            profile_history_html +=
                                '</div>' +
                                '</div>';
                        }
                        else {
                            profile_history_html += '<div class="listing" id="booking_history_div_' + value.restaurant_id + '">' +
                                '<input type="hidden" value="' + ' + value.booking_date + ' + '">' +
                                '<input type="hidden" value="' + ' + value.booking_time + ' + '">' +
                                '<a href="javascript:void(0)" class="thumnailimg"><img src="' + value.restaurant_image + '" class="img-responsive profile_restaurant_image"></a>' +
                                '<div class="rightside">' +
                                '<h4>' + value.restaurant_name + '</h4>' +
                                '<p>' + value.formatted_booking_date + '</p>' +
                                '<p>Booking Code: ' + value.booking_code + '</p>' +
                                '<p>Table for ' + value.number_of_guest + '</p>' +
                                '</div>' +
                                '</div>';
                        }

                    });
                    $("input[id=profile_history_booking_offset]").val(result.offset);
                    $("#profile_history_booking_load_more").remove();
                    $("#history_booking_list_div").append(profile_history_html);
                }

                $("#load_image").remove();
            }
        }
    });
}


//Get user's wishlist
function getwishlist() {
    $.ajax({
        type: 'POST',
        url: base_url + 'front/profile/get_wishlist',
        data: {offset: $("#wishlistOffset").val()},
        success: function (result) {
			var response = jQuery.parseJSON(result);
            if (response.status == 1) {
                $(".wishlist").append(response.html_arr);
                $("#wishlistOffset").val(response.offset);
                if (response.count > response.offset) {
                    $("#wishlistloadMore").show();
                }
                else {
                    $("#wishlistloadMore").hide();
                }
            }
            else {
				var profile_wishlist_html = '<div class="listing">' +
                    '<div class="rightside">' +
                    '<p>No restaurant(s) in wishlist.</p>' +
                    '</div>' +
                    '</div>';
                $(".wishlist").html(profile_wishlist_html);
            }
        }
    });
}

//Get user's suggestion list
function getsuggestionlist() {
    $.ajax({
        type: 'POST',
        url: base_url + 'front/profile/get_suggestionlist',
        data: {},
        success: function (result) {
			var response = jQuery.parseJSON(result);
			if (response.status != 0) {
                $(".suggestionprofile").html(response.html_arr);
            }
            else {
				var profile_sggesiton_html = '<div class="listing">' +
                    '<div class="rightside">' +
                    '<p>No Suggestion(s) found.</p>' +
                    '</div>' +
                    '</div>';
                $(".suggestionprofile").html(profile_sggesiton_html);
            }
        }
    });
}

function getReviewList() {
    $.ajax({
        type: 'POST',
        url: base_url + 'front/profile/getReviewList',
        data: {offset: $("#ReviewOffset").val()},
        success: function (result) {
            var response = jQuery.parseJSON(result);
            if (response.status == 1) {
                $(".reviewsbox").append(response.html_arr);
                if (response.offset < response.count) {
                    $("#ReviewOffset").val(response.offset);
                }
                if (response.count > response.offset) {
                    $("#reviewLoadMore").show();
                }
                else {
                    $("#reviewLoadMore").hide();
                }
            }
            else {
				var profile_review_html = '<div class="listing">' +
                    '<div class="rightside">' +
                    '<p>No review(s) found.</p>' +
                    '</div>' +
                    '</div>';
                $(".reviewsbox").html(profile_review_html);
            }
        }
    });
}


