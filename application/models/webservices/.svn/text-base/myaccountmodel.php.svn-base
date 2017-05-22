<?php
/******************** PAGE DETAILS ********************/
/* @Programmer  : TUSHAR KOCHAR
 * @Maintainer  : TUSHAR KOCHAR
 * @Created     : 5 Aug 2016
 * @Modified    : 
 * @Description : This is channel model which is used
 * to show top 10 channels as well as all channels.
********************************************************/
class Myaccountmodel extends CI_Model
{
            
    public function __construct()
    {
            parent::__construct();
    }
    
    public function mychannel($page_index=0,$user_id)
    {
        $offset  = $page_index* PAGE_LIMIT;
        $this->db->select("c.`channel_id`,t.is_monthly,t.is_yearly, c.`channel_title`, c.`channel_oneliner`, c.`channel_image`, c.`channel_icon`, c.`channel_share_count`,c.created_on");
        $this->db->from("channel c");
       
        $this->db->join("transection t", "c.channel_id=t.channel_id");
        $this->db->where("user_id",$user_id);
        $this->db->where("is_video",0);
        $this->db->limit(PAGE_LIMIT,$offset);
        $aResult = $this->db->get();
         if ($aResult->num_rows() > 0) {
            return $aResult->result_array();           
        } else {
            return false;
        }
    }
    
     public function channelMaxCount($user_id){
        
    
        //---- TABLE NAME ---//
        $offset  = $page_index* PAGE_LIMIT;
        $this->db->select("c.`channel_id`,t.is_monthly,t.is_yearly, c.`channel_title`, c.`channel_oneliner`, c.`channel_image`, c.`channel_icon`, c.`channel_share_count`,c.created_on");
        $this->db->from("channel c");
       
        $this->db->join("transection t", "c.channel_id=t.channel_id");
        $this->db->where("user_id",$user_id);
        $this->db->where("is_video",0);
        $aResult = $this->db->get();
         if ($aResult->num_rows() > 0) {
            return $aResult->num_rows();           
        } else {
            return false;
        }
    }
     
      public function getVideo($user_id)
        {
            $this->db->select("v.`vid_id`,v.video_share_count,chan.channel_icon,chan.channel_oneliner,chan.channel_image,chan.channel_icon,chan.channel_share_count,cat.category_icon,cat.created_on,v.video,v.video_type, v.`channel_id`,chan.channel_title,cat.category_name, v.`category_id`, v.`video_title`, v.`video_desc`, v.`video_status`, v.`is_deleted`, v.`created_on`,chan.`created_on` as channel_date, v.`modified_on`", FALSE);
            $this->db->from('video v');
            $this->db->join('channel chan', 'v.channel_id = chan.channel_id', 'left');
            $this->db->join('category cat', 'v.category_id = cat.cat_id', 'left');
            $this->db->join('transection t', 't.video_id = v.vid_id', 'left');
            $this->db->group_by("v.vid_id");
            $this->db->where(
                    array(
                        'v.is_deleted' => 0,
                        'v.video_status' => 1,
                        't.user_id' => $user_id
            ));
            
            $this->db->order_by('vid_id');
            $result = $this->db->get('video');
            if ($result->num_rows()) {            
                    return $result->result_array();
            }
            else
                return 0;
        }
}
?>