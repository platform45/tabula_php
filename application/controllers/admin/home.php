<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose:Admin Controller
 * Date:02 Sept 2016
 * Dependency: adminmodel.php
 */

class Home extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();
        $this->load->model('admin/adminmodel', 'adminmodel', TRUE);
    }

    /*
     * Purpose: Load Sign-in View.
     * Date: 18-12-2014
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function index() {
        if (!$this->session->userdata('user_id')) {
            $this->load->helper('captcha');
            $vals = array(
                'word' => rand_string(5),
                'img_path' => './assets/captcha/',
                'img_url' => base_url() . 'assets/captcha/',
                'img_width' => 150,
                'img_height' => 35,
                'expiration' => 7200
            );
            $aData['captcha'] = create_captcha($vals);

            $this->session->set_userdata('admin_login_captcha_word', $aData['captcha']['word']);
            $this->load->view('admin/sign-in', $aData);
        }
        else
            redirect('admin/dashboard');
    }

    public function verifylogin() {
        $this->form_validation->set_rules('txtUsername', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('txtPassword', 'Password', 'trim|required|xss_clean|callback_check_database');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('admin/sign-in');
        } else {
            redirect('admin/dashboard', 'refresh');
        }
    }

    public function check_database($password) {
        $username = $this->input->post('txtUsername');
        $result = $this->adminmodel->login($username, $password);

        if ($result) {
            foreach ($result as $row) {
                $this->session->set_userdata('user_id', $row->user_id);
                $this->session->set_userdata('user_username', $username);
                $this->session->set_userdata('user_first_name', $row->user_first_name);
                $this->session->set_userdata('user_email', $row->user_email);
            }

            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Invalid username or password');
            return FALSE;
        }
    }

    /*
     * Purpose: Destroy session and redirect to sign-in page.
     * Date: 18-12-2014
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function logout() {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_username');
        $this->session->unset_userdata('user_first_name');
        $this->session->unset_userdata('user_last_name');
        $this->session->unset_userdata('user_email');
        $this->session->sess_destroy();
        redirect('admin', 'refresh');
    }

}

?>