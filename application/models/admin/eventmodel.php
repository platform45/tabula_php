<?php
class Eventmodel extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
		
    public function getData($edit_id = 0)
    {
        $this->db->select("`ne_id`, `ne_date`,`ne_to_date`,`ne_title`, `ne_brief`, `ne_image`, `ne_metakeywords`, `ne_metatag`, `e_location`,`ne_desc`, `ne_sequence`, `ne_status`, `is_deleted`",FALSE);
        if($edit_id){
            $this->db->where('ne_id',$edit_id);
        }
        
        $this->db->where(
                    array(
                        'ne_type' => 2
                    ));
        $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
       $this->db->order_by('ne_sequence','ASC');
        $result = $this->db->get('newseventsmst');
        if($result->num_rows()){
            if($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }
    
     public function getDatastatus($edit_id = 0)
    {
        $this->db->select("`ne_id`, `ne_date`, `ne_to_date`,`ne_title`, `ne_brief`, `ne_image`, `ne_metakeywords`, `ne_metatag`, `ne_desc`, `ne_sequence`, `ne_status`, `is_deleted`",FALSE);
        if($edit_id){
            $this->db->where('ne_id',$edit_id);
        }
        $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
         $this->db->where(
                    array(
                        'ne_status' => 1
                    ));
         $this->db->where(
                    array(
                        'ne_type' => 2
                    ));
         
        
       $this->db->order_by('ne_sequence','ASC');
        $result = $this->db->get('newseventsmst');
        if($result->num_rows()){
            if($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }
    
    public function check_title_exists($link,$id = FALSE)
            {
                    if($id === FALSE)
                    {
                            $this->db->select('ne_title');
                            $this->db->from('newseventsmst');
                            $this->db->where('ne_title',urlencode($link));
                            $this->db->where(array('is_deleted' => 0));
                            $this->db->where(array('ne_type' => 2));
                            $this->db->limit(1);
                            $query = $this->db->get();
                            if($query->num_rows() > 0)
                                    return false;
                            else
                                    return true;
                    }
                    else
                    {
                            $this->db->select('ne_title');
                            $this->db->from('newseventsmst');
                            $this->db->where('ne_title',urlencode($link));
                            $this->db->where(array('is_deleted' => 0));
                            $this->db->where(array('ne_type' => 2));
                            $this->db->where('ne_id <> ',$id);
                            $this->db->limit(1);
                            $query = $this->db->get();
                            if($query->num_rows() > 0)
                                return false;
                            else
                                return true;
                    }
            }
    
    public function action($action,$arrData = array(),$edit_id =0)
    {
        switch($action){
            case 'insert':
                $this->db->insert('newseventsmst',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('ne_id',$edit_id);
                $this->db->update('newseventsmst',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }
    public function event_count()
   {
       $this->db->select("ne_id,ne_title,ne_sequence,ne_status",FALSE);
       $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
       $this->db->where(array('ne_type' => 2));
        $this->db->order_by('ne_sequence','ASC');
        $result = $this->db->get('newseventsmst');
        return $result->num_rows();
        
   }

    public function update_status($ne_id = 0)
    {
        $this->db->select('ne_status');
        $this->db->from('newseventsmst');
        $this->db->where('ne_id',$ne_id);
       
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $query = $query->row_array();
            if($query['ne_status'] == 1)
            {
                $data = array(
                                'ne_status' => 0
                        );
            }
            else
            {
                $data = array(
                                'ne_status' => 1
                        );
            }
            $this->db->where(array('ne_type' => 2));
            $this->db->where('ne_id',$ne_id);
            $this->db->update('newseventsmst',$data);
        }
    }
    
            public function getMaxSeq(){
                $this->db->select_max('ne_sequence');
                $this->db->from('newseventsmst');
                $this->db->where('is_deleted','0');
                $this->db->where(array('ne_type' => 2));
                $query = $this->db->get();
                if($query->num_rows() > 0)
                {
                    $query = $query->row();
                    $query = $query->ne_sequence;
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
                $this->db->select('ne_id,ne_sequence');
                $this->db->where('ne_id',$faqid);
                $result = $this->db->get('newseventsmst');
                if($result->num_rows() > 0)
                {
                    $curr_faq = $result->row();
                }
                    $other_menu = 0;
                    $this->db->select('ne_id,ne_sequence');
                    if($change_to == 'up')
                    {
                        $this->db->where('ne_sequence <',$curr_faq->ne_sequence);
                        
                        $this->db->order_by('ne_sequence','DESC');
                    }
                    else{
                        $this->db->where('ne_sequence >',$curr_faq->ne_sequence);
                         
                        $this->db->order_by('ne_sequence','ASC');
                    }
                    $this->db->where(array('ne_type' => 2));
                    $this->db->where('is_deleted',0);
                    $this->db->limit(1);
                    
                    $result = $this->db->get('newseventsmst');
                    if($result->num_rows() > 0)
                    {
                        $other_menu = $result->row();
                    }
                    else
                        return 'NA';
                    
                    if($other_menu){
                        // update sequence of current menu
                        $update_seq = ($other_menu->ne_sequence);
                        $update_data = array('ne_sequence'=>$update_seq);
                        $this->db->where('ne_id',$curr_faq->ne_id);
                        $this->db->where(array('ne_type' => 2));
                        $this->db->update('newseventsmst',$update_data);
                        
                        // update sequence of other menu
                        $update_seq = ($curr_faq->ne_sequence);
                        $update_data = array('ne_sequence'=>$update_seq);
                        $this->db->where('ne_id',$other_menu->ne_id);
                        $this->db->where(array('ne_type' => 2));
                        $this->db->update('newseventsmst',$update_data);
                        
                        return 'DONE';
                    }
                    
            }
    
}
?>