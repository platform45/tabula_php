<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

/*
  Class that contains function for user profile
 */

class Profile extends REST_Controller
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
        $this->load->model('webservices/searchmodel', 'searchmodel', TRUE); // loaded for  getting time slots
        $this->load->model('webservices/bookingmodel', 'bookingmodel', TRUE);
    }

    /*
     * Method Name: View
     * Purpose: To get user profile details.
     * params:
     *      input: user_id, user_type, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              userDetails - Array containing all the profile details for logged in user
     */
    public function view_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $user_type = $this->post("user_type") ? $this->post("user_type") : 0;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $user_type <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        if ($user_type == 2) //App user
        {
            $this->_view_app_user_profile($user_id, $user_type);
        } else if ($user_type == 3) //Restaurant user
        {
            $this->_view_restaurant_user_profile($user_id, $user_type);
        } else // Invalid user type
        {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
    }

    /*
     * Method Name: view_app_user_profile
     * Purpose: To get app user profile details.
     * params:
     *      input: user_id, user_type
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              userDetails - Array containing all the profile details
     */
    private function _view_app_user_profile($user_id, $user_type)
    {
        $user_details = $this->usermodel->get_user_details($user_id, $user_type);
        if ($user_details) {
            // $user_details->notification_flag = "1";
            $booking_type = CONFIRMED_BOOKING;
            $user_details->total_booking = $this->bookingmodel->get_total_booking_records($user_id, $booking_type, $booking_id = 0);
            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_USER;
            $result_array['response']['user_details'] = $user_details;

            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: _view_restaurant_user_profile
     * Purpose: To get restaurant user profile details.
     * params:
     *      input: user_id, user_type
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              userDetails - Array containing all the profile details
     */
    private function _view_restaurant_user_profile($user_id, $user_type)
    {
        $user_details = $this->usermodel->get_restaurant_user_details($user_id, $user_type);

        if ($user_details) {
            if(!$user_details->user_id)
            {
                $user_details->user_id = $user_id;
            }
            $operating_time = $this->searchmodel->get_operating_time($user_id);
            $cnt = 1;
            $days = $this->config->item("day_array");
            if ($operating_time) {
                foreach ($operating_time as $aKey => $aVal) {
                    $time = date('H:i', strtotime("$aVal->open_time_from")) . " - " . date('H:i', strtotime("$aVal->close_time_to"));
                    $aResTime[$days[$cnt]] = array("is_open" => $aVal->open_close_status, "open_close_time" => $time);
                    $cnt++;
                }
            }
            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_USER;
            $result_array['response']['user_details'] = $user_details;
            $result_array['response']['operating_time'] = $aResTime;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = RESTAURANT_NOT_FOUND;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Create by: Akshay Deshmukh
     * On: 22-12-2016
     * Method Name: restaurant_edit_user_profile_post
     * Purpose: To get restaurant user profile details with ambience,dietary,cuisines .
     * params:
     *      input: user_id, user_type
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              userDetails - Array containing all the profile details with ambience,dietary,cuisines
     */
    public function restaurant_edit_user_profile_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $user_type = $this->post("user_type") ? $this->post("user_type") : 0;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $user_type <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        if ($user_type == 3) //Restaurant user
        {
            $user_details = $this->usermodel->get_restaurant_user_details($user_id, $user_type);
            if(!$user_details->user_id)
            {
                $user_details->user_id = $user_id;
            }

            $operating_time = $this->searchmodel->get_operating_time($user_id);
            $ambience = $this->usermodel->get_ambience_by_restaurant_id($user_id);
            $dietary = $this->usermodel->get_dietary_preference_by_restaurant_id($user_id);
            $cuisines = $this->usermodel->get_cuisines_by_restaurant_id($user_id);

            $cnt = 1;
            $days = $this->config->item("day_array");
            if ($operating_time) {
                foreach ($operating_time as $aKey => $aVal) {
                    $time = $aVal->open_time_from . " to " . $aVal->close_time_to;
                    $aResTime[$days[$cnt]] = array("is_open" => $aVal->open_close_status, "open_close_time" => $time);
                    $cnt++;
                }
            }
            if ($user_details) {
                $result_array['status'] = SUCCESS;
                $result_array['message'] = VALID_USER;
                $result_array['response']['user_details'] = $user_details;
                $result_array['response']['ambience'] = $ambience;
                $result_array['response']['cuisines'] = $cuisines;
                $result_array['response']['dietary'] = $dietary;
                $result_array['response']['operating_time'] = $aResTime;
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = INVALID_USER;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else // Invalid user type
        {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
    }

    /*
     * Method Name: Contact list
     * Purpose: To get contact list.
     * params:
     *      input: json object with name and email, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              userDetails - Array containing all the contacts with name, email, profile image and flag indicating if user is a Halozi app user or no
     */
    public function contact_list_post()
    {
        $user_list = $this->post("user_list") ? $this->post("user_list") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if (empty($user_list)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Create emails array from data provided to check app users from database
        $emails = array();
        foreach ($user_list as $users) {
            if (!in_array($users['email'], $emails)) {
                array_push($emails, $users['email']);
            }
        }

        $user_list_data = $this->usermodel->get_app_users($emails);
        $result = array();

        foreach ($user_list as $users) {
            $key = $this->_get_key($users['email'], 'user_email', $user_list_data);

            // Not app user
            if ($key === FALSE) {
                $result[] = array('name' => $users['name'], 'image' => '', 'email' => $users['email'], 'app_user' => '0');
            } else {
                $result[] = array('name' => $user_list_data[$key]['user_name'], 'image' => $user_list_data[$key]['user_image'], 'email' => $user_list_data[$key]['user_email'], 'app_user' => '1');
            }
        }

        // Code to remove duplicate user emails
        $output_email = $output = array();
        foreach ($result as $r) {
            $email = strtolower($r['email']);
            if (!in_array($email, $output_email)) {
                $output_email[] = $email;
                $output[] = $r;
            }
        }

        // Sort by app_user
        $sorted_app_users = array();
        foreach ($output as $r) {
            $sorted_app_users[] = $r['app_user'];
        }
        array_multisort($sorted_app_users, SORT_DESC, $output);

        $result_array['status'] = SUCCESS;
        $result_array['message'] = VALID_CONTACTS;
        $result_array['response']['total_users_count'] = count($output);
        $result_array['response']['app_users_count'] = count($user_list_data);
        $result_array['response']['contact_list'] = $output;

        $this->response($result_array); // 200 being the HTTP response code
    }

    // Function to return key of matching value
    private function _get_key($value, $key, $array)
    {
        foreach ($array as $k => $val) {
            if (strtolower($val[$key]) === strtolower($value)) {
                return $k;
            }
        }
        return false;
    }

    /*
     * Method Name: Edit
     * Purpose: To edit user profile details.
     * params:
     *      input: form fields
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     */
    public function edit_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $user_type = $this->post("user_type") ? $this->post("user_type") : 0;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $user_type <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        if ($user_type == 2) //App user
        {
            $this->_edit_app_user_profile($user_id, $user_type);
        } else if ($user_type == 3) //Restaurant user
        {
            $this->_edit_restaurant_user_profile($user_id, $user_type);
        } else // Invalid user type
        {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
    }

    /*
     * Method Name: edit_app_user_profile
     * Purpose: To edit app user profile details.
     * params:
     *      input: user_id, user_type
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     */
    private function _edit_app_user_profile($user_id, $user_type)
    {
        $user_details = $this->usermodel->get_user_details($user_id, $user_type);

        // Check if user exists in Database
        if ($user_details) {
            // Form fields
            $first_name = $this->post("first_name") ? $this->post("first_name") : "";
            $email = $this->post("email") ? $this->post("email") : "";
            $date_of_birth = $this->post("date_of_birth") ? $this->post("date_of_birth") : "";
            $password_before_hash = $this->post("password") ? $this->post("password") : "";
            $password = $this->post("password") ? hash('sha256', $this->post("password")) : "";
            $contact_number = $this->post("contact_number") ? $this->post("contact_number") : "";
            $country = $this->post("country") ? $this->post("country") : "";
            $state = $this->post("state") ? $this->post("state") : "";
            $city = $this->post("city") ? $this->post("city") : "";
            $notification_flag = $this->post("notification_flag") ? $this->post("notification_flag") : "";
            $mgv_points = $this->post("mgv_points") ? $this->post("mgv_points") : 0;
            $gender = $this->post("gender") ? $this->post("gender") : "";
            // Empty data, i.e. improper data validation
            if (empty($first_name) || empty($email) || empty($date_of_birth) || empty($contact_number) || ($country <= 0) || ($state <= 0) || ($city <= 0)) {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMPTY_INPUT;
                $this->response($result_array);
            }

            // Email address validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result_array['status'] = FAIL;
                $result_array['message'] = INVALID_EMAIL;
                $this->response($result_array);
            }

            // Check if email already exist for other user
            $email_exists = $this->usermodel->check_email_exists($email, $user_id);
            if ($email_exists) {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMAIL_EXISTS;
                $this->response($result_array);
            }

            // Check if country, state and city are present in database or are invalid
            $country_exists = $this->usermodel->check_country_exists($country);
            if (!$country_exists) {
                $result_array['status'] = FAIL;
                $result_array['message'] = COUNTRY_NOT_EXISTS;
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

            // Profile image upload code
            $image_upload = 0;
            if (!empty($_FILES)) {
                $config['upload_path'] = MEMBER_IMAGE_PATH;
                $config['allowed_types'] = 'jpg|jpeg';
                $config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
                $config['max_height'] = "2160";
                $config['max_width'] = "4096";

                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('profile_image')) {
                    $upload_error = $this->upload->display_errors();

                    $result_array['status'] = FAIL;
                    $result_array['message'] = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
                    $this->response($result_array); // 404 being the HTTP response code
                } else {
                    $upload_error = '';
                    $upload_data = $this->upload->data();
                    $user_profile_image = $upload_data['file_name'];
                    $image_upload = 1;

                    // Delete previous image if exists
                    if ($user_details->user_image != '') {
                        unlink(MEMBER_IMAGE_PATH . basename($user_details->user_image));
                    }
                }
            }

            $current_time = date("Y-m-d H:i:s");
            $user_update_data = array(
                'user_first_name' => $first_name,
                'user_email' => $email,
                'date_of_birth' => $date_of_birth,
                'country_id' => $country,
                'region_id' => $state,
                'city_id' => $city,
                'notification_setting' => $notification_flag,
                'mvg_points' => $mgv_points,
                'user_contact' => $contact_number,
                'modified_on' => $current_time,
                'gender' => $gender
            );

            if ($image_upload == 1)
                $user_update_data['user_image'] = $user_profile_image;
            if ($password != '')
                $user_update_data['user_password'] = $password;

            $update_result = $this->usermodel->action('update', $user_update_data, $user_id);
            if ($update_result > 0) {
                $updated_user_details = $this->usermodel->get_user_details($user_id, $user_type);
                // $updated_user_details->notification_flag = 1;
                $result_array['status'] = SUCCESS;
                $result_array['message'] = EDIT_SUCCESS;
                $result_array['response']['user_details'] = $updated_user_details;

                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = EDIT_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: edit_restaurant_user_profile
     * Purpose: To edit restaurant user profile details.
     * params:
     *      input: user_id, user_type
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     */
    private function _edit_restaurant_user_profile($user_id, $user_type)
    {
        $user_details = $this->usermodel->get_restaurant_user_details($user_id, $user_type);
        if(!$user_details->user_id)
        {
            $user_details->user_id = $user_id;
        }
//    $this->response($user_details);

        // Check if user exists in Database
        if ($user_details) {
            // Form fields
            $restaurant_name = $this->post("restaurant_name") ? $this->post("restaurant_name") : "";
            $restaurant_owner_name = $this->post("contact_person") ? $this->post("contact_person") : "";
            $cuisine = $this->post("cuisine") ? $this->post("cuisine") : "";
            $ambience = $this->post("ambience") ? $this->post("ambience") : "";
            $dietary_preference = $this->post("dietary_preference") ? $this->post("dietary_preference") : "";
            $average_spend = $this->post("average_spend") ? $this->post("average_spend") : "";
            $email = $this->post("email") ? $this->post("email") : "";
            $password_before_hash = $this->post("password") ? $this->post("password") : "";
            $password = $this->post("password") ? hash('sha256', $this->post("password")) : "";
            $contact_number = $this->post("contact_number") ? $this->post("contact_number") : "";
            $country = $this->post("country") ? $this->post("country") : "";
            $state = $this->post("state") ? $this->post("state") : "";
            $city = $this->post("city") ? $this->post("city") : "";
            $brief_description = $this->post("user_description") ? $this->post("user_description") : "";
            $street_address = $this->post("street_address") ? $this->post("street_address") : "";
            $operating_time = $this->post("operating_time") ? $this->post("operating_time") : "";
            $notification_flag = $this->post("notification_flag") ? $this->post("notification_flag") : "";
            $web_domain = $this->post("web_domain") ? $this->post("web_domain") : "";

            $operating_time = json_decode(stripslashes($operating_time));
            $operating_time = (Array)$operating_time;


            // Empty data, i.e. improper data validation
            if (empty($restaurant_name) || empty($restaurant_owner_name) || empty($brief_description) || ($average_spend <= 0) || empty($email) || empty($contact_number) || ($country <= 0) || ($state <= 0) || ($city <= 0) || empty($street_address)) {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMPTY_INPUT;
                $this->response($result_array);
            }

            if(empty($cuisine))
            {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMPTY_CUISINE;
                $this->response($result_array);
            }

            if(empty($ambience))
            {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMPTY_AMBIENCE;
                $this->response($result_array);
            }

            if(empty($dietary_preference))
            {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMPTY_DIETARY_PREFERENCE;
                $this->response($result_array);
            }


            // Email address validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result_array['status'] = FAIL;
                $result_array['message'] = INVALID_EMAIL;
                $this->response($result_array);
            }

            // Check if email already exist for other user
            $email_exists = $this->usermodel->check_email_exists($email, $user_id);
            if ($email_exists) {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMAIL_EXISTS;
                $this->response($result_array);
            }

            // Check if country, state and city are present in database or are invalid
            $country_exists = $this->usermodel->check_country_exists($country);
            if (!$country_exists) {
                $result_array['status'] = FAIL;
                $result_array['message'] = COUNTRY_NOT_EXISTS;
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

            // Check if cuisine and ambience are present in database or are invalid
            $cuisine_exists = $this->usermodel->check_cuisine_exists($cuisine);
            if (!$cuisine_exists) {
                $result_array['status'] = FAIL;
                $result_array['message'] = CUISINE_NOT_EXISTS;
                $this->response($result_array);
            }

            $ambience_exists = $this->usermodel->check_ambience_exists($ambience);
            if (!$ambience_exists) {
                $result_array['status'] = FAIL;
                $result_array['message'] = AMBIENCE_NOT_EXISTS;
                $this->response($result_array);
            }

            // Code for checking if address is valid
            // We define our address
            $city_name = $this->usermodel->get_city_name($city);
            $state_name = $this->usermodel->get_state_name($state);
            $country_name = $this->usermodel->get_country_name($country);
            $address = $street_address . " " . $city_name . " " . $state_name . " " . $country_name;

            // We get the JSON results from this request
            $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&sensor=false');
            // We convert the JSON to an array
            $geo = json_decode($geo, true);
            // If everything is cool
            if ($geo['status'] == 'OK') {
                // We set our values
                $latitude = $geo['results'][0]['geometry']['location']['lat'];
                $longitude = $geo['results'][0]['geometry']['location']['lng'];
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = INVALID_ADDRESS;
                $this->response($result_array);
            }

            // Profile image upload code
            $image_upload = 0;
            if (!empty($_FILES['profile_image']['name'])) {
                $config['upload_path'] = MEMBER_IMAGE_PATH;
                $config['allowed_types'] = 'jpg|jpeg';
                $config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
                $config['max_height'] = "2160";
                $config['max_width'] = "4096";

                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('profile_image')) {
                    $upload_error = $this->upload->display_errors();

                    $result_array['status'] = FAIL;
                    $result_array['message'] = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
                    $this->response($result_array); // 404 being the HTTP response code
                } else {
                    $upload_error = '';
                    $upload_data = $this->upload->data();
                    $user_profile_image = $upload_data['file_name'];
                    $image_upload = 1;

                    // Delete previous image if exists
                    if ($user_details->restaurant_image != '') {
                        unlink(MEMBER_IMAGE_PATH . basename($user_details->restaurant_image));
                    }
                }
            }

            if (!empty($_FILES['hero_image']['name'])) {
                $config['upload_path'] = MEMBER_IMAGE_PATH;
                $config['allowed_types'] = 'jpg|jpeg';
                $config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
                $config['max_height'] = "2160";
                $config['max_width'] = "4096";

                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('hero_image')) {
                    $upload_error = $this->upload->display_errors();

                    $result_array['status'] = FAIL;
                    $result_array['message'] = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
                    $this->response($result_array); // 404 being the HTTP response code
                } else {
                    $upload_error = '';
                    $upload_data = $this->upload->data();
                    $restaurant_hero_image = $upload_data['file_name'];
                    $image_upload_hero = 1;

                    // Delete previous image if exists
                    if ($user_details->restaurant_image != '') {
                        unlink(MEMBER_IMAGE_PATH . basename($user_details->restaurant_image));
                    }
                }
            }

            $current_time = date("Y-m-d H:i:s");
            $user_update_data = array(
                'user_first_name' => $restaurant_name,
                'restaurant_owner_name' => $restaurant_owner_name,
                'country_id' => $country,
                'region_id' => $state,
                'city_id' => $city,
                'user_email' => $email,
                'street_address1' => $street_address,
                'user_contact' => $contact_number,
                'user_description' => $brief_description,
                'user_type' => $user_type,
                'average_spend' => $average_spend,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'modified_on' => $current_time,
                'web_domain' => $web_domain
            );
            if ($image_upload == 1)
                $user_update_data['user_image'] = $user_profile_image;
            if ($image_upload_hero == 1)
                $user_update_data['restaurant_hero_image'] = $restaurant_hero_image;
            if ($password != '')
                $user_update_data['user_password'] = $password;

            $update_result = $this->usermodel->action('update', $user_update_data, $user_id);
            if ($update_result > 0) {
                // Link cuisine and ambience
                $this->usermodel->update_cuisine_ambience_data($update_result, $cuisine, $ambience);
                $this->usermodel->update_dietary_preference_data($update_result, $dietary_preference);

                // insert/update time slots for restaurant
                // Delete all ids first
                $this->usermodel->deleteTimeSlots($user_id);
                foreach ($operating_time["days"] as $key => $aVal) {

                    $openCloseArray = array(
                        "user_id" => $user_id,
                        "open_close_day" => $aVal->day,
                        "open_close_status" => $aVal->is_open,
                        "open_time_from" => $aVal->open_time,
                        "close_time_to" => $aVal->close_time
                    );

                    $this->usermodel->insertTimeSlots($openCloseArray, $user_id);
                }

                $updated_user_details = $this->usermodel->get_restaurant_user_details($user_id, $user_type);
                if(!$updated_user_details->user_id)
                {
                    $updated_user_details->user_id = $user_id;
                }
                $operating_time = $this->searchmodel->get_operating_time($user_id);
                $cnt = 1;
                $days = $this->config->item("day_array");

                if ($operating_time) {
                    foreach ($operating_time as $aKey => $aVal) {
                        $time = $aVal->open_time_from . " to " . $aVal->close_time_to;
                        $aResTime[$days[$cnt]] = array("is_open" => $aVal->open_close_status, "open_close_time" => $time);
                        $cnt++;
                    }

                }
                $updated_user_details->notification_flag = 1;
                $result_array['status'] = SUCCESS;
                $result_array['message'] = EDIT_SUCCESS;
                $result_array['response']['user_details'] = $updated_user_details;
                $result_array['response']['operating_timing'] = $aResTime;

                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = EDIT_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }
}

/* End of file profile.php */
/* Location: ./application/controllers/webservices/profile.php */