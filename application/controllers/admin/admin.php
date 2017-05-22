<?php

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Admin Controller
 * Date: 02 Sept 2016
 * Dependency: adminmodel.php
 */

class Admin extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 02-09-2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();
        $this->load->model('admin/adminmodel', '', TRUE);
        $this->load->model('admin/membersmodel', '', TRUE);
        $this->load->helper('cryptojs_aes');
    }

    /*
     * Purpose: Load Sign-in View.
     * Date: 02-09-2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function index() {

        if (!$this->session->userdata('user_id')) {
            $this->load->view('admin/sign-in');
        }
        else
            redirect('admin/dashboard');
    }

    //Function to change profile details of user
    public function user() {
        $data = array();
        if (!empty($_POST)) {
            if ($this->session->userdata('user_id')) {
                if ($_FILES['image']['name'] != '') {
                    $upload_data = $this->upload_image();
                    if (array_key_exists('error', $upload_data)) {
                        $this->session->set_userdata('toast_error_message', $upload_data['error']);
                        redirect('admin/user', 'refresh');
                    } else {
                        $file_name = $upload_data['file_name'];
                        $this->adminmodel->update_user_credentials($file_name);
                        $this->session->set_userdata('toast_message', 'Profile updated successfully.');
                        redirect('admin/user');
                    }
                } else {
                    $this->adminmodel->update_user_credentials();
                    $this->session->set_userdata('toast_message', 'Profile updated successfully.');
                    redirect('admin/user');
                }
            } else {
                redirect('admin/dashboard', 'refresh');
            }
        }
        else
            $this->template->view('user', $data);
    }

    //Function to verify for login
    public function verifylogin() {
        if ($this->session->userdata('admin_login_captcha_word') == $this->input->post('txtSecureCode')) {
            $this->form_validation->set_rules('txtUsername', 'Username', 'trim|required|xss_clean');
            $this->form_validation->set_rules('txtSecureCode', 'Secure code', 'required|xss_clean');
            $this->form_validation->set_rules('txtPassword', 'Password', 'trim|required|xss_clean|callback_check_database');


            if ($this->form_validation->run() == FALSE) {
                $this->load->helper('captcha');
                $vals = array(
                    'word' => rand_string(5),
                    'img_path' => './assets/captcha/',
                    'img_url' => base_url() . 'assets/captcha/',
                    'img_width' => 150,
                    'img_height' => 35,
                    'expiration' => 7200
                );
                $data['captcha'] = create_captcha($vals);
                $this->session->set_userdata('admin_login_captcha_word', $data['captcha']['word']);

                $this->load->view('admin/sign-in', $data);
            } else {
                redirect('admin/dashboard', 'refresh');
            }
        } else {
            $this->session->set_userdata('toast_error_message', 'Please enter valid username,password and captcha.');
            redirect(base_url() . 'admin/dashboard', 'refresh');
        }
    }

    public function getCaptcha() {

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
        $outputArray = array('admin_login_captcha_word' => $aData['captcha']['word']);
        echo json_encode($outputArray);
    }

    //Function for login into the account
    public function check_database($password) {
        $password = hash('SHA256', $_POST['txtPassword']);
        $username = $this->input->post('txtUsername');
        $result = $this->adminmodel->login($username, $password);
        //print_r($result);die;
        if ($result) {
            foreach ($result as $row) {
                $this->session->set_userdata('user_id', $row->user_id);
                $this->session->set_userdata('user_username', $username);
                $this->session->set_userdata('user_first_name', $row->user_first_name);
                $this->session->set_userdata('user_last_name', $row->user_last_name);
                $this->session->set_userdata('user_email', $row->user_email);
                $this->session->set_userdata('user_type', $row->user_type);
                $this->session->set_userdata('user_image', $row->user_image);
            }
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Invalid username / email address or password.');
            return FALSE;
        }
    }

    //Function for displaying the menu
    public function dashboard() {
        if ($this->session->userdata('user_id')) {
            $data['username'] = $this->session->userdata('username');
            $data['menu'] = $this->adminmodel->load_menu();
            $this->template->view('dashboard', $data);
        } else {
            redirect(base_url() . 'admin', 'refresh');
        }
    }

    //Function for destroying the session and redirecting to the login page
    public function logout() {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_username');
        $this->session->unset_userdata('user_first_name');
        $this->session->unset_userdata('user_last_name');
        $this->session->unset_userdata('user_email');
        $this->session->sess_destroy();
        redirect('admin', 'refresh');
    }

    //Function for uploading the image into folder
    public function upload_image() {
        if (!file_exists(ADMIN_USER_IMAGE_PATH)) {
            mkdir(ADMIN_USER_IMAGE_PATH, 0700, true);
        }
        $config = array(
            'upload_path' => ADMIN_USER_IMAGE_PATH,
            'allowed_types' => "jpg|png|jpeg",
            'overwrite' => FALSE,
            'max_size' => MAX_UPOAD_IMAGE_SIZE,
            'max_height' => "768",
            'max_width' => "1024"
        );
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();
            $file_name = preg_replace('/[^a-zA-Z0-9_.]/s', '', $upload_data['file_name']);
            $config_resize['image_library'] = 'gd2';
            $config_resize['new_image'] = $file_name;
            $config_resize['source_image'] = $upload_data['full_path'];
            $config_resize['width'] = 160;
            $config_resize['height'] = 153;
            $config_resize['maintain_ratio'] = TRUE;
            $config_resize['create_thumb'] = FALSE;
            $this->load->library('image_lib', $config_resize);
            $this->image_lib->resize();
            $data = array('file_name' => $file_name);
            return $data;
        } else {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
    }

    //Function for checking the old password
    public function check_old_password() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $result = $this->adminmodel->login($username, $password);
        if ($result)
            echo 'true';
        else
            echo 'false';
    }

    //Function for resrtting the password
    public function reset_password() {
        if ($this->session->userdata('user_id')) {
            $user = $this->adminmodel->login($this->session->userdata('user_username'), $this->input->post('old_password'));
            if ($user) {
                $password1 = $this->input->post('new_password');
                $password2 = $this->input->post('conf_password');
                if (strcmp($password1, $password2) == 0) {
                    $this->adminmodel->reset_password();
                }
                $this->session->set_userdata('toast_message', 'Password updated successfully.');
            } else {
                $this->session->set_userdata('toast_error_message', 'Invalid old password.');
            }
            redirect('admin/user', 'refresh');
        } else {
            redirect('dashboard', 'refresh');
        }
    }

}

?>