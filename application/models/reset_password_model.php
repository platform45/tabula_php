<?php
/*
  Model that contains function related to reset user password
 */
class Reset_password_model extends CI_Model
{
  public function __construct( )
  {
    parent::__construct();
  }

  /*
   * Method Name: check_valid_token
   * Purpose: To verify if token exists in database
   * params:
   *      input: token
   *      output: user_id
   */
  public function check_valid_token( $token )
  {
    $this->db->select('user_id');
    $this->db->from('usermst');
    $data = array(
              'forgot_password_hash' => $token,
              'user_status' => '1',
              'is_deleted' => '0',
            );
    $this->db->where( $data );
    $this->db->limit(1);
    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? $query->row()->user_id : 0;
  }
}
?>