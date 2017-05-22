<?php
class Channelmodel extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
		
    public function getData($edit_id = 0)
    {
        $this->db->select("`channel_id`,channel_share_count,channel_icon, `channel_title`, `channel_oneliner`, `channel_image`, `channel_sequence`, `channel_status`, `is_deleted`, `created_on`, `modified_on`",FALSE);
        if($edit_id){
            $this->db->where('channel_id',$edit_id);
        }
        $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
        $this->db->order_by('channel_sequence','ASC');
        $result = $this->db->get('channel');
        if($result->num_rows()){
            if($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

      public function channel_count()
    {
        $this->db->select("`channel_id`, `channel_title`, `channel_oneliner`, `channel_image`, `channel_sequence`, `channel_status`, `is_deleted`, `created_on`, `modified_on`",FALSE);
    
        $this->db->where(
                    array(
                        'is_deleted' => 0,
                        'channel_status' => 1
                    ));
        
        $result = $this->db->get('channel');
        return $result->num_rows();
          
    }

    public function getDatastatus($edit_id = 0)
    {
        $this->db->select("channel_id,sli_title,channel_sequence,sli_status",FALSE);
        if($edit_id){
            $this->db->where('channel_id',$edit_id);
        }
        $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
         $this->db->where(
                    array(
                        'sli_status' => 1
                    ));
         $this->db->where(
                    array(
                        'sli_type' => 'Brand'
                    ));
        
       $this->db->order_by('channel_sequence','ASC');
        $result = $this->db->get('slidermst');
        if($result->num_rows()){
            if($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }
       
    
    public function action($action,$arrData = array(),$edit_id =0)
    {
        switch($action){
            case 'insert':
                $this->db->insert('channel',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('channel_id',$edit_id);
                $this->db->update('channel',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }
  
    
    public function update_channel_status($channel_id = 0)
    {
        $this->db->select('channel_status');
        $this->db->from('channel');
        $this->db->where('channel_id',$channel_id);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $query = $query->row_array();
            if($query['channel_status'] == 1)
            {
                $data = array(
                                'channel_status' => 0
                        );
            }
            else
            {
                $data = array(
                                'channel_status' => 1
                        );
            }
            $this->db->where('channel_id',$channel_id);
            $this->db->update('channel',$data);
        }
    }
    
            public function getMaxSeq(){
                $this->db->select_max('channel_sequence');
                $this->db->from('channel');
                $this->db->where('is_deleted','0');
                $query = $this->db->get();
                if($query->num_rows() > 0)
                {
                    $query = $query->row();
                    $query = $query->channel_sequence;
                    return $query + 1;
                }
                else
                {
                    return 1;
                }
                
            }
    
            public function change_sequence($faqid = 0,$change_to = 'up')
            {
                // get sequence of current menu
                $curr_faq = 0;
                $this->db->select('channel_id,channel_sequence');
                $this->db->where('channel_id',$faqid);
                $result = $this->db->get('channel');
                if($result->num_rows() > 0)
                {
                    $curr_faq = $result->row();
                }
                
                
                    $other_menu = 0;
                    $this->db->select('channel_id,channel_sequence');
                    if($change_to == 'up')
                    {
                        $this->db->where('channel_sequence <',$curr_faq->channel_sequence);
                        
                        $this->db->order_by('channel_sequence','DESC');
                    }
                    else{
                        $this->db->where('channel_sequence >',$curr_faq->channel_sequence);
                         
                        $this->db->order_by('channel_sequence','ASC');
                    }
                    $this->db->where('is_deleted',0);
                    $this->db->limit(1);
                    
                    $result = $this->db->get('channel');
                    if($result->num_rows() > 0)
                    {
                        $other_menu = $result->row();
                    }
                    else
                        return 'NA';
                    
                    if($other_menu){
                        // update sequence of current menu
                        $update_seq = ($other_menu->channel_sequence);
                        $update_data = array('channel_sequence'=>$update_seq);
                        $this->db->where('channel_id',$curr_faq->channel_id);
                        
                        $this->db->update('channel',$update_data);
                        
                        // update sequence of other menu
                        $update_seq = ($curr_faq->channel_sequence);
                        $update_data = array('channel_sequence'=>$update_seq);
                        $this->db->where('channel_id',$other_menu->channel_id);
                        $this->db->update('channel',$update_data);
                        
                        return 'DONE';
                    }
                    
            }
    
            
            // get free videos for shoeing sequential in app and website
              public function getVideos($channel_id,$category_id=0) {
                $this->db->select("v.`vid_id`,v.video,v.video_type, v.`channel_id`,chan.channel_title,cat.category_name, v.`category_id`, v.`video_title`, v.`video_desc`, v.`video_status`, v.`is_deleted`, v.`created_on`, v.`modified_on`", FALSE);
                $this->db->from('video v');
                $this->db->join('channel chan', 'v.channel_id = chan.channel_id', 'left');
                $this->db->join('category cat', 'v.category_id = cat.cat_id', 'left');
                
                if ($category_id) {
                    $this->db->where('v.category_id', $category_id);
                }                
                $this->db->where(
                        array(
                            'v.is_deleted' => 0,
                            'v.channel_id' => $channel_id,
                            'v.video_type' => 0,
                            'v.video_status' => 1
                ));
                $this->db->group_by(array("v.vid_id", "v.category_id"));
                
                $this->db->order_by('v.video_sequence', 'ASC');
                $result = $this->db->get('video');

                if ($result->num_rows()) {
                  
                        return $result->result_array();
                }
                else
                    return 0;
    }
    
      public function getFreeVideos($channel_id = 0) {
        $this->db->select('f.`free_id`,v.video_title,f.`channel_id`, f.`vid_id`, f.`sequence`');
        $this->db->from('freevideo f');
        $this->db->join('video v', 'v.vid_id = f.vid_id');
        $this->db->where('f.channel_id',$channel_id);
        $this->db->order_by('sequence',"Asc");
        $this->db->group_by('f.vid_id');
        $result = $this->db->get();
        if ($result->num_rows()) {
        return  $result->result_array();
        }
      }
    
    
    // category filter for 
     public function getCategories($channel_id = 0) {
        $this->db->select("`cat_id`, cat.`channel_id`, `category_name`, `category_desc`, `category_sequence`, `category_status`, `is_delete`", FALSE);
        $this->db->from('category cat');
        $this->db->join('video v', 'v.category_id = cat.cat_id');
        if($channel_id)
            $this->db->where('cat.channel_id',$channel_id);
        $this->db->where(
                array(
                    'is_delete' => 0,
                    'category_status' => 1,
                     'is_deleted' => 0,
                    'video_status' => 1,
                    'video_type' => 0
        ));
        $this->db->group_by("cat_id");
      
        $result = $this->db->get();
        
        //$option= "<option value='0'></option>";
        if ($result->num_rows()) {
        return  $result->result_array();
        }
}

 public function check_title_exists($link,$id = FALSE)
            {
                  
                  if($id === FALSE)
                    {
                            $this->db->select('channel_title');
                            $this->db->from('channel');
                            $this->db->where('channel_title',$link);
                            $this->db->where(array('is_deleted' => 0));
                            $this->db->limit(1);
                            $query = $this->db->get();
                            if($query->num_rows() > 0)
                                    return false;
                            else
                                    return true;
                    }
                    else
                    {
                         
                            $this->db->select('channel_title');
                            $this->db->from('channel');
                            $this->db->where('channel_title',$link);
                            $this->db->where(array('is_deleted' => 0));
                            $this->db->where('channel_id <> ',$id);
                            $this->db->limit(1);
                            $query = $this->db->get();
                            if($query->num_rows() > 0)
                                return false;
                            else
                                return true;
                    }
            }
            
         public function addsequenciaVideo($videos_array,$channel_id)
         {
               $this->db->where('channel_id',$channel_id);
               $this->db->delete('freevideo');
             
             foreach($videos_array as $key=>$value)
             {
                 $insert_array = array("channel_id"=>$channel_id,
                                       "vid_id"=> $value,
                                       "sequence"=>$key );
               $result =  $this->db->insert('freevideo',$insert_array);
                 
             }
             return $result;
         }
}
?>