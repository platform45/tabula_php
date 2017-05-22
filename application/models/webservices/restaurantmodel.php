<?php

/*
  Model that contains function related to restaurant
 */

class RestaurantModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('webservices/bookingmodel', 'bookingmodel', TRUE);
    }

    /*
     * Method Name: get_menu
     * Purpose: To get menu images from database for a restaurant
     * params:
     *      input: restaurant_id
     *      output: array containing restaurant menu images
     */

    public function get_menu($restaurant_id)
    {
        $this->db->select("fm_id as menu_id, IF( fm_image = '', '', CONCAT('" . base_url() . "', '" . FOODMENU_IMAGE_PATH . "', fm_image) ) AS menu_image", FALSE);
        $this->db->from("food_menu");
        $data = array(
            'user_id' => $restaurant_id,
            'status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->order_by("menu_image_seq");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /*
     * Method Name: get_menu
     * Purpose: To get gallery images from database for a restaurant
     * params:
     *      input: restaurant_id
     *      output: array containing restaurant gallery images
     */

    public function get_gallery($restaurant_id)
    {
        $this->db->select("`gal_id`, `user_id` as restaurant_id, `gal_title`, ,IF( gal_image = '', '', CONCAT('" . base_url() . "', '" . GALLERY_IMAGE_PATH . "', gal_image) ) AS gallery_image", FALSE);
        $this->db->from("tab_restuarant_gallery");
        $data = array(
            'user_id' => $restaurant_id,
            'gal_status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /*
     * Method Name: add_gallery
     * Purpose: To add gallery images to database for a restaurant
     * params:
     *      input: gallery array
     *      output: insert_id
     */

    public function add_gallery($gallery_array)
    {
        $this->db->insert("tab_restuarant_gallery", $gallery_array);
        return $this->db->insert_id();
    }

    /*
     * Method Name: add_menu
     * Purpose: To add menu images to database for a restaurant
     * params:
     *      input: gallery array
     *      output: insert_id
     */

    public function add_foodmenu($menu_array)
    {
        $this->db->insert("tab_food_menu", $menu_array);
        return $this->db->insert_id();
    }


    /*
     * Method Name: submit_review
     * Purpose: To submit or update rating and review for a restaurant
     * params:
     *      input: $data_array - array
     *      output: insert_id - int
     */

    public function submit_review($data_array)
    {
        $this->db->select("rating_id");
        $this->db->from("rating");
        $data = array(
            'customer_id' => $data_array['customer_id'],
            'restaurant_id' => $data_array['restaurant_id'],
            'status' => '1',
            'is_approved' => '1'
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();

        if ($result) { //Edit existing review
            $this->db->where('rating_id', $result->rating_id);
            $this->db->update('rating', $data_array);

            $rating_id = $result->rating_id;
        } else { //Insert new review
            $this->db->insert('rating', $data_array);

            $rating_id = $this->db->insert_id();
        }

        return $rating_id;
    }


    /*
     * Method Name: submit_review
     * Purpose: To submit or update rating and review for a restaurant
     * params:
     *      input: $data_array - array
     *      output: insert_id - int
     */


    /*
     * Method Name: get_rating_per_criteria
     * Purpose: To get average rating for restaurant for individual services
     * params:
     *      input: restaurant_id
     *      output: array containing average rating for each criteria
     */

    public function get_rating_per_criteria($restaurant_id)
    {
        $this->db->select("AVG(service_rating) as service_rating, AVG(ambience_rating) as ambience_rating, AVG(food_quality_rating) as food_rating, AVG(value_for_money_rating) as money_rating");
        $this->db->from("rating");
        $data = array(
            'restaurant_id' => $restaurant_id,
            'status' => '1',
            'is_approved' => '1'
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row() : array();
    }

    /*
     * Method Name: get_rating_to_restaurant_by_user
     * Purpose: To get average rating for restaurant for individual services by user
     * params:
     *      input: restaurant_id, user_id
     *      output: array containing average rating for each criteria
     */

    public function get_rating_to_restaurant_by_user($restaurant_id, $user_id)
    {
        $this->db->select("AVG(service_rating) as service_rating, AVG(ambience_rating) as ambience_rating, AVG(food_quality_rating) as food_rating, AVG(value_for_money_rating) as money_rating, your_thoughts");
        $this->db->from("rating");
        $data = array(
            'restaurant_id' => $restaurant_id,
            'customer_id' => $user_id,
            'status' => '1',
            'is_approved' => '1'
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row() : array();
    }

    /*
     * Method Name: get_reviews
     * Purpose: To get reviews for restiarant according to ratings.
     * params:
     *      input: restaurant_id
     *      output: array containing average rating for each criteria
     */

    public function get_reviews($restaurant_id, $type = 'top')
    {
        $this->db->select("IFNULL(ROUND((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4), 0) AS average_rating,your_thoughts,rating_id", FALSE);
        $this->db->from("rating rating");
        $data = array(
            'restaurant_id' => $restaurant_id,
            'status' => '1'
        );
        $this->db->where($data);
        $this->db->order_by('average_rating', 'desc');
        if ($type == 'top')
            $this->db->limit('20');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }


    /*
     * Method Name: get_rating_user_wise_total
     * Purpose: To get total of rating for restaurant per user
     * params:
     *      input: user_id
     *      output: array containing user details and rating for restaurant
     */

    public function get_rating_user_wise_total($user_id)
    {

        $this->db->select("CONCAT( u.user_first_name,' ',u.user_last_name ) as user_name, 
                IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_hero_image,
                    IFNULL(ROUND(avg(( r.service_rating + r.ambience_rating + r.food_quality_rating + r.value_for_money_rating )/4),1),0) AS rating, r.your_thoughts as review,
                    DATE_FORMAT(r.date_posted, '%D %b %Y') as posted_date", FALSE);
        $this->db->from("rating r");
        $data = array(
            'r.customer_id' => $user_id,
            'r.status' => '1',
            'r.is_approved' => '1',
            'user_status' => '1',
            'is_deleted' => '0'
        );


        $this->db->where($data);
        $this->db->join("usermst u", "u.user_id = r.restaurant_id");
        $this->db->group_by('r.restaurant_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->num_rows() : array();
    }

    /*
    * Method Name: get_rating_user_wise
    * Purpose: To get rating for restaurant per user
    * params:
    *      input: restaurant_id, criteria, limit, offset
    *      output: array containing user details and rating for restaurant
    */

    public function get_rating_user_wise($user_id, $limit, $offset)
    {

        $this->db->select("rating_id,CONCAT( u.user_first_name,' ',u.user_last_name ) as restaurant_name, 
                IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_hero_image, 
                    IFNULL(ROUND(avg(( r.service_rating + r.ambience_rating + r.food_quality_rating + r.value_for_money_rating )/4),1),0) AS rating,
                    r.your_thoughts as review,
                    DATE_FORMAT(r.date_posted, '%D %b %Y') as posted_date", FALSE);
        $this->db->from("rating r");
        $data = array(
            'r.customer_id' => $user_id,
            'r.status' => '1',
            'r.is_approved' => '1',
            'user_status' => '1',
            'is_deleted' => '0'
        );

        $this->db->where($data);
        $this->db->join("usermst u", "u.user_id = r.restaurant_id");
        $this->db->limit($limit, $offset);
        $this->db->group_by('r.restaurant_id');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }


    /*
     * Method Name: get_rating_details
     * Purpose: To get rating for restaurant per user
     * params:
     *      input: restaurant_id, criteria, limit, offset
     *      output: array containing user details and rating for restaurant
     */

    public function get_rating_details($rating_id)
    {

        $this->db->select("rating_id,service_rating,r.restaurant_id,r.customer_id,ambience_rating,food_quality_rating,value_for_money_rating,CONCAT( u.user_first_name,' ',u.user_last_name ) as restaurant_name, IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_hero_image,CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address, IFNULL(ROUND(avg(( r.service_rating + r.ambience_rating + r.food_quality_rating + r.value_for_money_rating )/4),1),0) AS rating, r.your_thoughts as review, DATE_FORMAT(r.date_posted, '%d-%m-%Y') as posted_date", FALSE);
        $this->db->from("rating r");
        $data = array(
            'r.rating_id' => $rating_id,
            'r.status' => '1',
            'r.is_approved' => '1',
            'user_status' => '1',
            'is_deleted' => '0'
        );

        $this->db->where($data);
        $this->db->join("usermst u", "u.user_id = r.restaurant_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /*
     * Method Name: get_rating_per_user
     * Purpose: To get rating for restaurant per user
     * params:
     *      input: restaurant_id, criteria, limit, offset
     *      output: array containing user details and rating for restaurant
     */

    public function get_rating_per_user($restaurant_id, $criteria = '', $limit, $offset)
    {
        if ($criteria == '') {
            $this->db->select("rating_id,CONCAT( u.user_first_name,' ',u.user_last_name ) as user_name, IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS user_image, IFNULL(ROUND(avg(( r.service_rating + r.ambience_rating + r.food_quality_rating + r.value_for_money_rating )/4),1),0) AS rating, r.your_thoughts as review, DATE_FORMAT(r.date_posted, '%d-%m-%Y') as posted_date", FALSE);
            $this->db->from("rating r");
            $data = array(
                'r.restaurant_id' => $restaurant_id,
                'r.status' => '1',
                'r.is_approved' => '1',
                'user_status' => '1',
                'is_deleted' => '0'
            );
        } else {
            $this->db->select("CONCAT( u.user_first_name,' ',u.user_last_name ) as user_name, IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS user_image, " . $criteria . " AS rating, r.your_thoughts as review, DATE_FORMAT(r.date_posted, '%d-%m-%Y') as posted_date", FALSE);
            $this->db->from("rating r");
            $this->db->having("rating > 0");
            $data = array(
                'r.restaurant_id' => $restaurant_id,
                'r.status' => '1',
                'r.is_approved' => '1',
                'user_status' => '1',
                'is_deleted' => '0'
            );
        }

        $this->db->where($data);
        $this->db->join("usermst u", "u.user_id = r.customer_id");
        $this->db->group_by('r.customer_id');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /*
     * Method Name: get_total_ratings
     * Purpose: To get total users who rated the restaurant
     * params:
     *      input: restaurant_id, criteria
     *      output: count of ratings
     */

    public function get_total_ratings($restaurant_id, $criteria = '')
    {
        $this->db->select("r.rating_id");
        $this->db->from("rating r");
        $data = array(
            'r.restaurant_id' => $restaurant_id,
            'r.status' => '1',
            'r.is_approved' => '1',
            'user_status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);

        if ($criteria != '')
            $this->db->where($criteria . " > 0");

        $this->db->join("usermst u", "u.user_id = r.customer_id");
        $query = $this->db->get();

        return $query->num_rows();
    }

    /*
     * Method Name: get_tables
     * Purpose: To get all tables of a restaurant from database at the time of booking on selected date. If not available send next four time slots.
     * params:
     *      input: $restaurant_id, $date, $time_slot_id, $start_time_slot, $date_time
     *      output: return available tables on the requested date and time slot, if not then return next four available time slot from the requested start time slot.
     */

    public function get_tables($restaurant_id, $date, $time_slot_id, $start_time_slot, $date_time)
    {
        $sql = "SELECT table_id, table_name, table_capacity FROM tab_restaurant_tables
        WHERE table_id
        NOT IN (SELECT table_id FROM tab_admin_booking_request
        WHERE restaurant_id = $restaurant_id AND request_date = '$date' AND time_slot_id = '$time_slot_id' AND status = '1')
        AND user_id = $restaurant_id AND is_deleted = '0' AND status = '1'
        ";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
			//print_r($query->result_array());exit();
            $result_array['status'] = SUCCESS;
            $result_array['message'] = ALL_TABLES;
            $result_array['is_table_list'] = SUCCESS;
            $result_array['response']['table_list'] = $query->result_array();
            return $result_array; // 200 being the HTTP response code
        } else {
            $result_array = $this->get_next_four_available_time_slots($restaurant_id, $date, $start_time_slot, $date_time);
            $result_array['is_table_list'] = FAIL;
            $result_array['response']['time_slot'] = $result_array['time_slot'];
            unset($result_array['time_slot']);
            return $result_array;
        }
    }

    /*
    * Method Name: get_next_four_available_time_slots
    * Purpose: To get next 4 available time slots
    * params:
    *      input: $day
    *      output: $dayId
    */

    public function get_next_four_available_time_slots($restaurant_id, $date, $start_time_slot, $date_time)
    {
        $query = $this->get_open_close_time_of_restaurant($date, $restaurant_id);
        $rounded_time_slot = '';
        if ($query->num_rows() > 0) {
            $open_close_time = $query->result_array();
            $close_time = $open_close_time[0]['close_time_to'];
            $close_time = date('H:i', strtotime("$close_time"));

            $current_date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
            $current_date = $current_date->format('Y-m-d 00:00:00');
            if ($current_date == $date) {
                $rounded_time_slot = $this->get_rounded_time();
            }

            $this->db->select('time_slot');
            $this->db->from('timeslot');
            $this->db->where('time_slot <=', $close_time);
            $this->db->where('time_slot >', $start_time_slot);

            if ($rounded_time_slot) {
                $this->db->where('slot_id >=', $rounded_time_slot['time_slot_id']);
            }

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $available_time_slot_for_today = $query->result_array();
                $sql = "SELECT DISTINCT DATE_FORMAT(b.booking_from_time,'%H:%i') time_slot
                        FROM tab_booking_request b
                        WHERE restaurant_id = $restaurant_id AND b.booking_from_time >= '$date_time' AND b.status = '3' ";
                $query = $this->db->query($sql);
				
                if ($query->num_rows() > 0) {

                    $booking_available_time_slot = $query->result_array();
					
					$sql = "SELECT count(table_id) as total_table
					FROM tab_restaurant_tables WHERE user_id = $restaurant_id
					";
					$query = $this->db->query($sql);
					$result = $query->result_array();
					$table_count = $result[0]['total_table'];
					
					$book_time_slots = [];
                                        $admin_table_booking_date = date('Y-m-d 00:00:00', strtotime("$date_time"));

					foreach ($booking_available_time_slot as $booking_time_slot) {
						
						$booked_slot_id = $this->get_time_slot_id($booking_time_slot['time_slot']);
						$booked_slot_id = $booked_slot_id[0]->slot_id;
						
						$sql = "SELECT count(booking_id) as total_booking_table
							FROM tab_admin_booking_request WHERE restaurant_id = $restaurant_id
							AND time_slot_id = $booked_slot_id AND request_date = '".$admin_table_booking_date. "'
							";
							
						$query = $this->db->query($sql);
						$result = $query->result_array();
						$total_booked_table = $result[0]['total_booking_table'];
						
						if($table_count <= $total_booked_table ){
							$book_time_slots[] = $booking_time_slot['time_slot'];
						}
                    }
				
                    $time_slot = [];
                    foreach ($available_time_slot_for_today as $today_time_slot) {
                        $time_slot[] = $today_time_slot['time_slot'];
                    }
                  
                    $unique_time_slot = array_diff($time_slot, $book_time_slots);
                    $time_slot = array_slice($unique_time_slot, 0, 4);

                } else {
                    $time_slot = [];
                    $count = 0;
                    foreach ($available_time_slot_for_today as $item) {
                        $time_slot[] = $item['time_slot'];
                        $count = $count + 1;
                        if ($count >= 4) {
                            break;
                        }
                    }
                }

                foreach ($time_slot as $ts) {
                    $this->db->select('slot_id, time_slot');
                    $this->db->from('timeslot');
                    $data = array(
                        'time_slot' => $ts
                    );
                    $this->db->where($data);
                    $query = $this->db->get();
                    $result = $query->result_array();
                    $time_slots[] = $result[0];
                }

                if (empty($time_slots)) {
                    $result_array['status'] = FAIL;
                    $result_array['message'] = NO_TIME_SLOT_TODAY;
                    return $result_array;
                }

                $result_array['status'] = SUCCESS;
                $result_array['message'] = AVAILABLE_TIME_SLOT_TODAY;
                $result_array['time_slot'] = $time_slots;
                return $result_array;
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = NO_TIME_SLOT_TODAY;
                $result_array['time_slot'] = array();
                return $result_array;
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_TIME_SLOT_TODAY;
            $result_array['time_slot'] = array();
            return $result_array;
        }
    }

    /*
    * Method Name: get_day_id
    * Purpose: To get day id for particular day
    * params:
    *      input: $day
    *      output: $dayId
    */

    public function get_day_id($day)
    {
        $dayId = 0;
        switch ($day) {
            case "Monday" :
                $dayId = 2;
                break;

            case "Tuesday" :
                $dayId = 3;
                break;

            case "Wednesday" :
                $dayId = 4;
                break;

            case "Thursday" :
                $dayId = 5;
                break;

            case "Friday" :
                $dayId = 6;
                break;

            case "Saturday" :
                $dayId = 7;
                break;

            case "Sunday" :
                $dayId = 1;
                break;
        }
        return $dayId;
    }

    /*
     * Author: Akshay Deshmukh
    * Method Name: get_open_close_time_of_restaurant
    * Purpose: To get start time and close time for restaurant
    * params:
    *      input: $day
    *      output: $dayId
    */
    public function get_open_close_time_of_restaurant($date, $restaurant_id)
    {
        $day = date('l', strtotime($date));
        $dayId = $this->get_day_id($day);
        $data = array(
            'user_id' => $restaurant_id,
            'open_close_day' => $dayId,
            'open_close_status' => '1'
        );
        $this->db->select('open_time_from, close_time_to')
            ->from('restaurant_open_close_time')
            ->where($data);
        $query = $this->db->get();
        return $query;
    }

    /*
     * Author: Akshay Deshmukh
    * Method Name: get_available_time_slots_for_restaurant
    * Purpose: To get time slots according to open time and close time
    * params:
    *      input: $day
    *      output: $dayId
    */
    public function get_available_time_slots_for_restaurant($date, $restaurant_id)
    {
        $result_array = [];
        $rounded_time_slot = '';
        $query = $this->get_open_close_time_of_restaurant($date, $restaurant_id);
		if ($query->num_rows() > 0) {
            $current_date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
            $current_date = $current_date->format('Y-m-d 00:00:00');
            if ($current_date == $date) {
                $rounded_time_slot = $this->get_rounded_time();
            }

            $open_close_time = $query->result_array();
            $open_time = $open_close_time[0]['open_time_from'];
            $open_time = date('H:i', strtotime("$open_time"));

            $close_time = $open_close_time[0]['close_time_to'];
            $close_time = date('H:i', strtotime("$close_time"));

            $this->db->select('slot_id, time_slot');
            $this->db->from('timeslot');
            if ($open_time != $close_time) {
                $this->db->where('time_slot >=', $open_time);
                $this->db->where('time_slot <=', $close_time);
            }

            if ($rounded_time_slot) {
                $this->db->where('slot_id >=', $rounded_time_slot['time_slot_id']);
            }

            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result_array['status'] = SUCCESS;
                $result_array['time_slots'] = $query->result_array();
                return $result_array;
            } else {
                $result_array['status'] = FAIL;
                $result_array['message'] = NO_TIME_SLOT_TODAY;
                return $result_array;
            }
        } else {
            $result_array['status'] = FAIL;
            $result_array['message'] = NO_TIME_SLOT_TODAY;
            return $result_array;
        }

    }

    /*
     * Method Name: is_valid_table
     * Purpose: To check if table is valid for a restaurant
     * params:
     *      input: user_id, tables - ( comma separated string )
     *      output: TRUE/FALSE
     */

    public function is_valid_table($user_id, $tables)
    {
        $tables_arr = explode(",", $tables);
        $this->db->select("t.table_id");
        $this->db->from("restaurant_tables t");

        $data = array(
            't.user_id' => $user_id,
            't.is_deleted' => '0',
            't.status' => '1'
        );
        $this->db->where($data);
        $this->db->where_in('table_id', $tables_arr);
        $query = $this->db->get();

        return (count($tables_arr) == $query->num_rows()) ? TRUE : FALSE;
    }

    /*
     * Method Name: update_wishlist
     * Purpose: add or remove from wishlist table
     * params:
     *      input: user_id, restaurant_id, status)
     *      output: -
     */

    public function update_wishlist($user_id, $restaurant_id, $status)
    {
        // delete data if availabele\
        $this->db->where("user_id", $user_id);
        $this->db->where("restaurant_id", $restaurant_id);
        $this->db->delete("tab_wishlist");
        if ($status == 1) {
            $insertData = array("user_id" => $user_id, "restaurant_id" => $restaurant_id, "created_on" => date("Y:m:d h:i:s"));
            $this->db->insert("tab_wishlist", $insertData);
        }
    }

    /*
     * Method Name: get_top10
     * Purpose: get top 10 record
     * params:
     *      input: -
     *      output: array
     */

    public function get_top10()
    {
        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        u.restaurant_owner_name,
                        u.user_email as email,
                        IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                        u.average_spend,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                        COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                        user_contact AS contact_number,
                        user_description AS description,
                        ", FALSE);
        $this->db->from("tab_usermst u");
        $this->db->join("tab_rating r", "r.restaurant_id = u.user_id AND r.status = '1' AND r.is_approved = '1'", "left");
        $this->db->join("tab_top10_restaurants top10", "top10.user_id = u.user_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");
	
		$data = array(
            'u.user_status' => '1',
            'u.is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->group_by("u.user_id");
        $this->db->order_by("u.user_first_name");
        $query = $this->db->get();
		
        if ($query->num_rows() > 0) {
            $restaurants = $query->result_array();
            $restaurants = $this->get_restaurant_cuisine($restaurants);
            return $restaurants;
        } else {
            return array();
        }
        //return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
    * Method Name: get_wishlist_count
    * Purpose: get wishlist of user
    * params:
    *      input: -
    *      output: array
    */

    public function get_wishlist_count($user_id)
    {
        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                        u.average_spend,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                        COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                        user_contact AS contact_number,
                        user_description AS description", FALSE);
        $this->db->from("tab_usermst u");
        $this->db->join("tab_rating r", "r.restaurant_id = u.user_id AND r.status = '1' AND r.is_approved = '1'", "left");
        $this->db->join("tab_wishlist w", "w.restaurant_id = u.user_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");

        $data = array(
            'u.user_status' => '1',
            'u.is_deleted' => '0',
            'w.user_id' => $user_id
        );
        $this->db->where($data);
        $this->db->group_by("u.user_id");
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->num_rows() : 0;
    }

    /*
    * Method Name: get_wishlist
    * Purpose: get get_wishlist record
    * params:
    *      input: -
    *      output: array
    */

    public function get_wishlist($user_id, $limit, $offset)
    {
        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        u.restaurant_owner_name,
                        u.user_email as email,
                        u.restaurant_detail_url,
                        u.street_address1,c.city_name,reg.region_name,cou.cou_name,
                        IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                        u.average_spend,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),1), 0) AS average_rating,
                        COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                        user_contact AS contact_number,
                        user_description AS description", FALSE);
        $this->db->from("tab_usermst u");
        $this->db->join("tab_rating r", "r.restaurant_id = u.user_id AND r.status = '1' AND r.is_approved = '1'", "left");
        $this->db->join("tab_wishlist w", "w.restaurant_id = u.user_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id");


        $data = array(
            'u.user_status' => '1',
            'u.is_deleted' => '0',
            'w.user_id' => $user_id
        );
        $this->db->where($data);
        $this->db->group_by("u.user_id");
        $this->db->order_by("u.user_first_name");
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $restaurants = $query->result_array();
            $restaurants = $this->get_restaurant_cuisine($restaurants);
            return $restaurants;
        } else {
            return array();
        }
    }


    /*
    * Method Name: get_restaurant_cuisine
    * Purpose: To get restaurant cuisine using array of restaurant details
    * params:
    *      input: - $restaurants
    *      output: array $restaurants
    */

    public function get_restaurant_cuisine($restaurants)
    {
        foreach ($restaurants as $key => $value) {
            $this->db->select('c.cuisine_id, c.cuisine_name');
            $this->db->from('tab_cuisine c');
            $this->db->join("tab_restaurant_cuisine_ambience rca", "rca.rca_cuisine_ambience_id= c.cuisine_id");
            $data = array(
                'rca.rca_type' => '1',
                'rca.user_id' => $value['restaurant_id'],
            );
            $this->db->where($data);
            $this->db->group_by("c.cuisine_id");
            $this->db->order_by("c.cuisine_name");
            $query = $this->db->get();
            $cuisines = $query->result_array();

            $cuisine_arr = array();
            foreach ($cuisines as $cuisine) {
                $cuisine_arr[] = $cuisine['cuisine_name'];
            }
            $cuisine = implode(", ", $cuisine_arr);
            $restaurants[$key]['cuisine'] = $cuisine;
        }
        return $restaurants;
    }

    /*
    * Method Name: restuarant_add
    * Purpose: get restuarant_add record
    * params:
    *      input: -
    *      output: array
    */

    public function get_restuarant_add()
    {
        $restaurantDetailsPath = base_url() . "restaurant-details/";
        $this->db->select("u.user_id AS restaurant_id,
                        u.user_first_name AS restaurant_name,
                        u.restaurant_owner_name,
                        u.user_email as email,
                        CONCAT('$restaurantDetailsPath', u.restaurant_detail_url) as resturantDetailsPath,
                        IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                        u.average_spend,
                        CONCAT(u.street_address1, ', ', c.city_name, ', ', reg.region_name, ', ', cou.cou_name) AS address,
                        IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating,
                        COUNT( NULLIF( your_thoughts, '' ) ) AS average_review,
                        user_contact AS contact_number,
                        user_description AS description", FALSE);
        $this->db->from("tab_usermst u");
        $this->db->join("tab_rating r", "r.restaurant_id = u.user_id AND r.status = '1' AND r.is_approved = '1'", "left outer");
        $this->db->join("tab_addmst addmst", "addmst.user_id = u.user_id");
        $this->db->join("tab_city c", "c.city_id = u.city_id", "left outer");
        $this->db->join("tab_region reg", "reg.region_id = u.region_id", "left outer");
        $this->db->join("tab_country cou", "cou.cou_id = u.country_id", "left outer");

        $data = array(
            'u.user_status' => '1',
            'u.is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->group_by("u.user_id");
        $this->db->order_by("u.user_first_name");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $restaurants = $query->result_array();
            $restaurants = $this->get_restaurant_cuisine($restaurants);
            return $restaurants;
        } else {
            return array();
        }
    }


    /*
    * Method Name: get_max_seq
    * Purpose: get getMaxSeq of  menu
    * params:
    *      input: -
    *      output: array
    */
    public function get_max_seq()
    {
        $this->db->select_max('menu_image_seq');
        $this->db->from('food_menu');
        $this->db->where('is_deleted', '0');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->row();
            $query = $query->menu_image_seq;
            return $query + 1;
        } else {
            return 1;
        }
    }


    /*
      * Author: Akshay Deshmukh
      * Date: 12-12-2016
      * Method Name: load_restaurants_filter_options
      * Purpose: Front search filter option - load all the Cuisines, Dietary Preference, Amenities
      * params:
      *      input: -
      *      output: array
      */

    public function load_restaurants_filter_options()
    {
        $return_array = [];

        //select cuisine
        $this->db->select("cuisine_id,cuisine_name", FALSE);
        $this->db->where('is_deleted', '0');
        $this->db->where('status', '1');
        $this->db->order_by('cuisine_name');
        $result = $this->db->get('cuisine');
        if ($result->num_rows()) {
            $return_array['cuisine'] = $result->result_array();
        } else {
            $return_array['cuisine'] = 0;
        }

        //select dietary_preference
        $this->db->select("diet_id,diet_preference", FALSE);
        $this->db->where('is_deleted', '0');
        $this->db->where('is_active', '1');
        $this->db->order_by('diet_preference');
        $result = $this->db->get('dietary_preference');
        if ($result->num_rows()) {
            $return_array['dietary_preference'] = $result->result_array();
        } else {
            $return_array['dietary_preference'] = 0;
        }

        //select ambience
        $this->db->select("ambience_id,ambience_name", FALSE);
        $this->db->where('is_deleted', '0');
        $this->db->where('status', '1');
        $this->db->order_by('ambience_name');
        $result = $this->db->get('ambience');
        if ($result->num_rows()) {
            $return_array['ambience'] = $result->result_array();
        } else {
            $return_array['ambience'] = 0;
        }
        return $return_array;
    }


    /*
      * Author: Akshay Deshmukh
      * Date: 13-12-2016
      * Method Name: restaurants_filter_search
      * Purpose: Search restaurants using filtered options
      * params:
      *      input: - Arrya of cuisine,dietary_preference,ambience and string location
      *      output: array
      */

    public function restaurants_filter_search_count($cuisine, $dietary_preference, $ambience, $location, $limit, $offset)
    {
        $user_type = 3;
        $logged_in_user_id = $this->session->userdata('user_id');
        $location = trim($location);
        $noRestaurentImagePath = base_url() . "assets/images/restaurent_no_image_available.png";
        $restaurantDetailsPath = base_url() . "restaurant-details/";
        $condition_query = $this->format_query($cuisine, $ambience, $dietary_preference, $location);

        $sql = "SELECT distinct u.user_id AS restaurant_id,u.latitude,u.longitude,
                u.web_domain,
                u.user_first_name AS restaurant_name,u.city_id, u.region_id, u.average_spend, reg.region_name, cou.cou_name,
                CONCAT('$restaurantDetailsPath', u.restaurant_detail_url) as resturantDetailsPath,
                (SELECT wish_id from tab_wishlist join tab_usermst on tab_usermst.user_id = tab_wishlist.user_id WHERE tab_wishlist.user_id = '$logged_in_user_id' AND tab_wishlist.restaurant_id = u.user_id) AS wish,

                IF( u.restaurant_hero_image = '', '$noRestaurentImagePath', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                CONCAT(u.street_address1, ', ', c.city_name) AS address,
                IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4), 2), 0) AS average_rating
                FROM `tab_usermst` u   
                        JOIN tab_city c ON c.city_id = u.city_id AND c.status = '1' AND c.city_delete = '0'
                        JOIN tab_region reg ON reg.region_id = u.region_id AND reg.status = '1' AND reg.region_delete = '0'
                        JOIN tab_country cou ON cou.cou_id = u.country_id AND cou.status = '1' AND cou.cou_delete = '0'
                        LEFT JOIN `tab_rating` r ON `r`.`restaurant_id` = `u`.`user_id` AND r.status = '1' AND r.is_approved = '1' 
                        " . $condition_query['cuisine_join'] . "
                        " . $condition_query['ambience_join'] . "
                        " . $condition_query['dietary_join'] . " 
                WHERE  `u`.`user_type` =  " . $user_type . "
                        " . $condition_query['cuisine_cond'] . "
                        " . $condition_query['ambience_cond'] . "
                        " . $condition_query['dietary_cond'] . " 
                        AND (
                             `u`.`street_address1` LIKE  '%$location%'
                              OR `u`.`street_address2` LIKE  '%$location%'
                              OR `cou`.`cou_name` LIKE  '%$location%'
                              OR `c`.`city_name` LIKE  '%$location%'
                              OR `reg`.`region_name` LIKE  '%$location%'
                        )
                        AND `u`.`user_status` =  '1'
                        AND `u`.`is_deleted` =  '0'
                        GROUP BY u.user_id  
               ";

        $query = $this->db->query($sql);
        return ($query->num_rows());
    }


    /*
      * Author: Akshay Deshmukh
      * Date: 13-12-2016
      * Method Name: restaurants_filter_search
      * Purpose: Search restaurants using filtered options
      * params:
      *      input: - Arrya of cuisine,dietary_preference,ambience and string location
      *      output: array
      */

    public function restaurants_filter_search($cuisine, $dietary_preference, $ambience, $location, $limit, $offset)
    {
        $user_type = 3;
        $logged_in_user_id = $this->session->userdata('user_id');
        $location = trim($location);
        $noRestaurentImagePath = base_url() . "assets/images/restaurent_no_image_available.png";
        $restaurantDetailsPath = base_url() . "restaurant-details/";
        $condition_query = $this->format_query($cuisine, $ambience, $dietary_preference, $location);

        $sql = "SELECT distinct u.user_id AS restaurant_id,u.latitude,u.longitude,
                u.web_domain,
                u.user_first_name AS restaurant_name,u.city_id, u.region_id, u.average_spend, reg.region_name, cou.cou_name,
                CONCAT('$restaurantDetailsPath', u.restaurant_detail_url) as resturantDetailsPath,
                (SELECT wish_id from tab_wishlist join tab_usermst on tab_usermst.user_id = tab_wishlist.user_id WHERE tab_wishlist.user_id = '$logged_in_user_id' AND tab_wishlist.restaurant_id = u.user_id) AS wish,

                IF( u.restaurant_hero_image = '', '$noRestaurentImagePath', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                CONCAT(u.street_address1, ', ', c.city_name) AS address,
                IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4), 1), 0) AS average_rating
                FROM `tab_usermst` u   
                        JOIN tab_city c ON c.city_id = u.city_id AND c.status = '1' AND c.city_delete = '0'
                        JOIN tab_region reg ON reg.region_id = u.region_id AND reg.status = '1' AND reg.region_delete = '0'
                        JOIN tab_country cou ON cou.cou_id = u.country_id AND cou.status = '1' AND cou.cou_delete = '0'
                        LEFT JOIN `tab_rating` r ON `r`.`restaurant_id` = `u`.`user_id` AND r.status = '1' AND r.is_approved = '1' 
                        " . $condition_query['cuisine_join'] . "
                        " . $condition_query['ambience_join'] . "
                        " . $condition_query['dietary_join'] . " 
                WHERE  `u`.`user_type` =  " . $user_type . "
                        " . $condition_query['cuisine_cond'] . "
                        " . $condition_query['ambience_cond'] . "
                        " . $condition_query['dietary_cond'] . " 
                        AND (
                             `u`.`street_address1` LIKE  '%$location%'
                              OR `u`.`street_address2` LIKE  '%$location%'
                              OR `cou`.`cou_name` LIKE  '%$location%'
                              OR `c`.`city_name` LIKE  '%$location%'
                              OR `reg`.`region_name` LIKE  '%$location%'
                        )
                        AND `u`.`user_status` =  '1'
                        AND `u`.`is_deleted` =  '0'
                        GROUP BY u.user_id  
                        limit $offset, $limit
        
               ";

        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }


    /*
      * Author: Akshay Deshmukh
      * Date: 06-02-2017
      * Method Name: restaurants_search_by_name_count
      * Purpose: Search restaurants using restaurant name
      * params:
      *      input: - search string
      *      output: array
      */

    public function restaurants_search_by_name_count($search_string, $limit, $offset)
    {
        $user_type = 3;
        $logged_in_user_id = $this->session->userdata('user_id');
        $search_string = trim($search_string);
        $noRestaurentImagePath = base_url() . "assets/images/restaurent_no_image_available.png";
        $restaurantDetailsPath = base_url() . "restaurant-details/";

        $sql = "SELECT distinct u.user_id AS restaurant_id,
                u.latitude,u.longitude,
                u.web_domain,
                u.user_first_name AS restaurant_name,u.city_id, u.region_id, u.average_spend, reg.region_name, cou.cou_name,
                CONCAT('$restaurantDetailsPath', u.restaurant_detail_url) as resturantDetailsPath,
                (SELECT wish_id from tab_wishlist join tab_usermst on tab_usermst.user_id = tab_wishlist.user_id WHERE tab_wishlist.user_id = '$logged_in_user_id' AND tab_wishlist.restaurant_id = u.user_id) AS wish,
                IF( u.restaurant_hero_image = '', '$noRestaurentImagePath', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                CONCAT(u.street_address1, ', ', c.city_name) AS address,
                IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4), 2), 0) AS average_rating
                FROM `tab_usermst` u   
                JOIN tab_city c ON c.city_id = u.city_id AND c.status = '1' AND c.city_delete = '0'
                JOIN tab_region reg ON reg.region_id = u.region_id AND reg.status = '1' AND reg.region_delete = '0'
                JOIN tab_country cou ON cou.cou_id = u.country_id AND cou.status = '1' AND cou.cou_delete = '0'
                LEFT JOIN `tab_rating` r ON `r`.`restaurant_id` = `u`.`user_id` AND r.status = '1' AND r.is_approved = '1'
                        
                WHERE  `u`.`user_type` =  " . $user_type . "
                AND `u`.`user_first_name` LIKE  '%$search_string%'
                AND `u`.`user_status` =  '1'
                AND `u`.`is_deleted` =  '0'
                GROUP BY u.user_id  
                ";

        $query = $this->db->query($sql);
        return ($query->num_rows());
    }

    /*
      * Author: Akshay Deshmukh
      * Date: 06-02-2017
      * Method Name: restaurants_search_by_name
      * Purpose: Search restaurants using restaurant name
      * params:
      *      input: - search string
      *      output: array
      */

    public function restaurants_search_by_name($search_string, $limit, $offset)
    {
        $user_type = 3;
        $logged_in_user_id = $this->session->userdata('user_id');
        $search_string = trim($search_string);
        $noRestaurentImagePath = base_url() . "assets/images/restaurent_no_image_available.png";
        $restaurantDetailsPath = base_url() . "restaurant-details/";

        $sql = "SELECT distinct u.user_id AS restaurant_id,
                u.latitude,u.longitude,
                u.web_domain,
                u.user_first_name AS restaurant_name,u.city_id, u.region_id, u.average_spend, reg.region_name, cou.cou_name,
                CONCAT('$restaurantDetailsPath', u.restaurant_detail_url) as resturantDetailsPath,
                (SELECT wish_id from tab_wishlist join tab_usermst on tab_usermst.user_id = tab_wishlist.user_id WHERE tab_wishlist.user_id = '$logged_in_user_id' AND tab_wishlist.restaurant_id = u.user_id) AS wish,
                IF( u.restaurant_hero_image = '', '$noRestaurentImagePath', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                CONCAT(u.street_address1, ', ', c.city_name) AS address,
                IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4), 1), 0) AS average_rating
                FROM `tab_usermst` u   
                JOIN tab_city c ON c.city_id = u.city_id AND c.status = '1' AND c.city_delete = '0'
                JOIN tab_region reg ON reg.region_id = u.region_id AND reg.status = '1' AND reg.region_delete = '0'
                JOIN tab_country cou ON cou.cou_id = u.country_id AND cou.status = '1' AND cou.cou_delete = '0'
                LEFT JOIN `tab_rating` r ON `r`.`restaurant_id` = `u`.`user_id` AND r.status = '1' AND r.is_approved = '1'
                        
                WHERE  `u`.`user_type` =  " . $user_type . "
                AND `u`.`user_first_name` LIKE  '%$search_string%'
                AND `u`.`user_status` =  '1'
                AND `u`.`is_deleted` =  '0'
                GROUP BY u.user_id  
                limit $offset, $limit
                ";

        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
   * Auhor: Akshay Deshmukh
   * Method Name: format_query
   * Purpose: To format query of cuisine, ambience, dietary_preference for search
   * params:
   *      input: $cuisine, $ambience, $dietary_preference
   *      output: formated query.
   */

    public function format_query($cuisine, $ambience, $dietary_preference, $location)
    {
        $cuisine_join = $cuisine_cond = $ambience_join = $ambience_cond = $dietary_join = $dietary_cond = '';

        if (!empty($cuisine)) {
            $cuisine_join = " JOIN `tab_restaurant_cuisine_ambience` rc ON `rc`.`user_id` = `u`.`user_id`";
            $cuisine_comma_selected = implode(",", $cuisine);
            $cuisine_cond = "AND ( rc.rca_type = '1' AND rc.rca_cuisine_ambience_id IN (" . $cuisine_comma_selected . ") )";
        }

        if (!empty($ambience)) {
            $ambience_join = " JOIN `tab_restaurant_cuisine_ambience` ra ON `ra`.`user_id` = `u`.`user_id`";
            $ambience_comma_selected = implode(",", $ambience);
            $ambience_cond = "AND (ra.rca_type = '2' AND ra.rca_cuisine_ambience_id IN (" . $ambience_comma_selected . ") )";
        }

        if (!empty($dietary_preference)) {
            $dietary_join = "JOIN `tab_dietary_restaurant` dr ON `dr`.`user_id` = `u`.`user_id`";
            $dietary_comma_selected = implode(",", $dietary_preference);
            $dietary_cond = "AND (  dr.diet_id IN (" . $dietary_comma_selected . ") )";
        }

        $result_condition = array('cuisine_join' => $cuisine_join, 'cuisine_cond' => $cuisine_cond, 'dietary_join' => $dietary_join, 'dietary_cond' => $dietary_cond, 'ambience_join' => $ambience_join, 'ambience_cond' => $ambience_cond);

        return $result_condition;
    }

    /*
   * Auhor: Akshay Deshmukh
   * Method Name: restaurant_add_user_wishlist
   * Purpose: Add restaurant to users withlist
   * params:
   *      input: $restaurant_id
   *      output: $result_array.
   */
    public function restaurant_add_user_wishlist($restaurant_id)
    {
        $result_array = [];
        $logged_in_user_id = $this->session->userdata('user_id');
        $query = $this->check_for_user_wishlist($logged_in_user_id, $restaurant_id);

        if (!$query->num_rows()) {
            $data = array(
                'restaurant_id' => $restaurant_id,
                'user_id' => $logged_in_user_id
            );

            $this->db->insert('wishlist', $data);
            if ($this->db->insert_id()) {
                $result_array['success'] = 1;
                $result_array['message'] = "Restaurant added to wishlist.";
                return $result_array;
            } else {
                $result_array['error'] = 1;
                $result_array['message'] = "Unable to add restaurant to wish list. Please try again.";
                return $result_array;
            }

        } else {
            $result_array['error'] = 1;
            $result_array['message'] = "Restaurant already added to wishlist.";
            return $result_array;
        }
    }

    /*
   * Auhor: Akshay Deshmukh
   * Method Name: restaurant_remove_user_wishlist
   * Purpose: Remove restaurant to users withlist
   * params:
   *      input: $restaurant_id
   *      output: $result_array.
   */
    public function restaurant_remove_user_wishlist($restaurant_id)
    {
        $result_array = [];
        $logged_in_user_id = $this->session->userdata('user_id');
        $query = $this->check_for_user_wishlist($logged_in_user_id, $restaurant_id);

        if ($query->num_rows()) {

            $this->db->where("user_id", $logged_in_user_id);
            $this->db->where("restaurant_id", $restaurant_id);
            $this->db->delete("tab_wishlist");

            if ($this->db->affected_rows()) {
                $result_array['success'] = 1;
                $result_array['message'] = "Restaurant removed from wishlist.";
                return $result_array;
            } else {
                $result_array['error'] = 1;
                $result_array['message'] = "Unable to remove restaurant from wishlist. Please try again.";
                return $result_array;
            }
        } else {
            $result_array['error'] = 1;
            $result_array['message'] = "Sorry! Please try again.";
            return $result_array;
        }
    }

    /*
  * Auhor: Akshay Deshmukh
  * Method Name: check_for_user_wishlist
  * Purpose: Check users already have restaurant to their withlist
  * params:
  *      input: $logged_in_user_id, $restaurant_id
  *      output: query result.
  */
    function check_for_user_wishlist($logged_in_user_id, $restaurant_id)
    {
        $this->db->select("*");
        $this->db->where("user_id", $logged_in_user_id);
        $this->db->where("restaurant_id", $restaurant_id);
        return $this->db->get("tab_wishlist");
    }

    /*
  * Auhor: Akshay Deshmukh
  * Method Name: load_news
  * Purpose: Load the news
  * params:
  *      output: result_array.
  */
    public function load_news()
    {
        $noImagePath = base_url() . "assets/images/restaurent_no_image_available.png";
        $sql = "SELECT news_id, news_title, news_description_link,short_description,
         DATE_FORMAT(news_date,'%d %M %Y') as news_date_formated,
         news_sequence, news_desc,
            IF( news_image = '', '$noImagePath', CONCAT('" . base_url() . "', '" . NEWS_IMAGE_PATH . "',news_image) ) AS news_image
            FROM `tab_news` 
            WHERE 
                `news_status` = '1' 
            AND `is_deleted` = '0'
            ORDER BY news_date DESC 
        ";

        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
  * Auhor: Akshay Deshmukh
  * Method Name: delete_restaurant_menu
  * Purpose: To delete restaurant menu with image
  * params:
  *      input: $restaurant_id, $menu_id
  *      output: TRUE, FALSE
  */
    public function delete_restaurant_menu($restaurant_id, $menu_id)
    {
        $this->db->select('fm_image');
        $this->db->from('food_menu');
        $data = array(
            'fm_id' => $menu_id,
        );
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $result = $query->result_array();
            $image_name = $result[0]['fm_image'];
            $data = array(
                'fm_id' => $menu_id,
                'user_id' => $restaurant_id
            );
            $this->db->where($data);
            $this->db->delete("tab_food_menu");
            if ($this->db->affected_rows() > 0) {
                $path = FCPATH . 'assets/upload/foodmenu/' . $image_name;
                if (file_exists($path)) {
                    unlink($path);
                }
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /*
  * Auhor: Akshay Deshmukh
  * Method Name: delete_restaurant_gallery_image
  * Purpose: To delete restaurant gallery image
  * params:
  *      input: $restaurant_id, $image_id
  *      output: TRUE, FALSE
  */
    public function delete_restaurant_gallery_image($restaurant_id, $image_id)
    {
        $this->db->select('gal_image');
        $this->db->from('restuarant_gallery');
        $data = array(
            'gal_id' => $image_id,
        );
        $this->db->where($data);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $result = $query->result_array();
            $image_name = $result[0]['gal_image'];
            $data = array(
                'gal_id' => $image_id,
                'user_id' => $restaurant_id
            );

            $this->db->where($data);
            $this->db->delete("tab_restuarant_gallery");
            if ($this->db->affected_rows() > 0) {
                $path = FCPATH . GALLERY_IMAGE_PATH . $image_name;
                if (file_exists($path)) {
                    unlink($path);
                }
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function get_restaurant_id_by_details_url($restaurant_details_url)
    {
        $this->db->select('user_id');
        $this->db->from('tab_usermst');
        $data = array(
            'restaurant_detail_url' => $restaurant_details_url
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->user_id : 0;
    }

    public function get_restaurant_id_by_booking_id($booking_id)
    {
        $this->db->select('b.restaurant_id, u.user_id, u.user_first_name,u.restaurant_detail_url');
        $this->db->from('tab_booking_request b');
        $this->db->join("usermst u", "u.user_id = b.restaurant_id");
        $data = array(
            'booking_id' => $booking_id
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row() : 0;
    }

    public function add_last_minute_cancellatinon_notification($last_minute_notification_data)
    {
        $this->db->insert("tab_last_minute_cancellation_notification", $last_minute_notification_data);
        return TRUE;
    }

    /*
    * Auhor: Akshay Deshmukh
    * Method Name: get_rounded_time
    * Purpose: To get parameter needed for get next four available time slots
    * params:
    *
    *      output: $date_time_array
    *
    */
    public function get_rounded_time()
    {
        $date = new DateTime("now", new DateTimeZone(CLIENT_ZONE));
        $today_date = $date->format('Y-m-d H:i:s');
        $hour = '';
        $minute = '';
        $date_time_array = array();

        if (date('i', strtotime($today_date)) >= 31) {
            $minute = '00';
            $hour = date('H', strtotime("$today_date +1 hour"));
        } else if (date('i', strtotime($today_date)) <= 29 && date('i', strtotime($today_date)) >= 01) {
            $hour = date('H', strtotime($today_date));
            $minute = '30';
        } else {
            $hour = date('H', strtotime($today_date));
            $minute = date('i', strtotime($today_date));
        }

        $date = date('Y-m-d', strtotime($today_date));
        $time = "$hour:$minute";
        $time_slot_id = $this->get_time_slot_id($time);


        $date_time_array['today_date'] = $today_date;
        $date_time_array['date_time'] = date('Y-m-d H:i:s', strtotime("$date $time"));
        $date_time_array['date'] = date('Y-m-d H:i:s', strtotime($date));
        $date_time_array['start_time_slot'] = $time;
        $date_time_array['time_slot_id'] = $time_slot_id[0]->slot_id;
        return $date_time_array;
    }

    /*
     * Author = Akshay Deshmukh : Note - this function is already created in bookinmolde but that was not working for admin booking ajax call.
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


    public function get_closing_slots($from_time_slot)
    {
        $this->db->select("slot_id,time_slot");
        $this->db->from("timeslot");
        $this->db->where('time_slot >', $from_time_slot);
        $result = $this->db->get();
        return $result->result_array();
    }

    /*
     * Auther: Akshay Deshmukh : Note: The get_top10 method is already mentioned for the same purpose but used as WS. I have created this method because i want to pass the loagin user id and also want to get is fav reatro or not.
     * Method Name: get_top10
     * Purpose: get top 10 record
     * params:
     *      input: -
     *      output: array
     */

    public function get_top10_front($logged_in_user_id = 0)
    {
        $noRestaurentImagePath = base_url() . "assets/images/restaurent_no_image_available.png";
        $restaurantDetailsPath = base_url() . "restaurant-details/";

        $sql = "
            SELECT u.user_id AS restaurant_id, 
            u.user_first_name AS restaurant_name,
            u.restaurant_owner_name, 
            u.user_email as email, 
            IF( u.restaurant_hero_image = '', '$noRestaurentImagePath', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image, 
            u.average_spend, 
             CONCAT('$restaurantDetailsPath', u.restaurant_detail_url) as resturantDetailsPath,
             reg.region_name,cou.cou_name,
             CONCAT(u.street_address1, ', ', c.city_name) AS address,
            IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4), 1), 0) AS average_rating, COUNT( NULLIF( your_thoughts, '' ) ) AS average_review, 
            user_contact AS contact_number,
            user_description AS description";
            if($logged_in_user_id)
            {
                $sql .= ",CASE WHEN twl.wish_id IS NOT NULL 
                                   THEN 1
                                   ELSE 0
                             END AS is_fav ";
            }
            $sql .= "
            FROM (`tab_usermst` u)
            LEFT JOIN `tab_rating` r ON `r`.`restaurant_id` = `u`.`user_id` AND r.status = '1' AND r.is_approved = '1' 
            JOIN `tab_top10_restaurants` top10 ON `top10`.`user_id` = `u`.`user_id` 
            JOIN `tab_city` c ON `c`.`city_id` = `u`.`city_id` 
            JOIN `tab_region` reg ON `reg`.`region_id` = `u`.`region_id` 
            JOIN `tab_country` cou ON `cou`.`cou_id` = `u`.`country_id` ";
            if($logged_in_user_id)
            {
                $sql .= "LEFT JOIN `tab_wishlist` twl ON `twl`.`user_id` = $logged_in_user_id AND `twl`.`restaurant_id` = `u`.`user_id`";
            }

        $sql .= "WHERE `u`.`user_status` = '1' 
            AND `u`.`is_deleted` = '0' 
            GROUP BY `u`.`user_id` 
            ORDER BY `u`.`user_first_name`
        ";

        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    public function get_restaurant_details_url($restaurant_id)
    {
        $this->db->select('restaurant_detail_url');
        $this->db->from('tab_usermst');
        $data = array(
            'user_id' => $restaurant_id
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->restaurant_detail_url : '';
    }
}

?>