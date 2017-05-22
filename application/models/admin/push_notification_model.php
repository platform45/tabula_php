<?php
/* * ****************** PAGE DETAILS ******************* *
 * @Programmer  : ANSHUMAN DESHPANDE
 * @Maintainer  : ANSHUMAN DESHPANDE
 * @Created     : 1 Oct 2015
 * @Modified    : 
 * @Description : Push_notification model.
 * ****************************************************** */

class Push_notification_model extends CI_Model
{
    public function __construct()
    {
            parent::__construct();
    }
    
    
    /**
    *Function to add members to the push notification queue
    *@Param: - 
    *@Return: -
    */
    public function queue_all_users()
    {
        //FETCH ALL THE UNSENT PUSH NOTIFICATIONS...
        $result = $this->getAllPushNotifications();
        if($result)
        {
            // IF PENDING NOTIFICATION FOUND --> THEN FETCH ALL THE MEMBERS...
            $members = $this->getAllMembers();
            if($members)
            {
                foreach ($result as $pushnotification)
                {
                    $finalArray = array();
                    foreach($members as $mem_id)
                    {
                        $aTemp = array(
                            'q_push_message_id' => $pushnotification['push_message_id'],
                            'q_message' => $pushnotification['push_message'],
                            'q_user_id' => $mem_id['id'],
                            'q_sent' => 0
                        );
                        array_push($finalArray, $aTemp);
                    }
                    $this->db->insert_batch('jinx_pushnotification_queue', $finalArray); 
                    
                    //UPDATE THE PUSH NOTIFICATION TABLE FOR THE CURRENT QUEUE...
                    $update_array = array(
                        'push_message_added_to_queue' => 1
                    );
                    $this->db->where("push_message_id", $pushnotification['push_message_id']);
                    $this->db->update("jinx_pushnotification", $update_array);
                }
            }
        }
    }
    
    
    /**
    *Function to add members to the push notification queue
    *@Param: - 
    *@Return: -
    */
    public function send_notifications_to_users()
    {
        $message_queue = $this->getQueuedMessages();
        //print_r($message_queue);die;
        if($message_queue)
        {
            foreach ($message_queue as $msg)
            {
                $messageId = 0;
                $msg_sent_date = date("Y-m-d H:i:s");
                $type_of_message = 'admin';
                $message_type = "jinx-admin";
                $message_text = ($msg['q_message']);
                
                //print_r($msg);echo '<br/>';
                
                $deviceDetails = $this->getDeviceToken($msg['q_user_id']);
                
                if($deviceDetails)
                {
                    //print_r($deviceDetails);echo '<br/><br/>';
                    sendPushNotification($deviceDetails['dev_device_type'], $message_text, $deviceDetails['dev_device_id'], $messageId, $msg_sent_date, $type_of_message, $message_type, "", 0);
                }
                
                $this->db->delete("jinx_pushnotification_queue", array("q_id" => $msg['q_id']));
                
            }
        }
    }
    
    
    public function getDeviceToken($user_id)
    {
        $this->db->select("dev_id, dev_user_id, dev_device_id, dev_device_type, dev_is_active");
        $this->db->from("jinx_devicemst");
        $this->db->where(array(
            'dev_user_id' => $user_id,
            'dev_is_active' => 1
        ));
        $this->db->limit(1);
        $result = $this->db->get();
        if($result->num_rows() > 0)
        {
            return $result->row_array();
        }
        else
        {
            return 0;
        }
    }
    
    
    public function getQueuedMessages()
    {
        $this->db->where("q_sent", 0);
        $result = $this->db->get("pushnotification_queue");
        
        if($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return 0;
        }
    }


    
    public function getAllPushNotifications() {
        $this->db->where("push_message_added_to_queue", 0);
        $this->db->order_by("push_message_id","ASC");
        $result = $this->db->get("pushnotification");
        if($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return 0;
        }
    }
    
    public function getAllMembers() {
        $this->db->select('id');
        $this->db->from("usermst");
        $this->db->where(array(
            "is_deleted" => 0,
            "active" => 1
        ));
        $result = $this->db->get();
        if($result->num_rows() > 0)
        {
            return $result->result_array();
        }
        else
        {
            return 0;
        }
    }
    
}
?>