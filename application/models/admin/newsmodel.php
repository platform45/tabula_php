<?php

class Newsmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0) {
        $this->db->select("news_id,news_title,news_image,short_description,news_desc,news_link,news_description_link,news_status,news_date", FALSE);
        if ($edit_id) {
            $this->db->where('news_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
        $this->db->order_by('news_date', 'DESC');
        $result = $this->db->get('news');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function news_count() {
        $this->db->select("news_id,news_title,news_image,news_link,news_status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));

        $result = $this->db->get('news');
        return $result->num_rows();
    }

    public function news_state_count() {
        $this->db->select("news_id,news_title,news_image,news_link,news_status", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));
        $this->db->where(
                array(
                    'news_status' => 1
        ));
        $result = $this->db->get('news');
        return $result->num_rows();
    }

    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('news', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('news_id', $edit_id);
                $this->db->update('news', $arrData);
                $this->db->last_query();
              //  echo $this->db->last_query();die;
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    public function update_status($news_id = 0) {
        $this->db->select('news_status');
        $this->db->from('news');
        $this->db->where('news_id', $news_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['news_status'] == 1) {
                $data = array(
                    'news_status' => 0
                );
            } else {
                $data = array(
                    'news_status' => 1
                );
            }
            $this->db->where('news_id', $news_id);
            $this->db->update('news', $data);
        }
    }

    public function getMaxSeq() {
        $this->db->select_max('news_sequence');
        $this->db->from('news');
        $this->db->where('is_deleted', 0);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->news_sequence;
            return $query + 1;
        } else {
            return 1;
        }
    }

    public function change_sequence($news_id = 0, $change_to = 'up') {
        // get sequence of current menu
        $curr_faq = 0;
        $this->db->select('news_id,news_sequence');
        $this->db->where('news_id', $news_id);
        $result = $this->db->get('news');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('news_id,news_sequence');
        if ($change_to == 'up') {
            $this->db->where('news_sequence <', $curr_faq->news_sequence);
            $this->db->order_by('news_sequence', 'DESC');
        } else {
            $this->db->where('news_sequence >', $curr_faq->news_sequence);
            $this->db->order_by('news_sequence', 'ASC');
        }
        $this->db->where('is_deleted', 0);
        $this->db->limit(1);

        $result = $this->db->get('news');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        }
        else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->news_sequence);
            $update_data = array('news_sequence' => $update_seq);
            $this->db->where('news_id', $curr_faq->news_id);
            $this->db->update('news', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->news_sequence);
            $update_data = array('news_sequence' => $update_seq);
            $this->db->where('news_id', $other_menu->news_id);
            $this->db->update('news', $update_data);

            return 'DONE';
        }
    }


    public function check_for_duplicate_title($news_title,$edit_id) {
        $this->db->select("news_id");
        $this->db->where('news_title', $news_title);
        if($edit_id != 0)
        {
            $this->db->where('news_id !=', $edit_id);
        }
        $this->db->where(
            array(
                'is_deleted' => 0
            ));
        $result = $this->db->get('news');
        return $result->num_rows();
    }



}

?>