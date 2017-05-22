<?php

class promotionmodel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('webservices/notificationmodel', 'notificationmodel', TRUE);
    }

    public function getData($edit_id = 0)
    {
        $this->db->select("promotion_id,promotion_title,promotion_pdf,promotion_link,promotion_image,promotion_desc,promotion_status", FALSE);
        if ($edit_id) {
            $this->db->where('promotion_id', $edit_id);
        }
        $this->db->where(
            array(
                'is_deleted' => 0
            ));
        $this->db->order_by('promotion_id', 'DESC');
        $result = $this->db->get('promotion');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        } else
            return 0;
    }

    public function promotion_count()
    {
        $this->db->select("promotion_id,promotion_title,promotion_image,promotion_link,promotion_status", FALSE);
        $this->db->where(
            array(
                'is_deleted' => 0
            ));

        $result = $this->db->get('promotion');
        return $result->num_rows();
    }

    public function promotion_state_count()
    {
        $this->db->select("promotion_id,promotion_title,promotion_image,promotion_link,promotion_status", FALSE);
        $this->db->where(
            array(
                'is_deleted' => 0
            ));
        $this->db->where(
            array(
                'promotion_status' => 1
            ));
        $result = $this->db->get('promotion');
        return $result->num_rows();
    }

    public function action($action, $arrData = array(), $edit_id = 0)
    {
        switch ($action) {
            case 'insert':
                $this->db->insert('promotion', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('promotion_id', $edit_id);
                $this->db->update('promotion', $arrData);
                $this->db->last_query();
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    public function update_status($promotion_id = 0)
    {
        $this->db->select('promotion_status, promotion_title, restaurant_id, notification_flag');
        $this->db->from('promotion');
        $this->db->where('promotion_id', $promotion_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $query = $query->row_array();
            if ($query['promotion_status'] == 1) {
                $data = array(
                    'promotion_status' => 0
                );
            } else {
                $data = array(
                    'promotion_status' => 1
                );
            }
            $this->db->where('promotion_id', $promotion_id);
            $this->db->update('promotion', $data);

            if ($query['notification_flag'] != 1) {
                $type = "PROMOTION";

                $text_message = 'Your promotion "' . $query['promotion_title'] . '" has been approved';
                $tables = $email_user_array = $amount_payable = $android_user_array = $ios_user_array = [];

                $user_device_details = $this->usermodel->get_user_device_details($query['restaurant_id']);
                if ($user_device_details) {
                    if ($user_device_details['dev_type'] == 'A') {
                        $android_user_array[] = array('user_id' => $user_device_details['user_id'], 'device_id' => $user_device_details['dev_device_id']);
                    } else if ($user_device_details['dev_type'] == 'I') {
                        $ios_user_array[] = array('user_id' => $user_device_details['user_id'], 'device_id' => $user_device_details['dev_device_id']);
                    }
                }
                $sender = 0;
                send_notification($sender, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);

                $this->db->select('user_first_name');
                $this->db->from('usermst');
                $this->db->where('user_id', $query['restaurant_id']);
                $restaurantDetails = $this->db->get();


                if ($restaurantDetails->num_rows() > 0) {
                    $restaurantDetails = $restaurantDetails->row_array();
                    $text_message = 'New promotion "' . $query['promotion_title'] . '" has been added by "' . $restaurantDetails['user_first_name'] . '".';
                } else {
                    $text_message = 'New promotion "' . $query['promotion_title'] . '" has been added';
                }

                $testing = $this->notificationmodel->news_promotion_push_notification($type, $text_message);
                $data = array(
                    'notification_flag' => 1
                );
                $this->db->where('promotion_id', $promotion_id);
                $this->db->update('promotion', $data);
            }
        }
    }

    public function getMaxSeq()
    {
        $this->db->select_max('promotion_sequence');
        $this->db->from('promotion');
        $this->db->where('is_deleted', 0);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->promotion_sequence;
            return $query + 1;
        } else {
            return 1;
        }
    }

    public function change_sequence($promotion_id = 0, $change_to = 'up')
    {
        // get sequence of current menu
        $curr_faq = 0;
        $this->db->select('promotion_id,promotion_sequence');
        $this->db->where('promotion_id', $promotion_id);
        $result = $this->db->get('promotion');
        if ($result->num_rows() > 0) {
            $curr_faq = $result->row();
        }


        $other_menu = 0;
        $this->db->select('promotion_id,promotion_sequence');
        if ($change_to == 'up') {
            $this->db->where('promotion_sequence <', $curr_faq->promotion_sequence);
            $this->db->order_by('promotion_sequence', 'DESC');
        } else {
            $this->db->where('promotion_sequence >', $curr_faq->promotion_sequence);
            $this->db->order_by('promotion_sequence', 'ASC');
        }
        $this->db->where('is_deleted', 0);
        $this->db->limit(1);

        $result = $this->db->get('promotion');
        if ($result->num_rows() > 0) {
            $other_menu = $result->row();
        } else
            return 'NA';

        if ($other_menu) {
            // update sequence of current menu
            $update_seq = ($other_menu->promotion_sequence);
            $update_data = array('promotion_sequence' => $update_seq);
            $this->db->where('promotion_id', $curr_faq->promotion_id);
            $this->db->update('promotion', $update_data);

            // update sequence of other menu
            $update_seq = ($curr_faq->promotion_sequence);
            $update_data = array('promotion_sequence' => $update_seq);
            $this->db->where('promotion_id', $other_menu->promotion_id);
            $this->db->update('promotion', $update_data);

            return 'DONE';
        }
    }

}

?>