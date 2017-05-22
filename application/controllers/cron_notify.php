<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_notify extends CI_Controller {
    
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
                $geo_latitude = $geo['geo_latitude'];
                $geo_longitude = $geo['geo_longitude'];
                
                
                $this->db->select(array(
                    "geozone_id",
                    "geo_id",
                    "user_id",
                    "user_status"
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
                        switch ($loc['user_status'])
                        {
                            case "Enters":
                                $nearbyUsers = $this->getNearbyUsers($geo['range_unit'], $geo['range_value'], "Enters",$geo);
                                if($nearbyUsers)
                                {
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
                                        sendPushNotification($deviceDetails['dev_device_type'], $message_text, $deviceDetails['dev_device_id'], $messageId, $msg_sent_date, $type_of_message, $message_type);
                                    }
                                }
                                break;
                            case "Leaves":
                                $nearbyUsers = $this->getNearbyUsers($geo['range_unit'], $geo['range_value'], "Leaves", $geo);
                                if($nearbyUsers)
                                {
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
                                        sendPushNotification($deviceDetails['dev_device_type'], $message_text, $deviceDetails['dev_device_id'], $messageId, $msg_sent_date, $type_of_message, $message_type);
                                    }
                                }
                                
                                break;
                            case "both":
                                $nearbyUsers = $this->getNearbyUsers($geo['range_unit'], $geo['range_value'], "both", $geo);
                                
                                
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
    }
    
    
    public function getNearbyUsers($range_unit, $range_value, $status, $geo)
    {
        $geo_latitude = $geo['geo_latitude'];
        $geo_longitude = $geo['geo_longitude'];
        $user_locations = array();
        switch ($range_unit) {
            case "metres":
                if($range_value == 300)
                {
                    $this->db->select(array(
                        "gu.geozone_id",
                        "gu.geo_id",
                        "gu.user_id",
                        "gu.user_status",
                        "ul.latitude",
                        "ul.longitude",
                        "(((Acos(Sin(( ul.latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( ul.latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( ul.longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                        //"( 6371 * acos( cos( radians(".$geo_latitude.") ) * cos( radians( ul.latitude ) ) * cos( radians( ul.longitude ) - radians(".$geo_longitude.") ) + sin( radians(".$geo_latitude.") ) * sin( radians( ul.latitude ) ) ) ) AS distance"
                    ));

                    /*
                        $strQuery = "SELECT (((
                        Acos(Sin(( '$fToLatitude' * Pi() / 180 )) * Sin(('$fFromLatitude' * Pi() / 180 )) +
                        Cos(( '$fToLatitude' * Pi() / 180 )) * Cos(( '$fFromLatitude' * Pi() / 180 )) *
                        Cos(( ( '$fToLongitude' - '$fFromLongitude') * Pi() / 180 )))
                        ) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance";
                    */
                    $this->db->from("jinx_geozone_users gu");
                    $this->db->where("geo_id", $geo['geo_id']);
                    $this->db->join("jinx_user_location ul","gu.user_id = ul.user_id");
                    
                    if($status == "Enters")
                    {
                        $this->db->having("distance <= 0.3");
                    }
                    if($status == "Leaves")
                    {
                        $this->db->having("distance >= 0.3");
                    }
                    if($status == "both")
                    {
                        $this->db->having("distance <= 0.3 OR distance > 0.3");
                    }
                    
                    
                    $user_locations = $this->db->get();
                }
                else if($range_value == 100)
                {
                    $this->db->select(array(
                        "gu.geozone_id",
                        "gu.geo_id",
                        "gu.user_id",
                        "gu.user_status",
                        "ul.latitude",
                        "ul.longitude",
                        "(((Acos(Sin(( ul.latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( ul.latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( ul.longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_geozone_users gu");
                    $this->db->where("geo_id", $geo['geo_id']);
                    $this->db->join("jinx_user_location ul","gu.user_id = ul.user_id");
                    
                    if($status == "Enters")
                    {
                        $this->db->having("distance <= 0.1");
                    }
                    if($status == "Leaves")
                    {
                        $this->db->having("distance >= 0.1");
                    }
                    if($status == "both")
                    {
                        $this->db->having("distance <= 0.1 OR distance > 0.1");
                    }
                    
                    $user_locations = $this->db->get();
                }
                break;
            case "kilometres":
                if($range_value == 3)
                {
                    $this->db->select(array(
                        "gu.geozone_id",
                        "gu.geo_id",
                        "gu.user_id",
                        "gu.user_status",
                        "ul.latitude",
                        "ul.longitude",
                        "(((Acos(Sin(( ul.latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( ul.latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( ul.longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_geozone_users gu");
                    $this->db->where("geo_id", $geo['geo_id']);
                    $this->db->join("jinx_user_location ul","gu.user_id = ul.user_id");
                    
                    if($status == "Enters")
                    {
                        $this->db->having("distance <= 3");
                    }
                    if($status == "Leaves")
                    {
                        $this->db->having("distance >= 3");
                    }
                    if($status == "both")
                    {
                        $this->db->having("distance <= 3 OR distance > 3");
                    }
                    
                    $user_locations = $this->db->get();
                }
                else if($range_value == 1)
                {
                    $this->db->select(array(
                        "gu.geozone_id",
                        "gu.geo_id",
                        "gu.user_id",
                        "gu.user_status",
                        "ul.latitude",
                        "ul.longitude",
                        "(((Acos(Sin(( ul.latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( ul.latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( ul.longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_geozone_users gu");
                    $this->db->where("geo_id", $geo['geo_id']);
                    $this->db->join("jinx_user_location ul","gu.user_id = ul.user_id");
                    
                    if($status == "Enters")
                    {
                        $this->db->having("distance <= 1");
                    }
                    if($status == "Leaves")
                    {
                        $this->db->having("distance >= 1");
                    }
                    if($status == "both")
                    {
                        $this->db->having("distance <= 1 OR distance > 1");
                    }
                    
                    $user_locations = $this->db->get();
                }
                break;
            default:
                //DEFAULT TRACK 1KM...
                $this->db->select(array(
                        "gu.geozone_id",
                        "gu.geo_id",
                        "gu.user_id",
                        "gu.user_status",
                        "ul.latitude",
                        "ul.longitude",
                        "(((Acos(Sin(( ul.latitude * Pi() / 180 )) * Sin(('$geo_latitude' * Pi() / 180 )) + Cos(( ul.latitude * Pi() / 180 )) * Cos(( '$geo_latitude' * Pi() / 180 )) * Cos(( ( ul.longitude - '$geo_longitude') * Pi() / 180 )))) * 180 / Pi()) * 60 * 1.1515 * 1.609344  ) as distance"
                    ));
                    $this->db->from("jinx_geozone_users gu");
                    $this->db->where("geo_id", $geo['geo_id']);
                    $this->db->join("jinx_user_location ul","gu.user_id = ul.user_id");
                    $this->db->having("distance <= 1");
                    $user_locations = $this->db->get();
                break;
        }
        
        if($user_locations && $user_locations->num_rows() > 0)
        {
            return $user_locations->result_array();
        }
        else
        {
            return 0;
        }
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */