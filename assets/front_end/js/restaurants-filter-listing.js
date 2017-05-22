var base_url;
var restaurant_search_count_for_jqoteTemplate = 0;
var cuisine = [];
var dietary_preference = [];
var ambience = [];
var front_booking_date = '';
var last_minute_time_slots = [];

function loadNews(base_url) {
    $.ajax({
        url: base_url + "load_news",
        success: function (result) {
            var data = jQuery.parseJSON(result);
            $('#reastaurant_filtered_list').jqotesub('#news_template', data);
        }
    });
}

function loadRestaurantsFilterOptions(base_url) {
    $.ajax({
        // url: "http://192.168.21.8//tabula/restaurants_filter_options",
        url: base_url + "restaurants_filter_options",
        success: function (result) {
            var data = jQuery.parseJSON(result);
            $('#cuisines_options').jqotesub('#cuisine_template', data['cuisine']);
            $('#dietary_preference_options').jqotesub('#dietary_preference_template', data['dietary_preference']);
            $('#ambience_options').jqotesub('#ambience_template', data['ambience']);
        }
    });
}

function restaurant_apply_filter_search() {
    var result = $("#restaurants-filter-listing-form").serializeArray();
    var location = $("#input_restaurants_filter_location").val();
	
    $("input[id=restaurant_offset]").val('0');
    $('.restaurant_filter_load_more').remove();

    cuisine = [];
    dietary_preference = [];
    ambience = [];

    $.each($("input[name='radio_cuisine']:checked"), function () {
        cuisine.push($(this).val());
    });
    $.each($("input[name='radio_dietary_preference']:checked"), function () {
        dietary_preference.push($(this).val());
    });
    $.each($("input[name='radio_ambience']:checked"), function () {
        ambience.push($(this).val());
    });

    if (location !== 'undefined' && location.length != 0) {
        if (location.length >= 3) {
			if(!isValidStringForSearch(location))
			{
				alert("Please enter valid characters in location.");
			}
			else{
			loadRestaurantsFilterResult(base_url, cuisine, dietary_preference, ambience, location);	
			}
            
        }
        else {
            alert("Please enter atleast 3 characters in Location.");
        }
    }
    else {
        loadRestaurantsFilterResult(base_url, cuisine, dietary_preference, ambience, location);
    }
}

function restaurantFilterSearchLoadMore() {
    var location = $("#input_restaurants_filter_location").val();
    $('.restaurant_filter_load_more').remove();
    $('#reastaurant_filtered_list').after('<div id="load_image"><img src="' + base_url + 'assets/front_end/images/Loading_icon.gif"></img></div>');
    loadRestaurantsFilterResult(base_url, cuisine, dietary_preference, ambience, location);
}

