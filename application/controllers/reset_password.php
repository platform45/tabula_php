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
    $data['message'] = "";
    if( $token )
    {
      $valid_token_user = $this->reset_password_model->check_valid_token( $token );
      if( $valid_token_user > 0 )
      {
        if( $this->input->post() )
        {
          $password = $this->input->post("txtpassword");
          $confirm_password = $this->input->post("txtcnf_password");

          if( ( $password == $confirm_password ) && $password != '' && $confirm_password != '' )
          {
            $update_array = array(
              'user_password' => hash( 'sha256', $password )
            );
            $this->db->where("user_id", $valid_token_user);
            $this->db->update("hzi_usermst", $update_array);
            redirect(base_url("thank_you"));
          }
          else
          {
            if( ($password != $confirm_password) )
            {
              $data['message'] = "New password and confirm password do not match.";
            }
            $data['user_id'] = $valid_token_user;
            $this->load->view("reset_password_view", $data);
          }
        }
        else
        {
          $data['user_id'] = $valid_token_user;
          $this->load->view("reset_password_view", $data);
        }
      }
      else
      {
        redirect(base_url("page_not_found"));
      }
    }
    else
    {
      redirect(base_url("page_not_found"));
    }
  }
}

/* End of file forgot_password.php */
/* Location: ./application/controllers/forgot_password.php */