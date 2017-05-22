<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'libraries/REST_Controller.php';

/*
  Class that contains function for content
 */
class Content extends REST_Controller {

  // Constructor
  function __construct()
  {
    parent::__construct();

    // Validate token
    if($this->uri->segment(3)!="terms")
    {
    
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
    }
    $this->load->model( 'webservices/contentmodel', 'contentmodel', TRUE );
  }

  /*
   * Method Name: privacy_policy
   * Purpose: To get privacy_policy content.
   * params:
   *      input: token
   *      output: status - FAIL / SUCCESS
   *              message - failure / Success message
   *              content
   */
	public function privacy_policy_post()
	{
    $result_array = array();

    $privacy_policy = $this->contentmodel->get_content('privacy-policy');
    
    if( $privacy_policy )
    {
      $result_array['status'] = SUCCESS;
      $result_array['message'] = VALID_CONTENT;
      $result_array['response']['content'] = $privacy_policy;

      $this->response( $result_array ); // 200 being the HTTP response code
    }
    else
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = INVALID_CONTENT;
      $this->response( $result_array ); // 404 being the HTTP response code
    }
  }
  
  
  /*
   * Method Name: contact
   * Purpose: To get contact content.
   * params:
   *      input: token
   *      output: status - FAIL / SUCCESS
   *              message - failure / Success message
   *              content
   */
	public function contact_post()
	{
    $result_array = array();

    $privacy_policy = $this->contentmodel->get_content('contact-us');
    
    if( $privacy_policy )
    {
      $result_array['status'] = SUCCESS;
      $result_array['message'] = VALID_CONTENT;
      $result_array['response']['content'] = $privacy_policy;

      $this->response( $result_array ); // 200 being the HTTP response code
    }
    else
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = INVALID_CONTENT;
      $this->response( $result_array ); // 404 being the HTTP response code
    }
  }
  
  /*
   * Method Name: Terms
   * Purpose: To get terms and condition content.
   * params:
   *      input: token
   *      output: status - FAIL / SUCCESS
   *              message - failure / Success message
   *              content
   */
	public function terms_post()
	{
    $result_array = array();

    $terms_content = $this->contentmodel->get_content('terms-and-condition');
    if( $terms_content )
    {
      $result_array['status'] = SUCCESS;
      $result_array['message'] = VALID_CONTENT;
      $result_array['response']['content'] = $terms_content;

      $this->response( $result_array ); // 200 being the HTTP response code
    }
    else
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = INVALID_CONTENT;
      $this->response( $result_array ); // 404 being the HTTP response code
    }
  }

  /*
   * Method Name: About us
   * Purpose: To get about us content.
   * params:
   *      input: token
   *      output: status - FAIL / SUCCESS
   *              message - failure / Success message
   *              content
   */
  public function about_us_post()
  {
    $result_array = array();

    $terms_content = $this->contentmodel->get_content('about-us');
    if( $terms_content )
    {
      $result_array['status'] = SUCCESS;
      $result_array['message'] = VALID_CONTENT;
      $result_array['response']['content'] = $terms_content;

      $this->response( $result_array ); // 200 being the HTTP response code
    }
    else
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = INVALID_CONTENT;
      $this->response( $result_array ); // 404 being the HTTP response code
    }
  }

  /*
   * Method Name: FAQ
   * Purpose: To get FAQ content.
   * params:
   *      input: token
   *      output: status - FAIL / SUCCESS
   *              message - failure / Success message
   *              faq
   */
  public function faq_post()
  {
    $result_array = array();

    $faq_content = $this->contentmodel->get_faq();
    if( $faq_content )
    {
      $result_array['status'] = SUCCESS;
      $result_array['message'] = VALID_CONTENT;
      $result_array['response']['faq'] = $faq_content;
      
      $this->response( $result_array ); // 200 being the HTTP response code
    }
    else
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = INVALID_CONTENT;
      $this->response( $result_array ); // 404 being the HTTP response code
    }
  }

}

/* End of file content.php */
/* Location: ./application/controllers/webservices/content.php */