<?php

class Videomodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0, $fp = '') {
        $this->db->select("v.`vid_id`,v.video,v.video_type, v.`channel_id`,chan.channel_title,cat.category_name, v.`category_id`, v.`video_title`, v.`video_desc`, v.`video_status`, v.`is_deleted`, v.`created_on`, v.`modified_on`", FALSE);
        $this->db->from('video v');
        $this->db->join('channel chan', 'v.channel_id = chan.channel_id', 'left');
        $this->db->join('category cat', 'v.category_id = cat.cat_id', 'left');
        if ($edit_id) {
            $this->db->where('v.vid_id', $edit_id);
        }
       $this->db->group_by("v.vid_id");
        $this->db->where(
                array(
                    'v.is_deleted' => 0
        ));

        $this->db->order_by('v.video_sequence', 'ASC');
        $result = $this->db->get('video');

        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function video_count() {
        $this->db->select("vid_id", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));

        $this->db->order_by('video_sequence', 'ASC');
        $result = $this->db->get('video');
        return $result->num_rows();
    }

    public function checkVideo($channel_id) {
        $this->db->select("vid_id", FALSE);
        $this->db->where(
                array(
                    'channel_id' => $channel_id
        ));
       
        $result = $this->db->get('video');
        return $result->num_rows();
    }
    public function video_state_count() {
        $this->db->select("vid_id,prod_title,prod_image,prod_brief,prod_zohocode,prod_status", FALSE);

        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
        $this->db->where(
                array(
                    'prod_status' => 1
        ));

        $this->db->order_by('video_sequence', 'ASC');
        $result = $this->db->get('video');
        return $result->num_rows();
    }

    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('video', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('vid_id', $edit_id);
                $this->db->update('video', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    public function update_status($vid_id = 0) {
        $this->db->select('video_status');
        $this->db->from('video');
        $this->db->where('vid_id', $vid_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['video_status'] == 1) {
                $data = array(
                    'video_status' => 0
                );
            } else {
                $data = array(
                    'video_status' => 1
                );
            }
            $this->db->where('vid_id', $vid_id);
            $this->db->update('video', $data);
        }
    }

    public function getMaxSeq() {
        $this->db->select_max('video_sequence');
        $this->db->from('video');
        $this->db->where('is_deleted', '0');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->video_sequence;
            return $query + 1;
        } else {
            return 1;
        }
    }

    public function change_sequence($vid_id = 0, $change_to = 'up') {
        // get sequence of current menu
        $curr_faq = 0;
        $this->db->select('vid_id,video_sequence');
        $this->db->where('vid_id', $vid_id);
        $result = $this->db->get('video');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('vid_id,video_sequence');
        if ($change_to == 'up') {
            $this->db->where('video_sequence <', $curr_faq->video_sequence);
            $this->db->where('prod_at_fp', 1);
            $this->db->order_by('video_sequence', 'DESC');
        } else {
            $this->db->where('video_sequence >', $curr_faq->video_sequence);
            $this->db->where('prod_at_fp', 1);
            $this->db->order_by('video_sequence', 'ASC');
        }
        $this->db->where('is_deleted', 0);

        $this->db->limit(1);

        $result = $this->db->get('video');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        }
        else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->video_sequence);
            $update_data = array('video_sequence' => $update_seq);
            $this->db->where('vid_id', $curr_faq->vid_id);
            $this->db->where('prod_at_fp', 1);
            $this->db->update('video', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->video_sequence);
            $update_data = array('video_sequence' => $update_seq);
            $this->db->where('vid_id', $other_menu->vid_id);
            $this->db->update('video', $update_data);

            return 'DONE';
        }
    }

    public function getChannels() {
       $this->db->select("chan.`channel_id`, chan.`channel_title`, chan.`channel_oneliner`, chan.`channel_image`, chan.`channel_sequence`, chan.`channel_status`, chan.`is_deleted`, chan.`created_on`, chan.`modified_on`",FALSE);
       $this->db->from("channel chan");
      $this->db->join('category cat','cat.channel_id = chan.channel_id');
        $this->db->where(
                    array(
                        'chan.is_deleted' => 0,
                        'chan.channel_status' => 1
                    ));        
        
        $this->db->order_by('chan.channel_sequence','ASC');
        $this->db->group_by('chan.channel_id');
        $result = $this->db->get('channel');
        if($result->num_rows()){
           
                return $result->result_array();
        }
        else
            return 0;
        
    }

     public function getChannelTitle($channel_id) {
       $this->db->select("chan.`channel_id`, chan.`channel_title`",FALSE);
       $this->db->from("channel chan");      
        $this->db->where(
                    array(                       
                        'chan.channel_id' => $channel_id
                    ));        
        
      
        $result = $this->db->get('channel');
        if($result->num_rows()){
           
                 $result = $result->row();
                 return $result->channel_title;
                 
        }
        else
            return "";
        
    }
    public function getCategories($channel_id = 0) {
        $this->db->select("`cat_id`, `channel_id`, `category_name`, `category_desc`, `category_sequence`, `category_status`, `is_delete`, `created_on`, `modiefied_on`", FALSE);
        if($channel_id)
            $this->db->where('channel_id',$channel_id);
        $this->db->where(
                array(
                    'is_delete' => 0,
                    'category_status' => 1
        ));

        $result = $this->db->get('category');
        //$option= "<option value='0'></option>";
        if ($result->num_rows()) {
        return  $result->result_array();
        }
    }
    
       public function check_title_exists($link,$id = FALSE,$channel_id,$category_id)
            {
                  
                  if($id === FALSE)
                    {
                            $this->db->select('video_title');
                            $this->db->from('video');
                            $this->db->where('video_title',$link);
                            $this->db->where(array('is_deleted' => 0));
                            $this->db->where(array('channel_id' => $channel_id));
                            $this->db->where(array('category_id' => $category_id));
                            $this->db->limit(1);
                            $query = $this->db->get();
                            if($query->num_rows() > 0)
                                    return false;
                            else
                                    return true;
                    }
                    else
                    {
                            
                            
                            $this->db->select('video_title');
                            $this->db->from('video');
                            $this->db->where('video_title',$link);
                            $this->db->where(array('is_deleted' => 0));
                            $this->db->where(array('channel_id' => $channel_id));
                            $this->db->where(array('category_id' => $category_id));
                            $this->db->where('vid_id <> ',$id);
                            $this->db->limit(1);
                            $query = $this->db->get();
                            if($query->num_rows() > 0)
                                return false;
                            else
                                return true;
                    }
            }

}

?>