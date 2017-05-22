<?php
/*
  Model that contains function related to push notifications
 */
class Push_notification_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  /*
   * Method Name: insert
   * Purpose: To insert data in push_notification table
   * params:
   *      input: data array
   *      output: id of inserted record
   */
  public function insert( $data )
  {
    $this->db->insert("push_notification",$data);
    return $this->db->insert_id();
  }
}
?>