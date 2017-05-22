<?php

/*
  Model that contains functions related to notification results
 */

class NotificationModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
    }

    /*
     * Method Name: get_total_notification_records
     * Purpose: To get total notification records for a user that are not deleted by him
     * params:
     *      input: user_id
     *      output: count of records
     */
    public function get_total_notification_records($user_id)
    {
        $this->db->select("notification_id");
        $this->db->from("push_notification");

        $data = array(
            'receiver_id' => $user_id,
            'is_deleted' => 0
        );
        $this->db->where($data);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->num_rows() : 0;
    }

    /*
     * Method Name: get_notification_records
     * Purpose: To get notification records
     * params:
     *      input: user_id, limit, offset
     *      output: array
     */
    public function get_notification_records($user_id, $limit, $offset)
    {
        $this->db->select("notification_id, notification_message,  notification_type, notification_date");
        $this->db->from("push_notification");

        $data = array(
            'receiver_id' => $user_id,
            'is_deleted' => 0
        );
        $this->db->where($data);
        $this->db->limit($limit, $offset);
        $this->db->order_by("notification_id", "desc");
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : array();
    }
	
	/*
	 * Author: Akshay deshmukh
     * Method Name: front_get_notification_records
     * Purpose: To get all notification records for front side
     * params:
     *      input: user_id
     *      output: array
     */
    public function front_get_notification_records($user_id)
    {
		
		$sql = "SELECT notification_id, notification_message,  notification_type, 
				DATE_FORMAT(notification_date,'%d %M %Y, %H:%i') as notification_date
            FROM `tab_push_notification` 
            WHERE  `receiver_id` = $user_id 
            AND `is_deleted` = '0'
            ORDER BY notification_id DESC 
        ";
		$query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
     * Method Name: is_valid_notification
     * Purpose: To check if notification is valid for a user
     * params:
     *      input: user_id, notification_id
     *      output: TRUE/FALSE
     */
    public function is_valid_notification($user_id, $notification_id)
    {
        $this->db->select("notification_id");
        $this->db->from("push_notification");

        $this->db->where('receiver_id', $user_id);
        $this->db->where('notification_id', $notification_id);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? TRUE : FALSE;
    }

    /*
     * Method Name: delete_notification
     * Purpose: To delete notification
     * params:
     *      input: notification_id
     *      output: id of record updated
     */
    public function delete_notification($notification_id)
    {
        $this->db->where('notification_id', $notification_id);
        $this->db->update('push_notification', array('is_deleted' => 1));

        return $notification_id;
    }

    public function news_promotion_push_notification($type, $text_message)
    {
        $tables = $email_user_array = $amount_payable = $android_user_array = $ios_user_array = [];

        $this->db->select("user_id");
        $this->db->from("usermst");

        if($type == "PROMOTION"){
            $data = array(
                'user_type' => '2',
                'is_deleted' => '0',
                'user_status' => '1'
            );
        }

        if($type == "NEWS"){
            $data = array(
                'is_deleted' => '0',
                'user_status' => '1'
            );
        }

        $this->db->where($data);
        $this->db->where("(user_type = 2 OR user_type = 3)");
        $query = $this->db->get();
        $user_details = $query->result_array();

        foreach ($user_details as $user_detail) {
            $user_device_details = $this->usermodel->get_user_device_details($user_detail['user_id']);
            if($user_device_details){
                if ($user_device_details['dev_type'] == 'A') {
                    $android_user_array[] = array('user_id' => $user_detail['user_id'], 'device_id' => $user_device_details['dev_device_id']);
                } else if ($user_device_details['dev_type'] == 'I') {
                    $ios_user_array[] = array('user_id' => $user_detail['user_id'], 'device_id' => $user_device_details['dev_device_id']);
                }
            }
        }

        $sender = 0;
        send_notification($sender, $type, $android_user_array, $ios_user_array, $email_user_array, $tables, $amount_payable, $text_message);
    }
}

?>