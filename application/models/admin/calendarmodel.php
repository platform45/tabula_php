<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose: Model for controlling database interactions regarding the content.
 * Date: 02 Sept 2016
 * Dependency: None
 */

class Calendarmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_monthly_booking_daywise_count($restaurant_id, $firstDay, $lastDay)
    {

        $sql = "
        SELECT count(booking_id) as count, DATE_FORMAT(booking_from_time, '%d-%m-%Y') as booking_date
        FROM tab_booking_request
        WHERE restaurant_id = $restaurant_id
        AND booking_from_time >= '". $firstDay . "' AND booking_from_time < '". $lastDay . "'
        AND status = '3'
        GROUP BY booking_date
        ";

        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }


}