<?php

/*
  Model that contains functions related to searching results
 */

class SearchModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
     * Method Name: get_total_search_records
     * Purpose: To get total records matching the keyword or the location
     * params:
     *      input: keyword, distance, latitude, longitude, user_type
     *      output: count of records
     */

    public function get_total_search_records($keyword = "", $distance = 0, $latitude=0, $longitude=0, $user_type) {
       
            $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image,
                        u.average_spend,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                        COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                        user_contact AS contact_number,
                        user_description AS description,
                        ROUND((((acos(sin((" . $latitude . "*pi()/180)) * sin((u.latitude*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((u.latitude*pi()/180)) * cos(((" . $longitude . "- u.longitude)*pi()/180)))))*180/pi())*60*1.1515*1.609344) AS distance", FALSE);
        $this->db->from("tab_usermst u");
        $this->db->join("tab_rating r", "r.restaurant_id = u.user_id AND r.status = '1' AND r.is_approved = '1'", "left");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        if (trim($keyword) != "") {
            $this->db->like('u.user_first_name', "$keyword");
        }
        if ($distance > 0) {
            $this->db->having("distance <=", $distance);
            $this->db->having("distance >", 0);
        }

        $data = array(
            'u.user_type' => $user_type,
            'u.user_status' => '1',
            'u.is_deleted' => '0'
        );
        $this->db->where($data);  
        $this->db->having("restaurant_id >",0);
        $this->db->group_by("u.user_id");
        $this->db->order_by("u.user_first_name");       
        $query = $this->db->get();
        return ( $query->num_rows() > 0 ) ? $query->num_rows() : array();
    }

     /*
     * Method Name: get_operating_time
     * Purpose: To get total records matching the keyword or the location
     * params:
     *      input: keyword, distance, latitude, longitude, user_type
     *      output: count of records
     */

    public function get_operating_time($user_id) {
       
       $this->db->select("*");
       $this->db->where("user_id",$user_id);
       $this->db->order_by("open_close_day","asc");
       $query = $this->db->get("restaurant_open_close_time");
       return ( $query->num_rows() > 0 ) ? $query->result() : 0;
    }
    /*
     * Method Name: get_search_records
     * Purpose: To search results matching the keyword
     * params:
     *      input: keyword, user_type, latitude, longitude, limit, offset
     *      output: array
     */

    public function get_search_records($keyword = "", $distance = 0, $latitude, $longitude, $user_type, $limit, $offset) {
        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image,
                        u.average_spend,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                        COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                        user_contact AS contact_number,
                        user_description AS description,
                        ROUND((((acos(sin((" . $latitude . "*pi()/180)) * sin((u.latitude*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((u.latitude*pi()/180)) * cos(((" . $longitude . "- u.longitude)*pi()/180)))))*180/pi())*60*1.1515*1.609344) AS distance", FALSE);
        $this->db->from("tab_usermst u");
        $this->db->join("tab_rating r", "r.restaurant_id = u.user_id AND r.status = '1' AND r.is_approved = '1'", "left");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        if (trim($keyword) != "") {
            $this->db->like('u.user_first_name', "$keyword");
        }
        if ($distance > 0) {
            $this->db->having("distance <=", $distance);
            $this->db->having("distance >", 0);
        }

        $data = array(
            'u.user_type' => $user_type,
            'u.user_status' => '1',
            'u.is_deleted' => '0'
        );
        $this->db->where($data);  
        $this->db->having("restaurant_id >",0);
        $this->db->group_by("u.user_id");
        $this->db->order_by("u.user_first_name");
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        
        return ( $query->num_rows() > 0 ) ? $query->result_array() : array();
    }

    /*
     * Method Name: get_total_detail_search_records
     * Purpose: To get total records matching the detail search fields
     * params:
     *      input: country, state, city, cuisine, ambience, min_price, max_price, number_of_guest, date_time, user_type
     *      output: count of total records
     */

    public function get_total_detail_search_records($state, $city, $cuisine, $ambience, $min_price, $max_price, $number_of_guest, $date_time, $latitude, $longitude, $user_type,$dietary_preference) {
         $condition_query = $this->_get_query($cuisine, $ambience, $min_price, $max_price, $number_of_guest, $date_time,$dietary_preference);

        $query1 = "SELECT distinct u.user_id AS restaurant_id,
                u.user_first_name AS restaurant_name,
                IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image,
                u.average_spend,
                CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                user_contact AS contact_number,
                user_description AS description,
                ROUND((((acos(sin((" . $latitude . "*pi()/180)) * sin((u.latitude*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((u.latitude*pi()/180)) * cos(((" . $longitude . "- u.longitude)*pi()/180)))))*180/pi())*60*1.1515*1.609344) AS distance
                FROM `tab_usermst` u
                LEFT JOIN tab_rating rt ON rt.restaurant_id = u.user_id AND rt.status = '1' AND rt.is_approved = '1'
                JOIN tab_city c ON c.city_id = u.city_id
                JOIN tab_region reg ON reg.region_id = u.region_id
                JOIN tab_country cou ON cou.cou_id = u.country_id
                " . $condition_query['cuisine_join'] . "
                " . $condition_query['ambience_join'] . "
                  
                AND `u`.`region_id` =  '" . $state . "'
                AND `u`.`city_id` =  '" . $city . "'
                " . $condition_query['cuisine_cond'] . "
                " . $condition_query['ambience_cond'] . "                
                " . $condition_query['dietary_cond'] . "   
                AND `u`.`user_type` =  " . $user_type . "
                AND `u`.`user_status` =  '1'
                AND `u`.`is_deleted` =  '0'
                 " . $condition_query['price_cond'] . "
                GROUP BY u.user_id
                ";

        if ($condition_query['query2'] != '') {
            $sql = "SELECT q1.*, SUM(q2.capacity) as capacity
            FROM
            (
              " . $query1 . "
            ) as q1
            JOIN
            (
              " . $condition_query['query2'] . "
            ) as q2 on q1.`restaurant_id` = q2.`user_id`
            GROUP BY q1.restaurant_id
            " . $condition_query['guest_cond'] . "
            ORDER BY q1.restaurant_name
            LIMIT $offset, $limit";
        } else {
            $sql = "SELECT q1.*
            FROM
            (
              " . $query1 . "
            ) as q1
            GROUP BY q1.restaurant_id
            ORDER BY q1.restaurant_name";
        }

        $query = $this->db->query($sql);
        return ( $query->num_rows() > 0 ) ? $query->num_rows() : array();
    }

    /*
     * Method Name: get_detail_search_records
     * Purpose: To search results matching the detail searvh criteria
     * params:
     *      input: country, state, city, cuisine, ambience, min_price, max_price, number_of_guest, date_time, latitude, longitude, user_type, limit, offset
     *      output: array
     */

    public function get_detail_search_records($state, $city, $cuisine, $ambience, $min_price, $max_price, $number_of_guest, $date_time, $latitude, $longitude, $user_type,$dietary_preference, $limit, $offset) {
        $condition_query = $this->_get_query($cuisine, $ambience, $min_price, $max_price, $number_of_guest, $date_time,$dietary_preference);

        $query1 = "SELECT distinct u.user_id AS restaurant_id,
                u.user_first_name AS restaurant_name,
                IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS restaurant_image,
                u.average_spend,
                CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                user_contact AS contact_number,
                user_description AS description,
                ROUND((((acos(sin((" . $latitude . "*pi()/180)) * sin((u.latitude*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((u.latitude*pi()/180)) * cos(((" . $longitude . "- u.longitude)*pi()/180)))))*180/pi())*60*1.1515*1.609344) AS distance
                FROM `tab_usermst` u
                LEFT JOIN tab_rating rt ON rt.restaurant_id = u.user_id AND rt.status = '1' AND rt.is_approved = '1'
                JOIN tab_city c ON c.city_id = u.city_id
                JOIN tab_region reg ON reg.region_id = u.region_id
                JOIN tab_country cou ON cou.cou_id = u.country_id
                " . $condition_query['cuisine_join'] . "
                " . $condition_query['ambience_join'] . "
                  
                AND `u`.`region_id` =  '" . $state . "'
                AND `u`.`city_id` =  '" . $city . "'
                " . $condition_query['cuisine_cond'] . "
                " . $condition_query['ambience_cond'] . "                
                " . $condition_query['dietary_cond'] . "   
                AND `u`.`user_type` =  " . $user_type . "
                AND `u`.`user_status` =  '1'
                AND `u`.`is_deleted` =  '0'
                 " . $condition_query['price_cond'] . "
                GROUP BY u.user_id
                ";

        if ($condition_query['query2'] != '') {
            $sql = "SELECT q1.*, SUM(q2.capacity) as capacity
            FROM
            (
              " . $query1 . "
            ) as q1
            JOIN
            (
              " . $condition_query['query2'] . "
            ) as q2 on q1.`restaurant_id` = q2.`user_id`
            GROUP BY q1.restaurant_id
            " . $condition_query['guest_cond'] . "
            ORDER BY q1.restaurant_name
            LIMIT $offset, $limit";
        } else {
            $sql = "SELECT q1.*
            FROM
            (
              " . $query1 . "
            ) as q1
            GROUP BY q1.restaurant_id
            ORDER BY q1.restaurant_name
            LIMIT $offset, $limit";
        }

        $query = $this->db->query($sql);
        return ( $query->num_rows() > 0 ) ? $query->result_array() : array();
    }

    /*
     * Method Name: _get_query
     * Purpose: Common function that generates joins and conditions for various cases
     * params:
     *      input: cuisine, ambience, min_price, max_price, number_of_guest, date_time
     *      output: array of conditions
     */

    private function _get_query($cuisine, $ambience, $min_price, $max_price, $number_of_guest, $date_time,$dietary_preference) {
        $cuisine_join = $cuisine_cond = $ambience_join = $ambience_cond = $price_cond = $guest_cond = $query2 = $dietary_join = $dietary_cond='';

        //if ($cuisine != '') {
            $cuisine_join = "LEFT JOIN `tab_restaurant_cuisine_ambience` rc ON `rc`.`user_id` = `u`.`user_id`";
            $cuisine_arr = explode(",", $cuisine);
            $cuisine_comma_selected = "'" . implode("', '", $cuisine_arr) . "'";

            $cuisine_cond = "AND ( rc.rca_type = '1' AND rc.rca_cuisine_ambience_id IN (" . $cuisine_comma_selected . ") )";
       // }
        
        // if ($dietary_preference != '') {
            $dietary_join = "LEFT JOIN `tab_dietary_restaurant` rc ON `rc`.`user_id` = `u`.`user_id`";
            $dietary_arr = explode(",", $dietary_preference);
            $dietary_comma_selected = "'" . implode("', '", $dietary_arr) . "'";

            $dietary_cond = "AND ( rc.rca_type = '1' AND rc.rca_cuisine_ambience_id IN (" . $dietary_comma_selected . ") )";
        //}

        //if ($ambience != '') {
            $ambience_join = "LEFT JOIN `tab_restaurant_cuisine_ambience` ra ON `ra`.`user_id` = `u`.`user_id`";
            $ambience_arr = explode(",", $ambience);
            $ambience_comma_selected = "'" . implode("', '", $ambience_arr) . "'";

            $ambience_cond = "AND (ra.rca_type = '2' AND ra.rca_cuisine_ambience_id IN (" . $ambience_comma_selected . ") )";
        //}

        if ($min_price >= 0 && $max_price > 0 && $max_price <= SEARCH_MAX_PRICE) {
            if ($max_price == SEARCH_MAX_PRICE)
                $price_cond = "where average_spend >= " . $min_price;
            else
                $price_cond = "where average_spend BETWEEN " . $min_price . " AND " . $max_price;
        }

        if ($date_time != '') {
            $query2 = "SELECT
                  t.user_id,
                  t.table_capacity AS capacity
                FROM tab_restaurant_tables t
                LEFT JOIN tab_booking_request r
                  ON r.restaurant_id = t.user_id
                  AND r.booking_table_number = t.table_id
                WHERE t.is_deleted = '0'
                AND t.status = '1'
                AND (
                  NOT (
                    ( r.status = 3 )
                    AND ('" . $date_time . "' BETWEEN r.booking_from_time AND r.booking_to_time )
                  )
                OR (
                  r.status IS NULL
                  OR ( r.booking_from_time IS NULL AND r.booking_to_time IS NULL )
                  )
                )";
            $guest_cond = "having capacity >=" . $number_of_guest;
        }

        $result_condition = array('cuisine_join' => $cuisine_join, 'cuisine_cond' => $cuisine_cond, 'ambience_join' => $ambience_join, 'dietary_join' => $dietary_join, 'ambience_cond' => $ambience_cond, 'price_cond' => $price_cond, 'guest_cond' => $guest_cond,'dietary_cond'=>$dietary_cond ,'query2' => $query2);

        return $result_condition;
    }

}

?>