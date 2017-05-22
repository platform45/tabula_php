<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose: Model for controlling database interactions regarding the content.
 * Date: 02 Sept 2016
 * Dependency: None
 */

class Adminmodel extends CI_Model {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    public function __construct() {
        parent::__construct();
    }

    //Method to load content menu on the admin page.
    public function load_menu() {
        $this->db->select(array('opt.opt_option_name', 'opt.opt_pagename', 'opt.opt_sequence_no', 'opt.opt_icon'));
        $this->db->from('optionmst opt');

        if ($this->session->userdata('user_type') > 0) {
            if ($this->session->userdata('user_type') == 3)
                $this->db->where_in("opt.opt_optionid", $this->config->item('restaurant_admin_menu'));
            else
                $this->db->join('accessmst acc', 'acc.acc_optionid = opt.opt_optionid AND acc.acc_userid =' . $this->session->userdata('user_id'));
        }
        if ($this->session->userdata('user_type') != 3)
            $this->db->where_not_in("opt.opt_optionid", $this->config->item('restaurant_admin_menu'));

        $this->db->order_by('opt.opt_sequence_no', 'asc');
        $this->db->where('opt.opt_status', 1);
        $query = $this->db->get();
        $arr = array();
        foreach ($query->result_array() as $row) {
            $arr[$row['opt_option_name']] = array($row['opt_pagename'], $row['opt_sequence_no'], $row['opt_icon']);
        }
        $this->session->set_userdata('menu', $arr);
        return $query->result_array();
    }

    //Method to check login and usernamestrip
    public function login($username, $password) {
        $this->db->select(array(
            'user_id',
            'user_username',
            'user_password',
            'user_image',
            'user_first_name',
            'user_last_name',
            'user_type',
            'user_email'
        ));
        $this->db->from('usermst');
        $where_condition = "(user_username = '" . $username . "' OR user_email = '" . $username . "') AND ( user_type = " . SEARCH_ADMIN_TYPE . " OR user_type = " . SEARCH_RESTAURANT_TYPE . " OR user_type = " . SEARCH_SUBADMIN_TYPE . ")";
        $this->db->where($where_condition);
        $this->db->where('user_password', $password);
        $this->db->where('user_status', '1');
        $this->db->where('is_deleted', '0');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    //Function to update User credentials
    public function update_user_credentials($img = '') {

        $data = array(
            'user_first_name' => $this->input->post('txtfname'),
            'user_last_name' => $this->input->post('txtlname'),
            'user_email' => $this->input->post('txtemail'),
        );
        if (!empty($img)) {
            $data['user_image'] = $img;
        }
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->update('usermst', $data);
        $this->session->set_userdata('user_first_name', $this->input->post('txtfname'));
        $this->session->set_userdata('user_last_name', $this->input->post('txtlname'));
        $this->session->set_userdata('user_email', $this->input->post('txtemail'));
        if (!empty($img)) {
            $this->session->set_userdata('user_image', $img);
        }
    }

    //Function to reset password
    public function reset_password() {
        $data = array(
            'user_password' => hash('SHA256', $_POST['new_password'])
        );
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->update('usermst', $data);
    }

}

?>