function loadRestaurantsFilterResult(base_url, cuisine, dietary_preference, ambience, location) {

    if ((cuisine !== 'undefined' && cuisine.length > 0) || (dietary_preference !== 'undefined' && dietary_preference.length > 0) || (ambience !== 'undefined' && ambience.length > 0) || (location !== 'undefined' && location.length != 0)) {
        $('#filterclose').trigger('click');
        var restaurant_offset = $("#restaurant_offset").val();
        if (restaurant_offset == 0) {
            $.showLoading({
                name: 'circle-fade',
                callback: function () {
                }
            });
        }
		
		$.ajax({
			url: base_url + "fetch_restaurants_by_search_filter",
			type: "POST",
			data: {
				offset: $("#restaurant_offset").val(),
				cuisine: cuisine,
				dietary_preference: dietary_preference,
				ambience: ambience,
				location: location
			},
			datatype: 'json',
			success: function (data) { 
				var data = jQuery.parseJSON(data);
				
				if (data['filter_restaurants_result_count'] === 0) {
					$('#reastaurant_filtered_list').html('');
					$('#reastaurant_filtered_list').append("<div class='col-sm-12 col-lg-12 col-md-12'><h3>No result found...</h3></div>");
				}
				else {
					if (restaurant_offset == 0) {
						$('#reastaurant_filtered_list').html('');
						if (data['filter_restaurants_result_count'] > data['offset']) {
							var template1 = $.jqote('#restaurant_template', data['result']);
							$('#reastaurant_filtered_list').append(template1);
							$("input[id=restaurant_offset]").val(data['offset']);
							$('#reastaurant_filtered_list').after("<div class='restaurant_filter_load_more search_btn' id='loadMore' onclick='restaurantFilterSearchLoadMore()'>View More</div>");
						}
						else {
							var template = $.jqote('#restaurant_template', data['result']);
							$('#reastaurant_filtered_list').append(template);
							$("input[id=restaurant_offset]").val(data['offset']);
							$('.restaurant_filter_load_more').remove();
						}

						$('html, body').animate({
							scrollTop: $("#reastaurant_filtered_featured_container").offset().top
						}, 2000);

					}
					else if (data['filter_restaurants_result_count'] > data['offset']) {
						var template1 = $.jqote('#restaurant_template', data['result']);
						$('#reastaurant_filtered_list').append(template1);
						$("input[id=restaurant_offset]").val(data['offset']);
						$('#reastaurant_filtered_list').after("<div class='restaurant_filter_load_more search_btn' id='loadMore' onclick='restaurantFilterSearchLoadMore()'>View More</div>");
					}
					else {
						var template = $.jqote('#restaurant_template', data['result']);
						$('#reastaurant_filtered_list').append(template);
						$("input[id=restaurant_offset]").val(data['offset']);
						$('.restaurant_filter_load_more').remove();
					}
				}
				$('#load_image').remove();
				$.hideLoading();
			}
	});
    }
    else {
        alert("Please select atleast one option.");
    }
}

function addToWishlist(restaurant_id, search_page) {
    var base_url = $("#base_url").val();
    var logged_in_user_id = $("#logged_in_user_id").val();
    if (!logged_in_user_id) {
        alert("Please sign in first.");
    }
    else {

        $.ajax({
            url: base_url + "restaurant_add_to_wish_list",
            type: "POST",
            data: {
                restaurant_id: restaurant_id
            },
            dataType: 'json',
            success: function (result) {
                if (result['success']) {
                    alert(result['message']);
                    if (search_page == 0) {
                        $('#filter_search_' + restaurant_id).addClass('active');
                        $('#filter_search_' + restaurant_id).attr("onclick", "removeFromWishlist(" + restaurant_id + ",0)");
                    }
                    else if (search_page == 1) {
                        $('.detailhearticon').html('<span></span> Remove from wishlist');
                        $('#restaurant_details_' + restaurant_id).addClass('active');
                        $('#restaurant_details_' + restaurant_id).attr("onclick", "removeFromWishlist(" + restaurant_id + ",1)");
                    }
                    else if (search_page == 2) {
                        $('#top_ten_' + restaurant_id).addClass('active');
                        $('#top_ten_' + restaurant_id).attr("onclick", "removeFromWishlist(" + restaurant_id + ",2)");
                    }
                }
                else {
                    alert(result['message']);
                }
            }
        });
    }

}

function removeFromWishlist(restaurant_id, search_page) {
    var base_url = $("#base_url").val();
    var logged_in_user_id = $("#logged_in_user_id").val();
    if (!logged_in_user_id) {
        alert("Please sign in first.");
    }
    else {

        $.ajax({
            url: base_url + "restaurant_remove_from_wish_list",
            type: "POST",
            data: {
                restaurant_id: restaurant_id
            },
            dataType: 'json',
            success: function (result) {
                if (result['success']) {
                    alert(result['message']);
                    if (search_page == 0) {
                        $('#filter_search_' + restaurant_id).removeClass('active');
                        $('#filter_search_' + restaurant_id).attr("onclick", "addToWishlist(" + restaurant_id + ",0)");
                    }
                    else if (search_page == 1) {
                        $('.detailhearticon').html('<span></span> Add to wishlist');
                        $('#restaurant_details_' + restaurant_id).removeClass('active');
                        $('#restaurant_details_' + restaurant_id).attr("onclick", "addToWishlist(" + restaurant_id + ",1)");
                    }
                    else if (search_page == 2) {
                        $('#top_ten_' + restaurant_id).removeClass('active');
                        $('#top_ten_' + restaurant_id).attr("onclick", "addToWishlist(" + restaurant_id + ",2)");
                    }
                }
                else {
                    alert(result['message']);
                }
            }
        });
    }
}

