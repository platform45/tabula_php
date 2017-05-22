<?php

class Faqmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0) {
        $this->db->select("faq_id,faq_sequenceno,faq_question,faq_answer,status,is_delete", FALSE);
        if ($edit_id) {
            $this->db->where('faq_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_delete' => '0'
        ));
        $this->db->order_by('faq_sequenceno', 'asc');
        $result = $this->db->get('faq');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('faq', $arrData);
                $insert_id = $this->db->insert_id();
                $this->adjust_sequence_numbers();
                return $insert_id;
                break;
            case 'update':
                $this->db->where('faq_id', $edit_id);
                $this->db->update('faq', $arrData);
                return $edit_id;
                break;
            case 'delete':
                $this->db->delete('faq', array('faq_id' => $edit_id));
                $this->adjust_sequence_numbers();
                break;
        }
    }

    public function getNewSequence() {
        $this->db->select_max('faq_sequenceno');
        $result = $this->db->get('faq');
        if ($result->num_rows() > 0) {
            $curr_seq = $result->row()->faq_sequenceno;
            if (!empty($curr_seq))
                return ($curr_seq + 1);
            else
                return 1;
        }
        else {
            return 1;
        }
    }

    public function adjust_sequence_numbers() {
        $this->db->select("faq_id, faq_sequenceno");
        $this->db->where(
                array(
                    'is_delete' => '0'
        ));
        $this->db->order_by('faq_sequenceno');
        $result = $this->db->get('faq');
        if ($result->num_rows() > 0) {
            $result = $result->result_array();
            $i = 1;
            foreach ($result as $faq) {
                $data = array(
                    'faq_sequenceno' => $i
                );
                $this->db->where('faq_id', $faq['faq_id']);
                $this->db->update('faq', $data);
                $i++;
            }
        }
    }

    public function change_sequence($faqid = 0, $change_to = 'up') {
        // get sequence of current menu
        $curr_faq = 0;
        $this->db->select('faq_id,faq_sequenceno');
        $this->db->where('faq_id', $faqid);
        $result = $this->db->get('faq');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('faq_id,faq_sequenceno');
        if ($change_to == 'up') {
            $this->db->where('faq_sequenceno <', $curr_faq->faq_sequenceno);
            $this->db->order_by('faq_sequenceno', 'DESC');
        } else {
            $this->db->where('faq_sequenceno >', $curr_faq->faq_sequenceno);
            $this->db->order_by('faq_sequenceno', 'ASC');
        }
        $this->db->where('is_delete', '0');
        $this->db->limit(1);

        $result = $this->db->get('faq');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        }
        else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->faq_sequenceno);
            $update_data = array('faq_sequenceno' => $update_seq);
            $this->db->where('faq_id', $curr_faq->faq_id);
            $this->db->update('faq', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->faq_sequenceno);
            $update_data = array('faq_sequenceno' => $update_seq);
            $this->db->where('faq_id', $other_menu->faq_id);
            $this->db->update('faq', $update_data);

            return 'DONE';
        }
    }

    public function faq_count() {
        $this->db->select("*", FALSE);
        $this->db->where('is_delete', '0');
        $result = $this->db->get('faq');
        return $result->num_rows();
    }

}

?>