<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/array_helper.html
 */
// ------------------------------------------------------------------------

if (!function_exists('stripJunk')) {

    function stripJunk($string) {
        $string = str_replace(" ", "-", trim($string));
        $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
        $string = strtolower($string);
        return $string;
    }

}

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Akshay Deshmukh
 * @link		http://codeigniter.com/user_guide/helpers/array_helper.html
 * To shuffle associativce array.
 */
// ------------------------------------------------------------------------
if (!function_exists('shuffle_assoc_array')) {

    function shuffle_assoc_array($array) {
        $keys = array_keys($array);

        shuffle($keys);

        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return $array;
    }

}



/**
 * Element
 *
 * Lets you validate the api key.
 *
 * @access  public
 * @return  mixed depends on success or failure
 */
if (!function_exists('validate_api_key')) {

    function validate_api_key($api_key = '') {
        if ($api_key) {
            $CI = &get_instance();
            $CI->db->select("user_id");
            $CI->db->from("user_access_token");
            $CI->db->where("user_access_token", $api_key);
            $result = $CI->db->get();
            if ($result->num_rows() > 0) {
                return json_encode(array("status" => SUCCESS, "message" => VALID_TOKEN_MESSAGE));
            } else {
                return json_encode(array("status" => FAIL, "message" => INVALID_TOKEN_MESSAGE));
            }
        }
    }

}


if (!function_exists('check_add')) {

    function check_add($user_id) {
        $CI = &get_instance();
        $CI->db->select("add_id");
        $CI->db->where("user_id", $user_id);
        $query = $CI->db->get("addmst");
        if ($query->num_rows()) {
            $aResult = $query->row();
            return $aResult->add_id;
        } else {
            return 0;
        }
    }


}

if (!function_exists('validate_email'))
  {

    function validate_email($email)
   {
          $CI = &get_instance();
          $CI->db->select("sub_email as Subscriber",$email); 
          $query = $CI->db->get("subscribermst");
          if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) 
          {
          return 1;
          } 
          else 
          {
            return 0;
          }
    }            
 }

 /**
 * Purpose: check restaurant is addd to top 10 list
 * Input Parameter:
 *          
 * Output Parameter:
 *          restaurant _id /0
 */
 
if (!function_exists('check_top10')) {

    function check_top10($user_id) {
        $CI = &get_instance();
        $CI->db->select("top10_id");
        $CI->db->where("user_id", $user_id);
        $query = $CI->db->get("top10_restaurants");
        if ($query->num_rows()) {
            $aResult = $query->row();
            return $aResult->top10_id;
        } else {
            return 0;
        }
    }

}

/**
 * Purpose: check restaurant is added to wishlist by user
 * Input Parameter:
 *          user_id,restaurant_id
 * Output Parameter:
 *          1 /0
 */
 
if (!function_exists('check_added_to_wishlist')) {

    function check_added_to_wishlist($user_id,$restaurant_id) {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->where("user_id", $user_id);
        $CI->db->where("restaurant_id", $restaurant_id);
        $query = $CI->db->get("tab_wishlist");
        if ($query->num_rows()) {            
            return 1;
        } else {
            return 0;
        }
    }

}


/**
 * Purpose: check restaurant is added to wishlist by user
 * Input Parameter:
 *          user_id,restaurant_id
 * Output Parameter:
 *          1 /0
 */
 
if (!function_exists('get_open_close_day')) {

    function get_open_close_day($restaurant_id,$client_zone) {
        $CI = &get_instance(); 
        $date = new DateTime();
        $tz = new DateTimeZone($client_zone);
        $date->setTimeZone($tz);
        $day = $date->format('l');
        $day_array = $CI->config->item("day_array");
        $key = array_search ($day, $day_array);
        $time = date("H:i:s"); 
        $CI->db->select("*");
        $CI->db->where("open_close_day", $key);
        $CI->db->where("user_id", $restaurant_id);
        $CI->db->where("open_close_status = '1'");
        //$CI->db->where("open_time_from <='".$time."' AND  close_time_to >='".$time."'");
        $query = $CI->db->get("tab_restaurant_open_close_time");
        if ($query->num_rows()) {            
           return $query->row();
        } else {
            return "Closed";
        }
    }

}
/**
 * Element
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty it returns FALSE (or whatever you specify as the default value.)
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */



