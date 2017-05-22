<?php
	class Rolemodel extends CI_Model
	{
            public function __construct()
            {
                    parent::__construct();
            }
            
            public function getActiveRoles()
            {
                $this->db->select("role_id,role_type",FALSE);
                $this->db->where(
                            array(
                                'role_removed 	' => '0',
                                'role_status 	' => '1'
                            ));
               
                $result = $this->db->get('rolemst');
                if($result->num_rows()){
                    return $result->result_array();
                }
                else
                    return 0;
            }
            
            public function getData($edit_id = 0)
            {
                $this->db->select("role_id,role_description,role_type,role_status,role_removed",FALSE);
                if($edit_id){
                    $this->db->where('role_id',$edit_id);
                }
                $this->db->where(
                            array(
                                'role_removed 	' => 0
                            ));
                 $this->db->where_not_in('role_id','3');
               
                $result = $this->db->get('rolemst');
                if($result->num_rows()){
                    if($edit_id)
                        return $result->row();
                    else
                        return $result->result_array();
                }
                else
                    return 0;
            }
            
           public function getAllMenu()
            {
                $result = $this->db->query("SELECT * FROM  tab_optionmst WHERE opt_status = '1' AND opt_optionid != '27' AND opt_optionid NOT IN (3,29,30,28, 5) ORDER BY opt_sequence_no");
                if($result->num_rows()){
                    return $result->result_array();
                }
                else
                    return 0;
                
            }
            
            public function update_access($accessArr = array(),$role_id =0,$type='role')
            {
                if($type == 'role')
                    $this->db->where('acc_roleid',$role_id);
                else
                    $this->db->where('acc_userid',$role_id);
                $this->db->delete('accessmst');
                $this->db->flush_cache();
                foreach ($accessArr as $acc){
                    if($type == 'role'){
                        $insertData = array(
                            'acc_roleid'=>$role_id,
                            'acc_optionid'=>$acc
                        );
                        $this->db->insert('accessmst',$insertData);
                    }
                    else{
                        $insertData = array(
                            'acc_userid'=>$role_id,
                            'acc_optionid'=>$acc
                        );
                        $this->db->insert('accessmst',$insertData);
                    }
                }
            }
            
            public function getAllMenuAccess($role_id,$type = 'role')
            {
                if($role_id == 0)
                    $role_id = -1;
                if($type == 'role')
                    $this->db->where('acc_roleid',$role_id);
                else
                    $this->db->where('acc_userid',$role_id);
                $this->db->select('acc_optionid');
                $result = $this->db->get('accessmst');
                $resultData = array();
                
                if($result->num_rows())
                {
                    foreach($result->result() as $row){
                        array_push($resultData, $row->acc_optionid);
                    }
                    return $resultData;
                }
                else
                    return 0;
            }


            public function action($action,$arrData = array(),$edit_id =0)
            {
                switch($action){
                    case 'insert':
                        $this->db->insert('rolemst',$arrData);
                        return $this->db->insert_id();
                        break;
                    case 'update':
                        $this->db->where('role_id',$edit_id);
                        $this->db->update('rolemst',$arrData);
                        return $edit_id;
                        break;
                    case 'delete':
                        break;
                }
            }
		
	}
?>