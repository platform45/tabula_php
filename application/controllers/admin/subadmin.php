<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';
/*
* Programmer Name:SK
* Purpose:Sub Admin Controller
* Date:19-12-2014
* Dependency: subadminmodel.php
*/
class Subadmin extends Access
{
    /*
    * Purpose: Constructor.
    * Date: 19-12-2014
    * Input Parameter: None
    *  Output Parameter: None
    */
    function __construct()
    {
        parent::__construct();
        error_reporting();
        $this->load->model('admin/subadminmodel','',TRUE);
        $this->load->model('admin/rolemodel','rolemodel',TRUE);
    }
    
    /*
        * Purpose: To Load subadmin
        * Date: 19 Dec 2014
        * Input Parameter: None
        * Output Parameter: None
        */
	public function index()
	{
                $data['activeRole']=$this->subadminmodel->getRoles();
                $data['subAdmin'] = $this->subadminmodel->getData();
                $this->template->view('subadmin',$data);
            
	}
        
       /*
        * Purpose: To add/edit subadmin
        * Date: 19 Dec 2014
        * Input Parameter: None
        * Output Parameter: None
        */
	public function addedit($edit_id = 0)
	{
            $this->load->model('admin/rolemodel','',TRUE);
          
                $data = array();
                $data['edit_id'] = $edit_id;
                $formData = array(
                    'txtusername'=>'',
                    'txtfname'=>'',
                    'txtlname'=>'',
                    'txtemail'=>'',
                    'new_password'=>'',
                    'conf_password'=>'',
                    'new_img'=>''
                );
                
                if(empty($_POST))
                {
                    
                        $data['activeRole']=$this->rolemodel->getActiveRoles();
                        $data['allMenu']=$this->rolemodel->getAllMenu();
                        if($edit_id)
                        {
                            $editData = $this->subadminmodel->getData($edit_id);
                            if($editData){
                                $formData = array(
                                    'txtusername'=>$editData->user_username,
                                    'txtfname'=>$editData->user_first_name,
                                    'txtlname'=>$editData->user_last_name,
                                    'txtemail'=>$editData->user_email,
                                    'new_img'=>$editData->user_image
                                );
                            }
                            
                            if($editData->role_id==3)
                            {
                                 $data['menuAccess']=$this->rolemodel->getAllMenuAccess($editData->role_id);
                            }
                            else
                            {
                             $data['menuAccess']=$this->rolemodel->getAllMenuAccess($edit_id,'subadmin');
                            }
                            
                        }
                        $data['formData']=$formData; 
                        $this->template->view('addsubadmin',$data);
                }
                else{
                    if(!empty($_POST['txtfname']) )
                    {
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    
                    // upload image if selected
                    $file_name = "";
                    if(!empty($_FILES['image']['name']))
                    {
                        $upload_data = $this->upload_subadmin_image();
                        $file_name = $upload_data['file_name'];
                    }
                        
                    // end of image upload
                    
                    if($edit_id){
                        $update_data = array(
                            'user_first_name'=> $this->input->post('txtfname'),
                            'user_last_name'=> $this->input->post('txtlname'),
                            'user_email'=> $this->input->post('txtemail')
                        );
                         $userPwd = hash('SHA256', $_POST['conf_password']);
                        $userImg = $this->input->post('new_img');
                        if(!empty($_POST['conf_password'])){
                            $update_data['user_password'] = $userPwd;
                        }
                        if(!empty($file_name)){
                            $update_data['user_image'] = $file_name;
                            $result = $this->subadminmodel->action('update',$update_data,$edit_id);
                            $this->rolemodel->update_access($_POST['chkaccess'],$result,'subadmin');
                            $this->session->set_userdata('uploaded_img', $file_name);
                            $this->session->set_userdata('redirect_to', 'admin/subadmin');
                            redirect('admin/subadmin_crop');
                        }
                                                
                        $result = $this->subadminmodel->action('update',$update_data,$edit_id);
                        $this->rolemodel->update_access($_POST['chkaccess'],$result,'subadmin');
                        if($result){
                           if($this->input->post('sel_role')==3)
                            {
                                 $data['menuAccess']=$this->rolemodel->getAllMenuAccess($this->input->post('sel_role'));
                            }
                            else
                            {
                                 $data['menuAccess']=$this->rolemodel->getAllMenuAccess($edit_id,'subadmin');
                            }
                            $this->session->set_userdata('toast_message','Record updated successfully.');
                            redirect('admin/subadmin');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record.');
                        }
                    }
                    else{
                        $password = hash('SHA256', $_POST['new_password']);
                        $insert_data = array(
                            'user_username'=> $this->input->post('txtusername'),
                            'user_email'=>  $this->input->post('txtemail'),
                            'user_password'=> $password,
                            'user_image'=>$file_name,
                            'user_first_name'=> $this->input->post('txtfname'),
                            'user_last_name'=> $this->input->post('txtlname'),
                            'user_type'=> '1',
                            'user_status'=> '1'
                        );
                        $result = $this->subadminmodel->action('insert',$insert_data);
                        if($result){
                            $this->rolemodel->update_access($_POST['chkaccess'],$result,'subadmin');
                           $this->session->set_userdata('toast_message', 'Record added successfully.');
                               $this->session->set_userdata('uploaded_img', $file_name);
                             $this->session->set_userdata('redirect_to', 'admin/subadmin');
                            redirect('admin/subadmin_crop');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record.');
                        }
                        
                    }
                }
                else{
                    $this->session->set_userdata('toast_error_message','All fields are mandatory.');
                    redirect('admin/subadmin/addedit/'.$edit_id,'refresh');
                }
                }
            
        }
        