function restaurantSearchByNameLoadMore() {
    var search_string = $("#search_by_restaurant_name_txt").val();
    $('.restaurant_filter_load_more').remove();
    $('#reastaurant_filtered_list').after('<div id="load_image"><img src="' + base_url + 'assets/front_end/images/Loading_icon.gif"></img></div>');
    search_by_restaurant_name(search_string);
}

function search_by_restaurant_name(search_string) {
    if (search_string.length < 3) {
        alert("Please enter atleast 3 characters.");
    }
    else {
		if(!isValidStringForSearch(search_string))
		{
			alert("Please enter valid characters in restaurant name.");
		}
		else
		{
			$('#serchpopup').trigger('click');
			var restaurant_offset = $("#restaurant_offset").val();
			if (restaurant_offset == 0) {
				$.showLoading({
					name: 'circle-fade',
					callback: function () {
					}
				});
			}

			$.ajax({
				url: base_url + "fetch_restaurants_by_name",
				type: "POST",
				data: {
					offset: $("#restaurant_offset").val(),
					search_string: search_string
				},
				datatype: 'json',
				success: function (data) {
					var data = jQuery.parseJSON(data);
					
					if (data['filter_restaurants_result_count'] === 0) {
						$('#reastaurant_filtered_list').html('');
						$('#reastaurant_filtered_list').append("<div class='col-sm-12 col-lg-12 col-md-12'><h3>No result found...</h3></div>");
					}
					else {
						if (restaurant_offset == 0) {
							$('#reastaurant_filtered_list').html('');
							if (data['filter_restaurants_result_count'] > data['offset']) {
								var template1 = $.jqote('#restaurant_template', data['result']);
								$('#reastaurant_filtered_list').append(template1);
								$("input[id=restaurant_offset]").val(data['offset']);
								$('#reastaurant_filtered_list').after("<div class='restaurant_filter_load_more search_btn' id='loadMore' onclick='restaurantSearchByNameLoadMore()'>View More</div>");
							}
							else {
								var template = $.jqote('#restaurant_template', data['result']);
								$('#reastaurant_filtered_list').append(template);
								$("input[id=restaurant_offset]").val(data['offset']);
								$('.restaurant_filter_load_more').remove();
							}

							$('html, body').animate({
								scrollTop: $("#reastaurant_filtered_featured_container").offset().top
							}, 2000);

						}
						else if (data['filter_restaurants_result_count'] > data['offset']) {
							var template1 = $.jqote('#restaurant_template', data['result']);
							$('#reastaurant_filtered_list').append(template1);
							$("input[id=restaurant_offset]").val(data['offset']);
							$('#reastaurant_filtered_list').after("<div class='restaurant_filter_load_more search_btn' id='loadMore' onclick='restaurantSearchByNameLoadMore()'>View More</div>");
						}
						else {
							var template = $.jqote('#restaurant_template', data['result']);
							$('#reastaurant_filtered_list').append(template);
							$("input[id=restaurant_offset]").val(data['offset']);
							$('.restaurant_filter_load_more').remove();
						}
					}
					$('#load_image').remove();
					$.hideLoading();
				}
			});
		}
		
        
    }
}

function show_social_icons(restaurant_id) {
    $("#social_icons_filter_search_"+restaurant_id).show();
}

function close_social_icons(restaurant_id) {
    $("#social_icons_filter_search_"+restaurant_id).hide();
}

//filter search and Top ten restaurant redirect booking page
function redirect_restaurant_details_with_booking_details(slot_id,restaurant_id) {
    if (confirm("Are you sure you want to book table?")) {
        window.location = base_url+'profile/booking_by_slot_id/'+restaurant_id+'/'+slot_id;
    }
}

function isValidStringForSearch(searchString) {
	var regex = /^[\w ]+$/;
	if (regex.test(searchString)) {
		return true;
	}
	else
	{
		return false;
	}
	
};

