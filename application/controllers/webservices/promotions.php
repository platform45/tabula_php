<?php

/* * ****************** PAGE DETAILS ******************* */
/* @Programmer  : KS
 * @Maintainer  : KS
 * @Created     : 5 Aug 2016
 * @Modified    : 
 * @Description : This is Event search controller which is used
 * to show top 10 channels as well as all channels.
 * ****************************************************** */

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Promotions extends REST_Controller {

  function __construct() {
    parent::__construct();

    // Validate token
    $api_key = ( $this->get('token') ) ? $this->get('token') : $this->post('token');
    $user_id = ( $this->get('user_id') ) ? $this->get('user_id') : $this->post('user_id');

    if ($api_key)
    {
      $api_status = validate_api_key($api_key);
      $api_status = json_decode($api_status);
      if ($api_status->status != SUCCESS)
      {
        echo json_encode(array("status" => $api_status->status, "message" => $api_status->message));
        die;
      }
    } 
    else 
    {
      echo json_encode(array("status" => FAIL, "message" => NO_TOKEN_MESSAGE));
      die;
    }
    $this->load->model('webservices/usermodel', 'usermodel', TRUE);
    $this->load->model('webservices/Promotionsmodel', 'promotionsmodel', TRUE);
//      $this->load->model('admin/promotionmodel', 'promotionmodel123', TRUE);
//      $this->load->model( 'webservices/notificationmodel', 'notificationmodel', TRUE );
  }

  /*
   * Method Name: get_promotions
   * Purpose: Get all  promotions
   * params:
   *      input: access Token
   *      output: status - FAIL / SUCCESS
   *              message - Sucess or failure message
   *              details - Array containing all  promotions
   *              
   */

  public function get_promotions_POST() {
    $offset = $this->post('offset') ? $this->post('offset') : 0;
    $limit = SEARCH_RESULTS_LIMIT;
    $promotion_data_count = $this->promotionsmodel->getPromotionCount();
    $promotion_data = $this->promotionsmodel->getPromotion($limit, $offset);
    $offset = $offset + $limit;
    if ($promotion_data) {
      $retArr['status'] = SUCCESS;
      $retArr['message'] = ALL_PROMOTIONS;
      $retArr['promotions'] = $promotion_data;
      $retArr['offset'] = $offset;
      $retArr['total_count'] = $promotion_data_count;
      $this->response($retArr); // 404 being the HTTP response code
    } else {
      $retArr['status'] = FAIL;
      $retArr['message'] = NO_PROMOTIONS;
      $this->response($retArr); // 404 being the HTTP response code
    }
  }
  /*
   * Method Name: add_promotions
   * Purpose: get details from application to add promotions
   * params:
   *      input: title, description, promotion_image, access Token
   *      output: status - FAIL / SUCCESS
   *              message - Sucess or failure message
   */
  public function add_promotions_POST() {
    $restaurant_id = $this->post('restaurant_id') ? $this->post('restaurant_id') : 0;
    $title = $this->post('title') ? $this->post('title') : 0;
    $description = $this->post('description') ? $this->post('description') : 0;
    
    // validate input
    if($title=="" || $description=="" || $restaurant_id <= 0 )
    {
      $result_array['status'] = FAIL;
      $result_array['message'] = EMPTY_INPUT;
      $this->response( $result_array );
    }
    
    // upload promotion image file
    if (!empty($_FILES['promotion_image']['name'])) 
    {      
      $file_name = $this->strip_junk($_FILES['promotion_image']['name']);
      $config['upload_path'] = promotion_IMAGE_PATH;
      $config['allowed_types'] = 'jpg|jpeg|png';
      $config['max_size'] = MAX_UPOAD_IMAGE_SIZE;
      $config['max_height'] = "2160";
      $config['max_width'] = "4096";
      $this->load->library('upload', $config);
      if (!$this->upload->do_upload('promotion_image'))
      {
        $upload_error = $this->upload->display_errors();
        $result_array['status'] = FAIL;
        $result_array['message'] = FILE_UPLOAD_FAILED . "<br>" . $upload_error;
        $this->response($result_array); 
      } 
      else
      {
        $upload_error = '';
        $upload_data = $this->upload->data();
        $promtion_image = $upload_data['file_name'];
      }
    }
    // upload promotion pdf file   
   if (!empty($_FILES['pdf']['name']))
    {    
       
      $file_name = $this->strip_junk($_FILES['pdf']['name']);
      $config = array(
          'upload_path' => PROMOTION_PDF_PATH,
          'allowed_types' => "pdf",
          'overwrite' => FALSE,
          'max_size' => MAX_UPOAD_PDF_SIZE,
          'file_name' => $file_name
      );
      $this->load->library('upload', $config);
      $this->upload->initialize($config);
      if ($this->upload->do_upload('pdf'))
      {
        $upload_error = '';
        $upload_data = $this->upload->data();
        $promtion_pdf = $upload_data['file_name'];
      }
      else
      {
        $upload_error = $this->upload->display_errors();
        $result_array['status'] = FAIL;
        $result_array['message'] = PDF_FILE_UPLOAD_FAILED . "<br>" . $upload_error;
        $this->response($result_array); 
      }
    }  
    // promotion array to insert in database
    $promotions_array = array(
        "promotion_title" => $title,
        "promotion_image" => $promtion_image,
        "promotion_desc"  => $description,
        "promotion_pdf"   => $promtion_pdf,
        "restaurant_id" => $restaurant_id
    );
    // insert data in database
    $promotion_id = $this->promotionsmodel->add_promotions($promotions_array);
    // return response
    if ($promotion_id)
    {
      $retArr['status'] = SUCCESS;
      $retArr['message'] = ADD_PROMOTIONS;
      $retArr['promotion_id'] = $promotion_id;
      $this->response($retArr); // 404 being the HTTP response code
    } 
    else 
    {
      $retArr['status'] = FAIL;
      $retArr['message'] = UNABLE_ADD_PROMOTIONS;
      $this->response($retArr); // 404 being the HTTP response code
    }
  }

  /*
   * Method Name: strip_junk
   * Purpose: Get strip junk funtion 
   * params:
   *      input: raw string 
   *      output: string after removing junk
   *              
   */

  function strip_junk($string) {
    $string = str_replace(" ", "-", trim($string));
    $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
    $string = strtolower($string);
    return $string;
  }

  /*
   * Method Name: get_promotions_detail
   * Purpose: Get news detail
   * params:
   *      input: 
   *      output: status - FAIL / SUCCESS
   *              message - Sucess or failure message
   *             Details - Array containing all promotion
   *              
   */

  public function get_promotions_detail_POST() {
    $promotion_id = $this->post('promotion_id') ? $this->post('promotion_id') : 0;
    $promotion_data = $this->promotionsmodel->getPromotionDetails($promotion_id);
    if ($promotion_data) {
      $retArr['status'] = SUCCESS;
      $retArr['message'] = ALL_PROMOTIONS;
      $retArr['promotions'] = $promotion_data;
      $this->response($retArr); // 404 being the HTTP response code
    } else {
      $retArr['status'] = FAIL;
      $retArr['message'] = NO_PROMOTIONS;
      $this->response($retArr); // 404 being the HTTP response code
    }
  }

//  public function test_promotion_post()
//  {
//      $type = "NEWS";
//      $text_message = 'New news "' . $this->input->post('txttitle') . '" has been added' ;
//      $abc = $this->notificationmodel->news_promotion_push_notification($type,$text_message);
////      $promotion_id = $this->input->post('promotion_id');
////      $query = $this->promotionmodel123->update_status($promotion_id);
//      $this->response($abc);
//  }

}

/* End of file promotion.php */
/* Location: ./application/controllers/webservices/promotion.php */
