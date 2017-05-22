<?php
/*
* Programmer Name:AD
* Purpose: Model for controlling database interactions regarding the user CRUD operations.
* Date: 14 Aug 2015
* Dependency: None
*/
class Password_forget_model extends CI_Model
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
        
        public function getDetailsFromHash($hash)
        {
            if($hash)
            {
                $this->db->select(array(
                    'user_id',
                    'user_first_name',
                    'user_last_name',
                    'user_username',
                    'user_contact',
                    'user_first_name',
                    'user_last_name',
                    'user_email'
                ));
                $this->db->from('usermst');
                if($hash)
                    $this->db->where('forget_password_hash', $hash);
                $this->db->where('user_status',1);
                $this->db->where('is_deleted',0);
                if($hash)
                    $this->db->limit(1);
                $this->db->order_by("user_first_name","ASC");
                $query = $this->db->get();
                
                if($query->num_rows() > 0)
                {
                    return $query->row_array();
                }
                else
                {
                    return 0;
                }
            }
            else
            {
                return 0;
            }
        }
       
}
?>