function fill_last_minute_cancellation_to_time()
{
	var time_slot = $("#last_minute_cancellation_from_time option:selected").text();
	var index = $("#last_minute_cancellation_from_time option:selected").index();
	var last_minute_to_time_slot = $("#last_minute_to_time").val();
	last_minute_time_slots = [];
	
	$("#last_minute_cancellation_from_time > option").each(function() {		
		last_minute_time_slots.push(this.text);		
	});

	var options = '';	
	var flag = 0;
	
	for(var loop = index+1; loop < last_minute_time_slots.length; loop++ )
	{
		if(last_minute_to_time_slot == last_minute_time_slots[loop]){
			options += "<option value='" + last_minute_time_slots[loop] + "' selected>" + last_minute_time_slots[loop] + "</option>";	
			flag = 1;
			
		}
		else{
			options += "<option value='" + last_minute_time_slots[loop] + "'>" + last_minute_time_slots[loop] + "</option>";
		}
		
	}	
	
	if(flag != 1 && last_minute_to_time_slot != 0 && last_minute_to_time_slot != "00:00"){
		options += "<option value='" + last_minute_to_time_slot + "' selected>" + last_minute_to_time_slot + "</option>";	
	}
	
	$('#last_minute_cancellation_to_time').html(options);
	$('#last_minute_cancellation_to_time').selectpicker('refresh');
}

