<?php

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Gallery Model
 * Date: 02 Sept 2016
 * Dependency: Gallerymodel.php
 */

class Restaurantgallerymodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Method to get data from DB
    public function getData($user_id = 0, $edit_id = 0,$type=0) {
        $this->db->select("rg.user_id,rg.gal_image ,rg.gal_id", FALSE);
        $this->db->from('restuarant_gallery rg');


        $this->db->join('usermst u', 'u.user_id = rg.user_id', 'left');
        $this->db->order_by('gal_sequence', 'ASC');
        $result = $this->db->get();
        if ($edit_id) {
            $this->db->where('gal_id', $edit_id);
        }
        if($type==1)
        {
            $this->db->where('gal_status', '1');
        }
        $this->db->where(
            array(
                'is_deleted' => '0'
            ));

        $this->db->where(
            array(
                'user_id' => $user_id
            ));
        $this->db->order_by('gal_sequence', 'ASC');
        $result = $this->db->get('restuarant_gallery');
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

        $this->db->select("gal_image ,gal_id,is_deleted,gal_status", FALSE);
        $this->db->where(
            array(
                'is_deleted' => '0'
            ));
        $this->db->order_by('gal_sequence', 'ASC');
        $result = $this->db->get('restuarant_gallery');
        return $result->num_rows();
    }

    //Method for slider state count
    public function slider_state_count() {

        $this->db->select("gal_image ,gal_id,is_deleted,gal_status", FALSE);
        $this->db->where(
            array(
                'is_deleted' => '0'
            ));
        $this->db->where(
            array(
                'gal_status' => '1'
            ));
        $this->db->order_by('gal_sequence', 'ASC');
        $result = $this->db->get('restuarant_gallery');
        return $result->num_rows();
    }

    //Method for insert,edit and delete
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('restuarant_gallery', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('gal_id', $edit_id);
                $this->db->update('restuarant_gallery', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    //Method for updating status
    public function update_status($gal_id = 0) {
        $this->db->select('gal_status');
        $this->db->from('restuarant_gallery');
        $this->db->where('gal_id', $gal_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['gal_status'] == 1) {
                $data = array(
                    'gal_status' => '0'
                );
            } else {
                $data = array(
                    'gal_status' => '1'
                );
            }
            $this->db->where('gal_id', $gal_id);
            $this->db->update('restuarant_gallery', $data);
        }
    }

    public function getMaxSeq() {
        $this->db->select_max('gal_sequence');
        $this->db->from('restuarant_gallery');
        $this->db->where('is_deleted', '0');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->gal_sequence;
            return $query + 1;
        } else {
            return 1;
        }
    }

    //Method for changing the sequence
    public function change_sequence($faqid = 0, $change_to = 'up') {

        $curr_faq = 0;
        $this->db->select('gal_id,gal_sequence');
        $this->db->where('gal_id', $faqid);
        $result = $this->db->get('restuarant_gallery');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('gal_id,gal_sequence');
        if ($change_to == 'up') {
            $this->db->where('gal_sequence <', $curr_faq->gal_sequence);
            $this->db->order_by('gal_sequence', 'DESC');
        } else {
            $this->db->where('gal_sequence >', $curr_faq->gal_sequence);
            $this->db->order_by('gal_sequence', 'ASC');
        }
        $this->db->where('is_deleted', '0');
        $this->db->limit(1);

        $result = $this->db->get('restuarant_gallery');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        }
        else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->gal_sequence);
            $update_data = array('gal_sequence' => $update_seq);
            $this->db->where('gal_id', $curr_faq->gal_id);
            $this->db->update('restuarant_gallery', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->gal_sequence);
            $update_data = array('gal_sequence' => $update_seq);
            $this->db->where('gal_id', $other_menu->gal_id);
            $this->db->update('restuarant_gallery', $update_data);
            return 'DONE';
        }
    }

}

?>