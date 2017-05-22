<?php
/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Slider Model
 * Date: 02 Sept 2016
 * Dependency: slider.php
 */

class Cuisinemodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Method to list data
    public function getData($edit_id = 0) {
        $this->db->select("cuisine_id,cuisine_name,is_deleted,status", FALSE);
        if ($edit_id) {
            $this->db->where('cuisine_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => '0'
        ));
         $this->db->order_by('cuisine_id', 'DESC');
        $result = $this->db->get('cuisine');
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
        $this->db->select("cuisine_id,cuisine_name,is_deleted,status", FALSE);
        if ($edit_id) {
            $this->db->where('cuisine_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => '0',
                    'status' => '1'
        ));

        $result = $this->db->get('cuisine');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function slider_state_count() {
        $this->db->select("cuisine_id,cuisine_name,is_deleted,status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => '0'
        ));

        $this->db->where(
                array(
                    'status' => '1'
        ));
        $result = $this->db->get('cuisine');
        return $result->num_rows();
    }

    //Method to add,edit and delete
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('cuisine', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('cuisine_id', $edit_id);
                $this->db->update('cuisine', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    //Metrhod to update status
    public function update_status($sli_id = 0) {
        $this->db->select('status');
        $this->db->from('cuisine');
        $this->db->where('cuisine_id', $sli_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['status'] == 1) {
                $data = array(
                    'status' => '0'
                );
            } else {
                $data = array(
                    'status' => '1'
                );
            }
            $this->db->where('cuisine_id', $sli_id);
            $this->db->update('cuisine', $data);
        }
    }

    public function slider_count() {
        $this->db->select("cuisine_id,cuisine_name,is_deleted,status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => '0'
        ));
        $result = $this->db->get('cuisine');
        return $result->num_rows();
    }

    public function change_sequence($faqid = 0, $change_to = 'up') {
        // get sequence of current menu
        $curr_faq = 0;
        $this->db->select('sli_id,sli_sequence');
        $this->db->where('sli_id', $faqid);
        $this->db->where('sli_type', 'Slider');
        $result = $this->db->get('slidermst');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }

        $other_menu = 0;
        $this->db->select('sli_id,sli_sequence');
        if ($change_to == 'up') {
            $this->db->where('sli_sequence <', $curr_faq->sli_sequence);
            $this->db->where('sli_type', 'Slider');
            $this->db->order_by('sli_sequence', 'DESC');
        } else {
            $this->db->where('sli_sequence >', $curr_faq->sli_sequence);
            $this->db->where('sli_type', 'Slider');
            $this->db->order_by('sli_sequence', 'ASC');
        }
        $this->db->where('is_deleted', 0);
        $this->db->where('sli_type', 'Slider');
        $this->db->limit(1);

        $result = $this->db->get('slidermst');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        }
        else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->sli_sequence);
            $update_data = array('sli_sequence' => $update_seq);
            $this->db->where('sli_id', $curr_faq->sli_id);
            $this->db->where('sli_type', 'Slider');
            $this->db->update('slidermst', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->sli_sequence);
            $update_data = array('sli_sequence' => $update_seq);
            $this->db->where('sli_id', $other_menu->sli_id);
            $this->db->where('sli_type', 'Slider');
            $this->db->update('slidermst', $update_data);

            return 'DONE';
        }
    }
}?>