/**
 * Purpose: Merging content from email view and userdata
 * Input Parameter:
 *          strParam = the parameters to be replaced
 *          strContent = the content from which the parameters are to be replaced
 * Output Parameter:
 *          template as text
 */
if (!function_exists('mergeContent')) {

    function mergeContent($strParam, $strContent) {
        foreach ($strParam as $key => $value) {
            $strContent = str_replace($key, $value, $strContent);
        }
        $CI = &get_instance();
        ob_start(); // start output buffer
        $data['content'] = $strContent;
        $CI->load->view('admin/email/template', $data);
        $template = ob_get_contents(); // get contents of buffer
        ob_end_clean();
        return $template;
    }

}


if (!function_exists('get_email_template')) {

    function get_email_template($templateId) {

        $CI = &get_instance();
        $CI->db->where('email_name', $templateId);
        $result = $CI->db->get("emailtemplatemst");
        if ($result->num_rows() > 0)
            return $result->row();
        else
            return 0;
    }

}

/**
 * Purpose: Merging content from email view and userdata
 * Input Parameter:
 *          strParam = the parameters to be replaced
 *          strContent = the content from which the parameters are to be replaced
 * Output Parameter:
 *          template as text
 */
if (!function_exists('getDateRange')) {

    function getDateRange($month, $year) {
        $strMonth = ($month < 10 ? "0" . ((int) $month) : $month);
        $retArr = array();
        $retArr['start_date'] = $year . "-" . $strMonth . "-01";
        switch ($month) {
            case 1:
            case 3:
            case 5:
            case 7:
            case 8:
            case 10:
            case 12:
                $retArr['end_date'] = $year . "-" . $strMonth . "-31";
                break;
            case 4:
            case 6:
            case 9:
            case 11:
                $retArr['end_date'] = $year . "-" . $strMonth . "-30";
                break;
            case 2:
                $isLeap = date("L");
                if ($isLeap) {
                    $retArr['end_date'] = $year . "-" . $strMonth . "-29";
                } else {
                    $retArr['end_date'] = $year . "-" . $strMonth . "-28";
                }
                break;
            default:
                $retArr['end_date'] = $year . "-" . $strMonth . "-31";
                break;
        }
        return $retArr;
    }

}

/**
 * Purpose: Getting the time in appropriate format
 * Input Parameter:
 *          strParam = the parameters to be replaced
 *          strContent = the content from which the parameters are to be replaced
 * Output Parameter:
 *          template as text
 */
if (!function_exists('findTime')) {

    function findTime($compareTime) {
        $currentTime = time();
        $compareTime = strtotime($compareTime);
        $resultantTime = $currentTime - $compareTime;

        if ($resultantTime <= 60) {
            //within a minute..
            return $resultantTime . " sec ago.";
        } else if ($resultantTime <= 3600) {
            //within an hour..
            $mins = (int) ($resultantTime / 60);
            return ($mins > 1) ? $mins . " mins ago." : $mins . " min ago.";
        } else if ($resultantTime <= 86400) {
            //within a day..
            $hours = (int) ($resultantTime / 3600);
            return ($hours > 1) ? $hours . " hours ago." : $hours . " hour ago.";
        } else {
            //greater than a day...
            $days = (int) ($resultantTime / 86400);
            return ($days > 1) ? $days . " days ago." : $days . " day ago.";
        }
    }

}


/**
 * Purpose: Merging content from email view and userdata
 * Input Parameter:
 *          strParam = the parameters to be replaced
 *          strContent = the content from which the parameters are to be replaced
 * Output Parameter:
 *          template as text
 */
if (!function_exists('replaceData')) {

    function replaceData($strParam = array(), $str) {
        $strContent = $str;
        if ($strParam) {
            foreach ($strParam as $key => $value) {
                $strContent = str_replace($key, $value, $strContent);
            }
        }
        return $strContent;
    }

}