$(document).ready(function () {
    base_url = $("#base_url").val();
    // loadNews(base_url);
    loadRestaurantsFilterOptions(base_url);
	//setTimeout(fill_last_minute_cancellation_to_time(), 5000);
	setTimeout(fill_last_minute_cancellation_to_time, 2000)
	
    
	
	$("#filter").click(function (e) {
        $('#myModal').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
			if (keycode == 13) {
				event.preventDefault();
                $("#applyfilter-button").click();
			}
        });
    });
	
	$('.keyword_search').on('shown.bs.modal', function () {
        $("#search_by_restaurant_name_txt").focus();
    });

    $("#searchbutton").click(function (e) {
        $('.keyword_search').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
				event.preventDefault();
                $("#search_by_restaurant_name_sbt_btn").click();
            }
        });
    });

    $('#reset-filter').click(function () {
        $('#restaurants-filter-listing-form')[0].reset();
    });

    $("#search_by_restaurant_name_sbt_btn").click(function () {
        var search_string = $("#search_by_restaurant_name_txt").val();
        search_by_restaurant_name(search_string);
    });

    $("#searchbutton").click(function () {
        $("#restaurant_offset").val('0');
        $("#search_by_restaurant_name_txt").val('');
    });


    $("#restaurant_details_social_icons").hide();
    $("#social_icons_share_span").click(function(){
        $("#restaurant_details_social_icons").toggle();
    });


    //Restaurant details review part start

    $('#service_rating').slider({
        formatter: function (value) {
            return 'Current value: ' + value;
        }
    });
    $('#ambience_rating').slider({
        formatter: function (value) {
            return 'Current value: ' + value;
        }
    });
    $('#quality_of_food').slider({
        formatter: function (value) {
            return 'Current value: ' + value;
        }
    });
    $('#value_for_money').slider({
        formatter: function (value) {
            return 'Current value: ' + value;
        }
    });

    $("#user_review_link").click(function () {
        $("#user_review_link").hide();
        $("#user_review_panel").show();
        $("#user_review").val('');
        $(".review_error_placement").html('');
    });

    $("#btn_review_cancel").click(function () {
        $("#user_review_link").show();
        $("#user_review_panel").hide();
        $("#user_review").val('');
    });

    $("#btn_review_submit").click(function () {
        var your_thoughts = $("#user_review").val();
        var value_for_money = $('#value_for_money').data('slider').getValue();
        var quality_of_food = $('#quality_of_food').data('slider').getValue();
        var ambience_rating = $('#ambience_rating').data('slider').getValue();
        var service_rating = $('#service_rating').data('slider').getValue();
        var restaurant_id = $('#user_id').val();
        var user_id = $('#logged_in_user_id').val();

        if (user_id <= 0) {
            alert("Please sign in first");
        }
        else if (your_thoughts.length < 4) {
            alert("Please write atleast four character");
        }
        else {
            $.ajax({
                url: base_url + "add_review",
                type: "POST",
                data: {
                    restaurant_id: restaurant_id,
                    quality_of_food: quality_of_food,
                    value_for_money: value_for_money,
                    service_rating: service_rating,
                    ambience_rating: ambience_rating,
                    your_thoughts: your_thoughts,
                    user_id: user_id
                },
                dataType: 'json',
                success: function (result) {
                    if (result.status == 1) {
                        $("#user_review_link").show();
                        $("#user_review_panel").hide();
                        $("#user_review").val('');


                        $.ajax({
                            url: base_url + "restaurant_get_updated_reviews",
                            type: "POST",
                            data: {
                                restaurant_id: restaurant_id
                            },
                            dataType: 'json',
                            success: function (review_data) {
                                $("#avg_rating_p").html('<span class="uic-review-star"></span> '+ review_data.average_rating +'');

                                $("#service_rating_progress_span").html( (Math.round( review_data.rating_criteria.service_rating * 100 )/100).toString() );
                                $("#service_rating_progress_div").css("width", review_data.service_rating+"%");

                                $("#ambience_rating_progress_span").html((Math.round( review_data.rating_criteria.ambience_rating * 100 )/100).toString());
                                $("#ambience_rating_progress_div").css("width", review_data.ambience_rating+"%");

                                $("#food_rating_progress_span").html((Math.round( review_data.rating_criteria.food_rating * 100 )/100).toString());
                                $("#food_rating_progress_div").css("width", review_data.food_rating+"%");

                                $("#money_rating_progress_span").html((Math.round( review_data.rating_criteria.money_rating * 100 )/100).toString());
                                $("#money_rating_progress_div").css("width", review_data.money_rating+"%");
                            }
                        });


                        if(document.getElementById("no_review_msg")){
                            $("#no_review_msg").remove();
                        }

                        if (document.getElementById("review_" + result.rating_id)) {
                            $("#review_" + result.rating_id + " p").html(result.your_thoughts);
                            var index = $("#review_" + result.rating_id).attr('data-slide-index');
                            $('.reviews').slick('slickGoTo', index);
                        }
                        else {
                                $('.reviews').slick('slickAdd', '<div class="reviews_in" id="review_' + result.rating_id + '"> <p>' + result.your_thoughts + '</p> </div>');
                                $('.reviews').slick('slickGoTo', 'last');
                        }
                        alert(result.message);
                        $("#user_review").attr("placeholder", your_thoughts);
                    }
                    else {
                        alert(result.message);
                    }
                }
            });
        }
    });
    //Restaurant details review part end
	
	//Front Restaurant book table start
	
	$("#details_booking_time").on('change', function () {
		var restaurant_id = $("#restaurant_id").val();
		var slot_id = this.value;
		var date = $("#details_booking_date").val();
		
		$("#front_next_four_available_time_slots").hide();
		$("#booking_number_of_people").val('1').trigger('change');
		$("#front_booking_error_div").html('');
		
            $.ajax({
                url: base_url+'front/restaurant/get_table_list',
                data: {
                    "date": date,
                    "slot_id": slot_id,
					"restaurant_id": restaurant_id
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
                        $('#booking_table_select').html(options);
						$('#booking_table_select').selectpicker('refresh');
						$("#booking_table_select").val('').trigger('change');
                    }
                    else {
                        $('#booking_table_select').find('option').remove();
						$('#booking_table_select').selectpicker('refresh');
                        var available_time_slots = data.result.response.time_slot;
                        if (available_time_slots.length > 0) {
                            var button = '';
                            $.each(available_time_slots, function (key, value) {
                                button += '<button name="name" value="' + value.slot_id + '" type="button" class="btn" disabled>' + value.time_slot + '</button> &nbsp;';
                            });

							$("#front_next_four_available_time_slots").show();
                            $("#front_available_time_slot_div").html(button);
                        }
                        else {
							$("#front_booking_error_div").html("Table not available for selected time slot.");
                            //alert("Table not available for selected time slot.");
                        }
                    }
                }
            });  
    })	
	
	$("#last_minute_cancellation_from_time").on('change', function () {
		fill_last_minute_cancellation_to_time();
	});
	
	$('#datetimepicker1').on('dp.change', function(e){ 
		var restaurant_id = $("#restaurant_id").val();
		var date = e.date._d;
		var selected_date = (date.getDate() + '-' + (date.getMonth() + 1) + '-' +  date.getFullYear());
		$("#front_next_four_available_time_slots").hide();
		front_booking_date = selected_date;
		//Clear booking time and all tables start
			$('#details_booking_time').find('option').remove();
			$('#details_booking_time').selectpicker('refresh');
			
			$('#booking_table_select').find('option').remove();
			$('#booking_table_select').selectpicker('refresh');	
			
			$('#last_minute_cancellation_from_time').find('option').remove();
			$('#last_minute_cancellation_from_time').selectpicker('refresh');
			
			$('#last_minute_cancellation_to_time').find('option').remove();
			$('#last_minute_cancellation_to_time').selectpicker('refresh');
			
			$("#booking_number_of_people").val('1').trigger('change');
			
			$("#front_booking_error_div").html('');
		//Clear booking time and all tables end
			$.ajax({
					url: base_url +'front/restaurant/get_time_slot',
					data: {
						"date": selected_date,
						"restaurant_id": restaurant_id
					},
					dataType: 'json',
					success: function (data) {
						var options = '';
						if(data.success == 1)
						{
							if(data.time_slots != null)
							{
								last_minute_time_slots = [];
								$.each(data.time_slots, function (key, value) {
									
									options += "<option value='" + value.slot_id + "'>" + value.time_slot + "</option>";
									last_minute_time_slots.push(value.time_slot);
								});
							}
							else
							{
								//alert("No time slot(s) available for selected date");
								$("#front_booking_error_div").html("No time slot(s) available for selected date.");
							}
							
						}
						else
						{
							$("#front_booking_error_div").html(data.message);
						}
						$('#details_booking_time').html(options);
						$('#details_booking_time').selectpicker('refresh');
						$("#details_booking_time").val(data.time_slots[0].slot_id).trigger('change');	
						
						$('#last_minute_cancellation_from_time').html(options);
						$('#last_minute_cancellation_from_time').selectpicker('refresh');
						fill_last_minute_cancellation_to_time();
					}
			});
	})
	
	$('#last_minute_cancellation_cb').change(function(){
		 
		if(this.checked){			
			$('#last_minute_cancellation_time_slots_div').show();
		}
		else{
			$('#last_minute_cancellation_time_slots_div').hide();
		}

	});

        $('#after_booking_detials_modal').on('hidden.bs.modal', function () {
		location.reload();
	});
	
	$("#front_book_table").click(function(){
		$("#front_booking_error_div").html('');
		var booking_id = $("#booking_id").val();
		var restaurant_id = $("#restaurant_id").val();
		var no_of_guest = $("#booking_number_of_people").val();
		var booking_date = $("#details_booking_date").val();
		var booking_time = $("#details_booking_time").val();
		var table_ids = $("#booking_table_select").val();
		var total_selected_guest = 0;
		var selected_table_array = [];
		var selected_table_txt = '';
		var time = $("#details_booking_time option[value='"+booking_time+"']").text();
		var is_last_minuit_condition_satisfy = 0;
		var is_notify = 0;
		var last_minute_cancellation_from_time = 0;
		var last_minute_cancellation_to_time = 0;
		
			$("#booking_table_select :selected").each(function () {
                total_selected_guest = total_selected_guest + parseInt($(this).attr('data-table_capacity'));
				selected_table_array.push($(this).text());
			});
			
			if($("#last_minute_cancellation_cb").is(':checked')){
				is_notify = 1;
				selected_last_minute_cancellation_from_time = $("#last_minute_cancellation_from_time option:selected").text();
				selected_last_minute_cancellation_to_time = $("#last_minute_cancellation_to_time").val();
				
				if(selected_last_minute_cancellation_from_time != '' && selected_last_minute_cancellation_to_time != null)
				{
					is_last_minuit_condition_satisfy = 1;
					last_minute_cancellation_from_time = selected_last_minute_cancellation_from_time;
					last_minute_cancellation_to_time = selected_last_minute_cancellation_to_time;
				}
				else{
					is_last_minuit_condition_satisfy = 0;
				}
			}
			
			if(booking_date == ''){
				$("#front_booking_error_div").html("Please select booking date.");
                event.preventDefault();                
            } 
			else if(booking_time == '' || booking_time == null) {
				$("#front_booking_error_div").html("Please select booking time.");
                event.preventDefault();
            }
			else if(no_of_guest <= 0) {
                    $("#front_booking_error_div").html("Please enter number of guest greater than or equal to 1.");
                    event.preventDefault();
            }
			else if(no_of_guest > 20) {
                    $("#front_booking_error_div").html("Please enter number of guest less than or equal to 20.");
                    event.preventDefault();
            }
			else if(total_selected_guest <= 0)
			{
				$("#front_booking_error_div").html("Please select Table(s)");
                    event.preventDefault();
			}
			else if(no_of_guest > total_selected_guest)	
			{
				$("#front_booking_error_div").html("Guest number should be less than or equal to table capacity.");
                    event.preventDefault();
			}
			else if($("#last_minute_cancellation_cb").is(':checked') && is_last_minuit_condition_satisfy == 0){
				$("#front_booking_error_div").html("Please select from time and last time for last minute availability.");
                event.preventDefault();
			}
			else
			{
				if (confirm("Are you sure you want to book table?")) {	
					$.showLoading({
						name: 'circle-fade',
						callback: function () {
						}
					});
					$.ajax({
						url: base_url +'front/restaurant/front_book_table',
						data: {
							"booking_id": booking_id,
							"no_of_guest": no_of_guest,
							"restaurant_id": restaurant_id,
							"booking_date": booking_date,
							"booking_time": booking_time,
							"table_ids": table_ids,	
							"is_notify": is_notify,
							"last_minute_cancellation_from_time": last_minute_cancellation_from_time,
							"last_minute_cancellation_to_time": last_minute_cancellation_to_time							
						},
						dataType: 'json',
						success: function (data) {
							if(data.status == 1)
							{
								$('#details_booking_time').find('option').remove();
								$('#details_booking_time').selectpicker('refresh');
								$("#details_booking_time").val('').trigger('change');
								
								$('#booking_table_select').find('option').remove();
								$('#booking_table_select').selectpicker('refresh');
								$("#booking_table_select").val('').trigger('change');
								
								$('#last_minute_cancellation_from_time').find('option').remove();
								$('#last_minute_cancellation_from_time').selectpicker('refresh');
								
								$('#last_minute_cancellation_to_time').find('option').remove();
								$('#last_minute_cancellation_to_time').selectpicker('refresh');
								
								$('#last_minute_cancellation_cb').attr('checked', false);
								$("#last_minute_cancellation_time_slots_div").hide();
								
								$("#booking_number_of_people").val('1').trigger('change');
								$("#booking_number_of_people").val('');
								$("#details_booking_date").val('');
								
								selected_table_txt = selected_table_array.join(", ");
								$("#lbl_booking_date").text(booking_date);
								$("#lbl_booking_time").text(time);
								$("#lbl_booking_code").text(data.booking_number);
								$("#lbl_no_of_people").text(no_of_guest);
								$("#lbl_tables").text(selected_table_txt);
								
								$('#after_booking_detials_modal').modal();
								
							}
							else
							{
								alert(data.message);
							}
						}
					});	
					$.hideLoading();
				}
			}
	});
	
	
	//Front Restaruant book table end
	
	
	
	/*$('#input_restaurants_filter_location').keypress(function (e) {
		var regex = new RegExp("^[a-zA-Z0-9]+$");
		var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		if (regex.test(str)) {
			return true;
		}

		e.preventDefault();
		return false;
	});
	
	$('#search_by_restaurant_name_txt').keypress(function (e) {
		var regex = new RegExp("^[a-zA-Z0-9]+$");
		var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
		if (regex.test(str)) {
			return true;
		}

		e.preventDefault();
		return false;
	});*/


});