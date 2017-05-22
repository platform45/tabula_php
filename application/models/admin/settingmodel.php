<?php
	class Settingmodel extends CI_Model
	{
            
            public function __construct()
            {
                    parent::__construct();
            }

            public function getData($edit_id = 0){

            	$this->db->select("setting_id,setting_name,setting_value,setting_parameter",FALSE);
            	if($edit_id){
            		$this->db->where('setting_id',$edit_id);
            	}
        		$this->db->order_by('setting_name', 'ASC');
        		 $result = $this->db->get('settings');
        		if ($result->num_rows()) {
           			if($edit_id)
              		  return $result->row();
         			   else
               	 return $result->result_array();
       				 }
    }
            

public function action($action,$arrData = array(),$edit_id =0)
    {
        switch($action){
            case 'insert':
                $this->db->insert('settings',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('setting_id',$edit_id);
                $this->db->update('settings',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

}
?>