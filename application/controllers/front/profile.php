<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* Kaustubh Bhujbal
* Profile related functions
* 2 Feb 2017
*/
class Profile extends CI_Controller
{
    function __construct()
    {
		
        parent::__construct();
        if($this->session->userdata('user_id')=="")
        {
			$this->session->set_userdata("toast_error_message", "Please sign in first.");
            redirect(base_url() . "home");
        }

        $this->load->library('form_validation');
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('webservices/contentmodel', 'contentmodel', TRUE);
        $this->load->model('admin/restaurant_table_model', 'restauranttablemodel', TRUE);
        $this->load->model('webservices/suggesstionModel', 'suggesstionmodel', TRUE);
        $this->load->model('webservices/notificationmodel', 'notificationmodel', TRUE );
        $this->load->model('webservices/bookingmodel', 'bookingmodel', TRUE);
    }

    /*
     * Method Name: index
     * Purpose:  to load user's profile data
     * params:
     *      input:
     *      output: -
     *
     */
    public function index()
    {
		$this->session->unset_userdata('book_slot_id');
		$this->session->unset_userdata('modified_booking');
		
        $user_id = $this->session->userdata('user_id');
        $notifi_offset = 0;
        $data['content']   = array();
        $data['countries'] = $this->usermodel->get_countries();
        $data['states']    = $this->usermodel->get_state_by_country(STATIC_COUNTRY_ID);
		
		//$data['cities'] = $this->usermodel->get_city_by_state($state_id);
        $this->template_front->view('profile', $data);
    }

    /*
     * Author: Akshay Deshmukh
     * Method Name: get_confirm_booking_list
     * Purpose:  to get booking list of user
     * params:
     *      input: limit, offset
     *      output: -
     *
     */
    public function get_confirm_booking_list()
    {
        $user_id = $this->session->userdata('user_id');
        $offset = $this->input->get('offset') ? $this->input->get('offset') : 0;
        $booking_type = FUTURE_BOOKING;
        $booking_id = FAIL;
        $limit = SEARCH_RESULTS_LIMIT;

        $total_records = $this->bookingmodel->front_profile_get_total_booking_records($user_id, $booking_type, $booking_id);
        $booking_records = $this->bookingmodel->front_profile_get_booking_records($user_id, $booking_type, $limit, $offset, $booking_id);
        $booking = [];
        foreach ($booking_records as $record) {
            $record['formatted_booking_date'] = date("j M, Y H:i", strtotime($record['booking_date'] . $record['booking_time']));
            $record['encoded_booking_id'] = urlencode($record['booking_id']);
            $booking[] = $record;
        }
        $offset = $offset + $limit;
        echo json_encode(array('status' => SUCCESS, 'user_id' => $user_id, 'offset' => $offset, 'total_records' => $total_records, 'booking_records' => $booking));
    }

    /*
     * Author: Akshay Deshmukh
     * Method Name: get_history_booking_list
     * Purpose:  to get history with cancel booking list of user
     * params:
     *      input: limit, offset
     *      output: -
     *
     */
    public function get_history_booking_list()
    {
        $user_id = $this->session->userdata('user_id');
        $offset = $this->input->get('offset') ? $this->input->get('offset') : 0;
        $booking_type = HISTORY_BOOKING;
        $booking_id = FAIL;
        $limit = SEARCH_RESULTS_LIMIT;

        $total_records = $this->bookingmodel->front_profile_get_total_booking_records($user_id, $booking_type, $booking_id);
        $booking_records = $this->bookingmodel->front_profile_get_booking_records($user_id, $booking_type, $limit, $offset, $booking_id);
        $booking = [];
        foreach ($booking_records as $record) {
            $record['formatted_booking_date'] = date("j M, Y H:i", strtotime($record['booking_date'] . $record['booking_time']));
            $booking[] = $record;
        }
        $offset = $offset + $limit;
        echo json_encode(array('status' => SUCCESS, 'user_id' => $user_id, 'offset' => $offset, 'total_records' => $total_records, 'booking_records' => $booking));
    }

