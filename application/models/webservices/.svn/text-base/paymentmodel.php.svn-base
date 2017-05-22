<?php
/*
  Model that contains function related to payment
 */
class PaymentModel extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  /*
   * Method Name: get_total_payment_records
   * Purpose: To count of payment records for a user
   * params:
   *      input: user_id, type
   *      output: count
   */
  public function get_total_payment_records( $user_id, $type )
  {
    $payment_status = ( $type == 1 ) ? '0' : '1';

    $this->db->select("r.restaurant_id");
    $this->db->from("hzi_booking_request r");
    $this->db->join("hzi_booking_guest g","g.booking_id = r.booking_id");
    $data = array(
              'g.guest_id' => $user_id,
              'r.status' => '3',
              'r.payment_status' => $payment_status
            );
    $this->db->where( $data );
    $this->db->where('r.total_amount != 0');
    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? $query->num_rows() : 0;
  }

  /*
   * Method Name: get_payment_details
   * Purpose: To get payment details for a user
   * params:
   *      input: user_id, type, limit, offset
   *      output: array containing payment details
   */
  public function get_payment_details( $user_id, $type, $limit, $offset )
  {
    $payment_status = ( $type == 1 ) ? '0' : '1';
    $this->db->select("CONCAT( u.user_first_name,' ',u.user_last_name ) as restaurant_name,
                      IF( u.user_image = '', '', CONCAT('".base_url()."', '".MEMBER_IMAGE_PATH."', u.user_image) ) AS restaurant_image,
                      DATE_FORMAT(r.booking_from_time, '%e %b %Y') as booking_date,
                      DATE_FORMAT(r.booking_from_time, '%k:%i') as booking_time,
                      r.booking_number as booking_code,
                      r.total_amount,
                      g.amount_to_pay as your_amount,
                      g.paid_amount,
                      g.loyalty_points_used,
                      g.loyalty_points_amount,
                      IF( r.payment_status = 0, '', IF( r.total_amount = g.amount_to_pay, 'Full Payment', 'Split Payment' ) ) as payment_type,
                      IF( r.payment_status = 0, '', IF( g.payment_type = 1, 'Card', 'Cash' ) ) as payment_mode,
                      r.booking_id", FALSE);
    $this->db->from("hzi_booking_request r");
    $this->db->join("hzi_usermst u","u.user_id = r.restaurant_id");
    $this->db->join("hzi_booking_guest g","g.booking_id = r.booking_id");
    $data = array(
              'g.guest_id' => $user_id,
              'r.status' => '3',
              'r.payment_status' => $payment_status,
              'r.client_removed' => 0
            );
    $this->db->where( $data );
    $this->db->where('r.total_amount != 0');
    $this->db->order_by("r.booking_from_time", "DESC");
    $this->db->limit( $limit, $offset );
    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? $query->result() : array();
  }

  /*
   * Method Name: assign_bill_amount
   * Purpose: To assign bill amount against a booking to a user
   * params:
   *      input: booking_id, amount
   *      output: customer_id
   */
  public function assign_bill_amount( $booking_id, $amount )
  {
    $data = array(
              'total_amount' => $amount
            );

    $this->db->where('booking_id', $booking_id );
    $this->db->update('hzi_booking_request', $data);

    $this->db->select("customer_id");
    $this->db->from("hzi_booking_request");
    $select_data = array(
              'booking_id' => $booking_id
            );
    $this->db->where( $select_data );
    $this->db->limit( 1 );
    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? $query->row()->customer_id : 0;
  }

  /*
   * Method Name: assign_payable_amount
   * Purpose: To assign payable amount per user for a booking
   * params:
   *      input: user_id, booking_id
   *      output: -
   */
  public function assign_payable_amount( $user_id, $booking_id )
  {
    $this->db->select("total_amount");
    $this->db->from("hzi_booking_request");
    $select_data = array(
              'booking_id' => $booking_id
            );
    $this->db->where( $select_data );
    $this->db->limit( 1 );
    $query = $this->db->get();

    $amount = ( $query->num_rows() > 0 ) ? $query->row()->total_amount : 0;

    $data = array(
              'amount_to_pay' => $amount
            );

    $this->db->where('booking_id', $booking_id );
    $this->db->where('guest_id', $user_id );
    $this->db->update('hzi_booking_guest', $data);
  }

  /*
   * Method Name: get_user_bill_details
   * Purpose: To get user bill details for a booking
   * params:
   *      input: user_id, booking_id
   *      output: array containing bill details
   */
  public function get_user_bill_details( $user_id, $booking_id )
  {
    $this->db->select("CONCAT( u.user_first_name,' ',u.user_last_name ) AS restaurant_name,
                      IF( u.user_image = '', '', CONCAT('".base_url()."', '".MEMBER_IMAGE_PATH."', u.user_image) ) AS restaurant_image,
                      DATE_FORMAT(r.booking_from_time, '%D, %b %Y') AS booking_date,
                      DATE_FORMAT(r.booking_from_time, '%k:%i') as booking_time,
                      r.booking_number AS booking_code,
                      r.total_amount,
                      g.amount_to_pay AS your_amount,
                      If( l.loyalty_points, l.loyalty_points, 0 ) AS available_loyalty_points
                      ", FALSE);
    $this->db->from("hzi_booking_request r");
    $this->db->join("hzi_usermst u","u.user_id = r.restaurant_id");
    $this->db->join("hzi_booking_guest g","g.booking_id = r.booking_id");
    $this->db->join("hzi_loyalty l","l.user_id = g.guest_id", "left");
    $data = array(
              'r.booking_id' => $booking_id,
              'g.guest_id' => $user_id
            );
    $this->db->where( $data );
    $this->db->limit(1);
    $query1 = $this->db->get();

    $result1_details = ( $query1->num_rows() > 0 ) ? $query1->row_array() : array();

    $result = array();
    $result['restaurant_name'] = $result1_details['restaurant_name'];
    $result['restaurant_image'] = $result1_details['restaurant_image'];
    $result['booking_date'] = $result1_details['booking_date'];
    $result['booking_time'] = $result1_details['booking_time'];
    $result['booking_code'] = $result1_details['booking_code'];
    $result['total_amount'] = $result1_details['total_amount'];
    $result['your_amount'] = $result1_details['your_amount'];
    $result['available_loyalty_points'] = $result1_details['available_loyalty_points'];
    $result['conversion_rate'] = FIXED_LOYALTY_POINT;
    $result['conversion_value'] = 1;

    return $result;
  }
}
?>