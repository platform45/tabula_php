<?php
/*
* Programmer Name:SK
* Purpose:Admin Controller
* Date:18-12-2014
* Dependency: adminmodel.php
*/
class User extends CI_Controller
{
    /*
    * Purpose: Constructor.
    * Date: 18-12-2014
    * Input Parameter: None
    *  Output Parameter: None
    */
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/adminmodel','adminmodel',TRUE);
    }
    // User Management.
    /*
    * Purpose: To change user profile.
    * Date: 18-12-2014
    * Input Parameter: None
    *  Output Parameter: None
    */
    public function index()
    {
        $data = array();
        if(!empty($_POST)){
            if ($this->session->userdata('user_id'))
            {       
                    if ($_FILES['image']['name'] != '') 
                    {
                        $upload_data = $this->upload_image();
                        if(array_key_exists('error', $upload_data))
                        {
                            $this->session->set_userdata('toast_error_message',$upload_data['error']);
                            redirect('admin/user','refresh');
                        }
                        else
                        {
                            $file_name = $upload_data['file_name'];
                            $this->adminmodel->update_user_credentials($file_name);
                            $this->session->set_userdata('toast_message','Profile updated successfully');
                            redirect('admin/user');
                        }
                    }
                    else
                    {
                        $this->adminmodel->update_user_credentials();
                        $this->session->set_userdata('toast_message','Profile updated successfully');
                        redirect('admin/user');
                    }
            }
            else
            {
                    redirect('admin/dashboard', 'refresh');
            }
        }
        else
            $this->template->view('user',$data);
        
    }
    
    public function reset_password()
    {
            if ($this->session->userdata('user_id'))
            {
                 $password = hash('SHA256', $_POST['old_password']);
                    $user = $this->adminmodel->login($this->session->userdata('user_username'), $password);
                    if($user)
                    {
                            $password1 = hash('SHA256', $_POST['new_password']);
                            $password2 = hash('SHA256', $_POST['conf_password']);
                            if(strcmp($password1,$password2) == 0)
                            {
                                    $this->adminmodel->reset_password();
                            }
                            $this->session->set_userdata('toast_message','Password updated successfully.');
                    }
                    else
                    {
                            $this->session->set_userdata('toast_error_message','Invalid old password.');
                    }
                    redirect('admin/user','refresh');
            }
            else
            {
                    redirect('dashboard', 'refresh');
            }
    }
    
    public function check_old_password()
    {
            $username = $this->input->post('username');
            $password = hash('SHA256', $_POST['old_password']);
            $result = $this->adminmodel->login($username,$password);
            if($result)
                    echo 'true';
            else
                    echo 'false';
    }
    
       public function upload_image()
    {
        if (!file_exists(ADMIN_USER_IMAGE_PATH)) {
                mkdir(ADMIN_USER_IMAGE_PATH, 0700,true);
        }
            $config =  array(
                            'upload_path'     => ADMIN_USER_IMAGE_PATH,
                            'allowed_types'   => "jpg|jpeg|JPG|JPEG",
                            'overwrite'       => FALSE,
                            'max_size'        => MAX_UPOAD_IMAGE_SIZE,
                            'max_height'      => "768",
                            'max_width'       => "1024"
                            );
                    $this->load->library('upload', $config);
                    if($this->upload->do_upload('image'))
                    {
                        $upload_data = $this->upload->data();
                        $file_name= preg_replace('/[^a-zA-Z0-9_.]/s', '', $upload_data['file_name']);
                        $config_resize['image_library'] = 'gd2';
                        $config_resize['new_image'] = $file_name;
                        $config_resize['source_image'] = $upload_data['full_path'];
                        $config_resize['width'] = 160;
                        $config_resize['height'] = 153;
                        $config_resize['maintain_ratio'] = TRUE;
                        $config_resize['create_thumb'] = FALSE;
                        $this->load->library('image_lib',$config_resize); 
                        $this->image_lib->resize();
                        $data = array('file_name' => $file_name);
                        return $data;
                    }
                    else
                    {
                        $error = array('error' => $this->upload->display_errors());
                        return $error;
                    }
                    
    }
    
}
?>