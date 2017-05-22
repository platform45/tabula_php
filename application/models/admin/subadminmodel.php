<?php

class Subadminmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0) {
        $this->db->select("user_id,user_username,user_contact,user_password,user_image,user_first_name,user_last_name,CONCAT_WS(' ',user_first_name,user_last_name) as fullname,user_email,user_type,role_id,user_status", FALSE);
        $this->db->from("usermst");

        if ($edit_id) {
            $this->db->where('user_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => '0'
        ));
        $this->db->where(
                array(
                    'user_type' => '1'
        ));
        $this->db->group_by("user_id");
         $this->db->order_by('user_id', 'DESC');
        $result = $this->db->get();
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function getRoles() {
        $this->db->select("*");
        $this->db->from("rolemst");
        $this->db->where('role_status', 1);
        $this->db->where('role_removed', 0);
        $result = $this->db->get("");

        return $result->result_array();
    }

    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('usermst', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('user_id', $edit_id);
                $this->db->update('usermst', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

}

?>