<?php
/*
* Programmer Name:AD
* Purpose: Model for controlling database interactions regarding the user CRUD operations.
* Date: 14 Aug 2015
* Dependency: None
*/
class Membersmodel extends CI_Model
{
        /*
        * Purpose: Constructor.
        * Date: 11 Oct 2014
        * Input Parameter: None
        *  Output Parameter: None
        */
        public function __construct( )
        {
            parent::__construct();
        }
        
        /*
        * Purpose: To check if user with same user exists.
        * Date: 11 Oct 2014
        * Input Parameter: 
        *		$page_number, $search_text
        *  Output Parameter: 
        *		Array : if records exist.
        *		FALSE : if records does not exist.
        */
        public function getAllMembers($page_number, $search_text = '', $user_id = 0)
        {
            $this->db->select('id');
            $this->db->from("jinx_jinxing_master j");
            $this->db->where(array(
                'j.user_id' => $user_id,
                //'j.request_accepted != ' => 0,
                //'j.request_accepted != ' => -2,
                'u.active' => 1,
                'u.is_deleted' => 0
            ));
            $this->db->where("(j.request_accepted = 1 OR j.request_accepted = -1 )");
            $this->db->join("jinx_usermst u","j.jinxing_user_id = u.id");
            $query = $this->db->get();
            $inStr = array();
            if($query->num_rows() > 0)
            {
                $query = $query->result_array();
                //print_r($query);die;
                foreach ($query as $value)
                {
                    array_push($inStr, $value['id']);
                }
                //print_r($inStr);die;
            }
            else
            {
                array_push($inStr, 0);
            }
            
            
            $this->db->select('id');
            $this->db->from("jinx_jinxed_master j");
            $this->db->where(array(
                'j.user_id' => $user_id,
                'u.active' => 1,
                'u.is_deleted' => 0
            ));
            $this->db->where("(j.request_accepted = 1 OR j.request_accepted = -1 )");
            $this->db->join("jinx_usermst u","j.jinxed_user_id = u.id");
            $query1 = $this->db->get();
            if($query1->num_rows() > 0)
            {
                $query1 = $query1->result_array();
                //print_r($query);die;
                foreach ($query1 as $value)
                {
                    array_push($inStr, $value['id']);
                }
                //print_r($inStr);die;
            }
            else
            {
                array_push($inStr, -1);
            }
            
            $this->db->select(array(
                'id',
                'first_name',
                'last_name',
                'username',
                'contact',
                'country',
                'profile_picture',
                'email',
                'cou_name as country_name'
            ));
            $this->db->from('jinx_usermst');
            //$this->db->join("jinx_country_master","cou_id = country","LEFT");
            $this->db->join("jinx_country_master","cou_id = country");
            $this->db->where('active',1);
            $this->db->where('id <> ',$user_id);
            $this->db->where_not_in('id', $inStr);
            $this->db->where('is_deleted',0);
            if($search_text)
            {
                $this->db->where("(first_name LIKE '%$search_text%' OR last_name LIKE '%$search_text%' OR username LIKE '%$search_text%' OR email  LIKE '%$search_text%' OR cou_name LIKE '%$search_text%')");
            }
            if(!$search_text)
                $this->db->limit( RESULT_SET_LIMIT , ($page_number * RESULT_SET_LIMIT));
            
            $this->db->order_by("first_name","ASC");
            $this->db->order_by("last_name","ASC");
            $this->db->order_by("username","ASC");
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            
            if ($query->num_rows() > 0)
            {
                $userArr = $query->result_array();
                $i=0;
                $count = count($userArr);
                for($i=0;$i<$count;$i++)
                {
                    if($userArr[$i]['profile_picture'])
                    {
                        $userArr[$i]['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$userArr[$i]['profile_picture']);
                    }
                    else
                    {
                        $userArr[$i]['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                    }
                    if(!$userArr[$i]['country_name'])
                    {
                        $userArr[$i]['country_name'] = "";
                    }
                    
                }
            }
            else
            {
                $userArr = array();
            }
            return $userArr;
        }
        
        
        /*
        * Purpose: To check if user with same user exists.
        * Date: 11 Oct 2014
        * Input Parameter: 
        *		$page_number, $search_text
        *  Output Parameter: 
        *		Array : if records exist.
        *		FALSE : if records does not exist.
        */
        public function getAllFriends($page_number = 0, $contact_list = '', $search_text = '', $user_id = 0)
        {
            $this->load->model('webservices/usermodel','usermodel',TRUE);
            $contact_data = $this->usermodel->getData($user_id);
            $contact_data = $contact_data['contact'];
            
            $this->db->select(array(
                'id',
                'first_name',
                'last_name',
                'username',
                'contact',
                'country',
                'status',
                'profile_picture',
                'email',
                'cou_name as country_name'
            ));
            $this->db->from('jinx_usermst');
            $this->db->join("jinx_country_master","cou_id = country","LEFT");
            //$this->db->where('active',1);
            //$this->db->where('is_deleted',0);
            $this->db->where("(active = 1 AND is_deleted = 0)");
            
            if(!$contact_list)
            {
                $contact_list = "-99999999999999";
            }
            else
            {
                $likeStr = "(";
                $i = 0;
                for($i=0;$i<count($contact_list)-1;$i++)
                {
                    if($contact_list[$i] != '')
                       $likeStr = $likeStr."contact LIKE '%".$contact_list[$i]."%' OR ";
                    //$this->db->or_like('contact', $contact);
                }
                if($contact_list[$i] != '')
                    $likeStr = $likeStr."contact LIKE '%".$contact_list[$i]."%' ) ";
                else
                    $likeStr = $likeStr." ) ";
                $this->db->where($likeStr);
            }
            //$this->db->where('contact IN ('.$contact_list.')');
            $this->db->where("contact NOT LIKE '%$contact_data%'");
            if($search_text)
            {
                $this->db->where("(first_name LIKE '%$search_text%' OR last_name LIKE '%$search_text%' OR username LIKE '%$search_text%' OR email  LIKE '%$search_text%' OR cou_name LIKE '%$search_text%')");
            }
            
            //$this->db->limit( RESULT_SET_LIMIT , ($page_number * RESULT_SET_LIMIT));
            $this->db->order_by("first_name","ASC");
            $this->db->order_by("last_name","ASC");
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            
            if ($query->num_rows() > 0)
            {
                $userArr = $query->result_array();
                $i=0;
                $count = count($userArr);
                
                //INSERT THE COUNT IN THE USER'S PROFILE...
                $update_count = array(
                    'friend_count' => $count
                );
                $this->db->where("id", $user_id);
                $this->db->update("jinx_usermst", $update_count);
                
                
                for($i=0;$i<$count;$i++)
                {
                    if($userArr[$i]['profile_picture'])
                    {
                        $userArr[$i]['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$userArr[$i]['profile_picture']);
                    }
                    else
                    {
                        $userArr[$i]['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                    }
                    
                    $whatStatus = getStatus($user_id, $userArr[$i]['id'], $contact_list);
                    //print_r($whatStatus);die;
                    $userArr[$i]['is_jinxing'] = $whatStatus['is_jinxing'];
                    $userArr[$i]['is_jinxed'] = $whatStatus['is_jinxed'];
                    $userArr[$i]['is_contact'] = $whatStatus['is_contact'];
                    
                }
            }
            else
            {
                $userArr = array();
            }
            return $userArr;
        }
        
        
        /*
        * Purpose: To check if user with same user exists.
        * Date: 11 Oct 2014
        * Input Parameter: 
        *				$username : Username.
        *				$password : Password.
        *  Output Parameter: 
        *				Array : if the user already exists.
        *				FALSE : if user does not exists.
        */
        public function getAllJinxingMembers($user_id, $page_number)
        {
            $this->db->select(array(
                'id',
                'first_name',
                'last_name',
                'username',
                'contact',
                'country',
                'profile_picture',
                'email',
                'cou_name as country_name'
            ));
            
            $this->db->from("jinx_jinxing_master j");
            $this->db->where(array(
                'j.user_id' => $user_id,
                'j.request_accepted' => 1,
                'u.id <> ' => $user_id,
                'u.active' => 1,
                'u.is_deleted' => 0
            ));
            $this->db->join("jinx_usermst u","j.jinxing_user_id = u.id");
            $this->db->join("jinx_country_master c","c.cou_id = u.country");
            $this->db->limit( RESULT_SET_LIMIT , ($page_number * RESULT_SET_LIMIT));
            $this->db->order_by("first_name","ASC");
            $this->db->order_by("last_name","ASC");
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            
            if ($query->num_rows() > 0)
            {
                $userArr = $query->result_array();
                $i=0;
                $count = count($userArr);
                for($i=0;$i<$count;$i++)
                {
                    if($userArr[$i]['profile_picture'])
                    {
                        $userArr[$i]['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$userArr[$i]['profile_picture']);
                    }
                    else
                    {
                        $userArr[$i]['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                    }
                }
            }
            else
            {
                $userArr = array();
            }
            return $userArr;
        }
    
        
        /*
        * Purpose: To get the list of all jinxed members.
        * Input Parameter: 
        *		$user_id, $page_number
        *  Output Parameter: 
        *		Array : if the user exists.
        *		FALSE : if member does not exists.
        */
        public function getAllJinxedMembers($user_id, $page_number, $status, $is_count = 0, $get_all_jinxed_member_list = 0)
        {
            $this->db->select(array(
                'id',
                'first_name',
                'last_name',
                'username',
                'contact',
                'country',
                'profile_picture',
                'email',
                'cou_name as country_name'
            ));
            
            $this->db->from("jinx_jinxed_master j");
            $this->db->where(array(
                'j.user_id' => $user_id,
                'j.request_accepted' => $status,
                'u.active' => 1,
                'u.is_deleted' => 0
            ));
            $this->db->join("jinx_usermst u","j.jinxed_user_id = u.id");
            $this->db->join("jinx_country_master c","c.cou_id = u.country");
            
            //if(!$is_count)
               // $this->db->limit( RESULT_SET_LIMIT , ($page_number * RESULT_SET_LIMIT));
            if($get_all_jinxed_member_list)
                $this->db->limit( RESULT_SET_LIMIT , ($page_number * RESULT_SET_LIMIT));
            
            $this->db->order_by("first_name","ASC");
            $this->db->order_by("last_name","ASC");
            $query = $this->db->get();
            //echo $this->db->last_query();die;
            
            if ($query->num_rows() > 0)
            {
                if($is_count)
                {
                    return count($query->result_array());
                }
                else
                {
                    $userArr = $query->result_array();
                    $i=0;
                    $count = count($userArr);
                    for($i=0;$i<$count;$i++)
                    {
                        if($userArr[$i]['profile_picture'])
                        {
                            $userArr[$i]['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$userArr[$i]['profile_picture']);
                        }
                        else
                        {
                            $userArr[$i]['profile_picture'] = base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                        }
                    }
                }
            }
            else
            {
                if($is_count)
                {
                    $userArr = 0;
                }
                else
                {
                    $userArr = array();
                }
            }
            return $userArr;
        }
    
        
        /*
        * Purpose: To send a jinx request.
        * Input Parameter: 
        *		$user_id, $jinxing_user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function member_jinxing($user_id, $jinxing_user_id)
        {
            $edit_id_jinxing = 0;
            $edit_id_jinxed = 0;
            $time = date("Y-m-d H:i:s");
            $result = $this->get_jinxing_member($user_id, $jinxing_user_id);
            
            if($result)
            {
                $update_array = array(
                    'request_accepted' => -1,
                    'reply_date' => $time
                );
                $this->db->where("jinxing_id", $result->jinxing_id);
                $this->db->update("jinx_jinxing_master", $update_array);
                $edit_id_jinxing = $result->jinxing_id;
                
                //echo $edit_id_jinxing;die;
            }
            else
            {
                $insert_array = array(
                    'user_id' => $user_id,
                    'jinxing_user_id' => $jinxing_user_id,
                    'request_accepted' => -1,
                    'reply_date' => $time
                );
                $this->db->insert("jinx_jinxing_master", $insert_array);
                $edit_id_jinxing = $this->db->insert_id();
            }
            
            $jinxed_status = $this->get_jinxed_member($jinxing_user_id, $user_id);
            //print_r($jinxed_status);die;
            if($jinxed_status)
            {
                $update_array = array(
                    'request_accepted' => -1,
                    'reply_date' => $time
                );
                $this->db->where("jinxed_id", $jinxed_status->jinxed_id);
                $this->db->update("jinx_jinxed_master", $update_array);
                $edit_id_jinxed = $jinxed_status->jinxed_id;
                //echo $edit_id_jinxed;die;
            }
            else
            {
                $insert_array = array(
                    'user_id' => $jinxing_user_id,
                    'jinxed_user_id' => $user_id,
                    'request_accepted' => -1,
                    'reply_date' => $time
                );
                $this->db->insert("jinx_jinxed_master", $insert_array);
                $edit_id_jinxed = $this->db->insert_id();
            }
            
            if($edit_id_jinxed > 0 && $edit_id_jinxing > 0)
            {
                //SUCCESS - JINXING SUCCESSFUL
                return $edit_id_jinxed && $edit_id_jinxing;
            }
            else
            {
                //FAIL - JINXING FAILED
                return 0;
            }
            
            
        }
        
        
        /*
        * Purpose: To reply to a jinx request.
        * Input Parameter: 
        *		$user_id, $jinxed_user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function member_jinxed($user_id, $jinxed_user_id, $status)
        {
            $edit_id_jinxing = 0;
            $edit_id_jinxed = 0;
            $time = date("Y-m-d H:i:s");
            $result = $this->get_jinxing_member($jinxed_user_id, $user_id);
            
            if($result)
            {
                $update_array = array(
                    'request_accepted' => $status,
                    'reply_date' => $time
                );
                $this->db->where("jinxing_id", $result->jinxing_id);
                $this->db->update("jinx_jinxing_master", $update_array);
                $edit_id_jinxing = $result->jinxing_id;
                
                //echo $edit_id_jinxing;die;
            }
            else
            {
                $edit_id_jinxing = 0;
            }
            
            $jinxed_status = $this->get_jinxed_member($user_id, $jinxed_user_id);
            //print_r($jinxed_status);die;
            if($jinxed_status)
            {
                $update_array = array(
                    'request_accepted' => $status,
                    'reply_date' => $time
                );
                $this->db->where("jinxed_id", $jinxed_status->jinxed_id);
                $this->db->update("jinx_jinxed_master", $update_array);
                $edit_id_jinxed = $jinxed_status->jinxed_id;
                //echo $edit_id_jinxed;die;
            }
            else
            {
                
                $edit_id_jinxed = 0;
            }
            
            if($edit_id_jinxed > 0 && $edit_id_jinxing > 0)
            {
                //SUCCESS - JINXING SUCCESSFUL
                return $edit_id_jinxed && $edit_id_jinxing;
            }
            else
            {
                //FAIL - JINXING FAILED
                return 0;
            }
            
            
        }
        
        
        /*
        * Purpose: To unjinx a member.
        * Input Parameter: 
        *		$user_id, $jinxed_user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function member_un_jinx($user_id, $jinxed_user_id, $status)
        {
            $edit_id_jinxing = 0;
            $edit_id_jinxed = 0;
            $time = date("Y-m-d H:i:s");
            $result = $this->get_jinxing_member($jinxed_user_id, $user_id);
            if(!$result)
            {
                $result = $this->get_jinxing_member($user_id, $jinxed_user_id);
            }
            //print_r($result);die;
            if($result)
            {
//                $update_array = array(
//                    'request_accepted' => $status,
//                    'reply_date' => $time
//                );
//                $this->db->where("jinxing_id", $result->jinxing_id);
//                $this->db->update("jinx_jinxing_master", $update_array);
//                $edit_id_jinxing = $result->jinxing_id;
                
                $this->db->delete('jinx_jinxing_master', array('jinxing_id' => $result->jinxing_id)); 
                $edit_id_jinxing = 1;
                
                //echo $edit_id_jinxing;die;
            }
            else
            {
                $edit_id_jinxing = 0;
            }
            
            $jinxed_status = $this->get_jinxed_member($user_id, $jinxed_user_id);
            if(!$jinxed_status)
            {
                $jinxed_status = $this->get_jinxed_member($jinxed_user_id, $user_id);
            }
            //print_r($jinxed_status);die;
            if($jinxed_status)
            {
//                $update_array = array(
//                    'request_accepted' => $status,
//                    'reply_date' => $time
//                );
//                $this->db->where("jinxed_id", $jinxed_status->jinxed_id);
//                $this->db->update("jinx_jinxed_master", $update_array);
//                $edit_id_jinxed = $jinxed_status->jinxed_id;
                
                $this->db->delete('jinx_jinxed_master', array('jinxed_id' => $jinxed_status->jinxed_id)); 
                $edit_id_jinxed = 1;
                
                //echo $edit_id_jinxed;die;
            }
            else
            {
                
                $edit_id_jinxed = 0;
            }
            
            if($edit_id_jinxed > 0 && $edit_id_jinxing > 0)
            {
                //SUCCESS - JINXING SUCCESSFUL
                return $edit_id_jinxed && $edit_id_jinxing;
            }
            else
            {
                //FAIL - JINXING FAILED
                return 0;
            }
        }
        
        /*
        * Purpose: To get a list of all jinxing members.
        * Input Parameter: 
        *		$user_id, $jinxing_user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function get_jinxing_member($user_id, $jinxing_user_id)
        {
            $this->db->select("*");
            $this->db->from("jinx_jinxing_master");
            $this->db->where(array(
                'user_id' => $user_id,
                'jinxing_user_id' => $jinxing_user_id,
            ));
            $result = $this->db->get();
            if($result->num_rows() > 0)
            {
                return $result->row();
            }
            else
            {
                return 0;
            }
        }
        
        
        /*
        * Purpose: To get a list of all jinxed members.
        * Input Parameter: 
        *		$user_id, $jinxed_user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function get_jinxed_member($user_id, $jinxed_user_id)
        {
            $this->db->select("*");
            $this->db->from("jinx_jinxed_master");
            $this->db->where(array(
                'user_id' => $user_id,
                'jinxed_user_id' => $jinxed_user_id,
            ));
            $result = $this->db->get();
            if($result->num_rows() > 0)
            {
                return $result->row();
            }
            else
            {
                return 0;
            }
        }
        
        /*
        * Purpose: To get the total count of jinxing members.
        * Input Parameter: 
        *		$user_id, $logged_in_user_id, $jinxing_jinxed = 0 - jinxing ; 1 - jinxed
        */
        public function getMembersSettings($user_id, $logged_in_user_id, $jinxing_jinxed = 0)
        {
            $this->db->from("jinx_user_settings");
            if($jinxing_jinxed)
            {
                $this->db->where(array(
                    'from_user_id' => $logged_in_user_id,
                    'to_user_id' => $user_id
                ));
            }
            else
            {
                $this->db->where(array(
                    'from_user_id' => $user_id,
                    'to_user_id' => $logged_in_user_id
                ));
                
            }
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
        
        /*
        * Purpose: To get a count of all the members who have sent a jinx request.
        * Input Parameter: 
        *		$user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function get_jinx_count($user_id)
        {
            $this->db->where(array(
                'user_id' => $user_id,
                'request_accepted' => -1
            ));
            $this->db->from('jinx_jinxed_master');
            return $this->db->count_all_results();
        }
        
        
        /*
        * Purpose: To get a count of all unread messages for a user.
        * Input Parameter: 
        *		$user_id
        *  Output Parameter: 
        *		count of all the unread messages.
        */
        public function get_chat_count($user_id)
        {
            $this->db->where(array(
                'receiver_id' => $user_id,
                'receiver_read_status' => 0
            ));
            $this->db->from('jinx_messages');
            return $this->db->count_all_results();
        }
        
        /*
        * Purpose: To get a count of all the calendar event requests.
        * Input Parameter: 
        *		$user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function get_calendar_count($user_id)
        {
            $this->db->where(array(
                'invite_to_user_id' => $user_id,
                'invite_status' => -1,
                "DATE_FORMAT(invite_sent_on, '%Y-%m-%d') >= " => date("Y-m-d")
            ));
            $this->db->from('jinx_events_invitation');
            return $this->db->count_all_results();
        }
        
        
        /*
        * Purpose: To get a count of all the calendar event requests.
        * Input Parameter: 
        *		$user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function get_notification_count($user_id)
        {
            $this->db->where(array(
                'notification_user_id' => $user_id,
                'notification_read_status' => 0,
                'notification_deleted' => 0,
                "DATE_FORMAT(notification_recieved_on, '%Y-%m-%d') >= " => date("Y-m-d")
            ));
            $this->db->from('jinx_notification_master');
            return $this->db->count_all_results();
        }
        
        /*
        * Purpose: To get the total count of jinxing members.
        * Input Parameter: 
        *		$user_id
        */
        public function getJinxingMembersCount($user_id)
        {
            $this->db->from("jinx_jinxing_master j");
            $this->db->where(array(
                'j.user_id' => $user_id,
                'j.request_accepted' => 1,
                'u.active' => 1,
                'u.is_deleted' => 0
            ));
            $this->db->join("jinx_usermst u","j.jinxing_user_id = u.id");
            $this->db->order_by("first_name","ASC");
            $this->db->order_by("last_name","ASC");
            return  $this->db->count_all_results();
        }
        
       
        /*
        * Purpose: To get total count of jinxed members.
        * Input Parameter: 
        *		$user_id
        */
        public function getJinxedMembersCount($user_id)
        {
            $this->db->from("jinx_jinxed_master j");
            $this->db->where(array(
                'j.user_id' => $user_id,
                'j.request_accepted' => 1,
                'u.active' => 1,
                'u.is_deleted' => 0
            ));
            $this->db->join("jinx_usermst u","j.jinxed_user_id = u.id");
            $this->db->order_by("first_name","ASC");
            $this->db->order_by("last_name","ASC");
            return $this->db->count_all_results();
        }
        
       
        /*
        * Purpose: To get total count of jinxed members.
        * Input Parameter: 
        *		$user_id
        */
        
        public function getFriendsCount($logged_in_user_id)
        {
            $this->db->select("friend_count");
            $this->db->from('jinx_usermst');
            $this->db->where('id',$logged_in_user_id);
            $this->db->where('active',1);
            $this->db->where('is_deleted',0);
            $result = $this->db->get();
            if($result->num_rows() > 0)
            {
                $result = $result->row_array();
                return $result['friend_count'];
            }
            else
            {
                return 0;
            }
        }
        
        /*
        * Purpose: To get total count of jinxed members.
        * Input Parameter: 
        *		$user_id
        */
        public function getLatestStatus($user_id)
        {
            $this->db->select("post_description");
            $this->db->from("jinx_wall_post_master");
            $this->db->where(array(
                "post_user_id" => $user_id,
                "post_deleted" => 0,
                "post_type" => 1
            ));
            $this->db->order_by("post_added_on","DESC");
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
        
        
        /*
        * Purpose: To get a list of top 10 members.
        * Input Parameter: 
        *		$user_id
        *  Output Parameter: 
        *		FALSE : if jinxing fails.
        *		TRUE : if jinxing succeeds.
        */
        public function getTopTenFriends($user_id)
        {
            $this->db->select("sender_id, count( * ) AS chat_count");
            $this->db->from("jinx_messages");
            $this->db->where(array(
                "receiver_id" => $user_id,
                "receiver_read_status" => 0
            ));
            $this->db->group_by("sender_id");
            $this->db->order_by("chat_count", "DESC");
            $this->db->limit(10);
            $result = $this->db->get();
            //echo $this->db->last_query();die;
            
            if($result->num_rows() > 0)
            {
                $result = $result->result_array();
                $idArr = array();
                foreach ($result as $value)
                {
                   array_push($idArr, $value['sender_id']);
                }
                
                 $this->db->select(array(
                    'id',
                    'first_name',
                    'last_name',
                    'username',
                    'contact',
                    'country',
                    'profile_picture',
                    'active',
                    'email',
                    'cou_name as country_name'
                ));
                $this->db->from('jinx_usermst');
                $this->db->join("jinx_country_master","cou_id = country","LEFT");
                $this->db->where('active',1);
                $this->db->where('is_deleted',0);
                $this->db->where_in('id',$idArr);
                $this->db->order_by("first_name","ASC");
                $this->db->order_by("username","ASC");
                $query = $this->db->get();
                
                $query = $query->result_array();
                for($i = 0; $i < count($query); $i++)
                {
                    if($query[$i]['profile_picture'] != '')
                    {
                        $query[$i]['profile_picture'] = base64_encode(base_url().USER_IMAGE_PATH.$query[$i]['profile_picture']);
                    }
                    else
                    {
                        $query[$i]['profile_picture'] =  base64_encode(base_url().DEFAULT_USER_IMAGE_PATH);
                    }
                }
                return $query;
            }
            else
            {
                return 0;
            }
        }
        
        
        
        public function check_if_request_sent($logged_in_user_id, $user_id)
        {
            $this->db->where("user_id", $logged_in_user_id);
            $this->db->where("jinxing_user_id", $user_id);
            $result = $this->db->get("jinx_jinxing_master");
            if($result->num_rows() > 0)
            {
                return $result->row_array();
            }
            else
            {
                $this->db->where("user_id", $user_id);
                $this->db->where("jinxing_user_id", $logged_in_user_id);
                $result1 = $this->db->get("jinx_jinxing_master");
                if($result1->num_rows() > 0)
                {
                    return $result1->row_array();
                }
                return 0;
            }
        }
        
        public function check_who_sent_the_request($user_id, $logged_in_user_id)
        {
            $this->db->where("user_id", $logged_in_user_id);
            $this->db->where("jinxing_user_id", $user_id);
            $result = $this->db->get("jinx_jinxing_master");
            if($result->num_rows() > 0)
            {
                return $logged_in_user_id;
            }
            else
            {
                $this->db->where("user_id", $user_id);
                $this->db->where("jinxing_user_id", $logged_in_user_id);
                $result1 = $this->db->get("jinx_jinxing_master");
                if($result1->num_rows() > 0)
                {
                    return $user_id;
                }
                return 0;
            }
        }
        
}
?>