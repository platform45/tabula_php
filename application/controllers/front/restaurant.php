<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Restaurant extends CI_Controller
{

    function __construct()
    {
        error_reporting(0);
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('webservices/contentmodel', 'contentmodel', TRUE);
        $this->load->model('webservices/searchmodel', 'searchmodel', TRUE);
        $this->load->model('webservices/bookingmodel', 'wsbookingmodel', TRUE);
        $this->load->model('admin/restaurant_table_model', 'restauranttablemodel', TRUE);
        $this->load->model('front/homemodel', 'homemodel', TRUE);
		$this->load->model('webservices/notificationmodel', 'notificationmodel', TRUE );
    }


    /*
     * Method Name: get_restaurant_detail
     * Purpose: To get feeetails of a restaurant.
     * params:
     *      input: restaurant_id, token,user_id,client_zone
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array containing restaurant name, address, restaurant details
     */
    public function index($restaurant)
    {
        $position = strrpos($restaurant, '-');
        $restaurant_id = substr($restaurant, $position + 1);
        $data = $this->restaurant_details_commom_function($restaurant_id);
        $this->template_front->view('restaurantsdetail', $data);
    }

    /*
     * Method Name: restaurant_details
     * Purpose: To get feeetails of a restaurant after search filter by guest.
     * params:
     *      input:
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array containing restaurant name, address, restaurant details
     */
    public function restaurant_details()
    {
        $data = array();
        $booked_data = array();
        $restaurant_details_url = $this->uri->segment(2);
        $restaurant_id = $this->restaurantmodel->get_restaurant_id_by_details_url($restaurant_details_url);
		$is_table_available = $this->restauranttablemodel->get_tables($restaurant_id);
		$booked_data['slots_available_for_now'] = 0;

        if ($restaurant_id) {
			
			
            //Redirected from profile page: modify booking : get all booking details with available table list and time slot list : start
            $booking_id = base64_decode($this->session->userdata('modified_booking'));
            if ($booking_id) {
				$this->session->unset_userdata('book_slot_id');
                if ($is_table_available) {
                    $booking_data = $this->homemodel->getData($booking_id);
					
                    if ($booking_data) {
                        $date = date('Y-m-d H:i:s', strtotime($booking_data->booking_date));
						$time_slots = $this->restaurantmodel->get_available_time_slots_for_restaurant($date, $restaurant_id);
                        $time_slots = $time_slots['time_slots'];
                        $flag = 0;
                        foreach ($time_slots as $key => $time_slot) {
                            if ($time_slot['time_slot'] == $booking_data->booking_time) {
                                $time_slots[$key]['status'] = SUCCESS;
                                $flag = SUCCESS;
                            } else {
                                $time_slots[$key]['status'] = FAIL;
                            }
                        }
                        if ($flag != SUCCESS) {
                            $insert_index = sizeof($time_slots);
                            $time_slots[$insert_index]['slot_id'] = $booking_data->time_slot_id;
                            $time_slots[$insert_index]['time_slot'] = $booking_data->booking_time;
                            $time_slots[$insert_index]['status'] = SUCCESS;
                        }
						

                        //Get available table list and check for status which is selected
                        $date_time = date('Y-m-d H:i:s', strtotime("$booking_data->booking_date $booking_data->booking_time"));
                        $table_list = $this->restaurantmodel->get_tables($restaurant_id, $date, $booking_data->time_slot_id, $booking_data->booking_time, $date_time);
						
					    $booking_table_list = array();
						$next_four_available_time_slots = array();
                        $booked_table_ids = explode(',', $booking_data->table_ids);
                        foreach ($booked_table_ids as $booked_table_id) {
                            if ($booked_table_id > 0) {
                                $table_data = $this->restauranttablemodel->get_table_data($booked_table_id);
                                $temp_table = array();
                                $temp_table['status'] = SUCCESS;
                                $temp_table['table_id'] = $table_data->table_id;
                                $temp_table['table_name'] = $table_data->table_name;
                                $temp_table['table_capacity'] = $table_data->table_capacity;
                                $booking_table_list[] = $temp_table;
                            }
                        }

                        if ($table_list['is_table_list'] == 1) {
                            $table_list = $table_list['response']['table_list'];
                            foreach ($table_list as $key => $item) {
                                $temp_table = array();
                                $temp_table['status'] = FAIL;
                                $temp_table['table_id'] = $item['table_id'];
                                $temp_table['table_name'] = $item['table_name'];
                                $temp_table['table_capacity'] = $item['table_capacity'];
                                $booking_table_list[] = $temp_table;
                            }
                        }

				        $booked_data = array(
							'booking_id' => $booking_id,
                            'no_of_guest' => $booking_data->number_of_guest,
                            'booking_date' => $booking_data->booking_date,
                            'time_slots' => $time_slots,
                            'booking_table_list' => $booking_table_list,
                            'is_table_list' => SUCCESS,
							'next_four_available_time_slots' => $next_four_available_time_slots,
							'is_notify' => $booking_data->is_notify,
							'last_minute_from_time' => date('H:i', strtotime($booking_data->last_minute_from_time)),
							'last_minute_to_time' => date('H:i', strtotime($booking_data->last_minute_to_time))
                        );
						$booked_data['slots_available_for_now'] = SUCCESS;
						
						
                    } else {
                        $this->session->unset_userdata('modified_booking');
                        $this->session->set_userdata("toast_error_message", "Unable to find booking details. Please try again.");
                        redirect('profile');
                    }
                } else {
                    $this->session->unset_userdata('modified_booking');
                    $this->session->set_userdata("toast_error_message", "Currently no table available for this restaurant.");
                    redirect('profile');
                }
            }
            //Redirected from profile page: modify booking : get all booking details with available table list and time slot list : end


			//Redirected from filter page, top ten restro: add booking : get available table list and time slot list : start
            $slot_id = base64_decode($this->session->userdata('book_slot_id'));
			
			if(!$slot_id)
            {
				$date_time_array = $this->restaurantmodel->get_rounded_time();
				$slot_id = $date_time_array['time_slot_id'];
				
				$date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
				$date_for_open_close = $date->format('Y-m-d 00:00:00');
				
				
			}
			if(!$booking_id)
			{
				$this->session->unset_userdata('modified_booking');
				if ($is_table_available) {
					
					$date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
					$date_for_slot = $date->format('Y-m-d 00:00:00');
					
					$restaurant_open_close_time = $this->restaurantmodel->get_open_close_time_of_restaurant($date_for_slot, $restaurant_id);
					$restaurant_open_close_time = $restaurant_open_close_time->result_array();
					$restro_open_time = date('H:i', strtotime($restaurant_open_close_time[0]['open_time_from']));
					$restro_close_time = date('H:i', strtotime($restaurant_open_close_time[0]['close_time_to']));
					
					
					$restro_open_time_id = $this->restaurantmodel->get_time_slot_id($restro_open_time);
					$restro_close_time_id = $this->restaurantmodel->get_time_slot_id($restro_close_time);
			
					
					if($slot_id >= $restro_open_time_id[0]->slot_id && $slot_id <= $restro_close_time_id[0]->slot_id)
					{
						//print_r($restro_open_time_id );exit;
						$requested_time_slot = $this->wsbookingmodel->get_time_slot($slot_id);
						$start_time_slot = $requested_time_slot[0]->time_slot;

						$time_slots = $this->restaurantmodel->get_available_time_slots_for_restaurant($date_for_slot, $restaurant_id);
						$time_slots = $time_slots['time_slots'];
						$flag = 0;
						foreach ($time_slots as $key => $time_slot) {
							if ($time_slot['slot_id'] == $slot_id) {
								$time_slots[$key]['status'] = SUCCESS;
								$flag = SUCCESS;
							} else {
								$time_slots[$key]['status'] = FAIL;
							}
						}
						if ($flag != SUCCESS) {
							$insert_index = sizeof($time_slots);
							$time_slots[$insert_index]['slot_id'] = $slot_id;
							$time_slots[$insert_index]['time_slot'] = $start_time_slot;
							$time_slots[$insert_index]['status'] = SUCCESS;
						}

						$date_for_table = $date->format('Y-m-d');
						$table_date= $date->format('Y-m-d 00:00:00');
						$date_time = date('Y-m-d H:i:s', strtotime("$date_for_table $start_time_slot"));
						$table_list = $this->restaurantmodel->get_tables($restaurant_id, $table_date, $slot_id, $start_time_slot, $date_time);
						
						$booking_table_list = array();
						$next_four_available_time_slots = array();
						$is_table_list = FAIL;
						if ($table_list['is_table_list'] == 1) {
							$is_table_list = SUCCESS;
							$table_list = $table_list['response']['table_list'];
							foreach ($table_list as $key => $item) {
								$temp_table = array();
								$temp_table['status'] = FAIL;
								$temp_table['table_id'] = $item['table_id'];
								$temp_table['table_name'] = $item['table_name'];
								$temp_table['table_capacity'] = $item['table_capacity'];
								$booking_table_list[] = $temp_table;
							}
						}
						else
						{
							$is_table_list = FAIL;
							$next_four_available_time_slots = $table_list['response']['time_slot'];
						}

						$booked_data = array(
							'booking_id' => FAIL,
							'no_of_guest' => FAIL,
							'booking_date' => $date->format('d-m-Y'),
							'time_slots' => $time_slots,
							'is_table_list' => $is_table_list,
							'booking_table_list' => $booking_table_list,
							'next_four_available_time_slots' => $next_four_available_time_slots,
							'is_notify' => 0,
							'last_minute_from_time' => 0,
							'last_minute_to_time' => 0
						);
						$booked_data['slots_available_for_now'] = SUCCESS;
						
					}
					else
					{
						$booked_data['slots_available_for_now'] = FAIL;
					}
					
					
				}
				else {
                    $this->session->unset_userdata('book_slot_id');
                    $this->session->set_userdata("toast_error_message", "Currently no table available for this restaurant.");
                }
            }
            //Redirected from filter page, top ten restro: add booking : get available table list and time slot list : end
            

			$data = $this->restaurant_details_commom_function($restaurant_id);
            $logged_in_user_id = $this->session->userdata('user_id');
            if ($logged_in_user_id) {
                $data['user_rating'] = $this->restaurantmodel->get_rating_to_restaurant_by_user($restaurant_id, $logged_in_user_id);
            } else {
                $data['user_rating'] = $this->restaurantmodel->get_rating_to_restaurant_by_user($restaurant_id, FAIL);
            }

            $data['bookings'] = $booked_data;
            $this->template_front->view('restaurantsdetail', $data);
        }
		else
		{
			 $this->session->set_userdata("toast_error_message", "Unable to find restaurant. Please try again later.");
             redirect('home');
		}

    }

    public function restaurant_details_commom_function($restaurant_id)
    {
        $country_id = STATIC_COUNTRY_ID;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);

        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $restaurant_details->is_wish = $this->usermodel->is_wish_restaurant($restaurant_id, $user_id);
        } else {
            $restaurant_details->is_wish = 0;
        }

        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }
        // open close status
        $open_close = get_open_close_day($restaurant_id, CLIENT_ZONE);
		
        // reviews
        $reviews = $this->restaurantmodel->get_reviews($restaurant_id);
        // address
        $address = $restaurant_details->address . ", " . $restaurant_details->city . ", " . $restaurant_details->state . ", " . $restaurant_details->country;
        // ratings in percentage
        $rating_criteria = $this->restaurantmodel->get_rating_per_criteria($restaurant_id);
        // view gallery

        $gallery_images = $this->restaurantmodel->get_gallery($restaurant_id);

        $data['service_rating'] = round($rating_criteria->service_rating / 10 * 100);
        $data['ambience_rating'] = round($rating_criteria->ambience_rating / 10 * 100);
        $data['food_rating'] = round($rating_criteria->food_rating / 10 * 100);
        $data['money_rating'] = round($rating_criteria->money_rating / 10 * 100);
        $data['rating_criteria'] = $rating_criteria;
        $aminities_array = explode(",", $restaurant_details->ambience);
		if ($open_close != "Closed") {
            $tz = new DateTimeZone(CLIENT_ZONE);
            $date = new DateTime();
            $date->setTimeZone($tz);
            $time = $date->format('H:i:s');
			//if ($time >= $open_close->open_time_from && $time <= $open_close->close_time_to) {
                $restaurant_details->open_status = "Open Now";
           // } else {
           //     $restaurant_details->open_status = "Closed";
            //}
            $restaurant_details->from_time = $open_close->open_time_from;
            $restaurant_details->to_time = $open_close->close_time_to;
        } else {
            $restaurant_details->open_status = "Closed";
            $restaurant_details->from_time = "";
            $restaurant_details->to_time = "";
        }
		
		$current_date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
		$data['current_date'] = $current_date->format('d-m-Y');

        $menu_images = $this->restaurantmodel->get_menu($restaurant_id);
        $data['restaurant_details'] = $restaurant_details;
        $data['menu_images'] = $menu_images;
        $data['aminities_array'] = $aminities_array;
        $data['gallery_images'] = $gallery_images;
        $data['reviews'] = $reviews;
        $data['address'] = $address;
        $data['states'] = $this->usermodel->get_state_by_country($country_id);
        return $data;
    }

    /*
     * Method Name: restaurant_get_updated_reviews
     * Date: 12-12-2016
     * Purpose: Front after user gave review: update overall rating of restaurant
     * params:
     *      input: - restairant
     *      output: response - Array containing restaurant reviews
     */
    public function restaurant_get_updated_reviews()
    {
        $user_type = SEARCH_RESTAURANT_TYPE;
        $restaurant_id = $this->input->post('restaurant_id');
        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        $ratings = $this->restaurantmodel->get_rating_per_criteria($restaurant_id);
        $data['service_rating'] = round($ratings->service_rating / 10 * 100);
        $data['ambience_rating'] = round($ratings->ambience_rating / 10 * 100);
        $data['food_rating'] = round($ratings->food_rating / 10 * 100);
        $data['money_rating'] = round($ratings->money_rating / 10 * 100);
        $data['rating_criteria'] = $ratings;
        $data['average_rating'] = $restaurant_details->average_rating;
        echo json_encode($data);
    }


    public function search()
    {
        $country_id = STATIC_COUNTRY_ID;
        $data['states'] = $this->usermodel->get_state_by_country($country_id);
        $this->template_front->view('restaurants-filter-listing', $data);
    }


    /*
     * Method Name: restaurants_filter_options
     * Date: 12-12-2016
     * Purpose: Front search filter option - load all the Cuisines, Dietary Preference,Aenities
     * params:
     *      input: -
     *      output: response - Array containing restaurant Cuisines, Dietary Preference, Amenities
     */
    public function restaurants_filter_options()
    {
        $restaurants_filter_options = $this->restaurantmodel->load_restaurants_filter_options();
        echo json_encode($restaurants_filter_options);
    }

    /*
     * Method Name: fetch_restaurants_by_search_filter
     * Date: 12-12-2016
     * Purpose: Fetch restaurants by using search filter options.
     * params:
     *      input: -
     *      output: response - Array containing restaurant Cuisines, Dietary Preference, Amenities
     */
    public function fetch_restaurants_by_search_filter()
    {
        $limit = RESTAURANT_SEARCH_LIMIT;
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $cuisine = $this->input->post('cuisine');
        $dietary_preference = $this->input->post('dietary_preference');
        $ambience = $this->input->post('ambience');
        $location = $this->input->post('location');

        $filter_restaurants_result_count = $this->restaurantmodel->restaurants_filter_search_count($cuisine, $dietary_preference, $ambience, $location, $limit, $offset);

        $restaurant_details = $this->restaurantmodel->restaurants_filter_search($cuisine, $dietary_preference, $ambience, $location, $limit, $offset);
        $filter_restaurants = array();
        foreach ($restaurant_details as $restaurant_detail) {
            $restaurant_table = $this->restauranttablemodel->get_tables($restaurant_detail['restaurant_id']);

            if (empty($restaurant_table)) {
                $restaurant_detail['no_of_active_table'] = FAIL;
                unset($restaurant_detail['time_slots']);
            } else {
                $restaurant_detail['no_of_active_table'] = SUCCESS;
                $date_time_array = $this->restaurantmodel->get_rounded_time();
                $restaurant_detail['time_slots'] = $this->restaurantmodel->get_next_four_available_time_slots($restaurant_detail['restaurant_id'], $date_time_array['date'], $date_time_array['start_time_slot'], $date_time_array['date_time']);
            }

            $filter_restaurants[] = $restaurant_detail;
        }

        $result['result'] = $filter_restaurants;
        $result['offset'] = $offset + $limit;
        $result['filter_restaurants_result_count'] = $filter_restaurants_result_count;
        echo json_encode($result);
    }

    /*
     * Method Name: fetch_restaurants_by_name
     * Date: 06-02-2017
     * Purpose: Fetch restaurants by using restaurant name
     * params:
     *      input: - search name
     *      output: response - Array containing restaurant Cuisines, Dietary Preference, Amenities
     */
    public function fetch_restaurants_by_name()
    {
        $limit = RESTAURANT_SEARCH_LIMIT;
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $search_string = $this->input->post('search_string') ? $this->input->post('search_string') : '';

        $filter_restaurants_result_count = $this->restaurantmodel->restaurants_search_by_name_count($search_string, $limit, $offset);

        $restaurant_details = $this->restaurantmodel->restaurants_search_by_name($search_string, $limit, $offset);
        $filter_restaurants = array();
        foreach ($restaurant_details as $restaurant_detail) {
            $restaurant_table = $this->restauranttablemodel->get_tables($restaurant_detail['restaurant_id']);

            if (empty($restaurant_table)) {
                $restaurant_detail['no_of_active_table'] = FAIL;
                unset($restaurant_detail['time_slots']);
            } else {
                $restaurant_detail['no_of_active_table'] = SUCCESS;
                $date_time_array = $this->restaurantmodel->get_rounded_time();
                $restaurant_detail['time_slots'] = $this->restaurantmodel->get_next_four_available_time_slots($restaurant_detail['restaurant_id'], $date_time_array['date'], $date_time_array['start_time_slot'], $date_time_array['date_time']);
            }

            $filter_restaurants[] = $restaurant_detail;
        }

        $result['result'] = $filter_restaurants;
        $result['offset'] = $offset + $limit;
        $result['filter_restaurants_result_count'] = $filter_restaurants_result_count;
        echo json_encode($result);
    }


    /*
      * Method Name: restaurant_add_to_wish_list
      * Date: 16-12-2016
      * Purpose: Add restaurant to user wish list
      * params:
      *      input: -
      *      output: response - Array containing status and success/error message.
      */
    public function restaurant_add_to_wish_list()
    {
        $restaurant_id = $this->input->post('restaurant_id');
        $result = $this->restaurantmodel->restaurant_add_user_wishlist($restaurant_id);
        echo json_encode($result);
    }

    /*
     * Method Name: restaurant_remove_from_wish_list
     * Date: 16-12-2016
     * Purpose: Remove restaurant to user wish list
     * params:
     *      input: -
     *      output: response - Array containing status and success/error message.
     */
    public function restaurant_remove_from_wish_list()
    {
        $restaurant_id = $this->input->post('restaurant_id');
        $result = $this->restaurantmodel->restaurant_remove_user_wishlist($restaurant_id);
        echo json_encode($result);
    }

    /*
     * Method Name: load_news
     * Date: 12-12-2016
     * Purpose: Fetch news.
     * params:
     *      input: -
     *      output: response - Array containing restaurant Cuisines, Dietary Preference, Amenities
     */

    public function load_news()
    {
        $result = $this->restaurantmodel->load_news();
        echo json_encode($result);
    }

    /*
     * Method Name: load_restaurant_ads
     * Date: 06-02-2017
     * Purpose: Fetch restaurant ads.
     * params:
     *      input: -
     *      output: response - Array containing restaurant Cuisines, Dietary Preference, Amenities
     */

    public function load_restaurant_ads()
    {
        $restaurant_details = shuffle_assoc_array($this->restaurantmodel->get_restuarant_add());
        if (count($restaurant_details) > 6) {
            $restaurant_details = array_slice($restaurant_details, 0, 6);
        }
        $result = $restaurant_details;
        echo json_encode($result);
    }

    /*
      * Method Name: add_review
      * Purpose: To add and update review for restaurant front
      * params:
      *      input: - restaurant_id, user_id and rating param
      *      output: - success and fail
      */
    public function add_review()
    {
        $restaurant_id = $this->input->post('restaurant_id');
        $quality_of_food = $this->input->post('quality_of_food');
        $value_for_money = $this->input->post('value_for_money');
        $service_rating = $this->input->post('service_rating');
        $ambience_rating = $this->input->post('ambience_rating');
        $your_thoughts = $this->input->post('your_thoughts');
        $user_id = $this->input->post('user_id');

        $insert_data = array(
            'customer_id' => $user_id,
            'restaurant_id' => $restaurant_id,
            'service_rating' => $service_rating,
            'ambience_rating' => $ambience_rating,
            'food_quality_rating' => $quality_of_food,
            'value_for_money_rating' => $value_for_money,
            'your_thoughts' => $your_thoughts,
            'status' => '1',
            'is_approved' => '1',
            'date_posted' => date("Y-m-d H:i:s")
        );
        $insert_result = $this->restaurantmodel->submit_review($insert_data);
        if ($insert_result > 0) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = RATING_SUCCESS;
            $result_array['your_thoughts'] = $your_thoughts;
            $result_array['rating_id'] = $insert_result;
            echo json_encode($result_array);
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = RATING_FAILED;
            echo json_encode($result_array);
        }
    }

    /*
      * Method Name: search_top_ten
      * Purpose: To fetch top 10 restro
      * params:
      *      output: - array of top ten restro with details
      */
    public function search_top_ten()
    {
        $country_id = STATIC_COUNTRY_ID;
        $data['states'] = $this->usermodel->get_state_by_country($country_id);

        $logged_in_user_id = $this->session->userdata('user_id');
        if ($logged_in_user_id <= 0) {
            $logged_in_user_id = 0;
        }

        $restaurants = $this->restaurantmodel->get_top10_front($logged_in_user_id);
        $top_ten_restaurants = array();
        foreach ($restaurants as $top_ten_restaurant) {
            $restaurant_table = $this->restauranttablemodel->get_tables($top_ten_restaurant['restaurant_id']);

            if (empty($restaurant_table)) {
                $top_ten_restaurant['no_of_active_table'] = FAIL;
                unset($top_ten_restaurant['time_slots']);
            } else {
                $top_ten_restaurant['no_of_active_table'] = SUCCESS;
                $date_time_array = $this->restaurantmodel->get_rounded_time();
				
                $top_ten_restaurant['time_slots'] = $this->restaurantmodel->get_next_four_available_time_slots($top_ten_restaurant['restaurant_id'], $date_time_array['date'], $date_time_array['start_time_slot'], $date_time_array['date_time']);
				//print_r($top_ten_restaurant['time_slots'] );die;
            }
            if ($logged_in_user_id <= 0) {
                $top_ten_restaurant['is_fav'] = FAIL;
            }
            $top_ten_restaurants[] = $top_ten_restaurant;
        }
        $data['top_ten_restaurants'] = $top_ten_restaurants;
        $this->template_front->view('top-ten-restaurant', $data);
    }
	
	/*
     * Method Name: get_time_slot
     * Purpose: Front: Get time slot which are available for selected date
     * params:
     *      input: date
     *      output: - booking list data for that date
     */
    public function get_time_slot()
    {
        $date = $this->input->get('date');
		$restaurant_id = $this->input->get('restaurant_id');
		
        $date = date('Y-m-d H:i:s', strtotime($date));
        $time_slots = $this->restaurantmodel->get_available_time_slots_for_restaurant($date, $restaurant_id);
		
        if ($time_slots) {
            $time_slots = $time_slots['time_slots'];
            echo json_encode(array('success' => SUCCESS, 'time_slots' => $time_slots));
        } else {
            echo json_encode(array('success' => FAIL, 'message' => NO_TIME_SLOT_TODAY));
        }
    }
	
	/*
    * Method Name: get_table_list
    * Purpose: Get table list or next four available time slots
    * params:
    *      input: time slot id and date
    *      output: - booking list data for that date
    */
    public function get_table_list()
    {
        $restaurant_id = $this->input->get('restaurant_id');
        $date = $this->input->get('date');
        $slot_id = $this->input->get('slot_id');
		
		$start_time_slot = $this->wsbookingmodel->get_time_slot($slot_id);
        $start_time_slot = json_decode(json_encode($start_time_slot), true);
        $start_time_slot = $start_time_slot[0]['time_slot'];
        $date_time = date('Y-m-d H:i:s', strtotime("$date $start_time_slot"));
        $date = date('Y-m-d H:i:s', strtotime("$date"));
		
		$result_array = $this->restaurantmodel->get_tables($restaurant_id, $date, $slot_id, $start_time_slot, $date_time);
        echo json_encode(array('success' => SUCCESS, 'result' => $result_array, 'restaurant_id' => $restaurant_id, 'slot_id' => $slot_id, 'start_time_slot' => $start_time_slot, 'date_time' => $date_time,'date'=>$date));
    }
	
	/*
    * Method Name: front_book_table
    * Purpose: Book table
    * params:
    *      input: 
    *      output: - status
    */
    public function front_book_table()
    {
		$response = array();
        $user_id = $this->session->userdata('user_id');
		$booking_id = $this->input->get('booking_id');
		$restaurant_id = $this->input->get('restaurant_id');
		$no_of_guest = $this->input->get('no_of_guest');
		$booking_date = $this->input->get('booking_date');
		$booking_time = $this->input->get('booking_time');
		$table_ids = $this->input->get('table_ids');
		$is_notify = $this->input->get('is_notify');
		$last_minute_cancellation_from_time = date('H:i:s', strtotime($this->input->get('last_minute_cancellation_from_time')));
		$last_minute_cancellation_to_time = date('H:i:s', strtotime($this->input->get('last_minute_cancellation_to_time')));
		$booking_by = "2";
		
		if($user_id > 0)
		{
			$time_slot = $this->wsbookingmodel->get_time_slot($booking_time);
			$time_slot = json_decode(json_encode($time_slot), true);
			$time_slot = $time_slot[0]['time_slot'];
			$from_time = date('Y-m-d H:i:s', strtotime("$booking_date $time_slot"));
			$to_time = date("Y-m-d H:i:s", strtotime("$booking_date $time_slot +30 minutes"));
			$date = date('Y-m-d H:i:s', strtotime($booking_date));
			
			
			if($booking_id > 0)
			{
				$update_last_minute_data = array();
				$has_requested = $this->wsbookingmodel->has_requested_table($restaurant_id, $date, $booking_time, $table_ids, $booking_id);
				
				if ($has_requested['status'] == 0) {
					$response['status'] = FAIL;
					$response['message'] = 'Sorry for inconvenience, The table you have selected is already booked.';
					echo json_encode($response);die;
				}
				else
				{
					if($is_notify == SUCCESS){
						$update_data = array(
						'booking_from_time' => $from_time,
						'booking_to_time' => $to_time,
						'number_of_guest' => $no_of_guest,
						'booking_status_change_on' => date("Y-m-d H:i:s"),
						'is_notify' => $is_notify,
						'last_minute_from_time' => $last_minute_cancellation_from_time,
						'last_minute_to_time' => $last_minute_cancellation_to_time
						);
					} else {
						$update_data = array(
						'booking_from_time' => $from_time,
						'booking_to_time' => $to_time,
						'number_of_guest' => $no_of_guest,
						'booking_status_change_on' => date("Y-m-d H:i:s"),
						'is_notify' => $is_notify
						);
					}
					

					$update_status = $this->wsbookingmodel->update_booking_request($update_data, $restaurant_id, $table_ids, $date, $booking_time, $booking_id, $update_last_minute_data);
					if ($update_status) {
						$booking_data = $this->homemodel->getData($booking_id);
						$response['status'] = SUCCESS;
						$response['booking_number'] = $booking_data->booking_number;
						$response['message'] = 'Booking updated successfully.';
						echo json_encode($response);die;
					} else {
						$response['status'] = FAIL;
						$response['message'] = 'Sorry for inconvenience. Unable to update booking, Please try again.';
						echo json_encode($response);die;
					}
				}
			}
			else
			{
				$booking_number = rand_string(7);
				$has_requested = $this->wsbookingmodel->has_requested_table($restaurant_id, $date, $booking_time, $table_ids);
				if ($has_requested['status'] == 0) {
					$response['status'] = FAIL;
					$response['message'] = 'Sorry for inconvenience. The table you have selected is already booked.';
					echo json_encode($response);die;
				}
				$insert_last_minute_data = array();

				if($is_notify == SUCCESS){
					$insert_data = array(
						'customer_id' => $user_id,
						'restaurant_id' => $restaurant_id,
						'booking_number' => $booking_number,
						'booking_from_time' => $from_time,
						'booking_to_time' => $to_time,
						'number_of_guest' => $no_of_guest,
						'status' => 3,
						'payment_status' => 0,
						'booking_on' => date("Y-m-d H:i:s"),
						'booking_status_change_on' => date("Y-m-d H:i:s"),
						'booking_by' => $booking_by,
						'is_notify' => $is_notify,
						'last_minute_from_time' => $last_minute_cancellation_from_time,
						'last_minute_to_time' => $last_minute_cancellation_to_time
					);
				}
				else {
					$insert_data = array(
						'customer_id' => $user_id,
						'restaurant_id' => $restaurant_id,
						'booking_number' => $booking_number,
						'booking_from_time' => $from_time,
						'booking_to_time' => $to_time,
						'number_of_guest' => $no_of_guest,
						'status' => 3,
						'payment_status' => 0,
						'booking_on' => date("Y-m-d H:i:s"),
						'booking_status_change_on' => date("Y-m-d H:i:s"),
						'booking_by' => $booking_by,
						'is_notify' => $is_notify
					);
				}
				

				$insert_result = $this->wsbookingmodel->insert_booking_request($insert_data, $table_ids, $date, $booking_time, $insert_last_minute_data);
				if ($insert_result['status'] == 1) {
					$this->session->unset_userdata('book_slot_id');
					$this->session->unset_userdata('modified_booking');
					$response['status'] = SUCCESS;
					$response['message'] = 'Booking created successfully.';
					$response['booking_number'] = $booking_number;
					echo json_encode($response);die;
				} else {
					$response['status'] = FAIL;
					$response['message'] = 'Sorry for inconvenience. Unable to add booking, Please try again.';
					echo json_encode($response);die;
				}
			}					
		}
		else
		{
			$response['status'] = FAIL;
			$response['message'] = 'Please sign in first.';
			echo json_encode($response);die;
		}
    }
	
	

}

?>