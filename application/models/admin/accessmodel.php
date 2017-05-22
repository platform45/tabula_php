<?php

/*
 * Programmer Name:Preeti M
 * Purpose: Model for checking user access.
 * Date: 06 Oct 2016
 * Dependency: None
 */

class Accessmodel extends CI_Model {

  public function __construct() {
    parent::__construct();
  }

  //Get option id for a option.
  public function get_option_id( $option_name )
  {
    $this->db->select("opt_optionid");
    $this->db->from("optionmst");
    $this->db->like("opt_pagename",$option_name);
    $this->db->limit(1);

    $query = $this->db->get();
    return ( $query->num_rows() > 0 ) ? $query->row()->opt_optionid : 0;
  }

  //Method to check if user has access to the menu.
  public function check_user_access( $user_id, $option_id )
  {
    $this->db->select("acc_accessid");
    $this->db->from("accessmst");
    $this->db->where("acc_optionid",$option_id);
    $this->db->where("acc_userid",$user_id);
    $this->db->limit(1);

    $query = $this->db->get();
    return ( $query->num_rows() > 0 ) ? TRUE : FALSE;
  }
}

?>