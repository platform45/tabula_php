<?php
/******************** PAGE DETAILS ********************/
/* \
 * @Description : This is News model which is used
********************************************************/
class Newsmodel extends CI_Model
{
            
    public function __construct()
    {
            parent::__construct();
    }
    
   
    //Webservice model for get News detail 
    public function getNewsCount()
    {
        $this->db->select("`news_id`, `news_title`, `news_date`, `news_desc`, `news_link`, `news_sequence`,IF( news_image = '', '', CONCAT('".base_url()."', '".NEWS_IMAGE_PATH."', news_image) ) AS news_image",FALSE);
        $this->db->where('news_status',1);
        $this->db->where('is_deleted',0);        
        $result = $this->db->get('tab_news');       
        if ($result->num_rows() > 0)
        {                   
               return $result->num_rows();
        } 
        else
        {
            return $result->num_rows();
        }
    }
   //Webservice model for get News detail 
    public function getNews($limit,$offset) {
        $this->db->select("`news_id`, `news_title`, `news_date`, `news_desc`, `news_link`, `news_sequence`,IF( news_image = '', '', CONCAT('".base_url()."', '".NEWS_IMAGE_PATH."', news_image) ) AS news_image",FALSE);
        $this->db->where('news_status',1);
        $this->db->where('is_deleted',0);
        $this->db->order_by('news_date',"desc");
        $this->db->limit( $limit, $offset );
        $result = $this->db->get('tab_news');       
        if ($result->num_rows() > 0) {                   
               return $result->result_array();
        } else {
            return 0;
        }
    }
    
    
    public function getNewsDetails($news_id) {
        $this->db->select("`news_id`, `news_title`, `news_date`, `news_desc`, `news_link`, `news_description_link`, `news_sequence`,IF( news_image = '', '', CONCAT('".base_url()."', '".NEWS_IMAGE_PATH."', news_image) ) AS news_image",FALSE);
        $this->db->where('news_status',1);
        $this->db->where('is_deleted',0);
        $this->db->where('news_id',$news_id);
        $result = $this->db->get('tab_news');       
        if ($result->num_rows() > 0) {                   
               return $result->result_array();
        } else {
            return 0;
        }
    }

    public function getNewsDetailsByDescriptionLink($news_description_link) {
        $this->db->select("`news_id`, `news_title`, 
                            DATE_FORMAT(news_date,'%d %M %Y') as news_date, 
                            `news_desc`, 
                            `news_link`,
                            `news_description_link`, `news_sequence`,
                            IF( news_image = '', '', CONCAT('".base_url()."', '".NEWS_IMAGE_PATH."', news_image) ) AS news_image",FALSE);
        $this->db->where('news_status',1);
        $this->db->where('is_deleted',0);
        $this->db->where('news_description_link',$news_description_link);
        $result = $this->db->get('tab_news');
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return 0;
        }
    }
}
?>