    /*
     * Author: Akshay Deshmukh
     * Method Name: modify_booking
     * Purpose:  to modify_booking
     * params:
     *      input: limit, offset
     *      output: -
     *
     */
    public function modify_booking()
    {
        $booking_id = $this->uri->segment(3);
        if($booking_id)
        {
            $encoded_id = base64_encode($booking_id);
            $this->session->set_userdata('modified_booking',$encoded_id);
			$this->session->unset_userdata('book_slot_id');
			
            $restaurant_details = $this->restaurantmodel->get_restaurant_id_by_booking_id($booking_id);
            redirect(base_url() . 'restaurant-details/'.$restaurant_details->restaurant_detail_url);
        }
        else
        {
            $this->session->set_userdata("toast_error_message", "Unable to proceed. Please try again.");
            redirect('profile');
        }
    }

    public function booking_by_slot_id()
    {
        $slot_id = $this->uri->segment(4);
        $restaurant_id = $this->uri->segment(3);
        if($restaurant_id && $slot_id)
        {
            $slot_id = base64_encode($slot_id);
            $this->session->set_userdata('book_slot_id',$slot_id);
			$this->session->unset_userdata('modified_booking');
			
            $restaurant_detail_url = $this->restaurantmodel->get_restaurant_details_url($restaurant_id);
            redirect(base_url() . 'restaurant-details/'.$restaurant_detail_url);
        }
        else
        {
            $this->session->set_userdata("toast_error_message", "Unable to proceed. Please try again.");
            redirect('profile');
        }
    }

    /*
     * Author: Akshay Deshmukh
     * Method Name: cancel_booking
     * Purpose:  to cancel booking
     * params:
     *      input: limit, offset
     *      output: -
     *
     */
    public function cancel_booking()
    {
        $user_id = $this->session->userdata('user_id');
		
		if($user_id > 0)
		{
			$booking_id = $this->input->get('booking_id') ? $this->input->get('booking_id') : 0;
			$user_type = SEARCH_APP_USER_TYPE;
			$status = CANCELLED_BOOKING;
			$status_result = $this->bookingmodel->change_booking_status($booking_id, $status, $user_type);

			if($status_result)
			{
				$this->send_last_minute_cancellation_notification($booking_id);
				echo json_encode(array("status"=> SUCCESS, 'message' => "Booking cancelled successfully."));
			}
			else{
				echo json_encode(array("status"=> FAIL, 'message' => "Sorry for inconvenience, We are unable to cancel you booking for now. Please try again after sometime."));
			}
		}
		else
		{
			$this->session->set_userdata("toast_error_message", "Please sign in first.");
            echo json_encode(array("status"=> FAIL, 'is_login'=> FAIL));
		}

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
        $tables = $email_user_array = $amount_payable = [];

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

            send_notification($data->restaurant_id, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);
        }

        $booking_date = date("jS F, Y", strtotime($data->booking_from_time));
        $booking_time = date("g:ia", strtotime($data->booking_from_time));
        $type = "BOOKING_CANCELLED";
        $android_user_array = [];
	$ios_user_array = [];

