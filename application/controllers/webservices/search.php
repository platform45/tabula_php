<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

/*
  Class that contains function for search
 */

class Search extends REST_Controller
{

    // Constructor
    function __construct()
    {
        parent::__construct();

        // Validate token
        $api_key = ($this->get('token')) ? $this->get('token') : $this->post('token');

        if ($api_key) {
            $api_status = validate_api_key($api_key);
            $api_status = json_decode($api_status);

            if ($api_status->status != SUCCESS) {
                echo json_encode(array("status" => $api_status->status, "message" => $api_status->message));
                die;
            }
        } else {
            echo json_encode(array("status" => FAIL, "message" => NO_TOKEN_MESSAGE));
            die;
        }

        $this->load->model('webservices/searchmodel', 'searchmodel', TRUE);
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('admin/restaurant_table_model', 'restauranttablemodel', TRUE);
    }

    /*
     * Method Name: restaurant_search
     * Purpose: Search Restaurant by keyword or location
     * params:
     *      input: Search Keyword, offset, latitude, longitude, distance Token,Type: 1 for GPS 0 for search
     *      output: status - FAIL / SUCCESS
     *              message - The reason if search fails / Success message
     *              response - Array containing all the search results.
     * 							offset - Offset Value to get Next page record
     * 	Input JSON Request type {"token":"ihW7eD4XSLqtwkxA","keyword":"xyz","offset":"0" }
     *
     */

