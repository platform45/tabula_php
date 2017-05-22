<?php

class Restaurantmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0) {
        $this->db->select("u.user_id,u.user_contact,u.user_password,u.user_description,u.web_domain,u.restaurant_detail_url,u.restaurant_owner_name,u.user_image,u.restaurant_hero_image,u.restaurant_floor_image,u.street_address1,u.user_first_name,u.average_spend,u.bank_name,u.bank_account_number,u.bank_branch_number,u.bank_account_holder_name,u.latitude,u.longitude,u.user_email,u.user_type,u.user_status,c.cou_id,r.region_id,ci.city_id,res.open_close_day,res.open_close_status,res.open_time_from,res.close_time_to,ad.add_id,count(t10.top10_id) as top10_count,t10.top10_id");
        $this->db->from("usermst u");

        $this->db->join('country c', 'c.cou_id = u.country_id', 'left');
        $this->db->join('region r', 'r.region_id = u.region_id', 'left');
        $this->db->join('city ci', 'ci.city_id = u.city_id', 'left');
        $this->db->join('restaurant_open_close_time res', 'res.user_id = u.user_id', 'left');
        $this->db->join('addmst ad', 'ad.user_id = u.user_id', 'left');
        $this->db->join('top10_restaurants t10', 't10.user_id = u.user_id', 'left');
        if ($edit_id) {
            $this->db->where('u.user_id', $edit_id);
        }
        $this->db->where(
            array(
                'u.is_deleted' => '0'
                ));
        $this->db->where('u.user_type =', '3');
        $this->db->order_by('user_id', 'DESC');
        $this->db->group_by('user_id');

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

    public function get_restaurant_time($edit_id) {
        $this->db->select("open_close_id,user_id,open_close_day,open_close_status, open_time_from,close_time_to");
        $this->db->from('restaurant_open_close_time');
        $this->db->where('user_id', $edit_id);
        $this->db->group_by('open_close_id');
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            if ($edit_id)
                return $result->result_array();
        } else
        return 0;
    }

    public function insert_restaurant_time($adata) {
        $this->db->insert('restaurant_open_close_time', $adata);
        return $this->db->insert_id();
    }

    public function update_restaurant_time1($id) {

        $this->db->where("user_id", $id);
        $this->db->delete('restaurant_open_close_time');
    }

    public function getlist($edit_id = 0) {
        $this->db->select("ca.rca_id,ca.user_id,ca.rca_cuisine_ambience_id,ca.rca_type,u.user_first_name");
        $this->db->from("restaurant_cuisine_ambience ca");
        $this->db->join('usermst u', 'u.user_id= ca.user_id', 'left');
        $this->db->where('ca.user_id', $edit_id);
        $result = $this->db->get();


        if ($result->num_rows()) {
            if ($edit_id)
                return $result->result_array();
        }
        else
            return 0;
    }

    public function get_diet_list($edit_id = 0) {

        $this->db->select("dr.dra_id,dr.user_id,dr.diet_id,u.user_first_name");
        $this->db->from("dietary_restaurant dr");
        $this->db->join('usermst u', 'u.user_id= dr.user_id', 'left');
        $this->db->where('dr.user_id', $edit_id);
        $result = $this->db->get();

        if ($result->num_rows()) {
            if ($edit_id)
                return $result->result_array();
        }
        else
            return 0;
    }

    public function update_dietary($user_id, $dietary) {

        $this->db->where("user_id", $user_id);
        $this->db->delete('dietary_restaurant');
        foreach ($dietary as $diet) {
            $data = array(
                'dra_id' => '',
                'user_id' => $user_id,
                'diet_id' => $diet
                );
            $this->db->insert('dietary_restaurant', $data);
        }
    }

    public function update_cuisine_ambience_data($user_id, $cuisines, $ambiences) {
        $this->db->where("user_id", $user_id);
        $this->db->delete('restaurant_cuisine_ambience');

        foreach ($cuisines as $cuisine) {

            $data = array(
                'rca_id' => '',
                'user_id' => $user_id,
                'rca_cuisine_ambience_id' => $cuisine,
                'rca_type' => '1'
                );
            $this->db->insert('restaurant_cuisine_ambience', $data);
        }

        // Insert ambience
        foreach ($ambiences as $ambience) {
            $data = array(
                'user_id' => $user_id,
                'rca_cuisine_ambience_id' => $ambience,
                'rca_type' => '2'
                );
            $this->db->insert('restaurant_cuisine_ambience', $data);
        }
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

    public function insert_add($id) {
        $data = array("user_id" => $id);
        $this->db->insert('addmst', $data);
        return $this->db->insert_id();
    }

    public function delete_add($id) {
        $this->db->where("user_id", $id);
        $this->db->delete('addmst');
    }

    public function count_top10() {
        $this->db->select('top10_id');
        $result = $this->db->get('top10_restaurants');
        return $result->num_rows();
    }

    public function insert_top10($id) {
        $data = array("user_id" => $id);
        $this->db->insert('top10_restaurants', $data);
        return $this->db->insert_id();
    }

    public function delete_top10($id) {
        $this->db->where("user_id", $id);
        $this->db->delete('top10_restaurants');
    }

    public function get_closing_slots($from_time_slot)
    {
//        $this->db->select("slot_id,time_slot");
//        $this->db->from("timeslot");
//        $this->db->where('slot_id >', $from_time_slot_id);
//        $result = $this->db->get();
//        return $result->result_array();

        $this->db->select("slot_id,time_slot");
        $this->db->from("timeslot");
        $this->db->where('time_slot >', $from_time_slot);
        $result = $this->db->get();
        return $result->result_array();
    }

}

?>