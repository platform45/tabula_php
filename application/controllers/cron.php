<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('webservices/membersmodel','membersmodel',TRUE);
        $this->load->model('webservices/wallmodel','wallmodel',TRUE);
        $this->load->model('webservices/geozonemodel','geozonemodel',TRUE);
        $this->load->model('webservices/mapmodel','mapmodel',TRUE);
        $this->load->model('webservices/calendarmodel','calendarmodel',TRUE);
        $this->load->model('webservices/notificationmodel','notificationmodel',TRUE);
        $this->load->model('webservices/chatmodel','chatmodel',TRUE);
        $this->load->model('webservices/usermodel','usermodel',TRUE);
    }
    
    /*
    * Purpose: To notify a user when a user registered under his geozone enters or leaves that geozone.
    * params: 
    *      input: 
    *      output: 
    */
    public function notify_all_users()
    {
        $this->db->select("geo_id, user_id, geo_name, geo_address, geo_latitude, geo_longitude, geo_range_id, range_value, range_unit");
        $this->db->from("jinx_geozone_master");
        $this->db->join("jinx_geozone_range_master","range_id = geo_range_id");
        $this->db->where(array(
            'geo_deleted' => 0
        ));
        $geozones = $this->db->get();
        if($geozones->num_rows() > 0)
        {
            $geozones = $geozones->result_array();
            //print_r($geozones);die;
            foreach ($geozones as $geo)
            {
                $geo_distance_radius = 0.3;
                switch ($geo['range_unit']) {
                    case "metres":
                        if($geo['range_value'] == 300)
                        {
                            $geo_distance_radius = 0.3;
                        }
                        else if($geo['range_value'] == 100)
                        {
                            $geo_distance_radius = 0.1;
                        }
                        break;
                    case "kilometres":
                        if($geo['range_value'] == 3)
                        {
                            $geo_distance_radius = 3;
                        }
                        else if($geo['range_value'] == 1)
                        {
                            $geo_distance_radius = 1;
                        }
                        break;
                    default:
                        $geo_distance_radius = 0.3;
                        break;
                }
                
                $this->db->select(array(
                    "geozone_id",
                    "geo_id",
                    "user_id",
                    "user_status",
                    "enter_status_flag",
                    "leave_status_flag"
                ));
                $this->db->from("jinx_geozone_users");
                $this->db->where("geo_id", $geo['geo_id']);
                $geo_users = $this->db->get();
                
                if($geo_users->num_rows() > 0)
                {
                    $geo_users = $geo_users->result_array();
                    //print_r($user_locations);
                    foreach ($geo_users as $loc)
                    {
                        $users = $this->getUserStatus($loc, $geo['geo_latitude'], $geo['geo_longitude'], $geo_distance_radius, $geo);
                        
                    }
                }
            }
        }
    }
    
    
    
    
    public function getUserStatus($loc, $geo_latitude, $geo_longitude, $geo_distance_radius, $geo)
    {
        $time = date("Y-m-d H:i:s");
        switch ($loc['user_status'])
        {
            case "Enters":
                if($loc['enter_status_flag'] == 1)
                {
                    //NOTIFICATION HAS BEEN SENT PREVIOUSLY...NOW CHECK IF THE USER HAS MOVED OUT OF THE GEOZONE...
                    $this->db->select(array(
                        "latitude",
                        "longitude",
                        "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    
                    /*
                        $strQuery = "SELECT (((
                        Acos(Sin(( '$fToLatitude' * Pi() / 180 )) * Sin(('$fFromLatitude' * Pi() / 180 )) +
                        Cos(( '$fToLatitude' * Pi() / 180 )) * Cos(( '$fFromLatitude' * Pi() / 180 )) *
                        Cos(( ( '$fToLongitude' - '$fFromLongitude') * Pi() / 180 )))
                        ) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance";
                    */
                    
                    $this->db->from("jinx_user_location");
                    $this->db->where("user_id", $loc['user_id']);
                    $this->db->having("distance > ".$geo_distance_radius);
                    $user_locations = $this->db->get();
                    
                    if($user_locations->num_rows() > 0)
                    {
                        $update_array = array(
                            'enter_status_flag' => 0
                        );
                        $this->db->where(array(
                            'geozone_id' => $loc['geozone_id']
                        ));
                        $this->db->update("jinx_geozone_users", $update_array);
                    }
                }
                else if($loc['enter_status_flag'] == 0)
                {
                    //NOTIFICATION HAS NOT BEEN SENT...NOW CHECK IF THE USER HAS MOVED IN THE GEOZONE...
                    $this->db->select(array(
                        "latitude",
                        "longitude",
                        "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_user_location");
                    $this->db->where("user_id", $loc['user_id']);
                    $this->db->having("distance <= ".$geo_distance_radius);
                    $user_locations = $this->db->get();
                    
                    if($user_locations->num_rows() > 0)
                    {
                        $update_array = array(
                            'enter_status_flag' => 1
                        );
                        $this->db->where(array(
                            'geozone_id' => $loc['geozone_id']
                        ));
                        $this->db->update("jinx_geozone_users", $update_array);
                        
                        //SEND PUSH NOTIFICATION...
                        //ADD NOTIFICATION REGARDING THE JINX REQUEST...
                        $memberData = $this->usermodel->getData($loc['user_id']);
                        if(isset($memberData['profile_picture']) && $memberData['profile_picture'] != '')
                        {
                            $memberData['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$memberData['profile_picture']);
                        }
                        else
                        {
                            $memberData['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                        }
                        $strParam = array(
                                '{NAME}'=> $memberData['first_name']." ".$memberData['last_name']
                        );
                        $insert_id = $this->notificationmodel->add_notification($geo['user_id'], $strParam, JINX_ENTERED_TO_GEOZONE);
                        
                        //SEND PUSH NOTIFICATION TO EACH USER WHO IS ADDED TO THE GEOZONE
                        $messageId = $insert_id;
                        $msg_sent_date = $time;
                        $type_of_message = 'admin';
                        $message_type = "jinx-geozone";
                        $message_text = replaceData($strParam, JINX_ENTERED_TO_GEOZONE);

                        $deviceDetails = $this->chatmodel->getDeviceToken($geo['user_id']);
                        if($deviceDetails)
                        {
                            sendPushNotification($deviceDetails['dev_device_type'], $message_text, $deviceDetails['dev_device_id'], $messageId, $msg_sent_date, $type_of_message, $message_type, '', 0);
                        }
                    }
                }
                break;
            case "Leaves":
                if($loc['leave_status_flag'] == 1)
                {
                    //NOTIFICATION HAS BEEN SENT PREVIOUSLY...NOW CHECK IF THE USER HAS MOVED OUT OF THE GEOZONE...
                    $this->db->select(array(
                        "latitude",
                        "longitude",
                        "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_user_location");
                    $this->db->where("user_id", $loc['user_id']);
                    $this->db->having("distance <= ".$geo_distance_radius);
                    $user_locations = $this->db->get();
                    
                    if($user_locations->num_rows() > 0)
                    {
                        $update_array = array(
                            'leave_status_flag' => 0
                        );
                        $this->db->where(array(
                            'geozone_id' => $loc['geozone_id']
                        ));
                        $this->db->update("jinx_geozone_users", $update_array);
                    }
                    
                }
                else if($loc['leave_status_flag'] == 0)
                {
                    //NOTIFICATION HAS NOT BEEN SENT...NOW CHECK IF THE USER HAS MOVED IN THE GEOZONE...
                    $this->db->select(array(
                        "latitude",
                        "longitude",
                        "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_user_location");
                    $this->db->where("user_id", $loc['user_id']);
                    $this->db->having("distance > ".$geo_distance_radius);
                    $user_locations = $this->db->get();
                    
                    if($user_locations->num_rows() > 0)
                    {
                        $update_array = array(
                            'leave_status_flag' => 1
                        );
                        $this->db->where(array(
                            'geozone_id' => $loc['geozone_id']
                        ));
                        $this->db->update("jinx_geozone_users", $update_array);
                        
                        //SEND PUSH NOTIFICATION...
                        //ADD NOTIFICATION REGARDING THE JINX REQUEST...
                        $memberData = $this->usermodel->getData($loc['user_id']);
                        if(isset($memberData['profile_picture']) && $memberData['profile_picture'] != '')
                        {
                            $memberData['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$memberData['profile_picture']);
                        }
                        else
                        {
                            $memberData['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                        }
                        $strParam = array(
                                '{NAME}'=> $memberData['first_name']." ".$memberData['last_name']
                        );
                        $insert_id = $this->notificationmodel->add_notification($geo['user_id'], $strParam, JINX_MOVED_OUT_TO_GEOZONE);
                        
                        //SEND PUSH NOTIFICATION TO EACH USER WHO IS ADDED TO THE GEOZONE
                        $messageId = $insert_id;
                        $msg_sent_date = $time;
                        $type_of_message = 'admin';
                        $message_type = "jinx-geozone";
                        $message_text = replaceData($strParam, JINX_MOVED_OUT_TO_GEOZONE);

                        $deviceDetails = $this->chatmodel->getDeviceToken($geo['user_id']);
                        if($deviceDetails)
                        {
                            sendPushNotification($deviceDetails['dev_device_type'], $message_text, $deviceDetails['dev_device_id'], $messageId, $msg_sent_date, $type_of_message, $message_type, '', 0);
                        }
                    }
                }
                break;
            case "both":
                if($loc['enter_status_flag'] == 0 && $loc['leave_status_flag'] == 0)
                {
                    //THIS CASE INDICATES THAT THE USER IS JUST REGISTERED IN THE GEOZONE AND NO NOTIFICATION HAS BEEN SENT TO HIM YET...
                    $this->db->select(array(
                        "latitude",
                        "longitude",
                        "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_user_location");
                    $this->db->where("user_id", $loc['user_id']);
                    $this->db->having("distance > ".$geo_distance_radius);
                    $user_locations = $this->db->get();
                    
                    if($user_locations->num_rows() > 0)
                    {
                        $update_array = array(
                            'leave_status_flag' => 1,
                            'enter_status_flag' => 0
                        );
                        $this->db->where(array(
                            'geozone_id' => $loc['geozone_id']
                        ));
                        $this->db->update("jinx_geozone_users", $update_array);
                    }
                    else
                    {
                        $this->db->select(array(
                            "latitude",
                            "longitude",
                            "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                        ));
                        $this->db->from("jinx_user_location");
                        $this->db->where("user_id", $loc['user_id']);
                        $this->db->having("distance <= ".$geo_distance_radius);
                        $user_locations = $this->db->get();
                        
                        if($user_locations->num_rows() > 0)
                        {
                            $update_array = array(
                                'leave_status_flag' => 0,
                                'enter_status_flag' => 1
                            );
                            $this->db->where(array(
                                'geozone_id' => $loc['geozone_id']
                            ));
                            $this->db->update("jinx_geozone_users", $update_array);
                        }
                    }
                }
                else
                {
                    //HERE EITHER OF THE FLAGS IS SET...
                    if($loc['enter_status_flag'] == 1)
                    {
                        //ENTER FLAG 1 INDICATES THAT USER HAS ENTERED THE GEOZONE AND NOTIFICATION HAS BEEN SENT...
                        //IT ALSO IMPLIES THAT LEAVE FLAG IS 0 AND USER NEEDS TO BE NOTIFIED WHEN MEMBER LEAVES THE GEOZONE...
                        
                        $this->db->select(array(
                            "latitude",
                            "longitude",
                            "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                        ));
                        $this->db->from("jinx_user_location");
                        $this->db->where("user_id", $loc['user_id']);
                        $this->db->having("distance > ".$geo_distance_radius);
                        $user_locations = $this->db->get();
                        
                        if($user_locations->num_rows() > 0)
                        {
                            $update_array = array(
                                'leave_status_flag' => 1,
                                'enter_status_flag' => 0
                            );
                            $this->db->where(array(
                                'geozone_id' => $loc['geozone_id']
                            ));
                            $this->db->update("jinx_geozone_users", $update_array);
                            
                            //SEND PUSH NOTIFICATION...
                            //ADD NOTIFICATION REGARDING THE JINX REQUEST...
                            $memberData = $this->usermodel->getData($loc['user_id']);
                            if(isset($memberData['profile_picture']) && $memberData['profile_picture'] != '')
                            {
                                $memberData['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$memberData['profile_picture']);
                            }
                            else
                            {
                                $memberData['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                            }
                            $strParam = array(
                                    '{NAME}'=> $memberData['first_name']." ".$memberData['last_name']
                            );
                            $insert_id = $this->notificationmodel->add_notification($geo['user_id'], $strParam, JINX_MOVED_OUT_TO_GEOZONE);
                            
                            //SEND PUSH NOTIFICATION TO EACH USER WHO IS ADDED TO THE GEOZONE
                            $messageId = $insert_id;
                            $msg_sent_date = $time;
                            $type_of_message = 'admin';
                            $message_type = "jinx-geozone";
                            $message_text = replaceData($strParam, JINX_MOVED_OUT_TO_GEOZONE);
                            
                            $deviceDetails = $this->chatmodel->getDeviceToken($geo['user_id']);
                            if($deviceDetails)
                            {
                                sendPushNotification($deviceDetails['dev_device_type'], $message_text, $deviceDetails['dev_device_id'], $messageId, $msg_sent_date, $type_of_message, $message_type, '', 0);
                            }
                        }
                    }
                    else
                    {
                        $this->db->select(array(
                            "latitude",
                            "longitude",
                            "(((Acos(Sin(( latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                        ));
                        $this->db->from("jinx_user_location");
                        $this->db->where("user_id", $loc['user_id']);
                        $this->db->having("distance <= ".$geo_distance_radius);
                        $user_locations = $this->db->get();

                        if($user_locations->num_rows() > 0)
                        {
                            $update_array = array(
                                'enter_status_flag' => 1,
                                'leave_status_flag' => 0
                            );
                            $this->db->where(array(
                                'geozone_id' => $loc['geozone_id']
                            ));
                            $this->db->update("jinx_geozone_users", $update_array);

                            //SEND PUSH NOTIFICATION...
                            //ADD NOTIFICATION REGARDING THE JINX REQUEST...
                            $memberData = $this->usermodel->getData($loc['user_id']);
                            if(isset($memberData['profile_picture']) && $memberData['profile_picture'] != '')
                            {
                                $memberData['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$memberData['profile_picture']);
                            }
                            else
                            {
                                $memberData['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                            }
                            $strParam = array(
                                    '{NAME}'=> $memberData['first_name']." ".$memberData['last_name']
                            );
                            $insert_id = $this->notificationmodel->add_notification($geo['user_id'], $strParam, JINX_ENTERED_TO_GEOZONE);

                            //SEND PUSH NOTIFICATION TO EACH USER WHO IS ADDED TO THE GEOZONE
                            $messageId = $insert_id;
                            $msg_sent_date = $time;
                            $type_of_message = 'admin';
                            $message_type = "jinx-geozone";
                            $message_text = replaceData($strParam, JINX_ENTERED_TO_GEOZONE);

                            $deviceDetails = $this->chatmodel->getDeviceToken($geo['user_id']);
                            if($deviceDetails)
                            {
                                sendPushNotification($deviceDetails['dev_device_type'], $message_text, $deviceDetails['dev_device_id'], $messageId, $msg_sent_date, $type_of_message, $message_type, '', 0);
                            }
                        }
                    }
                }
                break;
        }
        
        //SEND MAIL TO CHECK IF THE CRON IS BEING HIT OR NOT...
        
        $txtMessageStr = "Hello, <br/> The CRON has been hit.";
        
        //echo $txtMessageStr;die;
        $this->load->library('email');

        $this->email->from('shinchan.testingmaster@yahoo.com', $this->config->item('site_name'));
        $this->email->to("genknooz6@gmail.com");
        $this->email->subject($this->config->item('site_name').": CRON HIT");
        $this->email->message($txtMessageStr);
        $result = $this->email->send();
        
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */