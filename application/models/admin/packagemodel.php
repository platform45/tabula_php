<?php
class Packagemodel extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
		
    public function getData($edit_id = 0)
    {
        $this->db->select("*",FALSE);
        $this->db->from('saf_package');
      // $this->db->join('channel chan','cat.channel_id = chan.channel_id','left');
        if($edit_id){
            $this->db->where('p_id',$edit_id);
        }
        $this->db->where(
                    array(
                        'p_is_delete' => 0
                    ));
        $result = $this->db->get();
        //echo $this->db->last_query();die;
        if($result->num_rows()){
            if($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }
	public function update_package_status($p_id = 0)
    {
        $this->db->select('p_is_active');
        $this->db->from('saf_package');
        $this->db->where('p_id',$p_id);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $query = $query->row_array();
            if($query['p_is_active'] == 1)
            {
                $data = array(
                                'p_is_active' => 0
                        );
            }
            else
            {
                $data = array(
                                'p_is_active' => 1
                        );
            }
            $this->db->where('p_id',$p_id);
            $this->db->update('saf_package',$data);
           
        }
    }
	 public function package_count()
    {
        $this->db->select("`p_id`, `p_indivisual_video`, `p_m_channal`, `p_yearly`, `p_is_active`, `p_is_delete`",FALSE);
    
        $this->db->where(
                    array(
                        'p_is_delete' => 0,
                        'p_is_active' => 1
                    ));
        
        $result = $this->db->get('saf_package');
        return $result->num_rows();
          
    }
	public function action($action,$arrData = array(),$edit_id =0)
    {
        switch($action){
            case 'insert':
                $this->db->insert('saf_package',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('p_id',$edit_id);
                $this->db->update('saf_package',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }
  
}
?>