<?php

class Dashboard extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 18-12-2014
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();
        $this->load->model('admin/dashboardmodel', '', TRUE);
        $this->load->model('admin/adminmodel', '', TRUE);
    }

    /*
     * Purpose: Load Sign-in View.
     * Date: 18-12-2014
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function index() {
        if ($this->session->userdata('user_id')) {

            $data['title'] = "Dashboard";
            $data['username'] = $this->session->userdata('username');
            $data['menu'] = $this->adminmodel->load_menu();
            $data['total_count'] = $this->dashboardmodel->get_all_user();
            $data['total_active_users_count'] = $this->dashboardmodel->get_all_active_user();
            $this->template->view('dashboard', $data);
        } else {
            redirect(base_url() . 'admin', 'refresh');
        }
    }

}

?>