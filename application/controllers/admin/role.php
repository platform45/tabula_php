<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: rolemodel.php
*/
class Role extends CI_Controller
{
        /*
        * Purpose: Constructor.
        * Date: 19 Dec 2014
        * Input Parameter: None
        *  Output Parameter: None
        */
	function __construct()
        {
            parent::__construct();

            $this->load->model('admin/rolemodel','',TRUE);
            
        }
           
        /*
        * Purpose: To Load role
        * Date: 19 Dec 2014
        * Input Parameter: None
        * Output Parameter: None
        */
	public function index()
	{
            if($this->session->userdata('user_id'))
            {
                $data['roleData'] = $this->rolemodel->getData();
                $this->template->view('role',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        
       /*
        * Purpose: To add/edit role
        * Date: 19 Dec 2014
        * Input Parameter: None
        * Output Parameter: None
        */
	public function addedit($edit_id = 0)
	{
            if($this->session->userdata('user_id'))
            {
                $data = array();
                $data['edit_id'] = $edit_id;
                $formData = array(
                    'txtrolediscription'=>'',
                    'txtroletitle'=>'',
                );
                
                if(empty($_POST))
                {
                        if($edit_id)
                        {
                            $editData = $this->rolemodel->getData($edit_id);
                            if($editData){
                                $formData = array(
                                    'txtrolediscription'=>$editData->role_description,
                                    'txtroletitle'=>$editData->role_type
                                );
                            }
                        }
                        $data['allMenu']=$this->rolemodel->getAllMenu();
                        $data['menuAccess']=$this->rolemodel->getAllMenuAccess($edit_id);
                        $data['formData']=$formData;    
                        $this->template->view('addrole',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    
                    if($edit_id){
                        $update_data = array(
                            'role_type'=> mysql_real_escape_string($this->input->post('txtroletitle')),
                            'role_description'=> mysql_real_escape_string($this->input->post('txtrolediscription'))
                        );
                        $result = $this->rolemodel->action('update',$update_data,$edit_id);
                        if($result){
                            $this->rolemodel->update_access($_POST['chkaccess'],$result);
                            $this->session->set_userdata('toast_message','Record updated successfully');
                            redirect('admin/role');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                    }
                    else{
                        $insert_data = array(
                            'role_type'=> mysql_real_escape_string($this->input->post('txtroletitle')),
                            'role_description'=> mysql_real_escape_string($this->input->post('txtrolediscription')),
                            'role_status'=> 1,
                            'role_removed'=> 0
                        );
                        $result = $this->rolemodel->action('insert',$insert_data);
                        if($result){
                            $this->rolemodel->update_access($_POST['chkaccess'],$result);
                            $this->session->set_userdata('toast_message','Record added successfully');
                            redirect('admin/role');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                        
                    }
                }
            }
            else
            {
                redirect('admin', 'refresh');
            }
        }
        
        public function delete_role($user_id = 0)
        {
            $update_array = array(
                'role_removed'=>1
            );
            $this->rolemodel->action('update',$update_array,$user_id);
            $this->session->set_userdata('toast_message','Record deleted successfully');
            redirect('admin/role','refresh');

        }
        
        public function get_acccess_options($role_id =0){
            $menuAccess = $this->rolemodel->getAllMenuAccess($role_id,'role');
            echo json_encode($menuAccess);
        }
        
        public function update_role_status()
        {
            $this->load->model('rolemodel','',TRUE);
                $role_id = $this->input->post('role_id');
                $changeStatus = $this->input->post('changeStatus');
                if($changeStatus)
                    $changeStatus = 0;
                else
                    $changeStatus = 1;
                $update_array = array(
                    'role_status'=>$changeStatus
                );

                $this->rolemodel->action('update',$update_array,$role_id);
                return 1;
        }
}
?>