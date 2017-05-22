<?php

/*
  Model that contains function related to booking a table
 */

class BookingModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Method Name: get_matching_user_details
     * Purpose: To get list of users with matching results
     * params:
     *      input: name, email
     *      output: array containing user details
     *      output: array containing user details
     */
    public function get_matching_user_details($name, $email)
    {
        $this->db->select("CONCAT( user_first_name, ' ', user_last_name ) as name, user_email as email, user_contact as contact", FALSE);
        $this->db->from("usermst");

        $data = array(
            'user_type' => 2,
            'user_status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);

        if ($name != '')
            $this->db->like("CONCAT( user_first_name, ' ', user_last_name )", $name);
        if ($email != '')
            $this->db->like("user_email", $email);

        $query = $this->db->get();

        return $query->result();
    }

    public function get_table_details($restaurant_id, $date)
    {
        $date = date('Y-m-d H:i:s', strtotime($date));

        $sql = "SELECT t.*, s.*, If( r.admin_booking_request_id, 1, 0) as is_booked
          FROM (`tab_restaurant_tables` t)
          JOIN tab_timeslot s
          LEFT JOIN `tab_admin_booking_request` r ON `r`.`restaurant_id` = `t`.`user_id` AND `r`.`request_date` = '" . $date . "' AND r.time_slot_id = s.slot_id AND r.table_id = t.table_id
          WHERE `t`.`user_id` = '" . $restaurant_id . "'
          AND `t`.`is_deleted` = '0'
          ORDER BY t.table_id, s.slot_id";
        $query = $this->db->query($sql);

        return ($query->num_rows() > 0) ? $query->result() : '';
    }

    /*
     * Method Name: action
     * Purpose: To perform action insert/update
     * params:
     *      input: action, table_name, data array, edit_id
     *      output: id of record updated
     */
    public function action($action, $table_name, $data = array(), $edit_array = array())
    {
        switch ($action) {
            case 'insert':
                $this->db->insert($table_name, $data);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where($edit_array);
                $this->db->update($table_name, $data);
                break;
        }
    }

    /*
     * Author = Akshay Deshmukh
     * Method Name: action
     * Purpose: To insert data in booking_request as well as in admin_booking_request.
     * params:
     *      input: $data, $table_ids
     *      output: status with booking id.
     */
    public function insert_booking_request($data, $table_ids, $date, $time_slot_id, $insert_last_minute_data)
    {
        $result_array = [];
        $this->db->insert('booking_request', $data);
        $inserted_id = $this->db->insert_id();
        if ($inserted_id) {
            foreach ($table_ids as $table_id) {
                $insert_data = array(
                    'restaurant_id' => $data['restaurant_id'],
                    'request_date' => $date,
                    'time_slot_id' => $time_slot_id,
                    'booking_id' => $inserted_id,
                    'table_id' => $table_id,
                    'walkin_user' => '1',
                    'status' => '1'
                );
                $this->db->insert('admin_booking_request', $insert_data);
            }

            if (!empty($insert_last_minute_data)) {
                $insert_last_minute_data['booking_id'] = $inserted_id;
                $this->db->insert('tab_last_minute_cancellation', $insert_last_minute_data);
            }

            $result_array['status'] = 1;
            $result_array['booking_id'] = $inserted_id;
            return $result_array;
        } else {
            $result_array['status'] = 0;
            return $result_array;
        }
    }

    public function update_booking_request($update_data, $restaurant_id, $table_ids, $date, $time_slot_id, $booking_id, $update_last_minute_data)
    {
        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_request', $update_data);

        $this->db->where('booking_id', $booking_id);
        $this->db->delete('tab_admin_booking_request');

        foreach ($table_ids as $table_id) {
            $insert_data = array(
                'restaurant_id' => $restaurant_id,
                'request_date' => $date,
                'time_slot_id' => $time_slot_id,
                'booking_id' => $booking_id,
                'table_id' => $table_id,
                'walkin_user' => 0,
                'status' => 1
            );
            $this->db->insert('admin_booking_request', $insert_data);
        }

        if (!empty($update_last_minute_data)) {
            $this->db->where('booking_id', $booking_id);
            $this->db->update('tab_last_minute_cancellation', $update_last_minute_data);
        } else {
            $this->db->where('booking_id', $booking_id);
            $this->db->delete('tab_last_minute_cancellation');
        }

        return TRUE;
    }


    /*
     * Author = Akshay Deshmukh
     * Method Name: get_time_slot
     * Purpose: To get the time_slot depend upon the time_slot_id
     * params:
     *      input: $time_slot_id
     *      output: $time_slot.
     */
    public function get_time_slot($time_slot_id)
    {
        $this->db->select('time_slot');
        $this->db->from('timeslot');
        $data = array(
            'slot_id' => $time_slot_id
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : '';
    }

    /*
     * Author = Akshay Deshmukh
     * Method Name: get_time_slot_id
     * Purpose: To get slot id by time slot
     * params:
     *      input: $time
     *      output: $time_slot_id.
     */
    public function get_time_slot_id($time)
    {
        $this->db->select('slot_id');
        $this->db->from('timeslot');
        $data = array(
            'time_slot' => $time
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : '';
    }

    /*
     * Author = Akshay Deshmukh
     * Method Name: get_all_time_slot
     * Purpose: To get all time_slots
     * params:
     *      output: $time_slot.
     */
    public function get_all_time_slot()
    {
        $this->db->select('slot_id,time_slot');
        $this->db->from('timeslot');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : '';
    }

    /*
     * Changes made by = Akshay Deshmukh
     * Method Name: has_requested_table
     * Purpose: To check if user has already booked a table in the restaurant for same date and time slot
     * params:
     *      input: user_id, restaurant_id, from_time
     *      output: TRUE/FALSE
     */
    public function has_requested_table($restaurant_id, $date, $time_slot, $table_ids, $booking_id = 0)
    {
        //Flag to check on of the table ids are booked or not
        $flag = 0;
        $table_ids_length = count($table_ids);
        $result_array = [];
        $booked_table_count = 0;
        foreach ($table_ids as $table_id) {
            $this->db->select("admin_booking_request_id");
            $this->db->from("admin_booking_request");
            $data = array(
                'restaurant_id' => $restaurant_id,
                'request_date' => $date,
                'time_slot_id' => $time_slot,
                'table_id' => $table_id,
                'status' => '1',
            );
            $this->db->where($data);

            if ($booking_id != 0) {
                $this->db->where("booking_id !=", $booking_id);
            }

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $array['table_id'] = $table_id;
                $array['booked_status'] = 1;
                $flag = 1;
                $booked_table_count = $booked_table_count + 1;
                $result_array['booked_table'][] = $array;
            }
        }

        if ($flag == 1) {
            $message = TABLE_ALREADY_BOOKED;
            $result_array['message'] = $message;
            $result_array['status'] = 0;
            return $result_array;
        } else {
            $result_array['status'] = 1;
            return $result_array;
        }
    }

    /*
    * Method Name: get_total_booking_records_for_restaurant
    * Purpose: To get total booking records for the user for restuarant
    * params:
    *      input: user_id, restuarant_id
    *      output: count of records
    */
    public
    function get_total_booking_records_for_restaurant($booking_id)
    {
        $this->db->select("r.booking_id");
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        $cond = "( r.booking_id = " . $booking_id . " )";


        $data = array(
            'r.client_removed' => 0
        );
        $this->db->where($data);
        $this->db->where($cond);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->num_rows() : 0;
    }

    /*
     * Method Name: get_booking_records_for_restuarant
     * Purpose: To get booking records for a user for restaurant
     * params:
     *      input: user_id, restuarant_id, limit, offset
     *      output: array
     */
    public
    function get_booking_records_for_restuarant($booking_id, $limit, $offset)
    {
        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        u.user_email AS email,
                        u.user_contact AS contact_number,
                        DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date,
                        DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time,
                        r.number_of_guest,
                        CONCAT(host.user_first_name, ' ', host.user_last_name) AS host_name,
                        IF( r.status = 2, 1, 0 ) AS is_waiting_request,
                        r.booking_id,
                        r.booking_number as booking_code,
                        r.is_notify,
                        DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time ,
                        DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time", FALSE);
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        $cond = "( r.booking_id = " . $booking_id . " )";

        $data = array(
            'r.client_removed' => 0
        );
        $this->db->where($data);
        $this->db->where($cond);
        $this->db->order_by("r.booking_from_time");
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /*
     * Method Name: get_total_booking_records
     * Purpose: To get total booking records for the user
     * params:
     *      input: user_id, booking_type
     *      output: count of records
     */
    public
    function get_total_booking_records($user_id, $booking_type, $booking_id = 0)
    {
        $this->db->select("r.booking_id");
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");
        switch ($booking_type) {
            case REQUESTED_BOOKING:
                $cond = "( ( r.status = 1 OR r.status = 2 ) AND r.payment_status = 0 )";
                break;
            case CONFIRMED_BOOKING:
                $cond = "( r.status = 3 AND r.payment_status = 0 )";
                break;
            case CANCELLED_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 ) AND r.payment_status = 0 )";
                break;
            case ALL_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 OR r.status = 3 ) AND r.payment_status = 0 )";
                break;
        }

        $data = array(
            'r.customer_id' => $user_id,
            'r.client_removed' => 0
        );
        $this->db->where($data);

        if ($booking_id != 0) {
            $booking_id_condition = array(
                'r.booking_id' => $booking_id,
            );
            $this->db->where($booking_id_condition);
        }

        $this->db->where($cond);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->num_rows() : 0;
    }

    /*
     * Method Name: get_booking_records
     * Purpose: To get booking records for a user
     * params:
     *      input: user_id, booking_type, limit, offset
     *      output: array
     */
    public
    function get_booking_records($user_id, $booking_type, $limit, $offset, $booking_id = 0)
    {
        $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
        $date = $date->format('Y-m-d H:i:s');

        switch ($booking_type) {
            case REQUESTED_BOOKING:
                $cond = "( ( r.status = 1 OR r.status = 2 ) AND r.payment_status = 0 )";
                break;
            case CONFIRMED_BOOKING:
                $cond = "( r.status = 3 AND r.payment_status = 0 )";
                break;
            case CANCELLED_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 ) AND r.payment_status = 0 )";
                break;
            case ALL_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 OR r.status = 3 ) AND r.payment_status = 0 )";
                break;
        }

        if ($booking_id == 0) {
            $sql = "
             (SELECT u.user_id AS restaurant_id, u.user_first_name AS restaurant_name, u.restaurant_owner_name, u.user_email as email, 
              u.user_contact as contact_number,
              IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image, 
              CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address, u.user_email AS email, 
              u.user_contact AS contact_number, 
              DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date, 
              DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time, 
              r.number_of_guest, CONCAT(host.user_first_name, ' ', host.user_last_name) AS host_name, 
              IF( r.status = 2, 1, 0 ) AS is_waiting_request,
              CASE 
                 WHEN r.status = 5 THEN 2
                 WHEN r.status = 4 THEN 3
                 ELSE 0 
             END as cancel_by,
             CASE 
                 WHEN r.status = 5 THEN 1
                 WHEN r.status = 4 THEN 1
                 ELSE 0 
             END as is_cancel,
              r.booking_id, r.booking_number as booking_code, r.is_notify, 
              CASE 
                 WHEN r.booking_from_time >= '" . $date . "' 
                 THEN 1 
                 ELSE 0 
             END as is_new, 
             DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time, DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time
            FROM (`tab_booking_request` r)
            JOIN `tab_usermst` u ON `u`.`user_id` = `r`.`restaurant_id`
            JOIN `tab_usermst` host ON `host`.`user_id` = `r`.`customer_id`
            JOIN `tab_city` c ON `c`.`city_id` = `u`.`city_id`
            JOIN `tab_region` reg ON `reg`.`region_id` = `u`.`region_id`
            JOIN `tab_country` cou ON `cou`.`cou_id` = `u`.`country_id`
            WHERE `r`.`customer_id` =  $user_id
            AND `r`.`client_removed` =  0
            AND r.booking_from_time >= '" . $date . "'
            AND $cond
            ORDER BY r.booking_from_time ASC
            LIMIT 0,1000
            )
            UNION
            (SELECT u.user_id AS restaurant_id, u.user_first_name AS restaurant_name, u.restaurant_owner_name, u.user_email as email, 
             u.user_contact as contact_number,
             IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image, 
             CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address, 
             u.user_email AS email, u.user_contact AS contact_number, 
             DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date, 
             DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time, 
             r.number_of_guest, 
             CONCAT(host.user_first_name, ' ', host.user_last_name) AS host_name, 
             IF( r.status = 2, 1, 0 ) AS is_waiting_request,
             CASE 
                 WHEN r.status = 5 THEN 2
                 WHEN r.status = 4 THEN 3
                 ELSE 0 
             END as cancel_by,
              CASE 
                 WHEN r.status = 5 THEN 1
                 WHEN r.status = 4 THEN 1
                 ELSE 0 
             END as is_cancel,
             r.booking_id, r.booking_number as booking_code, r.is_notify, 
             CASE 
                 WHEN r.booking_from_time >= '" . $date . "' 
                 THEN 1 
                 ELSE 0 
             END as is_new,
             DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time, 
             DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time
            FROM (`tab_booking_request` r)
            JOIN `tab_usermst` u ON `u`.`user_id` = `r`.`restaurant_id`
            JOIN `tab_usermst` host ON `host`.`user_id` = `r`.`customer_id`
            JOIN `tab_city` c ON `c`.`city_id` = `u`.`city_id`
            JOIN `tab_region` reg ON `reg`.`region_id` = `u`.`region_id`
            JOIN `tab_country` cou ON `cou`.`cou_id` = `u`.`country_id`
            WHERE `r`.`customer_id` =  $user_id
            AND `r`.`client_removed` =  0
            AND r.booking_from_time < '" . $date . "' 
            AND $cond
            ORDER BY r.booking_from_time DESC
            LIMIT 0,1000
            )
            LIMIT $offset,$limit
            ";

            $query = $this->db->query($sql);
            $result = $query->result_array();
        } else {
            $result = $this->get_booking_using_booking_id($user_id, $booking_type, $booking_id, $date);
        }

        if (empty($result)) {
            return array();
        } else {
            $restaurants = $this->get_restaurant_booking_table_time_slot($result);
            return $restaurants;
        }
    }

    public function get_booking_using_booking_id($user_id, $booking_type, $booking_id, $date)
    {
        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        u.restaurant_owner_name, u.user_email as email, u.user_contact as contact_number, 
                        IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        u.user_email AS email,
                        u.user_contact AS contact_number,
                        DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date,
                        DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time,
                        r.number_of_guest,
                        CONCAT(host.user_first_name, ' ', host.user_last_name) AS host_name,
                        IF( r.status = 2, 1, 0 ) AS is_waiting_request,
                        r.booking_id,
                        r.booking_number as booking_code,
                        r.is_notify, 
                        CASE 
                             WHEN r.booking_from_time >= '" . $date . "' 
                                 THEN 1 
                             ELSE 0 
                        END  as is_new,
                        CASE 
                             WHEN r.status = 5 THEN 2
                             WHEN r.status = 4 THEN 3
                             ELSE 0 
                         END as cancel_by,
                          CASE 
                             WHEN r.status = 5 THEN 1
                             WHEN r.status = 4 THEN 1
                             ELSE 0 
                         END as is_cancel,
                        DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time ,
                        DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time ", FALSE);
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        switch ($booking_type) {
            case REQUESTED_BOOKING:
                $cond = "( ( r.status = 1 OR r.status = 2 ) AND r.payment_status = 0 )";
                break;
            case CONFIRMED_BOOKING:
                $cond = "( r.status = 3 AND r.payment_status = 0 )";
                break;
            case CANCELLED_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 ) AND r.payment_status = 0 )";
                break;
            case ALL_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 OR r.status = 3 ) AND r.payment_status = 0 )";
                break;
        }

        $data = array(
            'r.customer_id' => $user_id,
            'r.client_removed' => 0
        );

        $booking_id_condition = array(
            'r.booking_id' => $booking_id,
        );

        $this->db->where($booking_id_condition);
        $this->db->where($data);
        $this->db->where($cond);

        if ($booking_id == 0) {
            $this->db->where("r.booking_from_time >=", $date);
            $this->db->order_by("r.booking_from_time");
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * Method Name: get_restaurant_booking_table_time_slot
     * Purpose: To  get table list with booked status
     * params:
     *      input: $restaurants
     *      output: restaurant with table array
     */
    public function get_restaurant_booking_table_time_slot($restaurants)
    {
        foreach ($restaurants as $key => $value) {
            $booking_time_slot_id = $this->get_time_slot_id($value['booking_time']);
            $restaurants[$key]['slot_id'] = $booking_time_slot_id[0]->slot_id;

            $last_minute_from_time = $this->get_time_slot_id($value['last_minute_from_time']);
            $restaurants[$key]['last_minute_from_time_id'] = $last_minute_from_time[0]->slot_id;

            $last_minute_to_time = $this->get_time_slot_id($value['last_minute_to_time']);
            $restaurants[$key]['last_minute_to_time_id'] = $last_minute_to_time[0]->slot_id;


            $sql = "SELECT t.table_id, t.table_name, t.table_capacity,
                    CASE WHEN tabr.admin_booking_request_id IS NOT NULL 
                                           THEN 1
                                           ELSE 0
                    END AS is_booked
                    FROM tab_restaurant_tables t
                    LEFT JOIN tab_admin_booking_request tabr ON tabr.table_id = t.table_id AND tabr.restaurant_id = '" . $value['restaurant_id'] . "' AND tabr.booking_id = '" . $value['booking_id'] . "'
                    WHERE t.user_id = '" . $value['restaurant_id'] . "' ";

            $query = $this->db->query($sql);
            $restaurants[$key]['tables'] = $query->result_array();
        }
        return $restaurants;
    }


    /*
     * Method Name: get_total_booking_confirmed_history
     * Purpose: To get total booking records for the user
     * params:
     *      input: user_id, booking_type
     *      output: count of records
     */
    public
    function get_total_booking_confirmed_history($user_id)
    {
        $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
        $date = $date->format('Y-m-d H:i:s');

        $this->db->select("r.booking_id");
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");
        $cond = "( r.status = 3  AND r.booking_from_time <=  '$date')";
        $data = array(
            'r.customer_id' => $user_id,
            'r.client_removed' => 0
        );
        $this->db->where($data);
        $this->db->where($cond);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->num_rows() : 0;
    }

    /*
    * Method Name: get_booking_confirmed_history
    * Purpose: To get confirm booking records for a user
    * params:
    *      input: user_id, booking_type, limit, offset
    *      output: array
    */
    public
    function get_booking_confirmed_history($user_id, $limit, $offset)
    {
        $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
        $date = $date->format('Y-m-d H:i:s');

        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        u.user_email AS email,
                        u.user_contact AS contact_number,
                        IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                        DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date,
                        DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time,
                        r.number_of_guest,
                        CONCAT(host.user_first_name, ' ', host.user_last_name) AS host_name,
                        IF( r.status = 2, 1, 0 ) AS is_waiting_request,
                        r.booking_id,
                        r.booking_number as booking_code,
                        r.is_notify,
                        DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time ,
                        DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time", FALSE);
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_rating ra", "ra.restaurant_id = u.user_id AND ra.status = '1' AND ra.is_approved = '1'", "left");
        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        $cond = "( r.status = 3 AND r.booking_from_time <=  '$date') ";

        $data = array(
            'r.customer_id' => $user_id,
            'r.client_removed' => 0
        );
        $this->db->where($data);
        $this->db->where($cond);
        $this->db->group_by("r.booking_id");
        $this->db->order_by("r.booking_from_time","DESC");
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }


    /*
     * Method Name: get_invited_users
     * Purpose: To get users invited for a booking
     * params:
     *      input: booking_id
     *      output: array
     */
    public
    function get_invited_users($booking_id)
    {
        $this->db->select("IF( g.guest_id = 0, g.guest_name, CONCAT(u.user_first_name, ' ', u.user_last_name) ) AS guest_name", FALSE);
        $this->db->from("booking_guest g");
        $this->db->join("usermst u", "u.user_id = g.guest_id", "left");

        $data = array(
            'g.booking_id' => $booking_id,
            'g.is_inviter' => '0'
        );
        $this->db->where($data);
        $this->db->order_by("guest_name");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /*
     * Method Name: get_total_restaurant_booking_records
     * Purpose: To get total booking records for the restaurant
     * params:
     *      input: user_id, booking_type
     *      output: count of records
     */
    public
    function get_total_restaurant_booking_records($user_id, $booking_type)
    {
        $this->db->select("r.booking_id");
        $this->db->from("booking_request r");
        $this->db->join("usermst u", "u.user_id = r.customer_id");
        switch ($booking_type) {
            case REQUESTED_BOOKING:
                $cond = "( ( r.status = 1 OR r.status = 2 ) AND r.payment_status = 0 )";
                break;
            case CONFIRMED_BOOKING:
                $cond = "( r.status = 3 )";
                break;
            case CANCELLED_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 ) AND r.payment_status = 0 )";
                break;
        }

        $data = array(
            'r.restaurant_id' => $user_id,
            'r.restaurant_removed' => 0
        );
        $this->db->where($data);
        $this->db->where($cond);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->num_rows() : 0;
    }

    /*
     * Method Name: get_restaurant_booking_records
     * Purpose: To get booking records for a restaurant
     * params:
     *      input: user_id, booking_type, limit, offset
     *      output: array
     */
    public
    function get_restaurant_booking_records($user_id, $booking_type, $limit, $offset)
    {
        $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
        $date = $date->format('Y-m-d H:i:s');

        switch ($booking_type) {
            case REQUESTED_BOOKING:
                $cond = "( ( r.status = 1 OR r.status = 2 ) AND r.payment_status = 0 )";
                break;
            case CONFIRMED_BOOKING:
                $cond = "( r.status = 3 )";
                break;
            case CANCELLED_BOOKING:
                $cond = "( ( r.status = 4 OR r.status = 5 ) AND r.payment_status = 0 )";
                break;
        }

        $sql = "
            (
             SELECT u.user_first_name AS user_name, 
             IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS user_image, 
             u.user_email AS email, u.user_contact AS contact_number, 
             DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date, 
             DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time, 
             IF( r.status = 2, 1, 0 ) AS is_waiting_request, 
             CASE 
                 WHEN r.status = 5 THEN 2
                 WHEN r.status = 4 THEN 3
                 ELSE 0 
             END as cancel_by, 
             CASE 
                 WHEN r.status = 5 THEN 1
                 WHEN r.status = 4 THEN 1
                 ELSE 0 
             END as is_cancel, 
             r.payment_status AS is_paid, r.number_of_guest, r.booking_status_change_on, r.booking_id, r.is_notify, r.booking_number as booking_code, 
             DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time, 
             DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time
             FROM (`tab_booking_request` r)
             JOIN `tab_usermst` u ON `u`.`user_id` = `r`.`customer_id`
             WHERE `r`.`restaurant_id` =  $user_id
             AND r.booking_from_time >= '". $date. "'
             AND `r`.`restaurant_removed` =  0
             AND $cond
             ORDER BY `r`.`booking_from_time` ASC
             LIMIT 0,1000
             )
             UNION
             (
             SELECT u.user_first_name AS user_name, 
             IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "' , u.user_image) ) AS user_image, 
             u.user_email AS email, u.user_contact AS contact_number, 
             DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date, 
             DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time, 
             IF( r.status = 2, 1, 0 ) AS is_waiting_request, 
             CASE 
                 WHEN r.status = 5 THEN 2
                 WHEN r.status = 4 THEN 3
                 ELSE 0 
             END as cancel_by, 
             CASE 
                 WHEN r.status = 5 THEN 1
                 WHEN r.status = 4 THEN 1
                 ELSE 0 
             END as is_cancel, 
             r.payment_status AS is_paid, r.number_of_guest, r.booking_status_change_on, r.booking_id, r.is_notify, r.booking_number as booking_code, 
             DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time, 
             DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time
             FROM (`tab_booking_request` r)
             JOIN `tab_usermst` u ON `u`.`user_id` = `r`.`customer_id`
             WHERE `r`.`restaurant_id` =  $user_id
             AND r.booking_from_time <= '". $date. "'
             AND `r`.`restaurant_removed` =  0
             AND $cond
             ORDER BY `r`.`booking_from_time` DESC
             LIMIT 0,1000
             )
         LIMIT $offset,$limit
        ";

        $query = $this->db->query($sql);
        $result = $query->result_array();

        if (empty($result)) {
            return array();
        } else {
            return $result;
        }

    }

    /*
     * Method Name: is_valid_booking
     * Purpose: To check if booking is valid for a user
     * params:
     *      input: user_id, booking_id, user_type
     *      output: TRUE/FALSE
     */
    public
    function is_valid_booking($user_id, $booking_id, $user_type)
    {
        $this->db->select("r.booking_id");
        $this->db->from("booking_request r");

        $this->db->where('r.booking_id', $booking_id);
        if ($user_type == SEARCH_RESTAURANT_TYPE)
            $this->db->where('r.restaurant_id', $user_id);
        else if ($user_type == SEARCH_APP_USER_TYPE)
            $this->db->where('r.customer_id', $user_id);

        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? TRUE : FALSE;
    }

    /*
     * Method Name: is_valid_booking_status
     * Purpose: To check if booking status is valid
     * params:
     *      input: user_id, booking_id, user_type
     *      output: TRUE/FALSE
     */
    public
    function is_valid_booking_status($booking_id, $user_type)
    {
        $this->db->select("r.booking_id");
        $this->db->from("booking_request r");
        $this->db->where('r.booking_id', $booking_id);
        $where = '(r.status = "' . BOOKING_CANCELLED_TYPE . '" or r.status =  "' . BOOKING_REJECTED_TYPE . '")';
        $this->db->where($where);

        $this->db->limit(1);
        $query = $this->db->get();
//        print_r($query->num_rows());exit;
        return $query->num_rows();
    }

    /*
     * Method Name: change_booking_status
     * Purpose: To change booking status
     * params:
     *      input: booking_id, status ( 1 - Confirm, 2 - Waiting, 3 - Regret ), user_type, tables
     *      output: id of record updated
     */
    public
    function change_booking_status($booking_id, $status, $user_type)
    {
        switch ($status) {
            case '1':
                $status_val = 3;
                break;
            case '2':
                $status_val = 2;
                break;
            case '3':
                $status_val = ($user_type == SEARCH_RESTAURANT_TYPE) ? 4 : 5;
                break;
        }

        $data = array(
            'status' => $status_val,
            'booking_status_change_on' => date("Y-m-d H:i:s")
        );
        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_request', $data);

        if ($status == 3) {
            $data = array(
                'status' => '0',
            );
            $this->db->where('booking_id', $booking_id);
            $this->db->update('admin_booking_request', $data);
        }

        return $booking_id;
    }

    /*
     * Method Name: delete_booking
     * Purpose: To delete booking
     * params:
     *      input: booking_id, user_type
     *      output: id of record updated
     */
    public
    function delete_booking($booking_id, $user_type)
    {
        switch ($user_type) {
            case '2':
                $data = array('client_removed' => 1);
                break;
            case '3':
                $data = array('restaurant_removed' => 1);
                break;
        }

        $this->db->where('booking_id', $booking_id);
        $this->db->update('booking_request', $data);

        return $booking_id;
    }

    /*
     * Method Name: get_booking_customers
     * Purpose: Get booking customers
     * params:
     *      input: booking_id
     *      output: array
     */
    public
    function get_booking_customers($booking_id)
    {
        $this->db->select("g.guest_id,g.guest_email,g.is_inviter, d.dev_device_id, d.dev_type, d.is_live_entry");
        $this->db->from("booking_guest g");
        $this->db->join("user_devices d", "d.user_id = g.guest_id", "left");

        $data = array(
            'g.booking_id' => $booking_id
        );
        $this->db->where($data);
        $this->db->order_by("g.guest_id");
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
     * Method Name: get_tables_booked
     * Purpose: Get tables booked for a request
     * params:
     *      input: booking_id
     *      output: array
     */
    public
    function get_tables_booked($booking_id)
    {
        $this->db->select("r.booking_table_number");
        $this->db->from("booking_request r");
        $data = array(
            'r.booking_id' => $booking_id
        );
        $this->db->where($data);
        $query = $this->db->get();

        $result = array();
        if ($query->num_rows() > 0) {
            $tables_arr = explode(",", $query->row()->booking_table_number);

            $this->db->select("t.table_name");
            $this->db->from("restaurant_tables t");
            $this->db->where_in('table_id', $tables_arr);
            $table_query = $this->db->get();

            if ($table_query->num_rows() > 0) {
                foreach ($table_query->result() as $res) {
                    $result[] = $res->table_name;
                }
            }
        }
        return $result;
    }

    /*
     * Method Name: get_booked_restaurant
     * Purpose: To get restaurant id for a booking
     * params:
     *      input: booking_id
     *      output: restaurant_id
     */
    public
    function get_booked_restaurant($booking_id)
    {
        $this->db->select("r.restaurant_id");
        $this->db->from("booking_request r");

        $data = array(
            'r.booking_id' => $booking_id
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row()->restaurant_id : 0;
    }

    /*
     * Method Name: get_booking_details
     * Purpose: To get booking details using booking id
     * params:
     *      input: booking_id
     *      output: restaurant_id
     */
    public function get_booking_details($booking_id)
    {
        $this->db->select("*");
        $this->db->from("booking_request");
        $data = array(
            'booking_id' => $booking_id
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row() : array();
    }

    /*
     * Method Name: get_last_minuit_notify_users
     * Purpose: To get users id whome we want to notify
     * params:
     *      input: $booking_id,$date,$time
     *      output: user_ids
     */
    public function get_last_minuit_notify_users($booking_id, $date, $time)
    {
        $this->db->select("DISTINCT(user_id)");
        $this->db->from("last_minute_cancellation");
        $this->db->where("last_minute_from_time <=", $time);
        $this->db->where("last_minute_to_time >=", $time);
        $this->db->where("notify_date =", $date);
        $this->db->where("booking_id !=", $booking_id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
     * Method Name: get_next_booking_details
     * Purpose: To get all booking for particular user with is_new status
     * params:
     *      input: $booking_id,$date,$time
     *      output: user_ids
     */
    public function get_next_booking_details($user_id, $date, $first_day, $last_day)
    {
        $sql = "SELECT b.booking_id, b.booking_number,
                DATE_FORMAT(b.booking_from_time, '%d-%m-%Y') as booking_date, 
                DATE_FORMAT(b.booking_from_time, '%H:%i') as booking_time,
                b.number_of_guest,
                b.restaurant_id,
                u.user_first_name as restaurant_name,
                CASE 
                  WHEN b.booking_from_time >= '" . $date . "' 
                     THEN 1 
                  ELSE 0 
                END  as is_new
            FROM tab_booking_request b
            JOIN tab_usermst u ON b.restaurant_id = u.user_id
            WHERE b.customer_id = $user_id
            AND b.booking_from_time >= '" . $first_day . "'
            AND b.booking_from_time <= '" . $last_day . "'
            AND b.status = '" . BOOKING_CONFIRMED . "'
            ORDER BY b.booking_from_time
         ";
        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    public function get_last_minute_cancellation_notification_list($user_id, $date, $time, $limit, $offset)
    {
        $sql = "SELECT u.user_id AS restaurant_id,
                u.user_first_name AS restaurant_name,
                u.restaurant_owner_name,
                u.user_email as email, 
                u.user_contact as contact_number,
                IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                DATE_FORMAT(lmn.for_time_slot , '%H:%i') as time,
                t.slot_id,
                lmn.last_minute_notification_id as notification_id,
                DATE_FORMAT(lmn.for_date, '%d-%m-%Y') as booking_date
                FROM tab_usermst u
                JOIN tab_last_minute_cancellation_notification lmn ON lmn.restaurant_id = u.user_id
                JOIN tab_city c ON c.city_id = u.city_id AND c.status = '1' AND c.city_delete = '0'
                JOIN tab_region reg ON reg.region_id = u.region_id AND reg.status = '1' AND reg.region_delete = '0'
                JOIN tab_country cou ON cou.cou_id = u.country_id AND cou.status = '1' AND cou.cou_delete = '0'
                JOIN tab_timeslot t ON t.time_slot = DATE_FORMAT(lmn.for_time_slot , '%H:%i')
                WHERE  `lmn`.`user_id` =  " . $user_id . "
                AND  `lmn`.`for_date` >=  '" . $date . "'
                AND  `lmn`.`for_time_slot` >=  '" . $time . "'
                ORDER BY lmn.last_minute_notification_id  DESC
                limit $offset, $limit
         ";
        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    public function get_last_minute_cancellation_notification_list_count($user_id, $date, $time)
    {
        $sql = "SELECT lmn.user_id AS restaurant_id,
                u.user_first_name AS restaurant_name,
                u.restaurant_owner_name,
                u.user_email as email, 
                u.user_contact as contact_number,
                IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                DATE_FORMAT(lmn.for_time_slot , '%H:%i') as time
                FROM tab_usermst u
                JOIN tab_last_minute_cancellation_notification lmn ON lmn.restaurant_id = u.user_id
                JOIN tab_city c ON c.city_id = u.city_id AND c.status = '1' AND c.city_delete = '0'
                JOIN tab_region reg ON reg.region_id = u.region_id AND reg.status = '1' AND reg.region_delete = '0'
                JOIN tab_country cou ON cou.cou_id = u.country_id AND cou.status = '1' AND cou.cou_delete = '0'
                WHERE  `lmn`.`user_id` =  " . $user_id . "
                AND  `lmn`.`for_date` >=  '" . $date . "'
                AND  `lmn`.`for_time_slot` >=  '" . $time . "'
         ";
        $query = $this->db->query($sql);
        return $query->num_rows();
    }

    public function get_table_details_by_id($table_id)
    {
        $this->db->select('table_id, table_name, table_number, table_capacity');
        $this->db->from('restaurant_tables');
        $data = array(
            'table_id' => $table_id
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row() : 0;
    }


    /*
     * Author: Akshay deshmukh
     * Method Name: front_profile_get_total_booking_records
     * Purpose: To get total booking records for the user
     * params:
     *      input: user_id, booking_type
     *      output: count of records
     */
    public
    function front_profile_get_total_booking_records($user_id, $booking_type, $booking_id = 0)
    {
        $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
        $date = $date->format('Y-m-d H:i:s');

        $this->db->select("r.booking_id");
        $this->db->from("tab_booking_request r");
        $this->db->join("tab_usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_usermst host", "host.user_id = r.customer_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");
        switch ($booking_type) {
            case FUTURE_BOOKING:
                $cond = "( r.booking_from_time >= '" . $date . "' AND r.payment_status = 0 )";
                break;
            case HISTORY_BOOKING:
                $cond = "( r.booking_from_time < '" . $date . "' AND r.payment_status = 0 )";
                break;
        }

        $data = array(
            'r.customer_id' => $user_id,
            'r.client_removed' => 0
        );
        $this->db->where($data);

        if ($booking_id != 0) {
            $booking_id_condition = array(
                'r.booking_id' => $booking_id,
            );
            $this->db->where($booking_id_condition);
        }

        $this->db->where($cond);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->num_rows() : 0;
    }

    /*
     * Author: Akshay Deshmukh
     * Method Name: front_profile_get_booking_records
     * Purpose: To get booking records for a user
     * params:
     *      input: user_id, booking_type, limit, offset
     *      output: array
     */
    public
    function front_profile_get_booking_records($user_id, $booking_type, $limit, $offset, $booking_id = 0)
    {
        $noImagePath = base_url() . "assets/images/restaurent_no_image_available.png";
        $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
        $date = $date->format('Y-m-d H:i:s');

        switch ($booking_type) {
            case FUTURE_BOOKING:
                $cond = "( r.booking_from_time >= '" . $date . "' AND r.payment_status = 0 ) ORDER BY r.booking_from_time ASC";
                break;
            case HISTORY_BOOKING:
                $cond = "( r.booking_from_time < '" . $date . "' AND r.payment_status = 0 ) ORDER BY r.booking_from_time DESC";
                break;
        }

        if ($booking_id == 0) {
            $sql = "
             SELECT u.user_id AS restaurant_id, u.user_first_name AS restaurant_name, u.restaurant_owner_name, u.user_email as email, u.restaurant_detail_url,
              u.user_contact as contact_number,
              IF( u.restaurant_hero_image= '', '$noImagePath', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image, 
              CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address, u.user_email AS email, 
              u.user_contact AS contact_number, 
              DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date, 
              DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time, 
              r.number_of_guest, CONCAT(host.user_first_name, ' ', host.user_last_name) AS host_name, 
              CASE 
                 WHEN r.status = 5 THEN 2
                 WHEN r.status = 4 THEN 3
                 ELSE 0 
             END as cancel_by,
             CASE 
                 WHEN r.status = 5 THEN 1
                 WHEN r.status = 4 THEN 1
                 ELSE 0 
             END as is_cancel,
              r.booking_id, r.booking_number as booking_code, r.is_notify, 
              CASE 
                 WHEN r.booking_from_time >= '" . $date . "' 
                 THEN 1 
                 ELSE 0 
             END as is_new, 
            DATE_FORMAT(r.last_minute_from_time, '%H:%i') as last_minute_from_time, DATE_FORMAT(r.last_minute_to_time, '%H:%i') as last_minute_to_time
            FROM (`tab_booking_request` r)
            JOIN `tab_usermst` u ON `u`.`user_id` = `r`.`restaurant_id`
            JOIN `tab_usermst` host ON `host`.`user_id` = `r`.`customer_id`
            JOIN `tab_city` c ON `c`.`city_id` = `u`.`city_id`
            JOIN `tab_region` reg ON `reg`.`region_id` = `u`.`region_id`
            JOIN `tab_country` cou ON `cou`.`cou_id` = `u`.`country_id`
            WHERE `r`.`customer_id` =  $user_id
            AND `r`.`client_removed` =  0
            AND $cond
            LIMIT $offset,$limit
            ";

            $query = $this->db->query($sql);
            $result = $query->result_array();
        } else {
            $result = $this->get_booking_using_booking_id($user_id, $booking_type, $booking_id, $date);
        }

        if (empty($result)) {
            return array();
        } else {
            $restaurants = $this->get_restaurant_booking_table_time_slot($result);
            return $restaurants;
        }
    }
}

?>
