<?php
/*
* Programmer Name:PK
* Purpose:Legends Model
* Date:17 Aug 2016
*/
class Legendsmodel extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
		
    public function getData($edit_id = 0)
    {
        $this->db->select("*",FALSE);
        $this->db->from('sg_legends');
        if($edit_id){
            $this->db->where('legend_id',$edit_id);
        }
        $this->db->where(
                    array(
                        'is_delete' => 0
                    ));
		$this->db->order_by('legend_sequence','ASC');
        $result = $this->db->get();
        if($result->num_rows()){
            if($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }
	public function update_package_status($legend_id = 0)
    {
        $this->db->select('status');
        $this->db->from('sg_legends');
        $this->db->where('legend_id',$legend_id);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $query = $query->row_array();
            if($query['status'] == 1)
            {
                $data = array(
                                'status' => 0
                        );
            }
            else
            {
                $data = array(
                                'status' => 1
                        );
            }
            $this->db->where('legend_id',$legend_id);
            $this->db->update('sg_legends',$data);
           
        }
    }
	 public function package_count()
    {
        $this->db->select("*",FALSE);
    
        $this->db->where(
                    array(
                        'is_delete' => 0,
                        'status' => 1
                    ));
        
        $result = $this->db->get('sg_legends');
        return $result->num_rows();
          
    }
	public function action($action,$arrData = array(),$edit_id =0)
    {
        switch($action){
            case 'insert':
                $this->db->insert('sg_legends',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('legend_id',$edit_id);
                $this->db->update('sg_legends',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }
	public function change_sequence($faqid = 0,$change_to = 'up')
	{ 
		// get sequence of current menu
		$curr_faq = 0;
		$this->db->select('legend_id,legend_sequence');
		$this->db->where('legend_id',$faqid);
		$result = $this->db->get('sg_legends');
		if($result->num_rows() > 0)
		{
			$curr_faq = $result->row();
		}
			$other_menu = 0;
			$this->db->select('legend_id,legend_sequence');
			if($change_to == 'up')
			{	//echo 'hi';die();
				$this->db->where('legend_sequence <',$curr_faq->legend_sequence);
				$this->db->order_by('legend_sequence','DESC');
				
			}
			else{
				$this->db->where('legend_sequence >',$curr_faq->legend_sequence);
				$this->db->order_by('legend_sequence','ASC');
			}
			$this->db->where('is_delete',0);
			$this->db->limit(1);
			
			$result = $this->db->get('sg_legends');
			//echo $this->db->last_query();die;
			if($result->num_rows() > 0)
			{
				$other_menu = $result->row();
			}
			else
				return 'NA';
			
			if($other_menu){
				// update sequence of current menu
				$update_seq = ($other_menu->legend_sequence);
				$update_data = array('legend_sequence'=>$update_seq);
				$this->db->where('legend_id',$curr_faq->legend_id);
				$this->db->update('sg_legends',$update_data);
				
				// update sequence of other menu
				$update_seq = ($curr_faq->legend_sequence);
				$update_data = array('legend_sequence'=>$update_seq);
				$this->db->where('legend_id',$other_menu->legend_id);
				$this->db->update('sg_legends',$update_data);
				
				return 'DONE';
			}
			
	}
	
	public function getMaxSeq()
	{
		$this->db->select_max('legend_sequence');
		$this->db->from('sg_legends');
		$this->db->where('is_delete','0');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$query = $query->row();
			$query = $query->legend_sequence;
			return $query + 1;
		}
		else
		{
			return 1;
		}
		
	}
  
}
?>