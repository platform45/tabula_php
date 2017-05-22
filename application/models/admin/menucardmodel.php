<?php

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Gallery Model
 * Date: 02 Sept 2016
 * Dependency: Gallerymodel.php
 */

class MenucardModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Method to get data from DB
    public function getData($user_id = 0, $edit_id = 0,$type=0) {
        $this->db->select("f.fm_id,f.fm_image,f.is_deleted,f.status,f.created_on,f.created_by,f.modified_on,f.modified_by,f.user_id", FALSE);
        $this->db->from('food_menu f');


        $this->db->join('usermst u', 'u.user_id = f.user_id', 'left');
        $this->db->order_by('menu_image_seq', 'ASC');
        $result = $this->db->get();
        if ($edit_id) {
            $this->db->where('fm_id', $edit_id);
        }
        if($type==1)
        {
             $this->db->where('status', '1');
        }
        $this->db->where(
                array(
                    'is_deleted' => '0'
        ));

        $this->db->where(
                array(
                    'user_id' => $user_id
        ));
        $this->db->order_by('menu_image_seq', 'ASC');
        $this->db->order_by('fm_id','DESC');
        $result = $this->db->get('food_menu');
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

        $this->db->select("fm_id,user_id,fm_image,is_deleted,status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => '0'
        ));
        $this->db->order_by('menu_image_seq', 'ASC');
        $result = $this->db->get('food_menu');
        return $result->num_rows();
    }

    //Method for slider state count
    public function slider_state_count() {

        $this->db->select("fm_id,user_id,fm_image,is_deleted,status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => '0'
        ));
        $this->db->where(
                array(
                    'status' => '1'
        ));
        $this->db->order_by('menu_image_seq', 'ASC');
        $result = $this->db->get('food_menu');
        return $result->num_rows();
    }

    //Method for insert,edit and delete
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('food_menu', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('fm_id', $edit_id);
                $this->db->update('food_menu', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    //Method for updating status
    public function update_status($fm_id = 0) {
        $this->db->select('status');
        $this->db->from('food_menu');
        $this->db->where('fm_id', $fm_id);
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
            $this->db->where('fm_id', $fm_id);
            $this->db->update('food_menu', $data);
        }
    }

    public function getMaxSeq() {
        $this->db->select_max('menu_image_seq');
        $this->db->from('food_menu');
        $this->db->where('is_deleted', '0');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->menu_image_seq;
            return $query + 1;
        } else {
            return 1;
        }
    }

    //Method for changing the sequence
    public function change_sequence($faqid = 0, $change_to = 'up') {

        $curr_faq = 0;
        $this->db->select('fm_id,menu_image_seq');
        $this->db->where('fm_id', $faqid);
        $result = $this->db->get('food_menu');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('fm_id,menu_image_seq');
        if ($change_to == 'up') {
            $this->db->where('menu_image_seq <', $curr_faq->menu_image_seq);
            $this->db->order_by('menu_image_seq', 'DESC');
        } else {
            $this->db->where('menu_image_seq >', $curr_faq->menu_image_seq);
            $this->db->order_by('menu_image_seq', 'ASC');
        }
        $this->db->where('is_deleted', '0');
        $this->db->limit(1);

        $result = $this->db->get('food_menu');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        }
        else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->menu_image_seq);
            $update_data = array('menu_image_seq' => $update_seq);
            $this->db->where('fm_id', $curr_faq->fm_id);
            $this->db->update('food_menu', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->menu_image_seq);
            $update_data = array('menu_image_seq' => $update_seq);
            $this->db->where('fm_id', $other_menu->fm_id);
            $this->db->update('food_menu', $update_data);
            return 'DONE';
        }
    }

}

?>