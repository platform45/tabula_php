<?php
class Eventcategorymodel extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
		
    public function getData($edit_id = 0)
    {
        $this->db->select("category_id,category_title,category_sequence,category_status",FALSE);
        if($edit_id){
            $this->db->where('category_id',$edit_id);
        }
        $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
       $this->db->order_by('category_sequence','ASC');
        $result = $this->db->get('eventcategorymst');
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
        $this->db->select("category_id,category_title,category_sequence,category_status",FALSE);
        if($edit_id){
            $this->db->where('category_id',$edit_id);
        }
        $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
        
        $this->db->where(
                    array(
                        'category_status' => 1
                    ));
       $this->db->order_by('category_sequence','ASC');
        $result = $this->db->get('eventcategorymst');
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
                            $this->db->select('category_title');
                            $this->db->from('eventcategorymst');
                            $this->db->where('category_title',urlencode($link));
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
                            $this->db->select('category_title');
                            $this->db->from('eventcategorymst');
                            $this->db->where('category_title',urlencode($link));
                             $this->db->where(array('is_deleted' => 0));
                            $this->db->where('category_id <> ',$id);
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
                $this->db->insert('eventcategorymst',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('category_id',$edit_id);
                $this->db->update('eventcategorymst',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }
    public function brand_count()
   {
       $this->db->select("category_id,category_title,category_sequence,category_status",FALSE);
       $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
        $this->db->order_by('category_sequence','ASC');
        $result = $this->db->get('eventcategorymst');
        return $result->num_rows();
        
   }
   public function brand_state_count()
   {
       $this->db->select("category_id,category_title,category_status",FALSE);
       $this->db->where(
                    array(
                        'is_deleted' => 0
                    ));
        
        $this->db->where(
                    array(
                        'category_status' => 1
                    ));
       
        $this->db->order_by('category_sequence','ASC');
        $result = $this->db->get('eventcategorymst');
        return $result->num_rows();
        
   }
    
    public function update_status($category_id = 0)
    {
        $this->db->select('category_status');
        $this->db->from('eventcategorymst');
        $this->db->where('category_id',$category_id);
       
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $query = $query->row_array();
            if($query['category_status'] == 1)
            {
                $data = array(
                                'category_status' => 0
                        );
            }
            else
            {
                $data = array(
                                'category_status' => 1
                        );
            }
            $this->db->where('category_id',$category_id);
            $this->db->update('eventcategorymst',$data);
        }
    }
    
            public function getMaxSeq(){
                $this->db->select_max('category_sequence');
                $this->db->from('eventcategorymst');
                $this->db->where('is_deleted','0');
                $query = $this->db->get();
                if($query->num_rows() > 0)
                {
                    $query = $query->row();
                    $query = $query->category_sequence;
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
                $this->db->select('category_id,category_sequence');
                $this->db->where('category_id',$faqid);
                $result = $this->db->get('aoe_eventcategorymst');
                if($result->num_rows() > 0)
                {
                    $curr_faq = $result->row();
                }
                
                
                    $other_menu = 0;
                    $this->db->select('category_id,category_sequence');
                    if($change_to == 'up')
                    {
                        $this->db->where('category_sequence <',$curr_faq->category_sequence);
                        
                        $this->db->order_by('category_sequence','DESC');
                    }
                    else{
                        $this->db->where('category_sequence >',$curr_faq->category_sequence);
                         
                        $this->db->order_by('category_sequence','ASC');
                    }
                    $this->db->where('is_deleted',0);
                    $this->db->limit(1);
                    
                    $result = $this->db->get('aoe_eventcategorymst');
                    if($result->num_rows() > 0)
                    {
                        $other_menu = $result->row();
                    }
                    else
                        return 'NA';
                    
                    if($other_menu){
                        // update sequence of current menu
                        $update_seq = ($other_menu->category_sequence);
                        $update_data = array('category_sequence'=>$update_seq);
                        $this->db->where('category_id',$curr_faq->category_id);
                        $this->db->update('aoe_eventcategorymst',$update_data);
                        
                        // update sequence of other menu
                        $update_seq = ($curr_faq->category_sequence);
                        $update_data = array('category_sequence'=>$update_seq);
                        $this->db->where('category_id',$other_menu->category_id);
                        $this->db->update('aoe_eventcategorymst',$update_data);
                        
                        return 'DONE';
                    }
                    
            }
    
}
?>