        public function delete_subadmin($user_id = 0)
        {          
                $update_array = array(
                    'is_deleted'=>'1'
                );
                $this->subadminmodel->action('update',$update_array,$user_id);
                $this->session->set_userdata('toast_message','Record deleted successfully.');
                redirect('admin/subadmin','refresh');

        }
        
        public function upload_subadmin_image()
	{
            if (!file_exists(SUBADMIN_IMAGE_PATH)) {
                mkdir(SUBADMIN_IMAGE_PATH, 0700,true);
            }
            $file_name = $_FILES['image']['name'];
            $file_name= preg_replace('/[^a-zA-Z0-9_.]/s', '', $file_name);
            
		$config =  array(
                        'upload_path'     => SUBADMIN_IMAGE_PATH,
                        'allowed_types'   => "jpg|png|jpeg",
                        'overwrite'       => FALSE,
                        'max_size'        => MAX_UPOAD_IMAGE_SIZE,
                        'max_height'      => "2160",
                        'max_width'       => "4096",
                        'file_name'	    => $file_name
                    );
                    $this->load->library('upload', $config);
                    if($this->upload->do_upload('image'))
                      {
                    $upload_data = $this->upload->data();
                    $data = array('file_name' => $upload_data['file_name']);
                    return $data;
                }
                    else
                    {
                        $error = array('error' => $this->upload->display_errors());
                        return $error;
                    }
			
	}
        
        public function update_subadmin_status()
        {
            $this->load->model('subadminmodel','',TRUE);
                $user_id = $this->input->post('user_id');
               
                $changeStatus = $this->input->post('changeStatus');
                if($changeStatus)
                    $changeStatus = '0';
                else
                    $changeStatus = '1';
                $update_array = array(
                    'user_status'=>$changeStatus
                );
                $this->subadminmodel->action('update',$update_array,$user_id);
                return 1;
        }

       public function check_username_exists($user_id=0){

            
            $username = $this->input->post("title");
            $this->db->select("user_id");
            $this->db->where("is_deleted",'0');
            $this->db->where("user_username",$username);
             $this->db->where("user_type",'1');
             if($user_id)
            $this->db->where("user_id <>",$user_id);
            $result =$this->db->get("usermst");
            
            if($result->num_rows() > 0)
            {
                echo "false";
            }
            else
            {
                echo "true";
            }
                
        }
        public function check_email_exist($user_id=0){
            
            $email = $this->input->post("title");
            $this->db->select("user_id");
            $this->db->where("is_deleted",0);
            $this->db->where("user_email",$email);
            if($user_id)
             $this->db->where("user_id <>",$user_id);
            $result =$this->db->get("usermst");
            
            if($result->num_rows() > 0)
            {

                echo "false";

            }
            else
            {
                echo "true";
            }
                
        }

}
?>