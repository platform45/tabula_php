<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

/*
  Class that contains function for booking
 */

class Suggestions extends REST_Controller
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

        $this->load->model('webservices/suggesstionModel', 'suggesstionmodel', TRUE);
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('admin/restaurant_table_model', 'restauranttablemodel', TRUE);
        $this->load->helper('push_notification');
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
    public function get_suggestions_post()
    {
        $latitude = $this->post('latitude') ? $this->post('latitude') : 0;
        $longitude = $this->post('longitude') ? $this->post('longitude') : 0;
        $user_id = $this->post('user_id') ? $this->post('user_id') : 0;
        $is_all = $this->post('is_all') ? $this->post('is_all') : 0;
        $result_array = array();
        $distance = SUGGESSTIONS_DISTACE;
        $restaurant_details = $this->suggesstionmodel->get_suggesstions($distance, $latitude, $longitude, $user_id, $is_all);
        if ($restaurant_details) {
            $restaurant_details = json_decode(json_encode($restaurant_details), true);
            foreach ($restaurant_details as $aVal) {
                $aResultRes['restaurant_id'] = $aVal['user_id'];
                $aResultRes['restaurant_name'] = $aVal['restaurant_name'];
                $aResultRes['restaurant_owner_name'] = $aVal['restaurant_owner_name'];
                $aResultRes['email'] = $aVal['user_email'];
                $aResultRes['restaurant_image'] = $aVal['restaurant_image'];
                $aResultRes['average_rating'] = $aVal['average_rating'];
                $aResultRes['average_review'] = $aVal['average_review'];
                $aResultRes['contact_number'] = $aVal['user_contact'];
                $aResultRes['address'] = $aVal['address'];
                $aResultRes['average_spend'] = $aVal['average_spend'];
                $aResultRes['description'] = $aVal['restaurant_description'];
                $aResultRes['share_url'] = base_url();
                $aResultRes['is_fav'] = check_added_to_wishlist($user_id, $aVal['user_id']);
                $aResultRes['cuisine'] = $aVal['cuisine'];

                $restaurant_table = $this->restauranttablemodel->get_tables($aVal['user_id']);
                if (empty($restaurant_table)) {
                    $aResultRes['no_of_active_table'] = FAIL;
                    unset($aResultRes['time_slots']);
                } else {
                    $aResultRes['no_of_active_table'] = SUCCESS;
                    $date_time_array = $this->restaurantmodel->get_rounded_time();
                    $aResultRes['time_slots'] = $this->restaurantmodel->get_next_four_available_time_slots($aVal['user_id'], $date_time_array['date'], $date_time_array['start_time_slot'], $date_time_array['date_time']);
                }

                $aResultRestraunant[] = $aResultRes;
            }

            $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_SUGGESTIONS;
            $result_array['current_date'] = $date->format('d-m-Y');
            $result_array['response']['search_results'] = $aResultRestraunant;
            $this->response($result_array); // 200 being the HTTP response code
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_SUGGESTIONS;
            $this->response($result_array); // 404 being the HTTP response code
        }
    }
}