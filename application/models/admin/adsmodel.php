<?php

class Adsmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0) {
        $this->db->select("a.ad_id,chan.channel_title, a.channel_id, a.ad_title,a.ad_video, a.ad_description, a.ad_status, a.is_deleted", FALSE);
        $this->db->from('ads a');
        $this->db->join('channel chan', 'a.channel_id = chan.channel_id', 'left');

        if ($edit_id) {
            $this->db->where('a.ad_id', $edit_id);
        }
        $this->db->where(
                array(
                    'a.is_deleted' => 0,
                    'chan.is_deleted' => 0
        ));
        $this->db->group_by("a.ad_id");
        $result = $this->db->get('ads');

        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function ads_count() {
        $this->db->select("ad_id", FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0
        ));


        $result = $this->db->get('ads');
        return $result->num_rows();
    }

    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('ads', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('ad_id', $edit_id);
                $this->db->update('ads', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    public function update_status($ad_id = 0) {
        $this->db->select('ad_status');
        $this->db->from('ads');
        $this->db->where('ad_id', $ad_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['ad_status'] == 1) {
                $data = array(
                    'ad_status' => 0
                );
            } else {
                $data = array(
                    'ad_status' => 1
                );
            }
            $this->db->where('ad_id', $ad_id);
            $this->db->update('ads', $data);
        }
    }

    public function getMaxSeq() {
        $this->db->select_max('video_sequence');
        $this->db->from('video');
        $this->db->where('is_deleted', '0');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->video_sequence;
            return $query + 1;
        } else {
            return 1;
        }
    }

    public function change_sequence($ad_id = 0, $change_to = 'up') {
        // get sequence of current menu
        $curr_faq = 0;
        $this->db->select('ad_id,video_sequence');
        $this->db->where('ad_id', $ad_id);
        $result = $this->db->get('video');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('ad_id,video_sequence');
        if ($change_to == 'up') {
            $this->db->where('video_sequence <', $curr_faq->video_sequence);
            $this->db->where('prod_at_fp', 1);
            $this->db->order_by('video_sequence', 'DESC');
        } else {
            $this->db->where('video_sequence >', $curr_faq->video_sequence);
            $this->db->where('prod_at_fp', 1);
            $this->db->order_by('video_sequence', 'ASC');
        }
        $this->db->where('is_deleted', 0);

        $this->db->limit(1);

        $result = $this->db->get('video');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        }
        else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->video_sequence);
            $update_data = array('video_sequence' => $update_seq);
            $this->db->where('ad_id', $curr_faq->ad_id);
            $this->db->where('prod_at_fp', 1);
            $this->db->update('video', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->video_sequence);
            $update_data = array('video_sequence' => $update_seq);
            $this->db->where('ad_id', $other_menu->ad_id);
            $this->db->update('video', $update_data);

            return 'DONE';
        }
    }

    public function getChannels() {
        $this->db->select("`channel_id`, `channel_title`, `channel_oneliner`, `channel_image`, `channel_sequence`, `channel_status`, `is_deleted`, `created_on`, `modified_on`", FALSE);

        $this->db->where(
                array(
                    'is_deleted' => 0,
                    'channel_status' => 1
        ));

        $this->db->order_by('channel_sequence', 'ASC');
        $result = $this->db->get('channel');
        if ($result->num_rows()) {

            return $result->result_array();
        }
        else
            return 0;
    }

    public function getCategories($channel_id = 0) {
        $this->db->select("`cat_id`, `channel_id`, `category_name`, `category_desc`, `category_sequence`, `category_status`, `is_delete`, `created_on`, `modiefied_on`", FALSE);
        if ($channel_id)
            $this->db->where('channel_id', $channel_id);
        $this->db->where(
                array(
                    'is_delete' => 0,
                    'category_status' => 1
        ));

        $result = $this->db->get('category');
        if ($result->num_rows()) {
            return $result->result_array();
        }
    }

    public function check_title_exists($link, $id = FALSE, $channel_id) {

        if ($id === FALSE) {
            $this->db->select('ad_title');
            $this->db->from('ads');
            $this->db->where('ad_title', $link);
            $this->db->where(array('is_deleted' => 0));
            $this->db->where(array('channel_id' => $channel_id));
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0)
                return false;
            else
                return true;
        }
        else {


            $this->db->select('ad_title');
            $this->db->from('ads');
            $this->db->where('ad_title', $link);
            $this->db->where(array('is_deleted' => 0));
            $this->db->where(array('channel_id' => $channel_id));
            $this->db->where('ad_id <> ', $id);
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0)
                return false;
            else
                return true;
        }
    }

}

?>