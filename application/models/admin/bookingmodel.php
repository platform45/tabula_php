<?php

/*
  Model that contains function related to restaurant booking
 */

class BookingModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
     * Method Name: action
     * Purpose: Perform insert/update
     * params:
     *      input: restaurant_id
     *      output: array of restaurant tables
     */

    public function action($table, $action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert($table, $arrData);
                return $this->db->insert_id();
                break;
            case 'delete':
                $this->db->where($arrData);
                $this->db->delete($table);
                break;
        }
    }

    /*
     * Method Name: get_booking_id
     * Purpose: To get booking id from booking code
     * params:
     *      input: booking_code
     *      output: booking_id
     */

    public function get_booking_id($booking_code) {
        $this->db->select("booking_id");
        $this->db->from("booking_request");
        $data = array('booking_number' => $booking_code);
        $this->db->where($data);
        $query = $this->db->get();

        return ( $query->num_rows() > 0 ) ? $query->row()->booking_id : '';
    }

    /*
     * Method Name: get_table_details
     * Purpose: To get tables details for a restaurant from database
     * params:
     *      input: restaurant_id, date
     *      output: array of restaurant tables
     */

    public function get_table_details($restaurant_id, $date) {
        $date = date('Y-m-d H:i:s', strtotime($date));

        $sql = "SELECT t.*, s.*, If( r.admin_booking_request_id, 1, 0) as is_booked
            FROM (`tab_restaurant_tables` t)
            JOIN tab_timeslot s
            LEFT JOIN `tab_admin_booking_request` r ON `r`.`restaurant_id` = `t`.`user_id` AND `r`.`request_date` = '" . $date . "' AND r.time_slot_id = s.slot_id AND r.table_id = t.table_id
            WHERE `t`.`user_id` = '" . $restaurant_id . "'
            AND `t`.`is_deleted` = '0'
            ORDER BY t.table_id, s.slot_id";
        $query = $this->db->query($sql);

        return ( $query->num_rows() > 0 ) ? $query->result() : '';
    }

    public function get_table_details_user($user_id, $date) {
        $date = date('Y-m-d H:i:s', strtotime($date));

        $sql = "SELECT t.*, s.*, If( r.admin_booking_request_id, 1, 0) as is_booked
            FROM (`tab_restaurant_tables` t)
            JOIN tab_timeslot s
            LEFT JOIN `tab_admin_booking_request` r ON `r`.`restaurant_id` = `t`.`user_id` AND `r`.`request_date`= '" . $date . "' AND r.time_slot_id = s.slot_id AND r.table_id = t.table_id
            WHERE `t`.`user_id` = '" . $user_id . "'
            AND `t`.`is_deleted` = '0'
            ORDER BY t.table_id, s.slot_id";
        $query = $this->db->query($sql);
        return ( $query->num_rows() > 0 ) ? $query->result() : '';
    }

}

?>