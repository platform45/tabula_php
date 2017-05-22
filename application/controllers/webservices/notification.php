<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'libraries/REST_Controller.php';

/*
  Class that contains function for notification
 */
class Notification extends REST_Controller {

  // Constructor
  function __construct()
  {
    parent::__construct();

    // Validate token
    $api_key = ( $this->get('token') ) ? $this->get('token') : $this->post('token');

    if( $api_key )
    {
      $api_status = validate_api_key( $api_key );
      $api_status = json_decode( $api_status );

      if( $api_status->status != SUCCESS )
      {
        echo json_encode( array("status" => $api_status->status, "message" => $api_status->message) );
        die;
      }
    }
    else
    {
      echo json_encode( array("status" => FAIL, "message" => NO_TOKEN_MESSAGE) );
      die;
    }

    $this->load->model( 'webservices/usermodel', 'usermodel', TRUE );
    $this->load->model( 'webservices/notificationmodel', 'notificationmodel', TRUE );
  }

  /*
   * Method Name: get_notifications
   * Purpose: Get all notifications for a user
   * params:
   *      input: user_id, user_type, offset, token
   *      output: status - FAIL / SUCCESS
   *              message - fails / Success message
   *              response - Array containing all the notification results.
   *
   */
  public function get_notifications_post()
  {
    $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
    $user_type = $this->post("user_type") ? $this->post("user_type") : 0;
    $offset = $this->post('offset') ? $this->post('offset') : 0;

    $limit = SEARCH_RESULTS_LIMIT;

    $result_array = array();

    // Empty data, i.e. improper data validation
    if( $user_id <= 0 || $user_type <= 0 )
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = EMPTY_INPUT;
      $this->response( $result_array );
    }

    // Invalid user type
    if( $user_type != 2 && $user_type != 3 )
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = EMPTY_INPUT;
      $this->response( $result_array );
    }

    $is_valid_user = $this->usermodel->is_valid_user( $user_id, $user_type );
    if( $is_valid_user )
    {
      //Get total count of notification result
      $total_records = $this->notificationmodel->get_total_notification_records( $user_id );

      if( $total_records > 0 )
      {
        //Result array
        $notification_results = $this->notificationmodel->get_notification_records( $user_id, $limit, $offset );

        // Get time ago for each notification
        $notification_records = array();
        foreach ( $notification_results as $notification )
        {
          $notification_records[] = array(
                                    'notification_id' => $notification->notification_id,
                                    'notification_message' => $notification->notification_message,
                                    'notification_time_ago' => get_timeago( $notification->notification_date ),
                                    'notification_type' => $notification->notification_type
                                  );
        }
        $offset = $offset+$limit;

        $result_array['status'] = SUCCESS;
        $result_array['message'] = NOTIFICATION_SUCCESS;
        $result_array['response']['total_record'] = $total_records;
        $result_array['response']['offset'] = $offset;
        $result_array['response']['notification_results'] = $notification_records;
        $this->response( $result_array ); // 200 being the HTTP response code
      }
      else
      {
        $result_array['status'] = FAIL;
        $result_array['message'] = NOTIFICATION_FAILED;
        $this->response( $result_array ); // 404 being the HTTP response code
      }
    }
    else
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = ( $user_type == 2 ) ? INVALID_USER : INVALID_RESTAURANT;
      $this->response( $result_array ); // 404 being the HTTP response code
    }
  }

  /*
   * Method Name: delete_notification
   * Purpose: To delete notification.
   * params:
   *      input: user_id, notification_id, token
   *      output: status - FAIL / SUCCESS
   *              message - failure / Success message
   */
  public function delete_notification_post()
  {
    $user_id = $this->post("user_id") ? $this->post("user_id") : 0;
    $notification_id = $this->post("notification_id") ? $this->post("notification_id") : 0;

    $result_array = array();
   
   
    // Empty data, i.e. improper data validation
    if( $user_id <= 0 || $notification_id <= 0 )
    {       
      $result_array['status'] = FAIL;
      $result_array['message'] = EMPTY_INPUT;
      $this->response( $result_array );
    }

    // Check if it is valid notification for a user
    $is_valid_notification = $this->notificationmodel->is_valid_notification( $user_id, $notification_id );

    // If validations pass
    if( $is_valid_notification )
    {
      $status = $this->notificationmodel->delete_notification( $notification_id );
      if( $status )
      {
        $result_array['status'] = SUCCESS;
        $result_array['message'] = NOTIFICATION_DELETE_SUCCESS;
        $this->response( $result_array ); // 200 being the HTTP response code
      }
      else
      {
        $result_array['status'] = FAIL;
        $result_array['message'] = NOTIFICATION_DELETE_FAILED;
        $this->response( $result_array ); // 404 being the HTTP response code
      }
    }
    else
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = INVALID_NOTIFICATION;
      $this->response( $result_array ); // 404 being the HTTP response code
    }
  }
}

/* End of file notification.php */
/* Location: ./application/controllers/webservices/notification.php */