<?php
/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Dietary Model
 * Date: 02 Sept 2016
 * Dependency: dietary.php
 */

class Dietarymodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Method to list data
    public function getData($edit_id = 0) {
        $this->db->select("diet_id,diet_preference,is_deleted,is_active", FALSE);
        if ($edit_id) {
            $this->db->where('diet_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
         $this->db->order_by('diet_id', 'DESC');
        $result = $this->db->get('dietary_preference');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }


       public function getDataRestaurant($edit_id = 0) {
        $this->db->select("diet_id,diet_preference,is_deleted,is_active", FALSE);
        if ($edit_id) {
            $this->db->where('diet_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => 0,
                    'is_active' => 1
        ));

        $result = $this->db->get('dietary_preference');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    //Function for the total state count of dietary
    public function dietary_state_count() {
        $this->db->select("diet_id,diet_preference,is_deleted,is_active", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));

        $this->db->where(
                array(
                    'is_active' => 1
        ));
        $result = $this->db->get('dietary_preference');
        return $result->num_rows();
    }

    //Method to add,edit and delete
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('dietary_preference', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('diet_id', $edit_id);
                $this->db->update('dietary_preference', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    //Metrhod to update status
    public function update_status($diet_id = 0) {
        $this->db->select('is_active');
        $this->db->from('dietary_preference');
        $this->db->where('diet_id', $diet_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['is_active'] == 1) {
                $data = array(
                    'is_active' => 0
                );
            } else {
                $data = array(
                    'is_active' => 1
                );
            }
            $this->db->where('diet_id', $diet_id);
            $this->db->update('dietary_preference', $data);
        }
    }

    public function dietary_count() {
        $this->db->select("diet_id,diet_preference,is_deleted,is_active", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
        $result = $this->db->get('dietary_preference');
        return $result->num_rows();
    }

}?>