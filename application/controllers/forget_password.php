<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forget_password extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('password_forget_model','',TRUE);
    }
    
    public function index()
    {
        $hash = $this->input->get("hash");
        $data = array();
        $data['message'] = "";
        if($hash)
        {
            $hashData = $this->password_forget_model->getDetailsFromHash($hash);
            if($hashData)
            {
                if($this->input->post())
                {
                    $password1 = $this->input->post("txtpassword");
                    $password2 = $this->input->post("txtcnf_password");
                    
                    if(($password1 == $password2) && $password1 != '' && $password2 != '')
                    {
                        $update_array = array(
                            'user_password' => md5($password1)
                        );
                        $this->db->where("user_id", $hashData['user_id']);
                        $this->db->update("usermst", $update_array);
                        redirect(base_url("thank_you"));
                    }
                    else
                    {
                        if(($password1 != $password2))
                        {
                            $data['message'] = "New password and confirm password do not match.";
                        }
                        $data['aUserDetail'] = $hashData;
                        $this->load->view("change_password_view", $data);
                    }
                }
                else
                {
                    $data['aUserDetail'] = $hashData;
                    $this->load->view("change_password_view", $data);
                }
            }
            else
            {
                redirect(base_url("page_not_found"));
            }
        }
        else
        {
            redirect(base_url("page_not_found"));
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */