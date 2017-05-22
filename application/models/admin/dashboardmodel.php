<?php

class Dashboardmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
     * Purpose: Method  to be displayed in real time dashboard is:
     *  i. Total number of users
     *  ii. Total active users
     *  iii. Total non-active users (no activity for last month) 
     * iv. Number of invitations to Friends 
     * v. Number of accepted invitations/ downloads.
     *  vi. Others (if any)
     * Date: 20-10-2015
     * Input Parameter: None
     * Output Parameter: 
     * 		
     */

    function get_all_user() {
        $this->db->select(' COUNT(*) as count');
        $this->db->from('usermst');
        $this->db->where('is_deleted', '0');
        $this->db->where('user_type', '3');

        return $this->db->count_all_results();
    }

    function get_all_active_user() {
        $this->db->select(' COUNT(*) as count');
        $this->db->from('usermst');

        $this->db->where('user_status', '1');
        $this->db->where('is_deleted', '0');
        $this->db->where('user_type', '3');

        return $this->db->count_all_results();
    }

}

?>