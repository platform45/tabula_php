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

class News extends REST_Controller {

   
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
    $this->load->model( 'webservices/newsmodel', 'newsmodel', TRUE );
  }


  /*
     * Method Name: get_news
     * Purpose: Get all  news 
     * params:
     *      input: access Token
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              newsDetails - Array containing all  News
     *              
     */
    public function get_news_POST() {             
         $offset = $this->post('offset') ? $this->post('offset') : 0;
         $limit = SEARCH_RESULTS_LIMIT;
       
        $newsDataCount = $this->newsmodel->getNewsCount();
        $newsData = $this->newsmodel->getNews($limit,$offset);
        $offset = $offset + $limit;
       if ($newsData) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = ALL_NEWS;
            $retArr['news'] = $newsData;
            $retArr['offset'] = $offset;
            $retArr['total_count'] = $newsDataCount;
            $this->response($retArr); // 404 being the HTTP response code
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = NO_NEWS;
            $this->response($retArr); // 404 being the HTTP response code
        }
        
    }
    
    
    /*
     * Method Name: get_news_detail
     * Purpose: Get news detail
     * params:
     *      input: 
     *      output: status - FAIL / SUCCESS
     *              message - Sucess or failure message
     *              newsDetails - Array containing all News
     *              
     */
    public function get_news_detail_POST() {  
        $news_id = $this->post('news_id') ? $this->post('news_id') : 0;
        $newsData = $this->newsmodel->getNewsDetails($news_id);
       if ($newsData) {
            $retArr['status'] = SUCCESS;
            $retArr['message'] = ALL_NEWS;
            $retArr['news'] = $newsData;
            $this->response($retArr); // 404 being the HTTP response code
        }
        else {
            $retArr['status'] = FAIL;
            $retArr['message'] = NO_NEWS;
            $this->response($retArr); // 404 being the HTTP response code
        }
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */