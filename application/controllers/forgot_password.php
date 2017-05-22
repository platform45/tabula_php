<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
  Class that contains function for forgot password
 */
class Forgot_password extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->model('reset_password_model');
  }

  /*
   * Method Name: Reset password view
   * Purpose: Reset password.
   * params:
   *      input: token
   *      output: status - FAIL / SUCCESS
   *              message - Sucess or failure message
   */
  public function index()
  {
    $token = $this->input->get("token");
    $data = array();
    $data['success_message'] = $data['error_message'] = "";

    if( $this->input->post() )
    {
      // Validate token
      $token = $this->input->get("token");
      $valid_token_user = $this->reset_password_model->check_valid_token( $token );
      if( $valid_token_user == 0 )
      {
        $data['captcha'] = $this->get_captcha();
        $data['error_message'] = "Token Expired.";
        $data['user_id'] = $valid_token_user;
        $this->load->view("reset_password_view", $data);
      }

      $password = $this->input->post("password_text");
      $confirm_password = $this->input->post("confirm_password_text");
      $user_id = $this->input->post("user_id");

      if( $this->session->userdata('reset_password_captcha_word') == $this->input->post("secure_code_text") )
      {
        if( ( $password == $confirm_password ) && $password != '' && $confirm_password != '' )
        {
          $update_array = array(
            'user_password' => hash( 'sha256', $password ),
            'forgot_password_hash' => ""
          );
          $this->db->where("user_id", $user_id);
          $this->db->update("usermst", $update_array);
          $data['success_message'] = "Your password has been changed successfully. Please login using your app.";
          $data['user_id'] = $user_id;

          $data['captcha'] = $this->get_captcha();
          $this->load->view("reset_password_view", $data);
        }
        else
        {
          $data['captcha'] = $this->get_captcha();
          if( ($password != $confirm_password) )
          {
            $data['error_message'] = "New password and confirm password do not match.";
          }
          $data['user_id'] = $user_id;
          $this->load->view("reset_password_view", $data);
        }
      }
      else
      {
        $data['captcha'] = $this->get_captcha();
        $data['error_message'] = "Please enter valid captcha";
        $data['user_id'] = $user_id;
        $this->load->view("reset_password_view", $data);
      }
    }
    else if( $token )
    {
      $data['captcha'] = $this->get_captcha();

      $valid_token_user = $this->reset_password_model->check_valid_token( $token );
      if( $valid_token_user > 0 )
      {
        $data['user_id'] = $valid_token_user;
        $this->load->view("reset_password_view", $data);
      }
      else
      {
        redirect( base_url("admin") );
      }
    }
    else
    {
      redirect( base_url("admin") );
    }
  }

  // Function to generate captcha
  private function get_captcha()
  {
    $this->load->helper('captcha');

    $vals = array(
                  'word' => rand_string(5),
                  'img_path' => './assets/captcha/',
                  'img_url' => base_url() . 'assets/captcha/',
                  'img_width' => 150,
                  'img_height' => 35,
                  'expiration' => 7200
                );
    $data['captcha'] = create_captcha($vals);
    $this->session->set_userdata('reset_password_captcha_word',$data['captcha']['word']);

    return $data['captcha'];
  }
}

/* End of file forgot_password.php */
/* Location: ./application/controllers/forgot_password.php */