<?php

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Gallery Model
 * Date: 02 Sept 2016
 * Dependency: Gallerymodel.php
 */

class GalleryModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Method to get data from DB
    public function getData($edit_id = 0) {
        $this->db->select("sli_id,sli_title,sli_image,sli_status", FALSE);
        if ($edit_id) {
            $this->db->where('sli_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
        $this->db->where(
                array(
                    'sli_type' => "Gallery"
        ));
        $this->db->order_by('sli_sequence', 'ASC');
        $result = $this->db->get('slidermst');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    //Method for slider count
    public function slider_count() {
        $this->db->select("sli_id,sli_title,sli_image,sli_status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
        $this->db->where(
                array(
                    'sli_type' => "Gallery"
        ));

        $this->db->order_by('sli_sequence', 'ASC');
        $result = $this->db->get('slidermst');
        return $result->num_rows();
    }

    //Method for slider state count
    public function slider_state_count() {
        $this->db->select("sli_id,sli_title,sli_image,sli_status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
        $this->db->where(
                array(
                    'sli_type' => "Gallery"
        ));
        $this->db->where(
                array(
                    'sli_status' => 1
        ));
        $this->db->order_by('sli_sequence', 'ASC');
        $result = $this->db->get('slidermst');
        return $result->num_rows();
    }

    //Method for insert,edit and delete
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('slidermst', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('sli_id', $edit_id);
                $this->db->where('sli_type', 'Gallery');
                $this->db->update('slidermst', $arrData);
                $this->db->last_query();
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    //Method for updating status
    public function update_status($sli_id = 0) {
        $this->db->select('sli_status');
        $this->db->from('slidermst');
        $this->db->where('sli_id', $sli_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['sli_status'] == 1) {
                $data = array(
                    'sli_status' => 0
                );
            } else {
                $data = array(
                    'sli_status' => 1
                );
            }
            $this->db->where('sli_id', $sli_id);
            $this->db->where('sli_type', 'Gallery');
            $this->db->update('slidermst', $data);
        }
    }

    //Method for getting the sequence
    public function getMaxSeq() {
        $this->db->select_max('sli_sequence');
        $this->db->from('slidermst');
        $this->db->where('sli_type', 'Gallery');
        $this->db->where('is_deleted', '0');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->sli_sequence;
            return $query + 1;
        } else {
            return 1;
        }
    }

    //Method for changing the sequence
    public function change_sequence($faqid = 0, $change_to = 'up') {
        $curr_faq = 0;
        $this->db->select('sli_id,sli_sequence');
        $this->db->where('sli_id', $faqid);
        $this->db->where('sli_type', 'Gallery');
        $result = $this->db->get('slidermst');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('sli_id,sli_sequence');
        if ($change_to == 'up') {
            $this->db->where('sli_sequence <', $curr_faq->sli_sequence);
            $this->db->where('sli_type', 'Gallery');
            $this->db->order_by('sli_sequence', 'DESC');
        } else {
            $this->db->where('sli_sequence >', $curr_faq->sli_sequence);
            $this->db->where('sli_type', 'Gallery');
            $this->db->order_by('sli_sequence', 'ASC');
        }
        $this->db->where('is_deleted', 0);
        $this->db->where('sli_type', 'Gallery');
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
            $this->db->where('sli_type', 'Gallery');
            $this->db->update('slidermst', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->sli_sequence);
            $update_data = array('sli_sequence' => $update_seq);
            $this->db->where('sli_id', $other_menu->sli_id);
            $this->db->where('sli_type', 'Gallery');
            $this->db->update('slidermst', $update_data);
            return 'DONE';
        }
    }

}

?>