<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('webservices/contentmodel', 'contentmodel', TRUE);
		$this->load->model('webservices/notificationmodel', 'notificationmodel', TRUE );
    }


    /*
     * Method Name: index
     * Purpose:  to load index page with its data
     * params:
     *      input:
     *      output: -
     *
     */
    public function index()
    {
		$this->session->unset_userdata('book_slot_id');
		$this->session->unset_userdata('modified_booking');
					
        $country_id = STATIC_COUNTRY_ID;
        $data['states'] = $this->usermodel->get_state_by_country($country_id);
        $restaurant_details = shuffle_assoc_array($this->restaurantmodel->get_restuarant_add());
        if(count($restaurant_details) > 6)
        {
            $restaurant_details = array_slice($restaurant_details, 0, 6);
        }
        $data['restaurant_details'] = $restaurant_details;
        $page = $this->uri->segment(1);
        if($page != "home")
        {
            $data['content'] = $this->contentmodel->get_content($page);
        }
        else
        {
            $data['content'] = '';
        }
        $this->template_front->view('index', $data);
    }

    /* Method Name: Registration
    * Purpose: Register a user.
    * params:
    *      input: form fields
    *      output: status - FAIL / SUCCESS
    *              message - Sucess or failure message
    *              userDetails - Array containing all the details for logged in user, if login is successful.
    *              accesstoken
    */
    public function registration()
    {
        // $user_type = $this->input->post("user_type") ? $this->input->post("user_type") : "";
        //Changed by Akshay deshmukh: Reason - We allow only user_type 2 for front registration
        $user_type = 2;

        $result_array = array();
        if ($user_type == 2) { //App user
            $this->register_app_user();
        } else if ($user_type == 3) { //Restaurant user
            $this->register_restaurant_user();
        }
    }

    // Register app user
    function register_app_user()
    {
        // Form fields

        if ($_POST) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('first_name', 'Full Name', 'trim|required|xss_clean|callback_check_username');
            $this->form_validation->set_rules('email_address', 'Email', 'trim|required|valid_email|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|matches[conf_password]');
            $this->form_validation->set_rules('conf_password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('contact_number', 'Contact', 'trim|required|xss_clean');
            $this->form_validation->set_rules('country', 'Country', 'trim|required|xss_clean');
            $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
            $this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean');
            $this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_userdata("toast_error_message", validation_errors());
                redirect("front/home");
            } else {
                $first_name = $this->input->post("first_name") ? $this->input->post("first_name") : "";
                $user_type = $this->input->post("user_type") ? $this->input->post("user_type") : "";
                $email = $this->input->post("email_address") ? $this->input->post("email_address") : "";
                $date_of_birth = $this->input->post("dob") ? $this->input->post("dob") : "";
                $password_before_hash = $this->input->post("password") ? $this->input->post("password") : "";
                $password = $this->input->post("password") ? hash('sha256', $this->input->post("password")) : "";
                $contact_number = $this->input->post("contact_number") ? $this->input->post("contact_number") : "";
                $country = $this->input->post("country") ? $this->input->post("country") : "";
                $gender = $this->input->post("gender") ? $this->input->post("gender") : "";
                $state = $this->input->post("state") ? $this->input->post("state") : "";
                $city = $this->input->post("city") ? $this->input->post("city") : "";
                $is_subscribe = $this->input->post("is_subscriber") ? $this->input->post("is_subscriber") : 0;

				$date_of_birth = date('Y-m-d H:i:s', strtotime($date_of_birth));
				//print_r($date_of_birth);die;
				
                $result_array = array();
                // Validations Pass
                // Check if email already exist
                $email_exists = $this->usermodel->check_email_exists($email);

                if ($email_exists) {
                    $this->session->set_userdata("toast_error_message", EMAIL_EXISTS);
                    redirect("front/home");
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
                        $message = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
                        $this->session->set_userdata("toast_error_message", $message);
                        redirect("front/home");
                    } else {
                        $upload_error = '';
                        $upload_data = $this->upload->data();
                        $user_profile_image = $upload_data['file_name'];
                    }
                }

                $current_time = date("Y-m-d H:i:s");
                $user_insert_data = array(
                    'user_first_name' => $first_name,
                    'user_last_name' => "",
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
                    'user_access_token' => "",
                    'is_deleted' => '0',
                    'created_on' => $current_time
                );
                $insert_result = $this->usermodel->action('insert', $user_insert_data);
                if ($insert_result > 0) {
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
                    $this->session->set_userdata("toast_message", REGISTRATION_SUCCESS);
                    redirect("front/home");
                } else {
                    $this->session->set_userdata("toast_error_message", REGISTRATION_FAILED);
                    redirect("front/home");
                }
            }
        } else {
            $this->session->set_userdata("toast_error_message", REGISTRATION_FAILED . "333");
            redirect("front/home");
        }
    }

    //Function for checking the email address
    public function check_email_exist($user_id = 0)
    {
        $email = $this->input->post("title");
        $this->db->select("user_id");
        $this->db->where("is_deleted", '0');
        $this->db->where("user_email", $email);
        if ($user_id)
            $this->db->where("user_id <>", $user_id);
        $result = $this->db->get("usermst");
        if ($result->num_rows() > 0) {
            echo "false";
        } else {
            echo "true";
        }
    }

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
                'protocol' => 'smtp',
                'smtp_host' => 'mail.tabula.mobi',
                'smtp_port' => '25',
                'smtp_user' => 'noreply@tabula.mobi', // change it to yours
                'smtp_pass' => 'Tabula@123', // change it to yours
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

    public function login()
    {
        $email = $this->input->post("email") ? $this->input->post("email") : "";
        $password = $this->input->post("password") ? hash('sha256', $this->input->post("password")) : "";
        $user_type = $this->input->post("user_type") ? $this->input->post("user_type") : "";
        $result_array = array();

        $this->form_validation->set_rules('email', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('user_type', 'User type', 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_userdata("toast_error_message", validation_errors());
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            echo json_encode($result_array);
        }

        // Invalid user type
        if ($user_type != 2 && $user_type != 3) {
            $this->session->set_userdata("toast_error_message", EMPTY_INPUT);
            $result_array['status'] = FAIL;
            $result_array['message'] = EMPTY_INPUT;
            echo json_encode($result_array);
        }

        // Email address validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_EMAIL;
            echo json_encode($result_array);
        }
        // If all validations pass proceed to login
        $user_details = $this->usermodel->check_user_exists($email, $password, $user_type);
        if ($user_details) {
            $this->session->set_userdata(array(
                "user_id" => $user_details->user_id,
                "user_image" => $user_details->user_image,
                "user_type" => $user_details->user_type,
                "user_first_name" => $user_details->user_first_name
            ));

            $this->session->set_userdata("toast_message", "You have logged in succesfully.");
            $result_array['status'] = SUCCESS;
            $result_array['message'] = VALID_USER_CREDENTIALS;
            echo json_encode($result_array);
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = INVALID_USER_CREDENTIALS;
            echo json_encode($result_array);
        }
    }


    public function logout()
    {
		
        $this->session->set_userdata(array(
            "user_id" => '',
            "user_image" => '',
            "user_type" => '',
            "user_first_name" => ''
        ));
		
		$this->session->set_userdata("toast_message", "You have been successfully logged out.");
        redirect("home");
    }
	
    
  /* Method Name: forget_password
   * Purpose: forget password functionality
   * params:
   *      input: form fields
   *      output: status - FAIL / SUCCESS
   *              message - Sucess or failure message
   *              userDetails - Array containing all the details for logged in user, if login is successful.
   *              accesstoken
   */
 public function forget_password() {
		$email = $this->input->post("email") ? $this->input->post("email") : "";
        $result_array = array();
			
        // Empty data, i.e. improper data validation
        if (empty($email)) {
            //$this->session->set_userdata("toast_error_message", "Please enter email.");
			$result_array['status'] = FAIL;
            $result_array['message'] = INVALID_EMAIL;
            echo json_encode($result_array); die;
            //redirect("front/home");
        }

        // Email address validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //$this->session->set_userdata("toast_error_message", INVALID_EMAIL);
			$result_array['status'] = FAIL;
            $result_array['message'] = INVALID_EMAIL;
            echo json_encode($result_array); die;
            //redirect("front/home");

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
                    $this->session->set_userdata("toast_message", EMAIL_SENT);
					$result_array['status'] = SUCCESS;
					echo json_encode($result_array); die;
                    //redirect("front/home");
                } else {
                    //$this->session->set_userdata("toast_error_message", EMAIL_SEND_FAILED);
					$result_array['status'] = FAIL;
					$result_array['message'] = EMAIL_SEND_FAILED;
					echo json_encode($result_array); die;
                    //redirect("front/home");
                }
            } else {
                //$this->session->set_userdata("toast_error_message", ACCOUNT_INACTIVE);
				$result_array['status'] = FAIL;
				$result_array['message'] = ACCOUNT_INACTIVE;
				echo json_encode($result_array); die;
                //redirect("front/home");
            }
        } else {
            //$this->session->set_userdata("toast_error_message", EMAIL_NOT_FOUND);
			$result_array['status'] = FAIL;
			$result_array['message'] = EMAIL_NOT_FOUND;
			echo json_encode($result_array); die;
            //redirect("front/home");
        }
    }
}

?>