<?php
/******************** PAGE DETAILS ********************/
/* @Programmer  : TUSHAR KOCHAR
 * @Maintainer  : TUSHAR KOCHAR
 * @Created     : 5 Aug 2016
 * @Modified    : 
 * @Description : This is channel model which is used
 * to show top 10 channels as well as all channels.
********************************************************/
class Videomodel extends CI_Model
{
            
    public function __construct()
    {
            parent::__construct();
    }
    
     public function getChannels() {
       $this->db->select("`channel_id`, `channel_title`, `channel_oneliner`, `channel_image`, `channel_sequence`",FALSE);
       
        $this->db->where(
                    array(
                        'is_deleted' => 0,
                        'channel_status' => 1
                    ));       
        
        $this->db->order_by('channel_sequence','ASC');
        $result = $this->db->get('channel');
        if($result->num_rows()){
           
                return $result->result_array();
        }
        else
            return 0;
        
    }

    public function getCategories($channel_id = 0) {
        $this->db->select("`cat_id`, `channel_id`, `category_name`, `category_desc`", FALSE);
        if($channel_id)
            $this->db->where('channel_id',$channel_id);
        $this->db->where(
                array(
                    'is_delete' => 0,
                    'category_status' => 1
        ));

        $result = $this->db->get('category');        
        if ($result->num_rows()) {
        return  $result->result_array();
        }
        else
        {
            return 0;
        }
}

        public function getVideo($channel_id,$category_id="",$search_keyword="",$page_index=0)
        {
            $offset  = $page_index* PAGE_LIMIT;
            $this->db->select("v.`vid_id`,MATCH(v.video_title) AGAINST ('$search_keyword') as relevance,v.video_share_count,chan.channel_icon,chan.channel_oneliner,chan.channel_image,chan.channel_icon,chan.channel_share_count,cat.category_icon,cat.created_on,v.video,v.video_type, v.`channel_id`,chan.channel_title,cat.category_name, v.`category_id`, v.`video_title`, v.`video_desc`, v.`video_status`, v.`is_deleted`, v.`created_on`,chan.`created_on` as channel_date, v.`modified_on`", FALSE);
            $this->db->from('video v');
            $this->db->join('channel chan', 'v.channel_id = chan.channel_id', 'left');
            $this->db->join('category cat', 'v.category_id = cat.cat_id', 'left');
            $this->db->group_by("v.vid_id");
            $this->db->where(
                    array(
                        'v.is_deleted' => 0,
                        'v.video_status' => 1
            ));
            if($channel_id)
               $this->db->where('v.channel_id',$channel_id); 
            if($category_id)
               $this->db->where('v.category_id',$category_id);
            if($search_keyword) 
            {
//                
                $this->db->where("v.video_title LIKE '%$search_keyword%'");
                
            }
            $this->db->order_by('relevance');
            $this->db->limit(PAGE_LIMIT,$offset);
            $result = $this->db->get('video');
            if ($result->num_rows()) {            
                    return $result->result_array();
            }
            else
                return 0;
        }
        
      public function getFreeVideos($channel_id = 0) {
        $this->db->select('f.`free_id`,v.created_on,v.video_title,v.video_desc,v.video_share_count,v.video,f.`channel_id`, f.`vid_id`, f.`sequence`');
        $this->db->from('freevideo f');
        $this->db->join('video v', 'v.vid_id = f.vid_id');
        $this->db->where('f.channel_id',$channel_id);
        $this->db->order_by('sequence',"Asc");
        $this->db->group_by('f.vid_id');
        $this->db->limit('10');
        $result = $this->db->get();
        if ($result->num_rows()) {
        return  $result->result_array();
        }
      }
         public function getAds($channel_id) {
        $this->db->select("a.ad_id, a.channel_id, a.ad_title,a.ad_video, a.ad_description, a.ad_status, a.is_deleted", FALSE);
        $this->db->from('ads a');
         
        $this->db->where(
                array(
                    'a.is_deleted' => 0,
                    "a.ad_status"=>1,
                    "a.channel_id"=>$channel_id
                    
        ));  
        $this->db->group_by("a.ad_id");
        $this->db->order_by('ad_id', 'RANDOM');
        $this->db->limit(1);
        $result = $this->db->get('ads');

        if ($result->num_rows()) {
           
              $aResult = $result->row();
              return  base_url()."assets/ads/".$aResult->ad_video;
            
        }
        else
            return "";
    }
    
     public function getVideobyCategory($channel_id,$category_id,$page_index)
        {
            $offset  = $page_index* PAGE_LIMIT;
            //$offset  = $page_index * 2;
            $this->db->select("v.`vid_id`,v.video_share_count,cat.category_icon,cat.created_on,v.video,v.video_type, v.`channel_id`,cat.category_name, v.`category_id`, v.`video_title`, v.`video_desc`, v.`video_status`, v.`is_deleted`, v.`created_on`, v.`modified_on`", FALSE);
            $this->db->from('video v');           
            $this->db->join('category cat', 'v.category_id = cat.cat_id', 'left');
            $this->db->group_by("v.vid_id");
            $this->db->where(
                    array(
                        'v.is_deleted' => 0,
                        'v.video_status' => 1
            ));
            if($channel_id)
               $this->db->where('v.channel_id',$channel_id); 
            if($category_id)
               $this->db->where('v.category_id',$category_id);                           
              
            $this->db->order_by('v.video_title', 'ASC');
            $this->db->limit(PAGE_LIMIT,$offset);
           
            $result = $this->db->get('video');
            // echo $this->db->last_query();die;
            if ($result->num_rows()) {            
                    return $result->result_array();
            }
            else
                return 0;
        }
        
        function videoShareCount($video_id)
        {
            $this->db->where('vid_id',$video_id );
            $this->db->set('video_share_count', 'video_share_count+1', FALSE);
            $this->db->update('video');
            return $video_id;
        }
}
?>