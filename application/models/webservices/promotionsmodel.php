<?php

/*
 * @Description : This is Promotion model which is used
 */

class Promotionsmodel extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  
  public function getPromotionCount()
  {
    $this->db->select("`promotion_id`, `promotion_title`, `promotion_image`, `promotion_desc`, `promotion_pdf`, `promotion_status`, IF( promotion_image = '', '', CONCAT('".base_url()."', '".promotion_IMAGE_PATH."', promotion_image) ) AS promotion_image, IF( promotion_pdf = '', '', CONCAT('".base_url()."', '".PROMOTION_PDF_PATH."', promotion_pdf) ) AS promotion_pdf", FALSE);
    $this->db->where('promotion_status', 1);
    $this->db->where('is_deleted', 0);
    $this->db->order_by('promotion_id', 'DESC');
    $result = $this->db->get('tab_promotion');
    if ($result->num_rows() > 0)
    {
      return $result->num_rows();
    }
    else
    {
      return 0;
    }
  }
  
  /*
   * Method Name: getPromotion
   * Purpose: get details to add promotions
   * params:
   *     params:
   *      input: limit, offset
   *      output: result array
   *              
   */

  //Webservice model for get Promotion detail 
  public function getPromotion($limit, $offset)
  {
    $this->db->select("`promotion_id`, `promotion_title`, `promotion_image`, `promotion_desc`, `promotion_pdf`, `promotion_status`, IF( promotion_image = '', '', CONCAT('".base_url()."', '".promotion_IMAGE_PATH."', promotion_image) ) AS promotion_image, IF( promotion_pdf = '', '', CONCAT('".base_url()."', '".PROMOTION_PDF_PATH."', promotion_pdf) ) AS promotion_pdf", FALSE);
    $this->db->where('promotion_status', 1);
    $this->db->where('is_deleted', 0);
    $this->db->limit($limit, $offset);
    $this->db->order_by('promotion_id', 'DESC');
    $result = $this->db->get('tab_promotion');
    if ($result->num_rows() > 0)
    {
      return $result->result_array();
    }
    else
    {
      return 0;
    }
  }
  /*
   * Method Name: getPromotionDetails
   * Purpose: get details to add promotions
   * params:
   *      input:  promotion_id
   *      output: result array
   *              
   *              
   */
  public function getPromotionDetails($promotion_id) 
  {
     $this->db->select("`promotion_id`, `promotion_title`, `promotion_image`, `promotion_desc`, `promotion_pdf`, `promotion_status`, IF( promotion_image = '', '', CONCAT('".base_url()."', '".promotion_IMAGE_PATH."', promotion_image) ) AS promotion_image, IF( promotion_pdf = '', '', CONCAT('".base_url()."', '".PROMOTION_PDF_PATH."', promotion_pdf) ) AS promotion_pdf", FALSE);
    $this->db->where('promotion_status', 1);
    $this->db->where('is_deleted', 0);
    $this->db->where('promotion_id', $promotion_id);
    $result = $this->db->get('tab_promotion');
    if ($result->num_rows() > 0) {
      return $result->result_array();
    } else {
      return 0;
    }
  }
  
  /*
   * Method Name: add_promotions
   * Purpose: add promotion to database
   * params:
   *      input: promotions_array  data array of promotions
   *      output: insert_id
   *              
   *              
   */
  public function add_promotions($promotions_array) 
  {
    $this->db->insert("tab_promotion",$promotions_array);  
    return $this->db->insert_id();
  }

}

?>