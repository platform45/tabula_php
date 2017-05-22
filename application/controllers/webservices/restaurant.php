<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

/*
  Class that contains function for restaurant
 */

class Restaurant extends REST_Controller
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

        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/searchmodel', 'searchmodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('webservices/bookingmodel', 'bookingmodel', TRUE);
        $this->load->model('admin/restaurant_table_model', 'restauranttablemodel', TRUE);
    }

    /*
     * Method Name: View Menu
     * Purpose: To get restaurant menu.
     * params:
     *      input: restaurant_id, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              menu - Array containing restaurant name, address and all the menu images of restaurant
     */
    public function view_menu_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $user_type = '3';
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }
        if ($restaurant_details) {
            $name = $restaurant_details->restaurant_name;
            $address = $restaurant_details->address . ", " . $restaurant_details->city . ", " . $restaurant_details->state . ", " . $restaurant_details->country;
            $menu_images = $this->restaurantmodel->get_menu($restaurant_id);

            if ($menu_images) {
                $menu = array();
                foreach ($menu_images as $images) {
                    $menu[] = $images->menu_image;
                }

                $result_array['status'] = SUCCESS;
                $result_array['message'] = VALID_MENU;
                $result_array['response']['restaurant']['name'] = $name;
                $result_array['response']['restaurant']['address'] = $address;
                $result_array['response']['restaurant']['menu'] = $menu;
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = MENU_NOT_FOUND;
                $this->response($result_array);
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
    * Method Name: restaurant_view_menu_post
    * Purpose: To get restaurant menu.
    * params:
    *      input: restaurant_id, token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              menu - Array containing restaurant name, address and all the menu images of restaurant
    */
    public function restaurant_view_menu_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $user_type = '3';
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }
        if ($restaurant_details) {
            $name = $restaurant_details->restaurant_name;
            $address = $restaurant_details->address . ", " . $restaurant_details->city . ", " . $restaurant_details->state . ", " . $restaurant_details->country;
            $menu_images = $this->restaurantmodel->get_menu($restaurant_id);

            if ($menu_images) {
                $menu = array();
                foreach ($menu_images as $images) {
                    $menu[] = $images;
                }

                $result_array['status'] = SUCCESS;
                $result_array['message'] = VALID_MENU;
                $result_array['response']['restaurant']['name'] = $name;
                $result_array['response']['restaurant']['address'] = $address;
                $result_array['response']['restaurant']['menu'] = $menu;
                $this->response($result_array);
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = MENU_NOT_FOUND;
                $this->response($result_array);
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }


    /*
    * Method Name: View Menu
    * Purpose: To get restaurant menu.
    * params:
    *      input: restaurant_id, token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              menu - Array containing restaurant name, address and all the menu images of restaurant
    */
    public function add_menu_post()
    {
        $title = $this->post('title') ? $this->post('title') : "";
        $restuarant_id = $this->post('restuarant_id') ? $this->post('restuarant_id') : 0;

        // validate input
        if ($restuarant_id < 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // upload promotion image file
        if (!empty($_FILES['menu_image']['name'])) {
            $file_name = $this->strip_junk($_FILES['menu_image']['name']);
            $config['upload_path'] = FOODMENU_IMAGE_PATH;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
//            $config['max_height'] = "2160";
//            $config['max_width'] = "4096";
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('menu_image')) {
                $upload_error = $this->upload->display_errors();
                $result_array['status'] = FAIL;
                $result_array['message'] = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
                $this->response($result_array);
            } else {
                $upload_error = '';
                $upload_data = $this->upload->data();
                $menu_image = $upload_data['file_name'];
            }
        }
        // get last sequence
        // menu array to insert in database
        $sequence = $this->restaurantmodel->get_max_seq();
        $menu_array = array(
            "fm_image" => $menu_image,
            "user_id" => $restuarant_id,
            "menu_image_seq" => $sequence,
            "created_on" => date("Y-m-d H:i:s")
        );
        // insert data in database
        $menu_id = $this->restaurantmodel->add_foodmenu($menu_array);
        // return response
        if ($menu_id) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = ADD_MENU;
            $retArr['menu_id'] = $menu_id;
            $this->response($retArr); // 404 being the HTTP response code
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = UNABLE_ADD_MENU;
            $this->response($retArr); // 404 being the HTTP response code
        }
    }


    /*
     * Method Name: add_gallery
     * Purpose: get details from application to add gallery
     * params:
     *      input: title, description, promotion_image, access Token
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     */
    public function add_gallery_POST()
    {
        $title = $this->post('title') ? $this->post('title') : "";
        $restuarant_id = $this->post('restuarant_id') ? $this->post('restuarant_id') : 0;

        // validate input
        if ($restuarant_id < 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // upload promotion image file
        if (!empty($_FILES['gallery_image']['name'])) {
            $file_name = $this->strip_junk($_FILES['gallery_image']['name']);
            $config['upload_path'] = GALLERY_IMAGE_PATH;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
            $config['max_height'] = "2160";
            $config['max_width'] = "4096";
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('gallery_image')) {
                $upload_error = $this->upload->display_errors();
                $result_array['status'] = FAIL;
                $result_array['message'] = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
                $this->response($result_array);
            } else {
                $upload_error = '';
                $upload_data = $this->upload->data();
                $gallery_image = $upload_data['file_name'];
            }
        }

        // gallery array to insert in database
        $gallery_array = array(
            "gal_title" => $title,
            "gal_image" => $gallery_image,
            "user_id" => $restuarant_id
        );
        // insert data in database
        $gallery_id = $this->restaurantmodel->add_gallery($gallery_array);
        // return response
        if ($gallery_id) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = ADD_GALLERY;
            $retArr['gallery_id'] = $gallery_id;
            $this->response($retArr); // 404 being the HTTP response code
        } else {
            $retArr['status'] = FAIL;
            $retArr['message'] = UNABLE_ADD_GALLERY;
            $this->response($retArr); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name:  view_gallery
     * Purpose: To get restaurant added by restaurant.
     * params:
     *      input: restaurant_id, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              menu - Array containing restaurant name, address and all the menu images of restaurant
     */
    public function view_gallery_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $result_array = array();
        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $gallery_images = $this->restaurantmodel->get_gallery($restaurant_id);
        if ($gallery_images) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = GALLERY_FOUND;
            $result_array['response']['restaurant']['gallery'] = $gallery_images;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_GALLERY_FOUND;
            $this->response($result_array); // 200 being the HTTP response cod
        }

    }

    /*
     * Method Name: Submit Rating
     * Purpose: To post restaurant rating and review.
     * params:
     *      input: user_id, restaurant_id, service_rating, ambience_rating, food_rating, money_rating, review, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     */
    public function submit_rating_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $service_rating = $this->post("service_rating") ? $this->post("service_rating") : "";
        $ambience_rating = $this->post("ambience_rating") ? $this->post("ambience_rating") : "";
        $food_rating = $this->post("food_rating") ? $this->post("food_rating") : "";
        $money_rating = $this->post("money_rating") ? $this->post("money_rating") : "";
        $your_thoughts = $this->post("your_thoughts") ? $this->post("your_thoughts") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Validation so that user has selected at least one of four rating or a review
        if ($service_rating == "" && $ambience_rating == "" && $food_rating == "" && $money_rating == "") {

            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_RATING_INPUT;
            $this->response($result_array);
        }

        // Check if restaurant is valid and valid user is rating it.
        $is_valid_user = $this->usermodel->is_valid_user($user_id, 2);
        $is_valid_restaurant = $this->usermodel->is_valid_user($restaurant_id, 3);

        if ($is_valid_user && $is_valid_restaurant) {
            $invalid_rating = 0;

            if ($service_rating != "" && $service_rating < 1 && $service_rating > 10)
                $invalid_rating = 1;
            if ($ambience_rating != "" && $ambience_rating < 1 && $ambience_rating > 10)
                $invalid_rating = 1;
            if ($food_rating != "" && $food_rating < 1 && $food_rating > 10)
                $invalid_rating = 1;
            if ($money_rating != "" && $money_rating < 1 && $money_rating > 10)
                $invalid_rating = 1;

            if ($invalid_rating == 1) {
                $result_array['status'] = FAIL;
                $result_array['message'] = INVALID_RATING;
                $this->response($result_array); // 404 being the HTTP response code
            } else {
                $insert_data = array(
                    'customer_id' => $user_id,
                    'restaurant_id' => $restaurant_id,
                    'service_rating' => $service_rating,
                    'ambience_rating' => $ambience_rating,
                    'food_quality_rating' => $food_rating,
                    'value_for_money_rating' => $money_rating,
                    'your_thoughts' => $your_thoughts,
                    'status' => '1',
                    'is_approved' => '1',
                    'date_posted' => date("Y-m-d H:i:s")
                );
                $insert_result = $this->restaurantmodel->submit_review($insert_data);
                if ($insert_result > 0) {
                    $result_array['status'] = SUCCESS;
                    $result_array['message'] = RATING_SUCCESS;

                    $this->response($result_array); // 200 being the HTTP response code
                } else {
                    $result_array['status'] = FAIL;
                    $result_array['message'] = RATING_FAILED;
                    $this->response($result_array); // 404 being the HTTP response code
                }
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }


    /*
     * Method Name: Home Rating
     * Purpose: To get restaurant rating to display on restaurant home page.
     * params:
     *      input: restaurant_id, criteria ( 1 - service, 2 - ambience, 3 - food, 4 - money ), offset, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              average_rating - service wise rating
     *              total_record - total number of records
     *              offset - offset for pagination
     *              total average rating - average rating
     *              user_rating - user wise rating
     */
    public function home_rating_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $criteria = $this->post("criteria") ? $this->post("criteria") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $limit = SEARCH_RESULTS_LIMIT;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0 && $criteria == 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Check if restaurant is valid
        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }
        if ($restaurant_details) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_RATING;

            if ($offset == 0 && $criteria == 0) {
                $rating_criteria = $this->restaurantmodel->get_rating_per_criteria($restaurant_id);
                $total_average_rating = $restaurant_details->average_rating;

                $result_array['response']['restaurant']['average_rating'] = $rating_criteria;
                $result_array['response']['restaurant']['total_average_rating'] = $total_average_rating;
            } else {
                $result_array['response']['restaurant']['average_rating'] = '';
                $result_array['response']['restaurant']['total_average_rating'] = '';
            }

            if ($criteria == 1)
                $criteria = 'r.service_rating';
            else if ($criteria == 2)
                $criteria = 'r.ambience_rating';
            else if ($criteria == 3)
                $criteria = 'r.food_quality_rating';
            else if ($criteria == 4)
                $criteria = 'r.value_for_money_rating';
            else
                $criteria = '';

            $total_user_ratings = $this->restaurantmodel->get_total_ratings($restaurant_id, $criteria);
            $user_rating = $this->restaurantmodel->get_rating_per_user($restaurant_id, $criteria, $limit, $offset);
            $offset = $offset + $limit;
            $result_array['response']['restaurant']['total_record'] = $total_user_ratings;
            $result_array['response']['restaurant']['offset'] = $offset;
            $result_array['response']['restaurant']['user_rating'] = $user_rating;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }


    /*
     * Method Name: get_ratings
     * Purpose: To get restaurant rating to display on user_profile.
     * params:
     *      input: restaurant_id, criteria ( 1 - service, 2 - ambience, 3 - food, 4 - money ), offset, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              average_rating - service wise rating
     *              total_record - total number of records
     *              offset - offset for pagination
     *              total average rating - average rating
     *              user_rating - user wise rating
     */
    public function get_ratings_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $criteria = $this->post("criteria") ? $this->post("criteria") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $user_type = SEARCH_APP_USER_TYPE;
        $limit = SEARCH_RESULTS_LIMIT;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Check if user is valid
        $user_details = $this->usermodel->get_user_details($user_id, $user_type);
        if ($user_details) {
            $total_user_ratings = $this->restaurantmodel->get_rating_user_wise_total($user_id);
            $user_rating = $this->restaurantmodel->get_rating_user_wise($user_id, $limit, $offset);
            $offset = $offset + $limit;

            if ($user_rating) {
                $result_array['status'] = SUCCESS;
                $result_array['message'] = VALID_RATING;
                $result_array['response']['restaurant']['total_record'] = $total_user_ratings;
                $result_array['response']['restaurant']['offset'] = $offset;
                $result_array['response']['restaurant']['user_rating'] = $user_rating;
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = RATING_NOT_FOUND;
                $this->response($result_array); // 200 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }


    /*
     * Method Name: get_ratings_details
     * Purpose: To get restaurant rating to display on user_profile.
     * params:
     *      input: restaurant_id, criteria ( 1 - service, 2 - ambience, 3 - food, 4 - money ), offset, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              average_rating - service wise rating
     *              total_record - total number of records
     *              offset - offset for pagination
     *              total average rating - average rating
     *              user_rating - user wise rating
     */
    public function get_ratings_details_post()
    {
        $rating_id = $this->post("rating_id") ? $this->post("rating_id") : 0;
        $user_type = SEARCH_APP_USER_TYPE;
        $limit = SEARCH_RESULTS_LIMIT;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($rating_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
        $user_rating = $this->restaurantmodel->get_rating_details($rating_id);
        $result_array['status'] = SUCCESS;
        $result_array['message'] = VALID_RATING;
        $result_array['response']['restaurant']['user_rating'] = $user_rating;
        $this->response($result_array); // 200 being the HTTP response code


    }

    /*
     * Method Name: Table list
     * Purpose: To get list of tables for a restaurant and table not available send the next 4 free timeslot.
     * params:
     *      input: restaurant_id, token, date, time
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array containing table name, id of restaurant or next four available time slot
     */
    public function table_list_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $date = $this->post("date") ? $this->post("date") : 0;
        $time_slot_id = $this->post("time") ? $this->post("time") : 0;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
        if ($date == 0 || $time_slot_id == 0 || $time_slot_id >= 48) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $is_valid_restaurant = $this->usermodel->is_valid_user($restaurant_id, $user_type);
        if ($is_valid_restaurant) {
            $is_table_available = $this->restauranttablemodel->get_tables($restaurant_id);
            if ($is_table_available) {
                $start_time_slot = $this->bookingmodel->get_time_slot($time_slot_id);
                $start_time_slot = json_decode(json_encode($start_time_slot), true);
                $start_time_slot = $start_time_slot[0]['time_slot'];
                $date_time = date('Y-m-d H:i:s', strtotime("$date $start_time_slot"));
                $date = date('Y-m-d H:i:s', strtotime("$date"));

                $result_array = $this->restaurantmodel->get_tables($restaurant_id, $date, $time_slot_id, $start_time_slot, $date_time);
                $this->response($result_array);
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = NO_TABLES;
                $this->response($result_array);
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: Operating hours
     * Purpose: To get operating hours of a restaurant.
     * params:
     *      input: restaurant_id, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array containing restaurant name, address, contact number, operating days and operating hours
     */
    public function operating_hours_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $user_type = SEARCH_RESTAURANT_TYPE;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }

        if ($restaurant_details) {
            $name = $restaurant_details->restaurant_name;
            $address = $restaurant_details->address . ", " . $restaurant_details->city . ", " . $restaurant_details->state . ", " . $restaurant_details->country;
            $contact_number = $restaurant_details->user_contact;
            $operating_days = OPERATING_DAYS;
            $operating_hours = OPERATING_HOURS;

            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_HOURS;
            $result_array['response']['restaurant']['name'] = $name;
            $result_array['response']['restaurant']['address'] = $address;
            $result_array['response']['restaurant']['contact_number'] = $contact_number;
            $result_array['response']['restaurant']['operating_days'] = $operating_days;
            $result_array['response']['restaurant']['operating_hours'] = $operating_hours;

            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
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
    public function get_restaurant_detail_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $client_zone = $this->post("client_zone") ? $this->post("client_zone") : 0;
        $date = new DateTime();
        $user_type = SEARCH_RESTAURANT_TYPE;


        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }

        if ($restaurant_details) {
            $name = $restaurant_details->restaurant_name;
            $address = $restaurant_details->address . ", " . $restaurant_details->city . ", " . $restaurant_details->state . ", " . $restaurant_details->country;
            $restaurant_details->address = $address;
            $operating_time = $this->searchmodel->get_operating_time($restaurant_details->user_id);
            $reviews = $this->restaurantmodel->get_reviews($restaurant_id);
            $cnt = 1;
            $days = $this->config->item("day_array");


            if(!empty($restaurant_details->ambience))
            {
                $aminities_array = explode(",", $restaurant_details->ambience);
            }
            else
            {
                $aminities_array = array();
            }

            $rating_criteria = $this->restaurantmodel->get_rating_per_criteria($restaurant_id);
            $restaurant_details->share_url = base_url();
            $restaurant_details->is_fav = check_added_to_wishlist($user_id, $restaurant_id);
            $open_close = get_open_close_day($restaurant_id, $client_zone);

            if ($open_close != "Closed") {
                $tz = new DateTimeZone($client_zone);
                $date->setTimeZone($tz);
                $time = $date->format('H:i:s');
                if (@$open_close->open_status != "Closed" && $open_close->open_time_from == $open_close->close_time_to) {
                    $restaurant_details->open_status = "Open Now";
                } else if ($time >= $open_close->open_time_from && $time <= $open_close->close_time_to) {
                    $restaurant_details->open_status = "Open Now";
                } else {
                    $restaurant_details->open_status = "Closed";
                }
                $restaurant_details->from_time = $open_close->open_time_from;
                $restaurant_details->to_time = $open_close->close_time_to;
            } else {
                $restaurant_details->open_status = "Closed";
                $restaurant_details->from_time = "";
                $restaurant_details->to_time = "";
            }

            $result_array['status'] = SUCCESS;
            $result_array['message'] = SEARCH_SUCCESS;

            $restaurant_table = $this->restauranttablemodel->get_tables($restaurant_id);
            if (empty($restaurant_table)) {
                $result_array['no_of_active_table'] = FAIL;
            } else {
                $result_array['no_of_active_table'] = SUCCESS;
            }
            $result_array['response']['restaurant']['restaurant_details'] = $restaurant_details;
            $result_array['response']['restaurant']['aminities'] = $aminities_array;
            $result_array['response']['restaurant']['reviews'] = $reviews;
            $result_array['response']['restaurant']['rating_criteria_wise'] = $rating_criteria;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = SEARCH_FAILED;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: get_rating_reviews
     * Purpose: To get rating and reviews of a restaurant.
     * params:
     *      input: restaurant_id, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array containing restaurant name, address, user details who rated or posted review
     */
    public function get_rating_reviews_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $limit = SEARCH_RESULTS_LIMIT;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }

        if ($restaurant_details) {
            $name = $restaurant_details->restaurant_name;
            $address = $restaurant_details->address . ", " . $restaurant_details->city . ", " . $restaurant_details->state . ", " . $restaurant_details->country;

            $total_user_ratings = $this->restaurantmodel->get_total_ratings($restaurant_id);
            $user_rating = $this->restaurantmodel->get_rating_per_user($restaurant_id, '', $limit, $offset);
            $offset = $offset + $limit;

            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_RATING;
            $result_array['response']['restaurant']['name'] = $name;
            $result_array['response']['restaurant']['address'] = $address;
            $result_array['response']['restaurant']['total_record'] = $total_user_ratings;
            $result_array['response']['restaurant']['offset'] = $offset;
            $result_array['response']['restaurant']['user_rating'] = $user_rating;

            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: wishlist_change_status
     * Purpose: To change whishlist status of restuarant.
     * params:
     *      input: restaurant_id, token, user_id, status
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array containing restaurant name, address, user details who rated or posted review
     */
    public function wishlist_change_status_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $status = $this->post("status") ? $this->post("status") : 0;


        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
        $user_type = 3;
        $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, $user_type);
        if (!$restaurant_details->user_id) {
            $restaurant_details->user_id = $restaurant_id;
        }

        if ($restaurant_details) {
            $name = $restaurant_details->restaurant_name;
            $address = $restaurant_details->address . ", " . $restaurant_details->city . ", " . $restaurant_details->state . ", " . $restaurant_details->country;

            $restaurant_details = $this->restaurantmodel->update_wishlist($user_id, $restaurant_id, $status);


            $result_array['status'] = SUCCESS;
            if ($status == 0)
                $result_array['message'] = WISHLIST_STATUS_REMOVED;
            else
                $result_array['message'] = WISHLIST_STATUS_ADDED;
            $result_array['response']['restaurant']['name'] = $name;
            $result_array['response']['restaurant']['address'] = $address;
            $result_array['response']['restaurant']['restuarant_id'] = $restaurant_id;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: get_top_ten
     * Purpose: To get top ten restuarant.
     * params:
     *      input:  token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array containing restaurant details
     */
    public function get_top_ten_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $restaurant_details = $this->restaurantmodel->get_top10();
        if ($restaurant_details) {
            foreach ($restaurant_details as $aVal) {

                $aResultRes['restaurant_id'] = $aVal['restaurant_id'];
                $aResultRes['restaurant_name'] = $aVal['restaurant_name'];
                $aResultRes['restaurant_owner_name'] = $aVal['restaurant_owner_name'];
                $aResultRes['email'] = $aVal['email'];
                $aResultRes['restaurant_image'] = $aVal['restaurant_image'];
                $aResultRes['average_rating'] = $aVal['average_rating'];
                $aResultRes['average_review'] = $aVal['average_review'];
                $aResultRes['contact_number'] = $aVal['contact_number'];
                $aResultRes['address'] = $aVal['address'];
                $aResultRes['average_spend'] = $aVal['average_spend'];
                $aResultRes['description'] = $aVal['description'];
                $aResultRes['share_url'] = base_url();
                $aResultRes['is_fav'] = check_added_to_wishlist($user_id, $aVal['restaurant_id']);
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
            $result_array['message'] = TOP_10_RESTAURANTS;
            $result_array['current_date'] = $date->format('d-m-Y');
            $result_array['response']['search_results'] = $aResultRestraunant;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = RESTAURANT_NOT_FOUND;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
    * Method Name:  get_ads_listing
    * Purpose: To get top ten restuarant.
    * params:
    *      input:  token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              response - Array containing restaurant details
    */
    public function get_ads_listing_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $restaurant_details = $this->restaurantmodel->get_restuarant_add();
        if ($restaurant_details) {
            foreach ($restaurant_details as $aVal) {
                $aResultRes['restaurant_id'] = $aVal['restaurant_id'];
                $aResultRes['restaurant_name'] = $aVal['restaurant_name'];
                $aResultRes['restaurant_owner_name'] = $aVal['restaurant_owner_name'];
                $aResultRes['email'] = $aVal['email'];
                $aResultRes['restaurant_image'] = $aVal['restaurant_image'];
                $aResultRes['average_rating'] = $aVal['average_rating'];
                $aResultRes['average_review'] = $aVal['average_review'];
                $aResultRes['contact_number'] = $aVal['contact_number'];
                $aResultRes['address'] = $aVal['address'];
                $aResultRes['average_spend'] = $aVal['average_spend'];
                $aResultRes['description'] = $aVal['description'];
                $aResultRes['share_url'] = base_url();
                $aResultRes['is_fav'] = check_added_to_wishlist($user_id, $aVal['restaurant_id']);
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
            $result_array['message'] = ALL_ADS;
            $result_array['current_date'] = $date->format('d-m-Y');
            $result_array['response']['search_results'] = $aResultRestraunant;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_ADS;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }


    /*
    * Method Name:  get_wishlist_post
    * Purpose: To get wish list
    * params:
    *      input:  token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              response - Array containing restaurant details
    */
    public function get_wishlist_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $limit = SEARCH_RESULTS_LIMIT;
        $restaurant_details = $this->restaurantmodel->get_wishlist($user_id, $limit, $offset);
        if ($restaurant_details) {
            foreach ($restaurant_details as $aVal) {
                $aResultRes['restaurant_id'] = $aVal['restaurant_id'];
                $aResultRes['restaurant_name'] = $aVal['restaurant_name'];
                $aResultRes['restaurant_owner_name'] = $aVal['restaurant_owner_name'];
                $aResultRes['email'] = $aVal['email'];
                $aResultRes['restaurant_image'] = $aVal['restaurant_image'];
                $aResultRes['average_rating'] = $aVal['average_rating'];
                $aResultRes['average_review'] = $aVal['average_review'];
                $aResultRes['contact_number'] = $aVal['contact_number'];
                $aResultRes['address'] = $aVal['address'];
                $aResultRes['average_spend'] = $aVal['average_spend'];
                $aResultRes['description'] = $aVal['description'];
                $aResultRes['share_url'] = base_url();
                $aResultRes['is_fav'] = check_added_to_wishlist($user_id, $aVal['restaurant_id']);
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
            $result_array['message'] = WISHLIST_FOUND;
            $result_array['current_date'] = $date->format('d-m-Y');
            $result_array['count'] = $this->restaurantmodel->get_wishlist_count($user_id);
            $result_array['offset'] = $offset + $limit;
            $result_array['response']['search_results'] = $aResultRestraunant;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = WISHLIST_NOT_FOUND;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: strip_junk
     * Purpose: Get strip junk funtion
     * params:
     *      input: raw string
     *      output: string after removing junk
     *
     */

    function strip_junk($string)
    {
        $string = str_replace(" ", "-", trim($string));
        $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
        $string = strtolower($string);
        return $string;
    }

    /*
     * Auhor: Akshay Deshmukh
     * Method Name: delete_restaurant_menu
     * Purpose: To delete restaurant menu.
     * params:
     *      input: restaurant_id, token, menu_id
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     */
    public function delete_restaurant_menu_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $menu_id = $this->post("menu_id") ? $this->post("menu_id") : 0;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0 || $menu_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $is_valid_restaurant = $this->usermodel->is_valid_user($restaurant_id, $user_type);
        if ($is_valid_restaurant) {
            $delete_status = $this->restaurantmodel->delete_restaurant_menu($restaurant_id, $menu_id);
            if ($delete_status) {
                $result_array['status'] = SUCCESS;
                $result_array['message'] = DELETE_MENU_SUCCESS;
                $this->response($result_array);
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = DELETE_MENU_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Auhor: Akshay Deshmukh
     * Method Name: delete_restaurant_image_gallery_post
     * Purpose: To delete restaurant gallery image.
     * params:
     *      input: restaurant_id, token, image_id
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     */
    public function delete_restaurant_image_gallery_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $image_id = $this->post("image_id") ? $this->post("image_id") : 0;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $result_array = array();

        if ($restaurant_id <= 0 || $image_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $is_valid_restaurant = $this->usermodel->is_valid_user($restaurant_id, $user_type);
        if ($is_valid_restaurant) {
            $delete_status = $this->restaurantmodel->delete_restaurant_gallery_image($restaurant_id, $image_id);
            if ($delete_status) {
                $result_array['status'] = SUCCESS;
                $result_array['message'] = DELETE_GALLERY_IMAGE_SUCCESS;
                $this->response($result_array);
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = DELETE_GALLERY_IMAGE_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

}

/* End of file restaurant.php */
/* Location: ./application/controllers/webservices/restaurant.php */