if(! function_exists('clearJunk'))
{
    function clearJunk($string){
            $string = str_replace(" ", "-", trim($string));
            $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
            $string = strtolower($string);
            return $string;
        }
}
/**
 * Element
 *
 * Lets you send push notification to a user.
 *
 * @access	public
 * @return	mixed	depends on what the array contains
 */
if (!function_exists('sendPushNotification')) {

    function sendPushNotification($devicetype, $msg, $deviceToken, $channelId, $msg_sent_date, $type_of_message, $title = "") {
        if ($devicetype == 'A') {
            $api_key = GOOGLE_API_KEY;

            //Android connection
            $url = 'https://android.googleapis.com/gcm/send';
            $headers = array(
                'Authorization:key=' . $api_key,
                'Content-Type: application/json'
            );
            //End
            $notification_title = $title ? $title : " Safyre team";
            $registrationIds = (array) $deviceToken;
            //$message = array('type' => $message_type, 'title' => $notification_title, "messageId" => $messageId, "msg_sent_date" => $msg_sent_date, "type_of_message" => $type_of_message, 'message' => $msg, 'is_jinxing' => $statusArr['is_jinxing'], 'is_jinxed' => $statusArr['is_jinxed'], 'is_contact' => $statusArr['is_contact'], 'sender_name' => $statusArr['sender_name']);
            $message = array('type' => $type_of_message, 'title' => $notification_title, "channel_id" => $channelId, "msg_sent_date" => $msg_sent_date, 'message' => $msg);
            // Set POST variables
            $fields = array(
                'registration_ids' => $registrationIds,
                'data' => $message,
            );

            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            // Execute post
            $result = curl_exec($ch);
            // Close connection
            //echo $result;
            curl_close($ch);
            //echo $result;
        } else {
            $message = $msg;
            //echo '<br>>>'.PEM_FILE.'>>'.PASSPHRASE."<br/>";die;
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', PEM_FILE);
            stream_context_set_option($ctx, 'ssl', 'passphrase', PASSPHRASE);

            // Open a connection to the APNS server
            //$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            $fp = stream_socket_client(SSL_URL, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);
            $notification_title = $title ? $title : "Safyre team";
            $load = array(
                'aps' => array('alert' => $message, 'sound' => 'default'),
                'channel_id' => $channelId,
                'msg_sent_date' => $msg_sent_date,
                'type' => $type_of_message,
                'title' => $notification_title,
            );
            // Encode the payload as JSON
            //$payload = json_encode($body);
            $payload = json_encode($load);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            fclose($fp);
            echo $deviceToken . 'sent ! <br>' . $result;
            //die;
            // Close the connection to the server
        }
    }

}
/**
 * Element
 *
 * Lets you send push notification to a user.
 *
 * @access	public
 * @return	mixed	depends on what the array contains
 */
if (!function_exists('sendPush')) {

    function sendPush($channel_id, $title, $type) {
        $CI = &get_instance();
        $CI->db->select("`user_id`, `user_first_name`, `user_last_name`,`user_email`");
        $CI->db->from('usermst');
        $CI->db->where("(user_status = 1 AND is_deleted = 0)");


        $result = $CI->db->get();
        $users = $result->result_array();

        foreach ($users as $user) {
            $CI->db->select("`user_id`, `DeviceId`, `DeviceType`");
            $CI->db->from('userdevices');
            $CI->db->where("(IsActive = 1 AND IsRemoved = 0)");
            $CI->db->where('DeviceId != ', "");
            $CI->db->where('DeviceType != ', "");
            $CI->db->where('user_id', $user['user_id']);
            $result = $CI->db->get();
            $usersDevice = $result->row();

            $deviceType = $usersDevice->DeviceType;
            $DeviceId = $usersDevice->DeviceId;
            $userId = $user['user_id'];
            $channel_id = $channel_id;

            $msg_sent_date = date("Y-m-d h:i:s");
            if ($type == "channel") {
                $msg = str_replace("{name}", ucfirst($title), CHANNEL_MESSAGE);
                $title = "New channel";
                $type = PUSH_IS_CHANNEL_ADDED;
            } else {
                $msg = str_replace("{name}", ucfirst($title), VIDEO_MESSAGE);
                $title = "New Video";
                $type = PUSH_IS_VIDEO_ADDED;
            }
            // push notification
            if ($deviceType)
                sendPushNotification($deviceType, $msg, $DeviceId, $channel_id, $msg_sent_date, $type, $title);
            // send mail
            sendMail($msg, $title, $userId);
            // insert in db
            $arrData = array(
                'user_id' => $userId,
                'push_message' => $msg,
                'push_message_date' => $msg_sent_date,
                'push_type' => $type,
                'channel_id' => $channel_id
            );
            $CI->db->insert('notification', $arrData);
            echo $CI->db->insert_id();
        }
    }

}