        $text_message = "Your booking has been cancelled for " . $booking_date . " at " .$booking_time;
        $user_device_details = $this->usermodel->get_user_device_details($data->customer_id);
		if($user_device_details){
			if ($user_device_details['dev_type'] == 'A') {
				$android_user_array[] = array('user_id' => $data->customer_id, 'device_id' => $user_device_details['dev_device_id']);
			} else if ($user_device_details['dev_type'] == 'I') {
				$ios_user_array[] = array('user_id' => $data->customer_id, 'device_id' => $user_device_details['dev_device_id']);
			}
		}
        send_notification($data->restaurant_id, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);
    }


    /*
     * Method Name: get_profile
     * Purpose:  to load user's profile data
     * params:
     *      input: user_id, user_type= 2 constant for users
     *      output: -
     *
     */
    public function get_profile()
    {      
        $user_id   = $_POST['user_id'];
        $user_data = $this->usermodel->get_user_details($user_id,2);
        $user_data->date_of_birth = date("d-m-Y",strtotime($user_data->date_of_birth));
        echo json_encode($user_data);exit;
    }

    /*
     * Method Name: get_city_data
     * Purpose:  to load user's cities data
     * params:
     *      input: region_id
     *      output: -
     *
     */
    public function get_city_data()
    {      
        $state_id = $_POST['state_id'];
        $cities   = $this->usermodel->get_city_by_state($state_id);
        echo json_encode($cities);exit;
    }

    /*
     * Method Name: update_profile
     * Purpose:  to load user's profile data
     * params:
     *      input: user_id, user_type= 2 constant for users
     *      output: -
     *
     */
    public function update_profile()
    {
        if($_POST)
        {
            $user_id = $_POST['userId'];
            $this->form_validation->set_rules('full_name', 'Full Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|xss_clean|matches[conf_password]');
            $this->form_validation->set_rules('conf_password', 'Password', 'trim|xss_clean');
            $this->form_validation->set_rules('contact', 'Contact', 'trim|required|xss_clean');
            $this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean');
            $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
            $this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean');
            $this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
            if ($this->form_validation->run() == FALSE) {

                $this->session->set_userdata("toast_error_message", validation_errors());
                echo json_encode(validation_errors());exit;
            }
            else
            {

                $current_time = date("Y-m-d H:i:s");
                $update_data  = array('user_first_name'=>$_POST['full_name'],
                                     'date_of_birth'=>date("Y-m-d H:i:s",strtotime($_POST['dob_profile'])),
                                     'notification_setting'=>isset($_POST['radio']) ? '1' : '0',
                                     'gender'=>$_POST['gender'],
                                     'country_id'=>$_POST['country'],
                                     'region_id'=>$_POST['state'],
                                     'city_id'=>$_POST['city'],
                                     'user_contact'=>$_POST['contact'],
                                     'modified_on'=>$current_time
                                    );
                if($_POST['password']!="")
                {
                    $update_data['user_password'] = hash('sha256', $this->input->post("password"));
                }
                $insert_result = $this->usermodel->action('update', $update_data,$user_id);
            }
        }

        $user_id   = $_POST['user_id'];
        $user_data = $this->usermodel->get_user_details($user_id,2);
        $user_data->date_of_birth = date("d-m-y",strtotime($user_data->date_of_birth));
        echo json_encode($user_data);exit;
    }

    /*
    * Method Name:  get_wishlist_post
    * Purpose: To get wish list
    * Date: 6 Feb 2017
    * params:
    *      input:  token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              response - Array containing restaurant details
    */
    public function get_wishlist()
    {
        $user_id = $this->session->userdata("user_id");
        $offset  = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $limit   = SEARCH_RESULTS_LIMIT;
        $restaurant_details = $this->restaurantmodel->get_wishlist($user_id, $limit, $offset);

        if ($restaurant_details) {
            $divHtml = ''; $html_arr = array();
			
            foreach ($restaurant_details as $aVal) {
                
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

                $rImage = !empty($aVal['restaurant_image']) ?  $aVal['restaurant_image'] : base_url() . "assets/images/restaurent_no_image_available.jpg";
                $restaurantDetailsPath = base_url() . "restaurant-details/".$aVal['restaurant_detail_url'];
                $divHtml = ' <div class="col-xs-6 col-sm-6 col-md-6 col filterlistbox" id="listbox'.$aVal['restaurant_id'].'">
                                    <div class="filterlisting-box">
									
									<div class="social-icons" id="social_icons_filter_search_'.$aVal['restaurant_id']. '" hidden>
										<a href="javascript:void(0)" id="close_social_icons" onclick="close_social_icons('.$aVal['restaurant_id'].')"><span class="uic-close"></span></a>
										<a href="https://plus.google.com/share?url='.$restaurantDetailsPath.'"  target="_blank" title="Click to share on google plus">
											<div class="icon">
												<i aria-hidden="true" class="fa fa-google-plus"></i>
											</div>
										</a>
										<a href="http://www.facebook.com/sharer.php?u='.$restaurantDetailsPath.'" target="_blank" title="Click to share on facebook">
											<div class="icon">
												<i aria-hidden="true" class="fa fa-facebook"></i>
											</div>
										</a>
										<a href="http://twitter.com/share?text='.$aVal['restaurant_name'].'&url='.$restaurantDetailsPath.'" target="_blank" title="Click to share on Twitter">
											<div class="icon">
												<i aria-hidden="true" class="fa fa-twitter"></i>
											</div>
										</a>
									</div>
									
									
                                       <a href="#" data-id="'.$aVal['restaurant_id'].'" class="favourite active"></a>
                                       <a href="'. $restaurantDetailsPath.'" class="imgbox">
                                          <img src="'.$rImage.'" class="img-responsive">
                                       </a>
                                       <div class="con-box">
                                          <h3><a href="'. $restaurantDetailsPath.'">'.$aVal['restaurant_name'].'</a></h3>
                                          <h4>'.$aVal['street_address1'].', '.$aVal['city_name'].'</h4>
                                          <p>'.$aVal['region_name'].', '.$aVal['cou_name'].'</p>
                                          <div class="topicons">
                                             <div class="ricon">';
											 for($i=0;$i<$aVal['average_spend'];$i++)
											 {
											 
											$divHtml .= ' <span>R</span> ';
                                              } 
                                            $divHtml .= ' </div>
                                             <div class="ratting">
                                                <span class="uic-review-star"></span>'.
                                                $aVal['average_rating']
                                             .'</div>
                                          </div>
                                          <div class="bottomicons">
                                             <div class="timetag">';
                                                if($aResultRes['no_of_active_table'] && $aResultRes['time_slots']['status'])
                                                {
                                                    foreach($aResultRes['time_slots'] as $time)
                                                    {
                                                       if(is_array($time))
                                                        foreach($time as $slot)
                                                        {
                                                            $divHtml .= '<a href="javascript:void(0)" id="'.$slot["slot_id"].'" onclick="redirect_restaurant_details_with_booking_details('.$slot["slot_id"].','.$aVal["restaurant_id"].')">'.$slot['time_slot'].'</a> ';
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                     $divHtml .= '<a href="javascript:void(0)" class="a_link_disabled">No slots available for this restaurant.</a>';
                                                }											 
                                             $divHtml .= '</div>
                                             <a href="javascript:void(0)" class="share" id="share_icon_filter_search_" onclick="show_social_icons('.$aVal['restaurant_id'].')"><span class="uic-share"></span></a>
                                            </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>';
                $html_arr[] =  $divHtml;
            }

            $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
            $result_array['status']  = SUCCESS;
            $result_array['message'] = WISHLIST_FOUND;
            $result_array['current_date'] = $date->format('d-m-Y');
            $result_array['count']  = $this->restaurantmodel->get_wishlist_count($user_id);
            $result_array['offset'] = $offset + $limit;
            $result_array['html_arr'] = $html_arr;
            echo json_encode($result_array);  exit;
        }
		else
		{
			$result_array['status']  = FAIL;
			echo json_encode($result_array);  exit;
		}
    }

    /*
     * Method Name: restaurant_remove_from_wish_list
     * Date: 6 Feb 2017
     * Purpose: Remove restaurant to user wish list
     * params:
     *      input: -
     *      output: response - Array containing status and success/error message.
     */
    public function restaurant_remove_wish_list()
    {
        $restaurant_id = $this->input->post('restaurant_id');
        $result        = $this->restaurantmodel->restaurant_remove_user_wishlist($restaurant_id);
        echo json_encode($result);exit;
    }



     /*
    * Method Name:  get_suggestionlist
    * Purpose: To get suggestion list
    * Date: 6 Feb 2017
    * params:
    *      input:  token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              response - Array containing restaurant details
    */
    public function get_suggestionlist()
    {
        //$latitude = '19.9975'; // constant for testing
        //$longitude = '73.7898';
                
        include($this->config->item('root_path')."assets/geoip/geoipcity.inc");
        include($this->config->item('root_path')."assets/geoip/geoipregionvars.php");
        $gi = geoip_open($this->config->item('root_path').'assets/geoip/GeoLiteCity.dat',GEOIP_STANDARD);
        $ipAddr = $_SERVER['REMOTE_ADDR'];

        $record = geoip_record_by_addr($gi,$ipAddr);
        //echo '<pre>';print_r( $record);exit;
        //$result = array('country'=>$record->country_name,'region'=>$record->region,'city'=>$record->city);
		
        $latitude = $record->latitude; 
        $longitude = $record->longitude;
        geoip_close($gi);

        $user_id  = $this->session->userdata("user_id");       
        $is_all   = 1;
        $distance = SUGGESSTIONS_DISTACE;
        $limit    = SEARCH_RESULTS_LIMIT;
        $restaurant_details = $this->suggesstionmodel->get_suggesstions($distance, $latitude, $longitude, $user_id, $is_all);
		$restaurant_details = json_decode(json_encode($restaurant_details),true);
		
		if ($restaurant_details) {
            $divHtml = ''; $html_arr = array();
            
			foreach ($restaurant_details as $aVal) {
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

                $rImage = !empty($aVal['restaurant_image']) ?  $aVal['restaurant_image'] : base_url() . "assets/images/restaurent_no_image_available.jpg";
                $restaurantDetailsPath = base_url() . "restaurant-details/".$aVal['restaurant_detail_url'];
                $divHtml = ' <div class="col-xs-6 col-sm-6 col-md-6 col filterlistbox" id="listbox'.$aVal['user_id'].'">
                                    <div class="filterlisting-box">
									
									<div class="social-icons" id="social_icons_filter_search_'.$aVal['user_id']. '" hidden>
										<a href="javascript:void(0)" id="close_social_icons" onclick="close_social_icons('.$aVal['user_id'].')"><span class="uic-close"></span></a>
										<a href="https://plus.google.com/share?url='.$restaurantDetailsPath.'"  target="_blank" title="Click to share on google plus">
											<div class="icon">
												<i aria-hidden="true" class="fa fa-google-plus"></i>
											</div>
										</a>
										<a href="http://www.facebook.com/sharer.php?u='.$restaurantDetailsPath.'" target="_blank" title="Click to share on facebook">
											<div class="icon">
												<i aria-hidden="true" class="fa fa-facebook"></i>
											</div>
										</a>
										<a href="http://twitter.com/share?text='.$aVal['restaurant_name'].'&url='.$restaurantDetailsPath.'" target="_blank" title="Click to share on Twitter">
											<div class="icon">
												<i aria-hidden="true" class="fa fa-twitter"></i>
											</div>
										</a>
									</div>
									
									
                                       <a href="#" data-id="'.$aVal['user_id'].'" class="favourite active"></a>
                                       <a href="'. $restaurantDetailsPath.'" class="imgbox">
                                          <img src="'.$rImage.'" class="img-responsive">
                                       </a>
                                       <div class="con-box">
                                          <h3><a href="'. $restaurantDetailsPath.'">'.$aVal['restaurant_name'].'</a></h3>
                                          <h4>'.$aVal['address'].', '.$aVal['city'] .'</h4>
                                          <p>'.$aVal['state'] .', '.$aVal['country'] .'</p>
                                          <div class="topicons">
                                             <div class="ricon">';
											 for($i=0;$i<$aVal['average_spend'];$i++)
											 {
											 
											$divHtml .= ' <span>R</span> ';
                                              } 
                                            $divHtml .= ' </div>
                                             <div class="ratting">
                                                <span class="uic-review-star"></span>'.
                                                $aVal['average_rating']
                                             .'</div>
                                          </div>
                                          <div class="bottomicons">
                                             <div class="timetag">';
                                                if($aResultRes['no_of_active_table'] && $aResultRes['time_slots']['status'])
                                                {
                                                    foreach($aResultRes['time_slots'] as $time)
                                                    {
                                                       if(is_array($time))
                                                        foreach($time as $slot)
                                                        {
                                                            $divHtml .= '<a href="javascript:void(0)" id="'.$slot["slot_id"].'" onclick="redirect_restaurant_details_with_booking_details('.$slot["slot_id"].','.$aVal["user_id"].')">'.$slot['time_slot'].'</a> ';
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                     $divHtml .= '<a href="javascript:void(0)" class="a_link_disabled">No slots available for this restaurant.</a>';
                                                }											 
                                             $divHtml .= '</div>
                                            <a href="javascript:void(0)" class="share" id="share_icon_filter_search_" onclick="show_social_icons('.$aVal['user_id'].')"><span class="uic-share"></span></a>
                                          </div>
                                       </div>
                                    </div>
                                 </div>';
                $html_arr[] =  $divHtml;
            }

            $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
            $result_array['status']  = SUCCESS;
            $result_array['message'] = WISHLIST_FOUND;
            $result_array['current_date'] = $date->format('d-m-Y');
            $result_array['count']  = $this->restaurantmodel->get_wishlist_count($user_id);
            //$result_array['offset'] = $offset + $limit;
            $result_array['html_arr'] = $html_arr;
            echo json_encode($result_array);  exit;
        }
        else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_SUGGESTIONS;
            echo json_encode($result_array);  exit;
        }
    }


    /*
    * Method Name:  getReviewList
    * Purpose: To get wish list
    * Date: 6 Feb 2017
    * params:
    *      input:  token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              response - Array containing restaurant details
    */
    public function getReviewList()
    {
        $user_id = $this->session->userdata("user_id");
		$limit = SEARCH_RESULTS_LIMIT;
        $offset = $this->input->post('offset') ? $this->input->post('offset') : 0;
        $total_user_ratings = $this->restaurantmodel->get_rating_user_wise_total($user_id);
        $user_rating = $this->restaurantmodel->get_rating_user_wise($user_id, $limit, $offset);
        $offset = $offset + $limit;

        if ($user_rating) {
            $divHtml = ''; $html_arr = array();
            foreach ($user_rating as $aVal) {
			$divHtml = ''; 
            $rImage = !empty($aVal['restaurant_hero_image']) ?  $aVal['restaurant_hero_image'] : base_url() . "assets/images/restaurent_no_image_available.png";
            $divHtml .='<div class="listing">
                                    <a href="#" class="thumnailimg">
                                       <img src="'.$rImage.'" class="img-responsive profile_restaurant_image">
                                    </a>
                                    <div class="rightside">
                                       <h4>'.$aVal['restaurant_name'].'</h4>
                                       <p>'.$aVal['review'].'</p>
                                    </div>
                                    <div class="buttonbox">
                                       <p class="rating"><span class="uic-review-star"></span> '.$aVal['rating'].'</p>
                                    </div>
                                 </div> ';
                $html_arr[] =  $divHtml;
            }

            $result_array['status']  = SUCCESS;
            $result_array['message'] = VALID_RATING;
            $result_array['count'] = $total_user_ratings;
            $result_array['offset'] = $offset;
            $result_array['html_arr'] = $html_arr;
            echo json_encode($result_array);  exit;
        }
        else
        {
            $result_array['status']  = FAIL;
            $result_array['message'] = RATING_NOT_FOUND;
            echo json_encode($result_array);  exit;
        }


    }
	
	/*
	* Akshay Deshmukh
    * Method Name:  upload_profile_image
    * Purpose: 
    * Date: 6 Feb 2017
    * params:
    *      input:  token
    *      output: status - FAIL / SUCCESS
    *              message - failure / Success message
    *              response - Array containing restaurant details
    */
	public function upload_profile_image()
	{
		$user_id = $this->session->userdata("user_id");
		if($user_id > 0){
				if (!empty($_FILES)) {
				$config['upload_path'] = MEMBER_IMAGE_PATH;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
				$config['max_height'] = "2160";
				$config['max_width'] = "4096";
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('profile_image')) {
					$upload_error = $this->upload->display_errors();
					$message = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
					$this->session->set_userdata("toast_error_message", $message);
					redirect("front/home");
				} else {
					$upload_error = '';
					$upload_data = $this->upload->data();
					$user_profile_image = $upload_data['file_name'];
				
					
					$current_time = date("Y-m-d H:i:s");
					$update_data  = array(
									 'user_image'=>$user_profile_image,
									 'modified_on'=>$current_time
									);
					$insert_result = $this->usermodel->action('update', $update_data,$user_id);
					$session_profile_image_path = base_url(). "assets/upload/member/".$user_profile_image;
					$this->session->set_userdata("user_image",$session_profile_image_path	 );
					$this->session->set_userdata("toast_message", "Profile image updated successfully.");
					redirect("front/profile");
				}
			}
			else
			{
				$this->session->set_userdata("toast_error_message", "Please select profile image.");
				redirect("front/profile");
			}
		}
		else
		{
			$this->session->set_userdata("toast_error_message", "Please sign in first.");
			redirect(base_url() . "home");
		}
	}


	
}

?>