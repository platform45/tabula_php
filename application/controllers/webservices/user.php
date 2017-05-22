<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

/*
  Class that contains function for user login
 */

class User extends REST_Controller
{

    // Constructor
    function __construct()
    {
        parent::__construct();
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
    }

    /*
     * Method Name: Login
     * Purpose: To verify login credentials.
     * params:
     *      input: Email, password, user_type, device id, device type, device_id_live
     *      output: status - FAIL / SUCCESS
     *              message - The reason if login fails / Success message
     *              userDetails - Array containing all the details for logged in user, if login is successful.
     *              accesstoken
     */

    public function login_post()
    {
        $email = $this->post("email") ? $this->post("email") : "";
        $password = $this->post("password") ? hash('sha256', $this->post("password")) : "";
        $user_type = $this->post("user_type") ? $this->post("user_type") : "";
        $device_id = $this->post("device_id") ? $this->post("device_id") : "";
        $device_type = $this->post("device_type") ? $this->post("device_type") : "";
        $device_id_live = $this->post("device_id_live") ? $this->post("device_id_live") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if (empty($email) || empty($password) || empty($device_type) || empty($device_id_live)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Invalid user type
        if ($user_type != 2 && $user_type != 3) {
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

        // Invalid device type
        $device_type_arr = explode(",", DEVICE_TYPE);
        if (!in_array($device_type, $device_type_arr)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_DEVICE_TYPE;
            $this->response($result_array);
        }

        $device_id_live = ($device_id_live == 'yes') ? '1' : '0';

        // If all validations pass proceed to login
        $user_details = $this->usermodel->check_user_exists($email, $password, $user_type);

        if ($user_details) {
            // Generate token to be sent after login and insert in database
            $access_token = $this->get_token();

            $update_data = array(
                'user_access_token' => $access_token,
                'modified_on' => date("Y-m-d H:i:s")
            );
            $this->usermodel->update_user_details($update_data, $user_details->user_id);

            // Update device_id and device_type for user in tab_user_devices table
            $user_id = $this->usermodel->update_device_data($user_details->user_id, $device_id, $device_type, $device_id_live, $access_token);

            // insert access token for user
            $this->usermodel->insert_access_token($access_token, $user_details->user_id);

            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_USER_CREDENTIALS;
            $result_array['response']['access_token'] = $access_token;
            $result_array['response']['user_details'] = $user_details;


            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER_CREDENTIALS;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: Forgot password
     * Purpose: To send password creation link to email if user forgets password.
     * params:
     *      input: email
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     */

    public function forgot_password_post()
    {
        $email = $this->post("email") ? $this->post("email") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if (empty($email)) {
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

        $user_details = $this->usermodel->get_user_details_for_password($email);
        if ($user_details) {
            if ($user_details->user_status == 1) {
                // Generate forgot password token and update in database
                $forgot_password_token = md5($user_details->user_id . $email);
                $user_id = $this->usermodel->update_forgot_password_token($user_details->user_id, $forgot_password_token);
                $change_password_url = base_url("forgot_password/?token=" . $forgot_password_token);

                // Get the Forgot Password Email Template.
                $email_template = get_email_template("Forgot Password");
                $email_subject = $email_template->email_subject;
                $email_from = $email_template->email_from;
                $email_body = $email_template->email_body;

                $user_name = $user_details->user_first_name;
                $email_body = str_replace("{NAME}", $user_name, $email_body);
                $email_body = str_replace("{FORGOT_LINK}", "Please <a href='" . $change_password_url . "'>click here</a> to reset Password", $email_body);
                $email_body = str_replace("{LINK}", $change_password_url, $email_body);

                $strParam = array(
                    '{NAME}' => $user_name,
                    '{FORGOT_LINK}' => "<a href='" . $change_password_url . "'>" . $change_password_url . "</a> ",
                    '{LINK}' => $change_password_url
                );
                $txtMessageStr = mergeContent($strParam, 'template');
                $txtMessageStr = str_replace("undefined", "", $txtMessageStr);

                $result = $this->send_email($email, $email_subject, $email_body, $email_from);
                if ($result) {
                    $result_array['status'] = SUCCESS;
                    $result_array['message'] = EMAIL_SENT;
                    $this->response($result_array); // 200 being the HTTP response code
                } else {
                    $result_array['status'] = FAIL;
                    $result_array['message'] = EMAIL_SEND_FAILED;
                    $this->response($result_array); // 404 being the HTTP response code
                }
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = ACCOUNT_INACTIVE;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMAIL_NOT_FOUND;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: Send Email
     * Purpose: To send emails
     * params:
     *      input: user_email, email_subject, email_body, email_from
     *      output: TRUE/FALSE
     */

    function send_email($user_email, $email_subject, $email_body, $email_from)
    {
        if ($_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "192.168.21.7" || $_SERVER['HTTP_HOST'] == "192.168.43.47") {
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = 'ssl://smtp.gmail.com';
            $config['smtp_port'] = '465';
            $config['smtp_timeout'] = '7';
            $config['smtp_user'] = 'genknooz501@gmail.com';
            $config['smtp_pass'] = 'genknooz!';
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['mailtype'] = 'html'; // or html
            $config['validation'] = TRUE; // bool whether to validate email or not

            $this->load->library('email', $config);
            $this->email->from($email_from, $this->config->item('site_name'));
            $this->email->to($user_email);
            $this->email->subject($this->config->item('site_name') . " : " . $email_subject);
            $this->email->message($email_body);
            return $this->email->send();
        } else {
            $config = Array(
                'protocol' => 'mail',
                'smtp_host' => '',
                'smtp_port' => 25,
                'smtp_user' => '', // change it to yours
                'smtp_pass' => '', // change it to yours
                'mailtype' => 'html',
                'charset' => 'iso-8859-1',
                'wordwrap' => TRUE,
                'smtp_crypto' => 'tls'
            );

            $this->load->library('email', $config);
            $this->email->from($email_from, $this->config->item('site_name'));
            $this->email->to($user_email);
            $this->email->subject($this->config->item('site_name') . " : " . $email_subject);
            $this->email->message($email_body);
            return $this->email->send();
        }
    }

    /*
     * Method Name: Country_list
     * Purpose: To get list of countries.
     * params:
     *      input: -
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              country array
     */

    public function country_list_post()
    {
        $result_array = array();

        $countries = $this->usermodel->get_countries();
        if ($countries) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = ALL_COUNTRIES;
            $result_array['response']['country_list'] = $countries;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_COUNTRIES;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: State_list
     * Purpose: To get list of states in country provided.
     * params:
     *      input: country_id
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              states array
     */

    public function state_list_post()
    {
        $country_id = $this->post("country_id") ? $this->post("country_id") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($country_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $states = $this->usermodel->get_state_by_country($country_id);
        if ($states) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = ALL_STATES;
            $result_array['response']['state_list'] = $states;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_STATES;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: City_list
     * Purpose: To get list of cities in state provided.
     * params:
     *      input: state_id
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              cities array
     */

    public function city_list_post()
    {
        $state_id = $this->post("state_id") ? $this->post("state_id") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($state_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $cities = $this->usermodel->get_city_by_state($state_id);
        foreach ($cities as $city) {
            $cityArray['city_name'] = utf8_encode($city->city_name);
            $cityArray['city_id'] = $city->city_id;
            $resCityArray[] = $cityArray;
        }

        if ($cities) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = ALL_CITIES;
            $result_array['response']['city_list'] = $resCityArray;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_CITIES;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: Registration
     * Purpose: Register a user.
     * params:
     *      input: form fields
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              userDetails - Array containing all the details for logged in user, if login is successful.
     *              accesstoken
     */

    public function registration_post()
    {
        $user_type = $this->post("user_type") ? $this->post("user_type") : "";
        $result_array = array();
        if ($user_type == 2) { //App user
            $this->register_app_user();
        } else if ($user_type == 3) { //Restaurant user
            $this->register_restaurant_user();
        } else { //Invalid user type
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
    }

    // Register app user
    function register_app_user()
    {
        $user_type = $this->post("user_type") ? $this->post("user_type") : "";
        $device_id = $this->post("device_id") ? $this->post("device_id") : "";
        $device_type = $this->post("device_type") ? $this->post("device_type") : "";
        $device_id_live = $this->post("device_id_live") ? $this->post("device_id_live") : "";

        // Form fields
        $first_name = $this->post("first_name") ? $this->post("first_name") : "";
        $last_name = $this->post("last_name") ? $this->post("last_name") : "";
        $email = $this->post("email") ? $this->post("email") : "";
        $date_of_birth = $this->post("date_of_birth") ? $this->post("date_of_birth") : "";
        $password_before_hash = $this->post("password") ? $this->post("password") : "";
        $password = $this->post("password") ? hash('sha256', $this->post("password")) : "";
        $contact_number = $this->post("contact_number") ? $this->post("contact_number") : "";
        $country = $this->post("country") ? $this->post("country") : "";
        $gender = $this->post("gender") ? $this->post("gender") : "";
        $state = $this->post("state") ? $this->post("state") : "";
        $city = $this->post("city") ? $this->post("city") : "";
        $is_subscribe = $this->post("is_subscriber") ? $this->post("is_subscriber") : 0;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if (empty($first_name) || empty($email) || empty($date_of_birth) || empty($password) || empty($contact_number) || ($country <= 0) || ($state <= 0) || ($city <= 0) || empty($device_id) || empty($device_type) || empty($device_id_live)) {
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

        // Invalid device type
        $device_type_arr = explode(",", DEVICE_TYPE);
        if (!in_array($device_type, $device_type_arr)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_DEVICE_TYPE;
            $this->response($result_array);
        }

        // Validations Pass
        // Check if email already exist
        $email_exists = $this->usermodel->check_email_exists($email);
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
        $user_profile_image = '';
        if (!empty($_FILES)) {
            $config['upload_path'] = MEMBER_IMAGE_PATH;
            $config['allowed_types'] = 'jpg|jpeg|png';
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
            }
        }

        // Generate token to be sent after registration
        $access_token = $this->get_token();

        $device_id_live = ($device_id_live == 'yes') ? '1' : '0';
        $current_time = date("Y-m-d H:i:s");
        $user_insert_data = array(
            'user_first_name' => $first_name,
            'user_last_name' => $last_name,
            'user_image' => $user_profile_image,
            'user_email' => $email,
            'date_of_birth' => $date_of_birth,
            'country_id' => $country,
            'region_id' => $state,
            'city_id' => $city,
            'user_password' => $password,
            'user_contact' => $contact_number,
            'user_type' => $user_type,
            'gender' => $gender,
            'user_status' => '1',
            'user_access_token' => $access_token,
            'is_deleted' => '0',
            'created_on' => $current_time
        );
        $insert_result = $this->usermodel->action('insert', $user_insert_data);

        if ($insert_result > 0) {
            // Insert device details
            $this->usermodel->update_device_data($insert_result, $device_id, $device_type, $device_id_live);

            $this->usermodel->insert_access_token($access_token, $insert_result);
            // insert data in subscriber mst
            if ($is_subscribe) {
                $data = array("user_id" => $insert_result, "sub_email" => $email);
                $this->usermodel->insertSubscriber($data);
            }
            // Get the Registration Email Template.
            $email_template = get_email_template("Welcome to Tabula!");
            $email_subject = $email_template->email_subject;
            $email_from = $email_template->email_from;
            $email_body = $email_template->email_body;

            $user_name = $first_name;
            $email_body = str_replace("{NAME}", $user_name, $email_body);
            $active_link = "";
            $email_body = str_replace("{ACTIVE_LINK}", "Please <a href='" . $active_link . "'>click here</a> to Verify Email", $email_body);
            $email_body = str_replace("{LINK}", $active_link, $email_body);
            $email_body = str_replace("{USERNAME}", $email, $email_body);
            $email_body = str_replace("{PASSWORD}", $password_before_hash, $email_body);

            $strParam = array(
                '{NAME}' => $user_name,
                '{ACTIVE_LINK}' => "Please <a href='" . $active_link . "'>click here to Verify Email</a> ",
                '{LINK}' => $active_link,
                '{USERNAME}' => $email,
                '{PASSWORD}' => $password_before_hash,
            );
            $txtMessageStr = mergeContent($strParam, 'template');
            $txtMessageStr = str_replace("undefined", "", $txtMessageStr);

            $this->send_email($email, $email_subject, $email_body, $email_from);

            $user_details = $this->usermodel->get_user_details($insert_result, $user_type);

            $result_array['status'] = SUCCESS;
            $result_array['message'] = REGISTRATION_SUCCESS;
            $result_array['response']['access_token'] = $access_token;
            $result_array['response']['user_details'] = $user_details;

            // Send slider images also
            //$result_array['response']['slider_images'] = $this->usermodel->get_slider_images();
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = REGISTRATION_FAILED;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    // Register app user
    function register_restaurant_user()
    {
        $user_type = $this->post("user_type") ? $this->post("user_type") : "";
        $device_id = $this->post("device_id") ? $this->post("device_id") : "";
        $device_type = $this->post("device_type") ? $this->post("device_type") : "";
        $device_id_live = $this->post("device_id_live") ? $this->post("device_id_live") : "";

        // Form fields
        $restaurant_name = $this->post("restaurant_name") ? trim($this->post("restaurant_name")) : "";
        $average_spend = $this->post("average_spend") ? $this->post("average_spend") : "";
        $email = $this->post("email") ? $this->post("email") : "";
        $password_before_hash = $this->post("password") ? $this->post("password") : "";
        $password = $this->post("password") ? hash('sha256', $this->post("password")) : "";
        $contact_number = $this->post("contact_number") ? $this->post("contact_number") : "";
        $country = $this->post("country") ? $this->post("country") : "";
        $state = $this->post("state") ? $this->post("state") : "";
        $city = $this->post("city") ? $this->post("city") : "";
        $restaurant_contact_person = $this->post("restaurant_contact_person") ? trim($this->post("restaurant_contact_person")) : "";
        $brief_description = $this->post("user_description") ? $this->post("user_description") : "";
        $street_address = $this->post("street_address") ? $this->post("street_address") : "";
        $operating_time = $this->post("operating_time") ? $this->post("operating_time") : "";
        $is_subscriber = $this->post("is_subscriber") ? $this->post("is_subscriber") : 0;
        $web_domain = $this->post("web_domain") ? $this->post("web_domain") : "";

        $operating_time = json_decode(stripslashes($operating_time));
        $operating_time = (Array)$operating_time;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if (empty($restaurant_name) || empty($brief_description) || ($average_spend <= 0) || empty($email) || empty($password) || empty($contact_number) || ($country <= 0) || ($state <= 0) || ($city <= 0) || empty($street_address) || empty($device_type) || empty($device_id_live)) {
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

        // Invalid device type
        $device_type_arr = explode(",", DEVICE_TYPE);
        if (!in_array($device_type, $device_type_arr)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_DEVICE_TYPE;
            $this->response($result_array);
        }

        // Validations Pass
        // Check if email already exist
        $email_exists = $this->usermodel->check_email_exists($email);
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
        $user_profile_image = '';
        if (!empty($_FILES)) {
            $config['upload_path'] = MEMBER_IMAGE_PATH;
            $config['allowed_types'] = 'jpg|jpeg|png';
            //$config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
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
            }
        }

        // Generate token to be sent after registration
        $access_token = $this->get_token();

        $device_id_live = ($device_id_live == 'yes') ? '1' : '0';
        $current_time = date("Y-m-d H:i:s");

        $restaurant_details_url_string = preg_replace('/[^A-Za-z0-9\-]/', '-', $restaurant_name);
        $restaurant_details_url_string = preg_replace('/-+/', '-', $restaurant_details_url_string);
        $restaurant_details_url_string = strtolower($restaurant_details_url_string);
        $count = $this->usermodel->restaurant_details_same_url_count($restaurant_details_url_string);
        if ($count) {
            $restaurant_details_url_string = $restaurant_details_url_string . '-' . $count;
        }

        $user_insert_data = array(
            'user_password' => $password,
            'user_image' => $user_profile_image,
            'user_first_name' => $restaurant_name,
            'restaurant_owner_name' => $restaurant_contact_person,
            'country_id' => $country,
            'region_id' => $state,
            'city_id' => $city,
            'user_email' => $email,
            'street_address1' => $street_address,
            'restaurant_detail_url' => $restaurant_details_url_string,
            'user_contact' => $contact_number,
            'user_type' => $user_type,
            'average_spend' => $average_spend,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'user_description' => $brief_description,
            'user_status' => '1',
            'user_access_token' => $access_token,
            'is_deleted' => '0',
            'created_on' => $current_time,
            'web_domain' => $web_domain
        );
        $insert_result = $this->usermodel->action('insert', $user_insert_data);
        if ($insert_result > 0) {
            // Insert device details
            $this->usermodel->update_device_data($insert_result, $device_id, $device_type, $device_id_live);

            // Insert access token
            $this->usermodel->insert_access_token($access_token, $insert_result);

            if ($is_subscriber) {
                $data = array("user_id" => $insert_result, "sub_email" => $email);
                $this->usermodel->insertSubscriber($data);
            }
            // insert time slots for restaurant

            foreach ($operating_time["days"] as $key => $aVal) {

                $openCloseArray = array(
                    "user_id" => $insert_result,
                    "open_close_day" => $aVal->day,
                    "open_close_status" => $aVal->is_open,
                    "open_time_from" => $aVal->open_time,
                    "close_time_to" => $aVal->close_time
                );
                // print_r($openCloseArray);die;
                $insertTimeSlotId = $this->usermodel->insertTimeSlots($openCloseArray, $insert_result);
            }

            // Get the Registration Email Template.
            $email_template = get_email_template("Welcome to Tabula!");
            $email_subject = $email_template->email_subject;
            $email_from = $email_template->email_from;
            $email_body = $email_template->email_body;

            $user_name = $restaurant_name;
            $email_body = str_replace("{NAME}", $user_name, $email_body);
            $active_link = "";
            $email_body = str_replace("{ACTIVE_LINK}", "Please <a href='" . $active_link . "'>click here</a> to Verify Email", $email_body);
            $email_body = str_replace("{LINK}", $active_link, $email_body);
            $email_body = str_replace("{USERNAME}", $email, $email_body);
            $email_body = str_replace("{PASSWORD}", $password_before_hash, $email_body);

            $strParam = array(
                '{NAME}' => $user_name,
                '{ACTIVE_LINK}' => "Please <a href='" . $active_link . "'>click here to Verify Email</a> ",
                '{LINK}' => $active_link,
                '{USERNAME}' => $email,
                '{PASSWORD}' => $password_before_hash,
            );
            $txtMessageStr = mergeContent($strParam, 'template');
            $txtMessageStr = str_replace("undefined", "", $txtMessageStr);

            $this->send_email($email, $email_subject, $email_body, $email_from);

            $user_details = $this->usermodel->get_user_details($insert_result, $user_type);

            $result_array['status'] = SUCCESS;
            $result_array['message'] = REGISTRATION_SUCCESS;
            $result_array['response']['access_token'] = $access_token;
            $result_array['response']['user_details'] = $user_details;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = REGISTRATION_FAILED;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: Cuisine_list
     * Purpose: To get list of cuisines.
     * params:
     *      input: -
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              cuisine array
     */

    public function cuisine_list_post()
    {
        $result_array = array();

        $cuisines = $this->usermodel->get_cuisines();
        if ($cuisines) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = ALL_CUISINES;
            $result_array['response']['cuisine_list'] = $cuisines;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_CUISINES;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: Ambience_list
     * Purpose: To get list of ambience.
     * params:
     *      input: -
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              ambience array
     */

    public function ambience_list_post()
    {
        $result_array = array();

        $ambience = $this->usermodel->get_ambience();
        if ($ambience) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = ALL_AMBIENCE;
            $result_array['response']['ambience_list'] = $ambience;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_AMBIENCE;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    public function dietary_preference_list_post()
    {
        $result_array = array();

        $dietary = $this->usermodel->get_dietary_preference();
        if ($dietary) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = ALL_DIETARY;
            $result_array['response']['dietary_preference_list'] = $dietary;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_DIETARY;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    // Function to return the access token
    function get_token()
    {
        // If LIVE then return random token ELSE return fixed constant token
        if (ENVIRONMENT == 'development')
            return TOKEN;
        else
            return random_string('alnum', 16);
    }

    /*
     * Method Name: Logout
     * Purpose: To logout
     * params:
     *      input: user_id, access_token
     *      output: status - FAIL / SUCCESS
     *              message - Failure / Success message
     */

    public function logout_post()
    {
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

        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $this->usermodel->logout($user_id, $api_key);
        $result_array['status'] = SUCCESS;
        $result_array['message'] = LOGOUT_SUCCESS;
        $this->response($result_array); // 200 being the HTTP response code
    }

    /*
     * Method Name: Regenerate token
     * Purpose: To generate new token and update in database and return it if user is logged in and browses after long time
     * params:
     *      input: user_id, access_token
     *      output: status - FAIL / SUCCESS
     *              message - Failure / Success message
     *              access_token - new token generated
     */

    public function regenerate_token_post()
    {
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

        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Generate token to be sent after login and insert in database
        $access_token = $this->get_token();

        $update_data = array(
            'user_access_token' => $access_token,
            'modified_on' => date("Y-m-d H:i:s")
        );
        $this->usermodel->update_user_details($update_data, $user_id);
        $result_array['status'] = SUCCESS;
        $result_array['message'] = TOKEN_GENERATION_SUCCESS;
        $result_array['response']['access_token'] = $access_token;
        $this->response($result_array); // 200 being the HTTP response code
    }

    /*
     * Method Name: Invite user
     * Purpose: To send invitation email to users to register to app
     * params:
     *      input: email, sender_name, invitee_name, token
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     */

    public function invite_user_post()
    {
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

        $email = $this->post("email") ? $this->post("email") : "";
        $sender_name = $this->post("sender_name") ? $this->post("sender_name") : "";
        $invitee_name = $this->post("invitee_name") ? $this->post("invitee_name") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if (empty($email) || empty($sender_name) || empty($invitee_name)) {
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

        // If it is an app user dont send email
        $email_exists = $this->usermodel->check_email_exists($email);
        if ($email_exists) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMAIL_EXISTS;
            $this->response($result_array);
        }

        // Get the Invite User Email Template.
        $email_template = get_email_template("Invite User");
        $email_subject = $email_template->email_subject;
        $email_from = $email_template->email_from;
        $email_body = $email_template->email_body;

        $email_body = str_replace("{NAME}", $invitee_name, $email_body);
        $email_body = str_replace("{SENDER_NAME}", $sender_name, $email_body);

        $result = $this->send_email($email, $email_subject, $email_body, $email_from);
        if ($result) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = EMAIL_SENT;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMAIL_SEND_FAILED;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: get_loyalty_points
     * Purpose: To get user loyalty points
     * params:
     *      input: user_id, token
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              response - loyalty details
     */

    public function get_loyalty_points_post()
    {
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

        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Check if user is valid.
        $is_valid_user = $this->usermodel->is_valid_user($user_id, SEARCH_APP_USER_TYPE);
        if ($is_valid_user) {
            $loyalty = array();

            $loyalty_points = $this->usermodel->get_loyalty_points($user_id);
            $total = ($loyalty_points / FIXED_LOYALTY_POINT);

            $loyalty[] = array('default_point' => FIXED_LOYALTY_POINT, 'default_value' => 1, 'allocated_points' => $loyalty_points, 'total' => $total);

            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_LOYALTY;
            $result_array['response']['loyalty'] = $loyalty;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    function clean_string($string)
    {
        $s = trim($string);
        $s = iconv("UTF-8", "UTF-8//IGNORE", $s); // drop all non utf-8 characters
        // this is some bad utf-8 byte sequence that makes mysql complain - control and formatting i think
        $s = preg_replace('/(?>[\x00-\x1F]|\xC2[\x80-\x9F]|\xE2[\x80-\x8F]{2}|\xE2\x80[\xA4-\xA8]|\xE2\x81[\x9F-\xAF])/', ' ', $s);

        $s = preg_replace('/\s+/', ' ', $s); // reduce all multiple whitespace to a single space

        return $s;
    }

}

/* End of file user.php */
/* Location: ./application/controllers/webservices/user.php */