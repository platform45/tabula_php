<title>Tabula: <?php echo $restaurant_details->restaurant_name; ?></title>

<style>
    .control-label {
        text-align: right !important;
    }

    .share_icon {
        margin-left: 0 !important;
    }

    .review_div {
        padding: 10px;
    }

    .slider-handle {
        background-image: linear-gradient(to bottom, #e41444, #e41444) !important;
    }


</style>

<div class="restaurant-detail">
<img src="<?php echo $restaurant_details->restaurant_image ?>" title="<?php echo $restaurant_details->restaurant_name; ?>" hidden></img>

    <div class="banner_image restaurants_detail_banner"
         style="background-image:url(<?php echo $restaurant_details->restaurant_image ?>)">
        <div class="banner_caption">
        </div>
    </div>
    <!-- Banner Content -->
    <section class="restaurantsaddress">
        <div class="container">
            <input type="hidden" value="<?php echo $restaurant_details->user_id; ?>" id="user_id">

            <h3><?php echo $restaurant_details->restaurant_name; ?></h3>
            <p><?php echo $address; ?></p>
            <p class="rating" id="avg_rating_p"><span
                        class="uic-review-star"></span> <?php echo $restaurant_details->average_rating; ?>
            </p>
            <div class="time-outer">
                <p><span><?php echo $restaurant_details->open_status; ?></span></p>
                <?php if ($restaurant_details->open_status != "Closed") { ?>
                    <div class="time"><?php echo $restaurant_details->from_time; ?>
                        - <?php echo $restaurant_details->to_time; ?></div>
                <?php } ?>
            </div>

        </div>

    </section>
	
	<?php if($bookings) { ?>
		<input type="hidden" value="<?php echo $bookings['booking_id']; ?>" id="booking_id">
	<?php } else {?>
	<input type="hidden" id="booking_id">
	<?php } ?>
    <section class="restaurants-search">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-9 searchfieldouter">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="searchlable">Date</div>
							
                            <div class="form-group">
                                <div class='input-group date' id='datetimepicker1'>
                                   <!-- <input type='text' value="<?php if($bookings) {echo($bookings['booking_date']); }?>" class="form-control" name="details_booking_date" id="details_booking_date" /> -->
									
									<?php if($bookings) {
										if($current_date != $bookings['booking_date'])
										{ ?>
											<input type='text' value="<?php echo($bookings['booking_date']);?>" class="form-control" name="details_booking_date" id="details_booking_date" />
										<?php } else { ?>
											<input type='text' value="" class="form-control" name="details_booking_date" id="details_booking_date" />
										<?php	 }
									} else {?>
											<input type='text' value="" class="form-control" name="details_booking_date" id="details_booking_date" />
									<?php } ?>
                                    <span class="input-group-addon">
										<span class="droparrow"></span>
									</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="searchlable">Time</div>
                            <div class="form-group">
							    <select class="form-control selectpicker" name="details_booking_time" id="details_booking_time">
                                      <?php 
									  if($bookings)
									  {
										foreach($bookings['time_slots'] as $time_slot)
										{
											if ($time_slot['status'] == SUCCESS) { ?>
												<option value="<?php echo $time_slot['slot_id']; ?>" selected> <?php echo $time_slot['time_slot'] ?> </option>
											<?php
											} else { ?>
												<option value="<?php echo $time_slot['slot_id']; ?>"> <?php echo $time_slot['time_slot'] ?> </option>
											<?php }
										}											
									  }
									  
									  ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="searchlable">No. of people</div>
                                <div class="form-group">
                                    <!--<input type='number' min="1" max="20" value="<?php if($bookings) {echo($bookings['no_of_guest']); }?>" id="booking_number_of_people" name="details_booking_no_of_people" class="form-control" maxlength="2" /> -->
									<select class="form-control selectpicker" name="details_booking_no_of_people" id='booking_number_of_people'>
										<?php 
										 for($no_of_people = 1; $no_of_people <=20; $no_of_people++)
										 {
											 if($bookings)
											 {
												 if($bookings['no_of_guest'] == $no_of_people)
												 { ?>
													<option value="<?php echo $no_of_people; ?>" selected> <?php echo $no_of_people ?> <?php if($no_of_people > 1){ ?>Peoples <?php } else {?>People <?php }?></option>  
												 <?php }
												 else
												 { ?>
													<option value="<?php echo $no_of_people; ?>"> <?php echo $no_of_people ?> <?php if($no_of_people > 1){ ?>Peoples <?php } else { ?>People <?php } ?></option>  
												<?php }
											 }
											 else
											 { ?>
												<option value="<?php echo $no_of_people; ?>"> <?php echo $no_of_people ?> <?php if($no_of_people > 1){ ?>Peoples <?php } else { ?>People <?php } ?></option>  
											 <?php }
										 }											 
										?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="searchlable">Select Table</div>
                                <div class="form-group">
                                    <select class="form-control selectpicker" name="booking_table[]" id='booking_table_select' multiple>
                                      <?php 
									  if($bookings)
									  {
										foreach($bookings['booking_table_list'] as $booking_table)
										{
											if ($booking_table['status'] == SUCCESS) { ?>
												<option data-table_capacity="<?php echo $booking_table['table_capacity'] ?>" value="<?php echo $booking_table['table_id']; ?>" selected> <?php echo $booking_table['table_name'] ?>(<?php echo $booking_table['table_capacity'] ?>) </option>
											<?php
											} else { ?>
												<option data-table_capacity="<?php echo $booking_table['table_capacity'] ?>" value="<?php echo $booking_table['table_id']; ?>"> <?php echo $booking_table['table_name'] ?>(<?php echo $booking_table['table_capacity'] ?>) </option>
											<?php }
										}											
									  }
									  
									  ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-12 col-lg-3 searchbuttonin">
                    <button class="search-button" type="button" id="front_book_table">BOOK A TABLE</button>
                </div>
            </div>
			
			<div class="col-md-12" id="last_minute_cancellation_div">
				<label class="col-md-3"><input type="checkbox" name="last_minute_cancellation_cb" id="last_minute_cancellation_cb" <?php if($bookings['is_notify'] == 1){echo checked;} ?>> Notify me of Last Minute Availability </label>
				<div class="col-md-8" id="last_minute_cancellation_time_slots_div" <?php if($bookings['is_notify'] != 1){echo hidden;} ?>>
					<div class="col-sm-4">
						<div class="form-group">
							<select class="form-control selectpicker" name="last_minute_cancellation_from_time" id="last_minute_cancellation_from_time">
								  <?php 
								  if($bookings)
								  {
									foreach($bookings['time_slots'] as $time_slot)
									{
										if ($time_slot['time_slot'] == $bookings['last_minute_from_time']) { ?>
											<option value="<?php echo $time_slot['slot_id']; ?>" selected> <?php echo $time_slot['time_slot'] ?> </option>
										<?php
										} else { ?>
											<option value="<?php echo $time_slot['slot_id']; ?>"> <?php echo $time_slot['time_slot'] ?> </option>
										<?php }
									}											
								  }
								  
								  ?>
							</select>
						</div>
					</div>
					
					
					<div class="col-sm-4">
						<div class="form-group">
							<select class="form-control selectpicker" name="last_minute_cancellation_to_time" id="last_minute_cancellation_to_time">
							</select>
						</div>
					</div>
					
				</div>
			</div>
			
			<div class="col-md-12" id="front_next_four_available_time_slots" hidden>
				<label class="col-md-2">Next Available Time Slot(s): </label>
				<div class="col-md-7" id="front_available_time_slot_div">
				</div>
			</div>
			
			<div class="col-md-12" id="front_booking_error_div">
				<?php 
					if($bookings['slots_available_for_now'] == 0) {?>
					No time slot(s) available for today.
				<?php } ?>
			</div>
        </div>
    </section>

	<input type="hidden" value="<?php echo $bookings['last_minute_to_time']; ?>" id="last_minute_to_time">
	<input type="hidden" value="<?php echo $restaurant_details->user_id; ?>" id="restaurant_id">
    <input type="hidden" value="<?php echo $restaurant_details->latitude; ?>" id="latitude">
    <input type="hidden" value="<?php echo $restaurant_details->longitude; ?>" id="longitude">
    <section id="map" class="map" style="height:300px">
        <script>
            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 14,
                    center: new google.maps.LatLng(<?php echo $restaurant_details->latitude; ?>, <?php echo $restaurant_details->longitude; ?>)
                });
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(<?php echo $restaurant_details->latitude; ?>, <?php echo $restaurant_details->longitude; ?>),
                    map: map
                });
            }
        </script>
        <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCpGPT0S4Qd43UJAjvtbmMlSdzt3p2y9R0&callback=initMap">
        </script>
    </section>

    <section class="restaurants-detailbox">
        <div class="container">
            <div class="row">
                <div class="col-sm-7 leftside">
                    <h2>Description</h2>
                    <p>
                        <?php echo $restaurant_details->restaurant_description; ?>
                    </p>
                </div>
                <div class="col-sm-5">
                    <h2>Contact Details</h2>
                    <div class="iconwithtext">
                        <i class="fa fa-phone gray"
                           aria-hidden="true"></i> <a class="contact_mail_underline" href="tel:+<?php echo $restaurant_details->user_contact ?>"><?php echo $restaurant_details->user_contact ?></a>
                    </div>
                    <div class="iconwithtext">
                        <i class="fa fa-envelope gray"
                           aria-hidden="true"></i> <a class="contact_mail_underline" href="mailto:<?php echo $restaurant_details->user_email ?>"><?php echo $restaurant_details->user_email ?></a>
                    </div>

                    <?php if ($restaurant_details->web_domain) { ?>
                        <div class="iconwithtext">
                            <i class="fa fa-globe gray" aria-hidden="true"></i> <a target="_blank" class="contact_mail_underline" 
                                                                                   href="http://<?php echo $restaurant_details->web_domain ?>"><?php echo $restaurant_details->web_domain ?></a>
                        </div>
                    <?php } ?>


                    <div class="iconwithtext bold gallery margin-top-25">
                        <?php if ($menu_images) {
                            foreach ($menu_images as $images) { ?>
                                <a class="fancybox-button" rel="fancybox-button"
                                   href="<?php echo $images->menu_image ?>" title="">
                                    <span class="uic-menu1 green"></span> Menu<img
                                            src="<?php echo $images->menu_image ?>" alt=""/>
                                </a>
                            <?php }
                        } ?>
                    </div>

                    <div class="iconwithtext bold">
                        <?php if (!$restaurant_details->is_wish) { ?>
                            <a href="javascript:void(0)"
                               id="restaurant_details_<?php echo $restaurant_details->user_id; ?>"
                               onclick="addToWishlist(<?php echo $restaurant_details->user_id; ?>,1)"
                               class="detailhearticon"><span></span> Add to wishlist</a>
                        <?php } else { ?>
                            <a href="javascript:void(0)"
                               id="restaurant_details_<?php echo $restaurant_details->user_id; ?>"
                               onclick="removeFromWishlist(<?php echo $restaurant_details->user_id; ?>,1)"
                               class="detailhearticon active"><span></span> Remove from wishlist</a>
                        <?php } ?>
                    </div>

                    <div class="iconwithtext bold">
                        <a href="javascript:void(0)" id="social_icons_share_span"> <span class="uic-share"></span>Share</a>
                    </div>
                    <span class="social_icons" id="restaurant_details_social_icons">
                            <a href="https://plus.google.com/share?url=<?php echo base_url(); ?>restaurant-details/<?php echo $restaurant_details->restaurant_detail_url; ?>"
                               target="_blank" title="Click to share on google plus">
                                <div class="icon">
                                    <i aria-hidden="true" class="fa fa-google-plus"></i>
                                </div>
                            </a>

                            <a href="http://www.facebook.com/sharer.php?u=<?php echo base_url(); ?>restaurant-details/<?php echo $restaurant_details->restaurant_detail_url; ?>"
                               target="_blank" title="Click to share on facebook">
                                <div class="icon">
                                    <i aria-hidden="true" class="fa fa-facebook"></i>
                                </div>
                            </a>

                            <a href="http://twitter.com/share?text=&url=<?php echo base_url(); ?>restaurant-details/<?php echo $restaurant_details->restaurant_detail_url; ?>"
                               target="_blank" title="Click to share on Twitter">
                                <div class="icon">
                                    <i aria-hidden="true" class="fa fa-twitter"></i>
                                </div>
                            </a>
                    </span>
                </div>
            </div>
        </div>
    </section>
    <section class="restaurants-detailbox">
        <div class="container">
            <div class="row">
                <div class="col-sm-7 leftside">
                    <h2>Overall Rating</h2>
                    <div class="progressbar">
                        <h4>Service</h4>
                        <div class="progress">
                            <div class="progress-bar" id="service_rating_progress_div" role="progressbar"
                                 aria-valuenow="7" aria-valuemin="0"
                                 aria-valuemax="10" style="width: <?php echo $service_rating ?>%">
                                <span class="sr-only"
                                      id="service_rating_progress_span"><?php echo round($rating_criteria->service_rating, 1) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="progressbar">
                        <h4>Ambience</h4>
                        <div class="progress">
                            <div class="progress-bar" id="ambience_rating_progress_div" role="progressbar"
                                 aria-valuenow="40" aria-valuemin="0"
                                 aria-valuemax="10" style="width: <?php echo $ambience_rating ?>%">
                                <span class="sr-only"
                                      id="ambience_rating_progress_span"><?php echo round($rating_criteria->ambience_rating, 1) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="progressbar">
                        <h4>Quality of Food</h4>
                        <div class="progress">
                            <div class="progress-bar" id="food_rating_progress_div" role="progressbar"
                                 aria-valuenow="40" aria-valuemin="0"
                                 aria-valuemax="10" style="width: <?php echo $food_rating ?>%">
                                <span class="sr-only"
                                      id="food_rating_progress_span"><?php echo round($rating_criteria->food_rating, 1) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="progressbar">
                        <h4>Value for Money</h4>
                        <div class="progress">
                            <div class="progress-bar" id="money_rating_progress_div" role="progressbar"
                                 aria-valuenow="40" aria-valuemin="0"
                                 aria-valuemax="10" style="width: <?php echo $money_rating ?>%">
                                <span class="sr-only"
                                      id="money_rating_progress_span"><?php echo round($rating_criteria->money_rating, 1) ?></span>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-sm-5">
                    <h2>Amenities</h2>
                    <ul class="list-inline">
                        <?php foreach ($aminities_array as $aminities) { ?>
                            <li><?php echo $aminities ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="reviewbox">
        <h3>Reviews</h3>
        <?php if (!$reviews) { ?>
            <h4 id="no_review_msg">Be the first to review</h4>
        <?php } ?>
        <div class="reviews">
            <?php $index_for_slide = 0;
            foreach ($reviews as $review) { ?>
                <div class="reviews_in" data-slide-index="<?php echo $index_for_slide; ?>"
                     id="review_<?php echo $review['rating_id']; ?>">
                    <p><?php echo $review['your_thoughts']; ?>
                    </p>
                </div>
                <?php $index_for_slide = $index_for_slide + 1;
            } ?>
        </div>

        <a href="javascript:void(0)" id="user_review_link" data-toggle="modal" data-target="#review_modal">
            <i class="fa fa-pencil" aria-hidden="true"></i>
            Write a Review
        </a>
    </section>

    <!--    <section class="write_review_section">-->
    <div class="panel panel-default" id="user_review_panel" align="left" style="border:0px;" hidden>
        <div class="panel-body">
            <div class="dialog1">
                <div class="col-sm-12 review_div">
                    <div class="form-group">
                        <label class="control-label text-left col-sm-3">Service: </label>
                        <div class="col-sm-6">
                            <input id="service_rating" data-slider-id='service_rating_slider' type="text"
                                   data-slider-min="1" data-slider-max="10" data-slider-step="1"
                                   data-slider-value="<?php echo($user_rating->service_rating ? $user_rating->service_rating : 0); ?>">
                        </div>

                    </div>
                </div>

                <div class="col-sm-12 review_div">
                    <div class="form-group">
                        <label class="control-label text-left col-sm-3">Ambience: </label>
                        <div class="col-sm-6">
                            <input id="ambience_rating" data-slider-id='ambience_rating_slider' type="text"
                                   data-slider-min="1" data-slider-max="10" data-slider-step="1"
                                   data-slider-value="<?php echo($user_rating->ambience_rating ? $user_rating->ambience_rating : 0); ?>">
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 review_div">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Quality of Food: </label>
                        <div class="col-sm-6">
                            <input id="quality_of_food" data-slider-id='quality_of_food_slider' type="text"
                                   data-slider-min="1" data-slider-max="10" data-slider-step="1"
                                   data-slider-value="<?php echo($user_rating->food_rating ? $user_rating->food_rating : 0); ?>">
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 review_div">
                    <div class="form-group">
                        <label class="control-label text-left col-sm-3">Value for Money: </label>
                        <div class="col-sm-6">
                            <input id="value_for_money" data-slider-id='value_for_money_slider' type="text"
                                   data-slider-min="1" data-slider-max="10" data-slider-step="1"
                                   data-slider-value="<?php echo($user_rating->money_rating ? $user_rating->money_rating : 0); ?>">
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 review_div">
                    <div class="form-group">
                        <label class="control-label text-left col-sm-3">Your Thoughts: </label>
                        <div class="col-sm-6">
                  <textarea class="form-control" rows="3" id="user_review" name="user_review" maxlength="160"
                            onchange="check_review_input()"
                            placeholder="<?php echo($user_rating->your_thoughts ? $user_rating->your_thoughts : ''); ?>">

                  </textarea>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 review_div">
                    <div class="form-group">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-primary" id="btn_review_submit">SUBMIT</button>
                            <button type="button" class="btn btn-primary" id="btn_review_cancel">CANCEL</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--    </section>-->


    <?php if ($gallery_images) { ?>
    <section class="restaurantsdetail">

        <?php
            foreach ($gallery_images as $image) {
                ?>
                <div>
                    <img src="<?php echo $image->gallery_image ?>" class="img-responsive">
                </div>
                <?php
            }
            ?>
    </section>
    <?php    } else { ?>
            <h4 class="no_gallery_images_msg">No gallery images.</h4>
    <?php } ?>

</div>


<div id="after_booking_detials_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
		
		<div class="profiledetail">
			<a href="javascript:void(0)"><img src="<?php echo $restaurant_details->restaurant_image ?>" class="img-responsive" style="cursor:default; width: 35%; margin: 0 auto;"></a>
			<h2 style="margin-bottom:0 !important;"><?php echo $restaurant_details->restaurant_name; ?></h2>
		</div>
		
      </div>
      <div class="modal-body">
        <p>Guest Name: <?php echo $this->session->userdata('user_first_name'); ?></p>
		<p>Booking Date: <label id="lbl_booking_date"></label></p>
		<p>Booking Time: <label id="lbl_booking_time"></label></p>
		<p>Booking Code: <label id="lbl_booking_code"></label></p>
		<p>Number Of People(s): <label id="lbl_no_of_people"></label></p>
		<p>Table(s): <label id="lbl_tables"></label></p>
		<button type="button" id="after_booking_detials_modal_btn" class="btn" data-dismiss="modal">Ok</button>
      </div>
      
    </div>

  </div>
</div>