    public function restaurant_search_post()
    {
        $keyword = $this->post("keyword") ? $this->post("keyword") : "";
        $user_id = $this->post("user_id") ? $this->post("user_id") : "";
        $distance = $this->post("distance") ? $this->post("distance") : 0;
        $latitude = $this->post("latitude") ? $this->post("latitude") : 0;
        $longitude = $this->post("longitude") ? $this->post("longitude") : 0;
        $type = $this->post("type") ? $this->post("type") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;

        $limit = SEARCH_RESULTS_LIMIT;
        $user_type = SEARCH_RESTAURANT_TYPE;

        $result_array = array();


        //Get total count of search result
        $total_records = $this->searchmodel->get_total_search_records($keyword, $distance, $latitude, $longitude, $user_type);

        if ($total_records > 0) {
            //Result array
            $search_results = $this->searchmodel->get_search_records($keyword, $distance, $latitude, $longitude, $user_type, $limit, $offset, $type);
            $offset = $type == 0 ? $offset + $limit : 0;
            if ($search_results) {
                foreach ($search_results as $aVal) {
                    $aResultRes['restaurant_id'] = $aVal['restaurant_id'];
                    $aResultRes['restaurant_name'] = $aVal['restaurant_name'];
                    $aResultRes['restaurant_owner_name'] = $aVal['restaurant_owner_name'];
                    $aResultRes['email'] = $aVal['email'];
                    $aResultRes['restaurant_image'] = $aVal['restaurant_image'];
                    $aResultRes['average_rating'] = $aVal['average_rating'];
                    $aResultRes['average_review'] = $aVal['average_review'];
                    $aResultRes['contact_number'] = $aVal['contact_number'];
                    $aResultRes['latitude'] = $aVal['latitude'];
                    $aResultRes['longitude'] = $aVal['longitude'];
                    $aResultRes['address'] = $aVal['address'];
                    $aResultRes['average_spend'] = $aVal['average_spend'];
                    $aResultRes['description'] = $aVal['description'];
                    $aResultRes['share_url'] = base_url();
                    $aResultRes['is_fav'] = check_added_to_wishlist($user_id, $aVal['restaurant_id']);
                    $operating_time = $this->searchmodel->get_operating_time($aVal['restaurant_id']);
                    $aResultRes['cuisine'] = $aVal['cuisine'];

                    $restaurant_table = $this->restauranttablemodel->get_tables($aVal['restaurant_id']);
                    if (empty($restaurant_table)) {
                        $aResultRes['no_of_active_table'] = FAIL;
                        unset($aResultRes['time_slots']);
                    } else {
                        $aResultRes['no_of_active_table'] = SUCCESS;
                        $date_time_array = $this->restaurantmodel->get_rounded_time();
                        $aResultRes['time_slots'] = $this->restaurantmodel->get_next_four_available_time_slots($aVal['restaurant_id'], $date_time_array['date'], $date_time_array['start_time_slot'], $date_time_array['date_time']);
                    }

                    $aResultRestraunant[] = $aResultRes;
                }

                $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
                $result_array['status'] = SUCCESS;
                $result_array['message'] = SEARCH_SUCCESS;
                $result_array['current_date'] = $date->format('d-m-Y');
                $result_array['response']['total_record'] = $total_records;
                $result_array['response']['offset'] = $offset;
                $result_array['response']['search_results'] = $aResultRestraunant;
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = SEARCH_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = SEARCH_FAILED;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: restaurant_detail_search
     * Purpose: Search Restaurant by details provided
     * params:
     *      input: country, state, city, cuisine, ambience, min_price, max_price, number_of_guest, date_time, latitude, longitude, offset, token
     *      output: status - FAIL / SUCCESS
     *              message - The reason if search fails / Success message
     *              response - Array containing all the search results.
     * 							offset - Offset Value to get Next page record
     *
     */

    public function restaurant_detail_search_post()
    {
        $state = $this->post("state") ? $this->post("state") : 0;
        $city = $this->post("city") ? $this->post("city") : 0;
        $cuisine = $this->post("cuisine") ? $this->post("cuisine") : "";
        $ambience = $this->post("ambience") ? $this->post("ambience") : "";
        $max_price = $this->post("max_price") ? $this->post("max_price") : 0;
        $min_price = $this->post("min_price") ? $this->post("min_price") : 0;
        $dietary_preference = $this->post("dietary_preference") ? $this->post("dietary_preference") : 0;
        $number_of_guest = $this->post("number_of_guest") ? $this->post("number_of_guest") : 0;
        $date_time = $this->post("date_time") ? trim($this->post("date_time")) : "";
        $latitude = $this->post("latitude") ? $this->post("latitude") : 0;
        $longitude = $this->post("longitude") ? $this->post("longitude") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $user_id = $this->post("user_id") ? $this->post("user_id") : "";

        $limit = SEARCH_RESULTS_LIMIT;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($state <= 0 && $city <= 0 && empty($cuisine) && empty($ambience) && $min_price <= 0 && $max_price <= 0 && $number_of_guest <= 0 && empty($date_time) && $offset <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_SEARCH_INPUT;
            $this->response($result_array);
        }


        $state_exists = $this->usermodel->check_state_exists($state);
        if (!$state_exists) {
            $result_array['status'] = FAIL;
            $result_array['message'] = STATE_NOT_EXISTS;
            $this->response($result_array);
        }

        $city_exists = $this->usermodel->check_city_exists($city);
        if (!$city_exists) {
            $result_array['status'] = FAIL;
            $result_array['message'] = CITY_NOT_EXISTS;
            $this->response($result_array);
        }

        //Get total count of search result
        $total_records = $this->searchmodel->get_total_detail_search_records($state, $city, $cuisine, $ambience, $min_price, $max_price, $number_of_guest, $date_time, $user_type, $dietary_preference);

        if ($total_records > 0) {
            //Result array
            $search_results = $this->searchmodel->get_detail_search_records($state, $city, $cuisine, $ambience, $min_price, $max_price, $number_of_guest, $date_time, $latitude, $longitude, $user_type, $dietary_preference, $limit, $offset);

            if ($search_results) {

                $offset = $offset + $limit;
                foreach ($search_results as $aVal) {
                    $aResultRes['restaurant_id'] = $aVal['restaurant_id'];
                    $aResultRes['restaurant_name'] = $aVal['restaurant_name'];
                    $aResultRes['restaurant_owner_name'] = $aVal['restaurant_owner_name'];
                    $aResultRes['email'] = $aVal['email'];
                    $aResultRes['restaurant_image'] = $aVal['restaurant_image'];
                    $aResultRes['average_rating'] = $aVal['average_rating'];
                    $aResultRes['average_review'] = $aVal['average_review'];
                    $aResultRes['latitude'] = $aVal['latitude'];
                    $aResultRes['longitude'] = $aVal['longitude'];
                    $aResultRes['contact_number'] = $aVal['contact_number'];
                    $aResultRes['address'] = $aVal['address'];
                    $aResultRes['average_spend'] = $aVal['average_spend'];
                    $aResultRes['description'] = $aVal['description'];
                    $aResultRes['share_url'] = base_url();
                    $aResultRes['is_fav'] = check_added_to_wishlist($user_id, $aVal['restaurant_id']);
                    $operating_time = $this->searchmodel->get_operating_time($aVal['restaurant_id']);

                    $restaurant_table = $this->restauranttablemodel->get_tables($aVal['restaurant_id']);
                    if (empty($restaurant_table)) {
                        $aResultRes['no_of_active_table'] = FAIL;
                        unset($aResultRes['time_slots']);
                    } else {
                        $aResultRes['no_of_active_table'] = SUCCESS;
                        $date_time_array = $this->restaurantmodel->get_rounded_time();
                        $aResultRes['time_slots'] = $this->restaurantmodel->get_next_four_available_time_slots($aVal['restaurant_id'], $date_time_array['date'], $date_time_array['start_time_slot'], $date_time_array['date_time']);
                    }

                    $aResultRestraunant[] = $aResultRes;
                }

                $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
                $result_array['status'] = SUCCESS;
                $result_array['message'] = SEARCH_SUCCESS;
                $result_array['current_date'] = $date->format('d-m-Y');
                $result_array['response']['total_record'] = $total_records;
                $result_array['response']['offset'] = $offset;
                $result_array['response']['search_results'] = $aResultRestraunant;
                $this->response($result_array); // 200 being the HTTP response code
            }
            {

                $result_array['status'] = FAIL;
                $result_array['message'] = SEARCH_FAILED;
                $this->response($result_array);
            }

        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = SEARCH_FAILED;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

}

/* End of file search.php */
/* Location: ./application/controllers/webservices/search.php */