/**
 * Element
 *
 * Lets you send mail notification to a user when new channel or video is added.
 *
 * @access	public
 * @return	mixed	depends on what the array contains
 */
if (!function_exists('sendMail')) {

    function sendMail($message, $title, $userId) {
        $CI = &get_instance();
        $CI->db->select("`user_id`, `user_first_name`, `user_last_name`,`user_email`");
        $CI->db->from('usermst');
        $CI->db->where("(user_status = 1 AND is_deleted = 0)");
        $CI->db->where('user_id', $userId);

        $result = $CI->db->get();
        $users = $result->row();

        $aEmailTemplate = getEmailTemplate(9);
        $aSendEmailTemplate = $aEmailTemplate->emt_text;

        $sUsername = $users->user_first_name . " " . $users->user_last_name;
        $aSendEmailTemplate = str_replace("{NAME}", $sUsername, $aSendEmailTemplate);
        $aSendEmailTemplate = str_replace("{TITLE}", $title, $aSendEmailTemplate);
        $aSendEmailTemplate = str_replace("{MESSAGE}", $message, $aSendEmailTemplate);

        $strParam = array(
            '{NAME}' => $sUsername,
            '{TITLE}' => $title,
            '{MESSAGE}' => $message
        );
        $txtMessageStr = mergeContent($strParam, 'template');
        $txtMessageStr = str_replace("undefined", "", $txtMessageStr);
        $CI->load->library('email');
        $CI->email->from('websites.tester@gmail.com', $CI->config->item('site_name'));
        $CI->email->to($users->user_email);
        $CI->email->subject($CI->config->item('site_name') . ": $title");
        $CI->email->message($txtMessageStr);
        $result = $CI->email->send();
    }

}

if ( ! function_exists('urlClean'))
{
    function urlClean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
}

/**
 * Element
 *
 * Lets you get categories by channel (used for channel screen front).
 *
 * @access	public
 * @return	mixed	depends on what the array contains
 */
// function that calculates time ago
if (!function_exists('get_timeago')) {

    function get_timeago($ptime) {
        $ptime = strtotime($ptime);
        $estimate_time = time() - $ptime;

        if ($estimate_time < 1) {
            return 'less than 1 second ago';
        }

        $condition = array(
            12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($condition as $secs => $str) {
            $d = $estimate_time / $secs;

            if ($d >= 1) {
                $r = round($d);
                return 'about ' . $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
            }
        }
    }

}

if (!function_exists('rand_string')) {

    function rand_string($length) {
        $chars = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
        return substr(str_shuffle($chars), 0, $length);
    }

    return rand_string(5);
}




if (!function_exists('clean_string')) {

    function clean_string($string) {
        $s = trim($string);
        $s = iconv("UTF-8", "UTF-8//IGNORE", $s); // drop all non utf-8 characters
        // this is some bad utf-8 byte sequence that makes mysql complain - control and formatting i think
        $s = preg_replace('/(?>[\x00-\x1F]|\xC2[\x80-\x9F]|\xE2[\x80-\x8F]{2}|\xE2\x80[\xA4-\xA8]|\xE2\x81[\x9F-\xAF])/', ' ', $s);

        $s = preg_replace('/\s+/', ' ', $s); // reduce all multiple whitespace to a single space

        return $s;
    }

}


/* End of file common_helper.php */
/* Location: ./appplication/helpers/array_helper.php */