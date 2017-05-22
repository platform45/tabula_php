<?php

class Membersmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0) {
        $this->db->select("u.user_id,u.user_username,u.user_contact,u.date_of_birth,u.gender,u.user_password,u.user_image,u.user_first_name,u.user_email,u.user_type,u.role_id,u.user_status,c.cou_id,r.region_id,ci.city_id,u.notification_setting , u.mvg_points, l.loyalty_points", FALSE);
        $this->db->from("usermst u");

        $this->db->join('country c', 'u.country_id= c.cou_id', 'left');
        $this->db->join('region r', 'u.region_id= r.region_id', 'left');
        $this->db->join('city ci', 'u.city_id= ci.city_id', 'left');
        $this->db->join('loyalty l', 'u.user_id = l.user_id', 'left');
        if ($edit_id) {
            $this->db->where('u.user_id', $edit_id);
        }
        $this->db->where(
                array(
                    'u.is_deleted' => '0'
        ));
        $this->db->where('u.user_type =', '2');
        $this->db->group_by('u.user_id');
        $this->db->order_by('u.user_id','DESC');
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

    public function getLoyalty($edit_id = 0, $loyalty_id = 0) {
        $this->db->select('loyalty_id,user_id,loyalty_points');
        $this->db->from('loyalty');
        if ($edit_id) {
            $this->db->where('user_id', $edit_id);
        }
        if ($loyalty_id) {
            $this->db->where('loyalty_id', $loyalty_id);
        }
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

    public function insertLoyalty($user_id, $loyalty) {
        $data = array(
            'loyalty_id' => '',
            'user_id' => $user_id,
            'loyalty_points' => $loyalty
        );
        $this->db->insert('loyalty', $data);
    }

    public function updateLoyalty($edit_id, $loyalty) {
        $data = array(
            'user_id' => $edit_id,
            'loyalty_points' => $loyalty
        );
        $this->db->update('loyalty', $data);
        $this->db->where('user_id', $edit_id);
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