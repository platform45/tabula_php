<?php
class Eventgallerymodel extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
		
    public function getData($pm_id = 0)
    {
        $this->db->select("`id`, `event_id`, `image`, `is_delete`",FALSE);
        $this->db->where(
                    array(
                        'event_id' => $pm_id,
                        'is_delete' => 0
                    ));        
        $result = $this->db->get('eventgallery');
        if($result->num_rows()){
                return $result->result_array();
        }
        else
            return 0;
    }
    
    public function action($action,$arrData = array(),$edit_id =0)
    {
        switch($action){
            case 'insert':
                $this->db->insert('eventgallery',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('id',$edit_id);
                $this->db->update('eventgallery',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }
    
    public function update_status($sli_id = 0)
    {
        $this->db->select('sli_status');
        $this->db->from('ca_slidermst');
        $this->db->where('sli_id',$sli_id);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $query = $query->row_array();
            if($query['sli_status'] == 1)
            {
                $data = array(
                                'sli_status' => 0
                        );
            }
            else
            {
                $data = array(
                                'sli_status' => 1
                        );
            }
            $this->db->where('sli_id',$sli_id);
            $this->db->update('ca_slidermst',$data);
        }
    }
    
     public function getSequence($type,$pm_id)
         {
       if($type=='max')
       {
           $this->db->select('pg_id');
           $this->db->where('is_deleted','0');
           $this->db->where('pg_pmid',$pm_id);
           $this->db->order_by('pg_sequence','DESC');
           $this->db->limit(1);
           $result = $this->db->get('ca_portfoliogallerymst');
           //print_r($result);
           if($result->num_rows() > 0)
           {
               $curr_seq = $result->row()->pg_id;
           }
       }
       else 
        {
           $this->db->select('pg_id');
           $this->db->where('is_deleted','0');
           $this->db->where('pg_pmid',$pm_id);
           $this->db->order_by('pg_sequence','ASC');
           $this->db->limit(1);
           $result = $this->db->get('ca_portfoliogallerymst');
           if($result->num_rows() > 0)
           {
              $curr_seq = $result->row()->pg_id;
           }
       }
       return $curr_seq;
      }
    
      public function change_sequence($pg_id = 0,$change_to = 'up',$pm_id)
            { // get sequence of current menu
                $curr_menu = 0;
                $this->db->select('pg_id,pg_sequence');
                $this->db->where('pg_pmid',$pm_id);
                $this->db->where('pg_id',$pg_id);
                $this->db->where('is_deleted',0);
                $result = $this->db->get('ca_portfoliogallerymst');
                if($result->num_rows() > 0)
                {
                    $curr_port = $result->row();
                    // print_r($curr_port);
                }
                    $other_menu = 0;
                    $this->db->select('pg_id,pg_sequence');
                    if($change_to == 'up')
                    {
                        $this->db->where('pg_sequence <',$curr_port->pg_sequence);
                        $this->db->order_by('pg_sequence','DESC');
                    }
                    else{
                        $this->db->where('pg_sequence >',$curr_port->pg_sequence);
                        $this->db->order_by('pg_sequence','ASC');
                    }
                    $this->db->where('is_deleted',0);
                     $this->db->where('pg_pmid',$pm_id);
                    $this->db->limit(1);
                    
                    $result = $this->db->get('ca_portfoliogallerymst');
                    if($result->num_rows() > 0)
                    {
                        $other_menu = $result->row();
                        //print_r($other_menu);exit;
                    }
                    else
                        return 'NA';
                    
                    if($other_menu){
                        // update sequence of current 
                        $update_seq = ($other_menu->pg_sequence);
                        $update_data = array('pg_sequence'=>$update_seq);
                        $this->db->where('pg_id',$curr_port->pg_id);
                         $this->db->where('pg_pmid',$pm_id);
                        $this->db->update('ca_portfoliogallerymst',$update_data);
                        
                        // update sequence of other 
                        $update_seq = ($curr_port->pg_sequence);
                        $update_data = array('pg_sequence'=>$update_seq);
                        $this->db->where('pg_id',$other_menu->pg_id);
                         $this->db->where('pg_pmid',$pm_id);
                        $this->db->update('ca_portfoliogallerymst',$update_data);
                        
                        return 'DONE';
                    }
            }
            
             public function getNewSequence($pm_id)
            {
                $this->db->select_max('pg_sequence');
                $this->db->where('pg_pmid',$pm_id);
                $this->db->limit(1);
                $result = $this->db->get('ca_portfoliogallerymst');
                if($result->num_rows() > 0)
                {
                    $curr_seq = $result->row()->pg_sequence;
                    if(!empty($curr_seq))
                        return ($curr_seq + 1);
                    else
                        return 1;
                }
                else
                {
                    return 1;
                }
            }
}
?>