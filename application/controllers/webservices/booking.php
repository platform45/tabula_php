<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

/*
  Class that contains function for booking
 */

class Booking extends REST_Controller
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

        $this->load->model('webservices/bookingmodel', 'bookingmodel', TRUE);
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->helper('push_notification');
    }


    /*
     * Author: Akshay Deshmukh - Delete this function once it test.
     *
     * Method Name: test_method_to_send_push_notification_post
     * Purpose: To test push notification for IOS and Android.
     */
    public function test_method_to_send_push_notification_post()
    {
        //android
        $google_api_key = (ENVIRONMENT == 'development') ? DEV_FIREBASE_API_KEY : LIVE_FIREBASE_API_KEY;
        $message_data = array('title' => "booking confirmation", 'text' => "got it");
        $type_data = array('type' => BOOKING_CONFIRMED_TYPE);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $google_api_key,
            'Content-Type: application/json'
        );

        $fields = array(
            'to' => 'f0YD3HbIBCE:APA91bF1EWyjSAu0NZcWlSI_CvFPTflMBnMYZE1-fwYb2d3Izz0dRMFE0cwym4Ub3f4hWo8TiwBi5XcO5zLDUZwg7i0LeMsO8JnQG7v9iFb_ZwuguvcvfmS9ZuNKvnnGwaU-NILwI74f',
            'notification' => $message_data,
            'data' => $type_data
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result);
        //$this->response($data);

        //ios
        $passphrase = $url = '';
        if (ENVIRONMENT == 'development') {
            $certificate = DEV_PEM_FILE;
            $passphrase = DEV_PASSPHRASE;
            $url = DEV_SSL_URL;
        } else {
            $certificate = LIVE_PEM_FILE;
            $passphrase = LIVE_PASSPHRASE;
            $url = LIVE_SSL_URL;
        }

        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', $certificate);
        stream_context_set_option($stream_context, 'ssl', 'passphrase', $passphrase);

        $fp = @stream_socket_client($url, $err, $errstr, 60, (STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT), $stream_context);

        if ($fp) {
            // Build the payload
            $load = array(
                'aps' => array('alert' => 'IOS notificatoin', 'badge' => 0, 'sound' => 'default'),
                'type' => $type_data,
                'title' => 'confirm',
            );
            // Encode the payload as JSON
            $payload = json_encode($load);

            $msg = chr(0) . pack('n', 32) . pack('H*', 'd16d7e53120d4c7b96c1fae3ac97446bfe5ba350d6cae9c9b3a7efc34827713f') . pack('n', strlen($payload)) . $payload;
            $result = fwrite($fp, $msg, strlen($msg));


            fclose($fp);

            $this->response($result);
        }
    }

    /*
     * Method Name: autocomplete_guest_list
     * Purpose: To get list of users matching with name and email passed for requesting a table.
     * params:
     *      input: name, email, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              menu - Array containing user name, email and number
     */
    public function autocomplete_guest_list_post()
    {
        $name = $this->post("name") ? $this->post("name") : "";
        $email = $this->post("email") ? $this->post("email") : "";

        $result_array = array();

        // Empty data, i.e. improper data validation
        if (empty($name) && empty($email)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $user_details = $this->bookingmodel->get_matching_user_details($name, $email);
        if ($user_details) {
            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_USERS_LIST;
            $result_array['response']['user_details'] = $user_details;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_USERS;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }


    /*
     * Method Name: booking_details_for_restaurant_post
     * Purpose: To get list of bookings per user and restaurant.
     * params:
     *      input: user_id, booking_type ( 1 - Request, 2 - Confirmed, 3 - Cancelled ), offset, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              bookings - Array containing booking records
     */
    public function booking_details_for_restaurant_post()
    {

        $booking_id = $this->post("booking_id") ? $this->post("booking_id") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;

        $limit = SEARCH_RESULTS_LIMIT;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($booking_id <= 0) {

            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
        $this->_get_app_user_bookings_for_restuarant($booking_id, $limit, $offset);


    }

    /*
     * Method Name: _get_app_user_bookings_for_restuarant
     * Purpose: To get app user booking details.
     * params:
     *      input: user_id, booking_type, limit, offset
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              bookings - Array containing booking records
     */
    private function _get_app_user_bookings_for_restuarant($booking_id, $limit, $offset)
    {
        // Check valid user

        // Get total records
        $total_records = $this->bookingmodel->get_total_booking_records_for_restaurant($booking_id);

        if ($total_records > 0) {
            $booking = array();

            // Get booking records
            $booking_records = $this->bookingmodel->get_booking_records_for_restuarant($booking_id, $limit, $offset);

            // Get invited users list for each booking
            foreach ($booking_records as $record) {
                $invited_users = $this->bookingmodel->get_invited_users($record->booking_id);
                $booking[] = array('booking_detail' => $record);
            }
            $offset = $offset + $limit;

            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_BOOKING_LIST;
            $result_array['response']['total_bookings'] = $total_records;
            $result_array['response']['offset'] = $offset;
            $result_array['response']['booking_records'] = $booking;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_BOOKING;
            $this->response($result_array); // 404 being the HTTP response code
        }

    }

    /*
     * Method Name: request_table
     * Purpose: To request a table or update a existing booking by guest
     * params:
     *      input: restaurant_id, user_id, date, number_of_guest, time_slot, guest_list - ( name, email, number ), message, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - Array
     */
    public function request_table_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : "";
        $user_id = $this->post("user_id") ? $this->post("user_id") : "";
        $table_id = $this->post("table_id") ? $this->post("table_id") : "";
        $date = $this->post("date") ? $this->post("date") : "";
        $number_of_guest = $this->post("number_of_guest") ? $this->post("number_of_guest") : "";
        $time_slot_id = $this->post("time") ? $this->post("time") : "";
        $booking_id = $this->post("booking_id") ? $this->post("booking_id") : 0;
        $is_notify = $this->post("is_notify") ? $this->post("is_notify") : 0;
        $last_minute_from_time = $this->post("last_minute_from_time") ? $this->post("last_minute_from_time") : "";
        $last_minute_to_time = $this->post("last_minute_to_time") ? $this->post("last_minute_to_time") : "";
        //$booking_by = $this->post("booking_by") ? $this->post("booking_by") : 0;
$booking_by= 1;

        $result_array = array();
        $table_ids = explode(',', $table_id);

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0 || $user_id <= 0 || empty($table_id) || empty($date) || $number_of_guest <= 0 || empty($time_slot_id) || $time_slot_id >= 48 || $booking_by <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // If guests exceed 20
        if ($number_of_guest > PERMITTED_GUEST_NUMBER) {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_GUEST_NUMBER;
            $this->response($result_array);
        }

        if ($is_notify == 1) {
            if (empty($last_minute_from_time) || empty($last_minute_to_time)) {
                $result_array['status'] = FAIL;
                $result_array['message'] = EMPTY_INPUT;
                $this->response($result_array);
            }
        }


        // Check if restaurant is valid and valid user is rating it.
        $is_valid_user = $this->usermodel->is_valid_user($user_id, SEARCH_APP_USER_TYPE);
        $is_valid_restaurant = $this->usermodel->is_valid_user($restaurant_id, SEARCH_RESTAURANT_TYPE);
        if ($is_valid_user && $is_valid_restaurant) {
            // Enter data in booking request table
            $time_slot = $this->bookingmodel->get_time_slot($time_slot_id);
            $time_slot = json_decode(json_encode($time_slot), true);
            $time_slot = $time_slot[0]['time_slot'];
            $from_time = date('Y-m-d H:i:s', strtotime("$date $time_slot"));
            $to_time = date("Y-m-d H:i:s", strtotime("$date $time_slot +30 minutes"));
            $response_date = date('d-m-Y', strtotime($date));
            $date = date('Y-m-d H:i:s', strtotime($date));
            if ($booking_id > 0) {
                $booking_number = $this->post("booking_number") ? $this->post("booking_number") : "";
                if (empty($booking_number)) {
                    $result_array['status'] = FAIL;
                    $result_array['message'] = EMPTY_INPUT;
                    $this->response($result_array);
                }
                $is_valid_booking = $this->bookingmodel->is_valid_booking($user_id, $booking_id, SEARCH_APP_USER_TYPE);
                if ($is_valid_booking) {
                    $has_requested = $this->bookingmodel->has_requested_table($restaurant_id, $date, $time_slot_id, $table_ids, $booking_id);
                    if ($has_requested['status'] == 0) {
                        $this->response($has_requested);
                    }
                    $update_last_minute_data = array();
                    if ($is_notify == 1) {
                        $update_data = array(
                            'booking_from_time' => $from_time,
                            'booking_to_time' => $to_time,
                            'number_of_guest' => $number_of_guest,
                            'is_notify' => '1',
                            'last_minute_from_time' => $last_minute_from_time,
                            'last_minute_to_time' => $last_minute_to_time,
                            'booking_status_change_on' => date("Y-m-d H:i:s"),
                        );

                        $update_last_minute_data = array(
                            'notify_date' => date('Y-m-d', strtotime($response_date)),
                            'last_minute_from_time' => $last_minute_from_time,
                            'last_minute_to_time' => $last_minute_to_time,
                            'updated_at' => date("Y-m-d H:i:s"),
                        );

                    } else {
                        $update_data = array(
                            'booking_from_time' => $from_time,
                            'booking_to_time' => $to_time,
                            'number_of_guest' => $number_of_guest,
                            'is_notify' => '0',
                            'booking_status_change_on' => date("Y-m-d H:i:s"),
                        );
                    }

                    $update_status = $this->bookingmodel->update_booking_request($update_data, $restaurant_id, $table_ids, $date, $time_slot_id, $booking_id, $update_last_minute_data);
                    if ($update_status) {
                        $result_array['status'] = SUCCESS;
                        $result_array['message'] = BOOKING_UPDATE_SUCCESS;
                    } else {
                        $result_array['status'] = FAIL;
                        $result_array['message'] = BOOKING_UPDATE_FAILED;
                        $this->response($result_array); // 404 being the HTTP response code
                    }
                } else {
                    $result_array['status'] = FAIL;
                    $result_array['message'] = INVALID_BOOKING;
                    $this->response($result_array); // 404 being the HTTP response code
                }

            } else {
                $booking_number = rand_string(7);
                // Check if user already requested a table for same date and timeslot for same restaurant
                $has_requested = $this->bookingmodel->has_requested_table($restaurant_id, $date, $time_slot_id, $table_ids);
                if ($has_requested['status'] == 0) {
                    $this->response($has_requested);
                }
                $insert_last_minute_data = array();
                if ($is_notify == 1) {
                    $insert_data = array(
                        'customer_id' => $user_id,
                        'restaurant_id' => $restaurant_id,
                        'booking_number' => $booking_number,
                        'booking_from_time' => $from_time,
                        'booking_to_time' => $to_time,
                        'number_of_guest' => $number_of_guest,
                        'status' => 3,
                        'payment_status' => 0,
                        'is_notify' => '1',
                        'last_minute_from_time' => $last_minute_from_time,
                        'last_minute_to_time' => $last_minute_to_time,
                        'booking_on' => date("Y-m-d H:i:s"),
                        'booking_status_change_on' => date("Y-m-d H:i:s"),
                        'booking_by' => $booking_by
                    );

                    $insert_last_minute_data = array(
                        'restaurant_id' => $restaurant_id,
                        'user_id' => $user_id,
                        'notify_date' => date('Y-m-d', strtotime($response_date)),
                        'last_minute_from_time' => $last_minute_from_time,
                        'last_minute_to_time' => $last_minute_to_time,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    );
                } else {
                    $insert_data = array(
                        'customer_id' => $user_id,
                        'restaurant_id' => $restaurant_id,
                        'booking_number' => $booking_number,
                        'booking_from_time' => $from_time,
                        'booking_to_time' => $to_time,
                        'number_of_guest' => $number_of_guest,
                        'status' => 3,
                        'payment_status' => 0,
                        'booking_on' => date("Y-m-d H:i:s"),
                        'booking_status_change_on' => date("Y-m-d H:i:s"),
                    );
                }

                //          $insert_result = $this->bookingmodel->action('insert', 'tab_booking_request', $insert_data);
                $insert_result = $this->bookingmodel->insert_booking_request($insert_data, $table_ids, $date, $time_slot_id, $insert_last_minute_data);
                if ($insert_result['status'] == 1) {
                    $booking_id = $insert_result['booking_id'];
                    $type = "BOOKING_CONFIRMED";

                    //send notification to restaurant for booking placed and last minute cancellation if is_notify = 1
                    $this->_send_notification($booking_id, $type , $table_ids,$insert_last_minute_data);

                    $result_array['status'] = SUCCESS;
                    $result_array['message'] = BOOKING_SUCCESS;
                } else {
                    $result_array['status'] = FAIL;
                    $result_array['message'] = BOOKING_FAILED;
                    $this->response($result_array); // 404 being the HTTP response code
                }
            }

            $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, SEARCH_RESTAURANT_TYPE);
            if (!$restaurant_details->user_id) {
                $restaurant_details->user_id = $restaurant_id;
            }
            $result_array['response']['booking']['booking_code'] = $booking_number;
            $result_array['response']['booking']['date'] = $response_date;
            $result_array['response']['booking']['time'] = $time_slot;
            $result_array['response']['booking']['is_notify'] = $is_notify;
            $result_array['response']['booking']['last_minute_from_time'] = $last_minute_from_time;
            $result_array['response']['booking']['last_minute_to_time'] = $last_minute_to_time;
            $result_array['response']['restaurant_details'] = $restaurant_details;
            $this->response($result_array);
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    public function getOperatingTime_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : "";
        $user_id = $this->post("user_id") ? $this->post("user_id") : "";
        $date = $this->post("date") ? $this->post("date_time_slot") : "";
        $time_slot = $this->post("time_slot") ? $this->post("time_slot") : "";
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0 || empty($time_slot) || empty($date)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
        $is_valid_restaurant = $this->usermodel->is_valid_user($restaurant_id, SEARCH_RESTAURANT_TYPE);

        if ($is_valid_restaurant) {
            // From time
            $from_time = date('Y-m-d H:i:s', strtotime($date . ' ' . $time_slot));

            // To time - add 2 hours i.e 4 slots
            $timestamp = strtotime($date . ' ' . $time_slot) + 120 * 60;
            $to_time = date('Y-m-d H:i:s', $timestamp);

            // Check if user already requested a table for same date and timeslot for same restaurant
            $has_requested = $this->bookingmodel->has_requested_table($user_id, $restaurant_id, $from_time);

            if ($has_requested) {
                $result_array['status'] = FAIL;
                $result_array['message'] = REQUEST_EXISTS;
                $this->response($result_array);
            }

            $insert_data = array(
                'customer_id' => $user_id,
                'restaurant_id' => $restaurant_id,
                'booking_number' => $booking_number,
                'booking_from_time' => $from_time,
                'booking_to_time' => $to_time,
                'number_of_guest' => $number_of_guest,
                'booking_message' => $message,
                'status' => 1,
                'payment_status' => 0,
                'booking_on' => date("Y-m-d H:i:s"),
                'booking_status_change_on' => date("Y-m-d H:i:s")
            );
            $insert_result = $this->bookingmodel->action('insert', 'tab_booking_request', $insert_data);
            if ($insert_result > 0) {
                $restaurant_details = $this->usermodel->get_restaurant_user_details($restaurant_id, SEARCH_RESTAURANT_TYPE);
                if (!$restaurant_details->user_id) {
                    $restaurant_details->user_id = $restaurant_id;
                }

                $result_array['status'] = SUCCESS;
                $result_array['message'] = BOOKING_SUCCESS;
                $result_array['response']['booking']['booking_code'] = $booking_number;
                $result_array['response']['booking']['date'] = $date;
                $result_array['response']['booking']['time'] = $time_slot;
                $result_array['response']['restaurant_details'] = $restaurant_details;
                $this->response($result_array); // 404 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = BOOKING_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: booking_list
     * Purpose: To get list of bookings per user that are Requested/Confirmed/Cancelled.
     * params:
     *      input: user_id, booking_type ( 1 - Request, 2 - Confirmed, 3 - Cancelled ), offset, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              bookings - Array containing booking records
     */
    public function booking_list_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $user_type = $this->post("user_type") ? $this->post("user_type") : 0;
        $booking_type = $this->post("booking_type") ? $this->post("booking_type") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $limit = SEARCH_RESULTS_LIMIT;
        $result_array = array();


        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $user_type <= 0 || $booking_type <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        // Check valid booking type
        if ($booking_type != REQUESTED_BOOKING && $booking_type != CONFIRMED_BOOKING && $booking_type != CANCELLED_BOOKING && $booking_type != ALL_BOOKING ) {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_BOOKING_TYPE;
            $this->response($result_array);
        }

        if($user_type == SEARCH_RESTAURANT_TYPE && $booking_type == ALL_BOOKING )
        {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_BOOKING_TYPE;
            $this->response($result_array);
        }

        if ($user_type == SEARCH_APP_USER_TYPE) //App user
        {
            $this->_get_app_user_bookings($user_id, $booking_type, $limit, $offset);
        } else if ($user_type == SEARCH_RESTAURANT_TYPE) //Restaurant user
        {
            $this->_get_restaurant_user_bookings($user_id, $booking_type, $limit, $offset);
        } else // Invalid user type
        {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
    }


    /*
     * Method Name: get_app_user_bookings
     * Purpose: To get app user booking details.
     * params:
     *      input: user_id, booking_type, limit, offset
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              bookings - Array containing booking records
     */
    private function _get_app_user_bookings($user_id, $booking_type, $limit, $offset)
    {
        // Check valid user
        $booking_id = $this->post("booking_id") ? $this->post("booking_id") : 0;
        $is_valid_user = $this->usermodel->is_valid_user($user_id, SEARCH_APP_USER_TYPE);
        if ($is_valid_user) {
            // Get total records
            $total_records = $this->bookingmodel->get_total_booking_records($user_id, $booking_type, $booking_id);
            if ($total_records > 0) {
                $booking = array();
                // Get booking records
                $booking_records = $this->bookingmodel->get_booking_records($user_id, $booking_type, $limit, $offset, $booking_id);
                // Get invited users list for each booking
                foreach ($booking_records as $record) {
//                    $invited_users = $this->bookingmodel->get_invited_users($record->booking_id);
//                    $booking[] = array('booking_detail' => $record);
                    $booking[] = $record;
                }
                if ($booking_records) {
                    $result_array['status'] = SUCCESS;
                    $result_array['message'] = VALID_BOOKING_LIST;
                    $result_array['response']['total_bookings'] = $total_records;
                    $result_array['response']['offset'] = $offset + $limit;;
                    $result_array['response']['booking_records'] = $booking;
                    $this->response($result_array); // 200 being the HTTP response code
                } else {
                    $result_array['status'] = FAIL;
                    $result_array['message'] = NO_BOOKING;
                    $this->response($result_array);
                }
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = NO_BOOKING;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
    * Method Name: booking_history_confirmed_post
    * Purpose: To get list of  bookings per user that are Requested/Confirmed/Cancelled.
    * params:
    *      input: user_id, booking_type ( 1 - Request, 2 - Confirmed, 3 - Cancelled ), offset, token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              bookings - Array containing booking records
    */
    public function booking_history_confirmed_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $user_type = $this->post("user_type") ? $this->post("user_type") : 2;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $limit = SEARCH_RESULTS_LIMIT;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $user_type <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        if ($user_type == SEARCH_APP_USER_TYPE) //App user
        {
            $this->_get_app_user_confimed_bookings($user_id, $limit, $offset);
        } else // Invalid user type
        {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
    }


    /*
     * Method Name: _get_app_user_confimed_bookings
     * Purpose: To get app user booking details.
     * params:
     *      input: user_id,  limit, offset
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              bookings - Array containing booking records
     */
    private function _get_app_user_confimed_bookings($user_id, $limit, $offset)
    {
        // Check valid user
        $is_valid_user = $this->usermodel->is_valid_user($user_id, SEARCH_APP_USER_TYPE);
        if ($is_valid_user) {
            // Get total records
            $total_records = $this->bookingmodel->get_total_booking_confirmed_history($user_id);
            if ($total_records > 0) {
                $booking = array();

                // Get booking records
                $booking_records = $this->bookingmodel->get_booking_confirmed_history($user_id, $limit, $offset);

                // Get invited users list for each booking
                foreach ($booking_records as $record) {
                    $record = json_decode(json_encode($record), true);

                    $invited_users = $this->bookingmodel->get_invited_users($record['booking_id']);
                    $record['is_fav'] = check_added_to_wishlist($user_id, $record['restaurant_id']);
                    $record['share_url'] = base_url();
                    $booking[] = $record;
                }
                $offset = $offset + $limit;
                $result_array['status'] = SUCCESS;
                $result_array['message'] = VALID_BOOKING_LIST;
                $result_array['response']['total_bookings'] = $total_records;
                $result_array['response']['offset'] = $offset;
                $result_array['response']['booking_records'] = $booking;
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = NO_BOOKING;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }


    /*
     * Method Name: get_restaurant_user_bookings
     * Purpose: To get restaurant user booking details.
     * params:
     *      input: user_id, booking_type, limit, offset
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              bookings - Array containing booking records
     */
    private function _get_restaurant_user_bookings($user_id, $booking_type, $limit, $offset)
    {
        // Check valid restaurant
        $is_valid_restaurant = $this->usermodel->is_valid_user($user_id, SEARCH_RESTAURANT_TYPE);
        if ($is_valid_restaurant) {
            // Get total records
            $total_records = $this->bookingmodel->get_total_restaurant_booking_records($user_id, $booking_type);

            if ($total_records > 0) {
                $booking = array();

                // Get booking records
                $booking_records = $this->bookingmodel->get_restaurant_booking_records($user_id, $booking_type, $limit, $offset);

                // Get time ago for each booking
                foreach ($booking_records as $record) {
                    $record['booking_time_ago'] = get_timeago($record['booking_status_change_on']);
                    $invited_users = $this->bookingmodel->get_invited_users($record->booking_id);
//                    $booking[] = array('booking_detail' => $record, 'invited_users' => $invited_users);
                    $booking[] = $record;
                }

                $result_array['status'] = SUCCESS;
                $result_array['message'] = VALID_BOOKING_LIST;
                $result_array['response']['total_bookings'] = $total_records;
                $result_array['response']['offset'] = $offset + $limit;
                $result_array['response']['booking_records'] = $booking;
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = NO_BOOKING;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: change_booking_status
     * Purpose: To change status of booking.
     * params:
     *      input: user_id, user_type, booking_id, tables - comma separated list ( multiple tables ), status - ( 1 - Confirm, 2 - Waiting, 3 - Regret ), token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     *              response - tables if confirmed
     */
    public function change_booking_status_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $user_type = $this->post("user_type") ? $this->post("user_type") : 0;
        $booking_id = $this->post("booking_id") ? $this->post("booking_id") : 0;
        $status = $this->post("status") ? $this->post("status") : 0;
        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $user_type <= 0 || $booking_id <= 0 || $status <= 0) {
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

        // Check if valid booking id for that restaurant
        $is_valid_booking = $this->bookingmodel->is_valid_booking($user_id, $booking_id, $user_type);

        // If valid booking and table then proceed further
        if ($is_valid_booking) {
            if ($status == 3) {
                $is_valid_booking_status = $this->bookingmodel->is_valid_booking_status($booking_id, $user_type);
                if ($is_valid_booking_status != 0) {
                    $result_array['status'] = SUCCESS;
                    $result_array['message'] = BOOKING_CANCELLED;
                    $this->response($result_array);
                }
            }
            $status_result = $this->bookingmodel->change_booking_status($booking_id, $status, $user_type);
            if ($status_result) {
                // Send notification ( push and email ) code
                $result_array['status'] = SUCCESS;
                $booking_status = '';
                switch ($status) {
                    case '1':
                        $booking_status = "BOOKING_CONFIRMED";
                        $result_array['message'] = BOOKING_CONFIRMED_MESSAGE;

                        break;
                    case '2':
                        $booking_status = "BOOKING_WAITING";
                        $result_array['message'] = BOOKING_WAITING_MESSAGE;
                        break;
                    case '3':
                        $booking_status = ($user_type == SEARCH_RESTAURANT_TYPE) ? "BOOKING_REJECTED" : "BOOKING_CANCELLED";
                        $result_array['message'] = BOOKING_CANCELLED;
                        break;
                }
                // Get table names to send in response if booking is confirmed
                $this->_send_notification($booking_id, $booking_status, $table_ids = '', $last_minute_data = array());
                if($status == 3){
                    $this->send_last_minute_cancellation_notification($booking_id);
                }
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = BOOKING_STATUS_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_BOOKING;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: delete_booking
     * Purpose: To delete booking.
     * params:
     *      input: user_id, booking_id, token
     *      output: status - FAIL / SUCCESS
     *              message - failure / Success message
     */
    public function delete_booking_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $user_type = $this->post("user_type") ? $this->post("user_type") : 0;
        $booking_id = $this->post("booking_id") ? $this->post("booking_id") : 0;

        $result_array = array();

        // Empty data, i.e. improper data validation
        if ($user_id <= 0 || $user_type <= 0 || $booking_id <= 0) {
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

        // Check if valid booking id
        $is_valid_booking = $this->bookingmodel->is_valid_booking($user_id, $booking_id, $user_type);

        // If validations pass
        if ($is_valid_booking) {
            $status = $this->bookingmodel->delete_booking($booking_id, $user_type);
            if ($status) {
                $result_array['status'] = SUCCESS;
                $result_array['message'] = BOOKING_DELETE_SUCCESS;
                $this->response($result_array); // 200 being the HTTP response code
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = BOOKING_DELETE_FAILED;
                $this->response($result_array); // 404 being the HTTP response code
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_BOOKING;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: _send_notification
     * Purpose: To send push notification and email to users
     * params:
     *      input: restaurant_id, booking_id, booking_status, table_list
     *      output: -
     */
    private function _send_notification($booking_id, $type, $table_ids, $last_minute_data)
    {
        $tables = $email_user_array = $amount_payable = $android_user_array = $ios_user_array = [];
        $booking_details = $this->bookingmodel->get_booking_details($booking_id);
        $restaurant_id = $booking_details->restaurant_id;
        $customer_id = $booking_details->customer_id;
        $booking_code = $booking_details->booking_number;

        $booking_date = date("jS F, Y", strtotime($booking_details->booking_from_time));
        $booking_time = date("g:ia", strtotime($booking_details->booking_from_time));

        if($type == "BOOKING_CANCELLED" || $type == "BOOKING_REJECTED"  )
        {
            $text_message = "Your booking has been cancelled for " . $booking_date . " at " .$booking_time;
            $user_device_details = $this->usermodel->get_user_device_details($customer_id);
            if ($user_device_details['dev_type'] == 'A') {
                $android_user_array[] = array('user_id' => $customer_id, 'device_id' => $user_device_details['dev_device_id']);
            } else if ($user_device_details['dev_type'] == 'I') {
                $ios_user_array[] = array('user_id' => $customer_id, 'device_id' => $user_device_details['dev_device_id']);
            }
            send_notification($restaurant_id, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);
        }
        else if($type == "BOOKING_CONFIRMED" )
        {
            //get booked table name
//            $table_names = "";
//            foreach ($table_ids as $table_id) {
//                $table_detail = $this->bookingmodel->get_table_details_by_id($table_id);
//                $this->response($table_detail->table_name);
//            }

            $text_message = "New table booking has been generated for " . $booking_date . " at " .$booking_time . " with code " . $booking_code;
            $user_device_details = $this->usermodel->get_user_device_details($restaurant_id);
            if ($user_device_details['dev_type'] == 'A') {
                $android_user_array[] = array('user_id' => $restaurant_id, 'device_id' => $user_device_details['dev_device_id']);
            } else if ($user_device_details['dev_type'] == 'I') {
                $ios_user_array[] = array('user_id' => $restaurant_id, 'device_id' => $user_device_details['dev_device_id']);
            }
            send_notification($customer_id, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);
        }

        if(!empty($last_minute_data))
        {
            $tables = $email_user_array = $amount_payable = $android_user_array = $ios_user_array = [];
            $type = "LAST_MINUTE_CANCELLATION";
            $text_message = "Last minute cancellation has been set for " . $booking_date . " from " . $last_minute_data['last_minute_from_time'] . " to " . $last_minute_data['last_minute_to_time']  . " with code " . $booking_code;
            $user_device_details = $this->usermodel->get_user_device_details($restaurant_id);
            if ($user_device_details['dev_type'] == 'A') {
                $android_user_array[] = array('user_id' => $restaurant_id, 'device_id' => $user_device_details['dev_device_id']);
            } else if ($user_device_details['dev_type'] == 'I') {
                $ios_user_array[] = array('user_id' => $restaurant_id, 'device_id' => $user_device_details['dev_device_id']);
            }
            send_notification($customer_id, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);
        }

    }


    /*
     * Method Name: get_time_slots_post
     * Purpose: To get available time slots for restaurant
     * params:
     *      input: restaurant_id
     *      output: - time slots
     */
    public function get_time_slots_post()
    {
        $restaurant_id = $this->post("restaurant_id") ? $this->post("restaurant_id") : 0;
        $date = $this->post("date") ? $this->post("date") : 0;
        $user_type = SEARCH_RESTAURANT_TYPE;
        $result_array = array();
        // Empty data, i.e. improper data validation
        if ($restaurant_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
        if ($date == 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $is_valid_restaurant = $this->usermodel->is_valid_user($restaurant_id, $user_type);
        if ($is_valid_restaurant) {
            $date = date('Y-m-d H:i:s', strtotime($date));
            $time_slots = $this->restaurantmodel->get_available_time_slots_for_restaurant($date, $restaurant_id);
            $this->response($time_slots);
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_RESTAURANT;
            $this->response($result_array); // 404 being the HTTP response code
        }

    }

    /*
    * Method Name: get_all_time_slots
    * Purpose: To get all time slots
    * params:
    *      output: - time slots
    */
    public function get_all_time_slots_post()
    {
        $result_array = array();
        $time_slots = $this->bookingmodel->get_all_time_slot();
        $result_array['status'] = SUCCESS;
        $result_array['message'] = TIME_SLOT_SUCCESS;
        $result_array['response']['time_slots'] = $time_slots;
        $this->response($result_array);
    }


    /*
     * Author: Akshay Deshmukh
    * Method Name: send_last_minute_cancellation_notification
    * Purpose: Send notification to user if he tick for last minute cancallatino request
    * params:
    *      output: -
    */
    public function send_last_minute_cancellation_notification($booking_id)
    {
        $android_user_array = array();
        $ios_user_array = array();

        $data = $this->bookingmodel->get_booking_details($booking_id);
        $date = date('Y-m-d', strtotime($data->booking_from_time));
        $time = date('H:i:s', strtotime($data->booking_from_time));
        $last_minuit_notify_users = $this->bookingmodel->get_last_minuit_notify_users($booking_id, $date, $time);
        $restaurant_details = $this->restaurantmodel->get_restaurant_id_by_booking_id($booking_id);
        if ($last_minuit_notify_users) {
            foreach ($last_minuit_notify_users as $last_minuit_notify_user) {
                $user_device_details = $this->usermodel->get_user_device_details($last_minuit_notify_user['user_id']);
                if ($user_device_details['dev_type'] == 'A') {
                    $android_user_array[] = array('user_id' => $last_minuit_notify_user['user_id'], 'device_id' => $user_device_details['dev_device_id']);
                } else if ($user_device_details['dev_type'] == 'I') {
                    $ios_user_array[] = array('user_id' => $last_minuit_notify_user['user_id'], 'device_id' => $user_device_details['dev_device_id']);
                }

                $last_minute_notification_data = array(
                    'user_id' => $last_minuit_notify_user['user_id'],
                    'restaurant_id' => $restaurant_details->restaurant_id,
                    'for_date' => $date,
                    'for_time_slot' => $time,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->restaurantmodel->add_last_minute_cancellatinon_notification($last_minute_notification_data);
            }

            $type = "LAST_MINUTE_CANCELLATION";
            $date = date('jS F, Y', strtotime($data->booking_from_time));
            $time = date('H:i', strtotime($data->booking_from_time));
            $text_message = "The timeslot  " . $date . " at " . $time . " is now available for '" . $restaurant_details->user_first_name . "''";
            $tables = $email_user_array = $amount_payable = [];
            send_notification($data->restaurant_id, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);
        }
    }


    /*
     * Author: Akshay Deshmukh
    * Method Name: get_last_minute_cancellation_notification_list_post
    * Purpose: Fetch all notification of last minute cancellation
    * params:
     *      input: user_id
    *      output: -
    */
    public function get_last_minute_cancellation_notification_list_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $offset = $this->post('offset') ? $this->post('offset') : 0;
        $limit = LAST_MINUIT_NOTIFICATION_LIMIT;

        $user_type = SEARCH_APP_USER_TYPE;
        $result_array = array();
        if ($user_id <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }

        $is_valid_user = $this->usermodel->is_valid_user($user_id, $user_type);
        if ($is_valid_user) {
            $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
            $last_minute_cancellation_notification_list_count = $this->bookingmodel->get_last_minute_cancellation_notification_list_count($user_id, $date->format('Y-m-d'), $date->format('H:i:s'));
            $last_minute_cancellation_notification_list = $this->bookingmodel->get_last_minute_cancellation_notification_list($user_id, $date->format('Y-m-d'),$date->format('H:i:s'), $limit, $offset);
            if ($last_minute_cancellation_notification_list) {

                $result_array['status'] = SUCCESS;
                $result_array['message'] = LAST_MINUTE_CANCELLED_NOTIFICATION_FOUND;
                $result_array['total'] = $last_minute_cancellation_notification_list_count;
                $result_array['current_date'] = $date->format('d-m-Y');
                $result_array['offset'] = $offset + $limit;
                $result_array['response']['last_minute_cancel_list'] = $last_minute_cancellation_notification_list;
                $this->response($result_array);
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = LAST_MINUTE_CANCELLED_NOTIFICATION_NOT_FOUND;
                $this->response($result_array);
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Author: Akshay Deshmukh
    * Method Name: next_booking_post
    * Purpose: Fetch all booking for user and set flag for is old or not.
    * params:
     *      input: user_id
    *      output: -
    */

    public function next_booking_post()
    {
        $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
        $current_date = $this->post("current_date") ? $this->post("current_date") : 0;
        $first_day = date('Y-m-01 H:i:s', strtotime($current_date));
        $last_day = date('Y-m-t H:i:s', strtotime($current_date));

        $user_type = SEARCH_APP_USER_TYPE;
        $result_array = array();
        if ($user_id <= 0 || $current_date <= 0) {
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            $this->response($result_array);
        }
        $is_valid_user = $this->usermodel->is_valid_user($user_id, $user_type);
        if ($is_valid_user) {
            $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
            $date = $date->format('Y-m-d H:i:s');
            $booking_details = $this->bookingmodel->get_next_booking_details($user_id, $date, $first_day, $last_day);

            if ($booking_details) {
                $result_array['status'] = SUCCESS;
                $result_array['message'] = BOOKING_FOUND;
                $result_array['date'] = date('d-m-Y H:i:s');
                $result_array['response']['booking_details'] = $booking_details;
                $this->response($result_array);

            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = NO_BOOKING;
                $this->response($result_array);
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }

    /*
     * Method Name: get_closing_slots
     * Purpose: To get slots greater than inputed slots; for ws
     * params:
     *      input: - slot_id
     *      output: - array() - time slots
     */
    public function get_closing_slots_post()
    {
        $from_time_slot = $this->input->post('slot_id');
        $closing_time_slots = $this->restaurantmodel->get_closing_slots($from_time_slot);
        $result_array = array();
        if($closing_time_slots)
        {
            $result_array['success'] = SUCCESS;
            $result_array['closing_time_slots'] = $closing_time_slots;
            $this->response($result_array);
        }
        else
        {
            $result_array['success'] = FAIL;
            $result_array['message'] = "No closing slot available";
            $this->response($result_array);
        }

    }

}

/* End of file booking.php */
/* Location: ./application/controllers/webservices/booking.php */