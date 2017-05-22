<?php
class Transactionmodel extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
		
    public function getData($transect_id = 0)
    {
        $this->db->select("t.`transect_id`, t.`user_id`,u.user_first_name, u.user_last_name,t.`transection_no`, t.`subscription_id`, t.`is_monthly`, t.`is_yearly`, t.`is_video`, t.`amount`, t.`date`, t.`account_status`, t.`transection_status`, t.`channel_id`, t.`video_id`, t.`transection_by`",FALSE);
        $this->db->from('transection as t');
        $this->db->join('usermst as u',"t.user_id=u.user_id");
        if($transect_id)
            $this->db->where('t.transect_id',$transect_id);
        $this->db->where(
                    array(
                        't.is_delete' => 0
                    ));
        $this->db->group_by('transect_id');
        $result = $this->db->get('transection');
        
        if($result->num_rows()){
            if($transect_id)
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
  
    
    
}
?>