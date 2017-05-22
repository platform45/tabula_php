<?php

class LoginModel extends CI_Model
{
            
    public function __construct()
    {
            parent::__construct();
    }
    
    public function checkUsernameExist($username)
    {
        if($username)
        {
            $query = $this->db->query("SELECT username FROM saf_user WHERE username = '{$username}'");
            $row = $query->row();
            if($row)
                return $row->username;
            else
                return false;
        }
        else
        return false;
    }
    
    public function checkEmailExist($email)
    {
        if($email)
        {
            $query = $this->db->query("SELECT username,user_id,contactEmail,firstname,lastname FROM saf_user WHERE contactEmail = '{$email}'");
            $row = $query->row();
            if($row)
                return $row;
            else
                return false;
        }
        else
        return false;
    }
    
    
    public function checkEmailExistance($email,$user_id)
    {
        
        $this->db->select('username')
         ->from('saf_user')
         ->where_not_in('user_id' , $user_id)
         ->where('contactEmail' , $email); 
        
        $result = $this->db->get();
        $row = $result->row();
        if($row)
            return $row;
        else
            return false;
    }
    
    public function checkHashByEmailUserID($email,$user_id)
    {
        
        $this->db->select('username')
         ->from('saf_user')
         ->where('user_id' , $user_id)
         ->where('contactEmail' , $email); 
        
        $result = $this->db->get();
        $row = $result->row();
        if($row)
            return $row;
        else
            return false;
    }
    
    public function checkUsernameExistance($username,$user_id)
    {
        $this->db->select('username')
         ->from('saf_user')
         ->where_not_in('user_id' , $user_id)
         ->where('username' , $username); 
        
        $result = $this->db->get();
        $row = $result->row();
        if($row)
            return $row;
        else
            return false;
    }
    
    public function checkLoginDetails($user,$p)
    {
            $p = md5($p);
            $query = $this->db->query("SELECT user_id FROM saf_user WHERE username = '{$user}' AND password = '{$p}'");
            $row = $query->row();
            if($row)
                return $row->user_id; 
            else
                return false;
    }
    
    public function getUserLoggingData($user,$p)
    {
        $p = md5($p);
        $query = $this->db->query("SELECT user_id,username,longitude,latitude,userPhoto FROM saf_user WHERE username = '{$user}' AND password = '{$p}'");
        $row = $query->row();
        return $row = $row ? $row : '';
    }
    
    public function get_Role($user)
    {
        $this->db->select('saf_userroles.name')
         ->from('saf_userroles')
         ->join('saf_user', 'saf_user.role_id = saf_userroles.role_id')
         ->where('saf_user.user_id' , $user);         
        
        $result = $this->db->get();
        $row = $result->row();
        if($row)
            return $row->name;
        else
            return false;
    }
    
    public function get_Status($user)
    {
        $this->db->select('status')
         ->from('saf_user')
         ->where('user_id' , $user);         
        
        $result = $this->db->get();
        $row = $result->row();
        if($row)
            return $row->status;
        else
            return false;
    }
    
    public function updatePassword($pwd,$user_id)
    {
        $updatePwd = array('password' => $pwd);
        $this->db->where('user_id', $user_id);
        $this->db->update('saf_user', $updatePwd); 
        return $user_id;
    }
    
    public function isUserEmail($email)
    {
        if($email)
        {
            $query = $this->db->query("SELECT username,user_id FROM saf_user WHERE contactEmail = '{$email}' AND role_id = 4");
            $row = $query->row();
            if($row)
                return $row;
            else
                return false;
        }
        else
        return false;
    }
    
}


?>