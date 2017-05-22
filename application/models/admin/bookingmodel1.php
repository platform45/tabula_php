<?php

/*
  Model that contains function related to restaurant booking
 */

class BookingModel1 extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
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
        $day = date('l', strtotime($date));
        $dayId = $this->restaurantmodel->get_day_id($day);

        $sql = "SELECT DATE_FORMAT(open_time_from,'%H:%i') open_time_from, DATE_FORMAT(close_time_to,'%H:%i') close_time_to
        FROM tab_restaurant_open_close_time 
        WHERE `user_id` = '$restaurant_id'
        AND `open_close_day` = '$dayId'
        AND `open_close_status` = '1'
        ";

        $query = $query = $this->db->query($sql);
        if($query->num_rows() > 0){
            $open_close_time = $query->result_array();
            $open_time = $open_close_time[0]['open_time_from'];
            $close_time = $open_close_time[0]['close_time_to'];

            $sql = "SELECT t.*,s.*, If( r.admin_booking_request_id, 1, 0) as is_booked, If( r.booking_id, r.booking_id, 0) as booking_id
                    FROM tab_restaurant_tables t 
                    JOIN tab_timeslot s
                    LEFT JOIN `tab_admin_booking_request` r ON `r`.`restaurant_id` = `t`.`user_id` AND `r`.`request_date` = '" . $date . "' AND r.time_slot_id = s.slot_id AND r.table_id = t.table_id
                    WHERE `t`.`user_id` = '" . $restaurant_id . "'
                    AND `t`.`is_deleted` = '0'
                    AND s.time_slot <= '$close_time' AND s.time_slot >= '$open_time'
                    ORDER BY t.table_id, s.slot_id
            ";

            $query = $this->db->query($sql);
            return ( $query->num_rows() > 0 ) ? $query->result() : '';
        }
        else{
            return '';
        }
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

    public function get_booking_number($booking_id)
    {
        $this->db->select("booking_number");
        $this->db->from("tab_booking_request");
        $this->db->where("booking_id", $booking_id);
        $query = $this->db->get();
        return ( $query->num_rows() > 0 ) ? $query->row()->booking_number : '';
    }

    public function get_booking_list($restaurant_id,  $first_date, $next_date, $searchUserString = '')
    {
//        $this->db->select("u.user_id AS user_id,
//                        u.user_first_name AS user_name,
//                        u.user_email as email, u.user_contact as contact_number,
//                        DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date,
//                        DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time,
//                        r.number_of_guest,
//                        r.booking_id,
//                        r.booking_number as booking_code,
//                        r.is_notify,
//                        r.booking_by,
//                        DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time ,
//                        DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time ", FALSE);
//        $this->db->from("tab_booking_request r");
//        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
//        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
//        $this->db->join("tab_city c", "c.city_id = u.city_id");
//        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
//        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        $this->db->select("u.user_id AS user_id,
                        u.user_first_name AS user_name,
                        u.user_email as email, u.user_contact as contact_number, 
                        DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date,
                        DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time,
                        r.number_of_guest,
                        r.booking_id,
                        r.booking_number as booking_code,
                        r.is_notify,
                        r.booking_by,
                        DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time ,
                        DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time ", FALSE);
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.customer_id");

        $data = array(
            'r.restaurant_id' => $restaurant_id,
            'r.status' => 3,
        );
        $where = "r.booking_from_time >= '". $first_date . "' AND r.booking_from_time < '". $next_date . "'";

        $this->db->where($data);
        $this->db->where($where);
        if(!empty($searchUserString))
        {
            $this->db->where("u.user_first_name LIKE '%".$searchUserString."%'");
        }

        $this->db->order_by('r.booking_from_time', 'DESC');

        $query = $this->db->get();
        return ( $query->num_rows() > 0 ) ? $query->result_array() : array();
    }

    public function get_booked_table_details_using_booking_id($restaurant_id, $booking_id)
    {
        $sql = "
        SELECT table_name, table_number, table_capacity
        FROM tab_restaurant_tables
        WHERE table_id IN (
                          SELECT table_id 
                          FROM tab_admin_booking_request
                          WHERE booking_id = $booking_id AND restaurant_id = $restaurant_id
                           )
        ";
        $query = $this->db->query( $sql );
        return ( $query->num_rows() > 0 )  ? $query->result_array() : array();
    }

    public function getData($booking_id)
    {
        $sql = "
        SELECT  r.booking_id,  
        DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date,
        DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time,
        r.number_of_guest,
        adr.time_slot_id,
        GROUP_CONCAT(adr.table_id) as table_ids
        FROM tab_booking_request r 
        JOIN tab_admin_booking_request adr ON r.booking_id = adr.booking_id
        WHERE r.booking_id = $booking_id
        ";

        $query = $this->db->query( $sql );
        return ( $query->num_rows() > 0 )  ? $query->row() : array();
    }
}

?>