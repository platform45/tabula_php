<?php

/*
  Model that contains functions related to suggestions results
 */

class SuggesstionModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
    }


    /*
     * Method Name: get_suggesstions
     * Purpose: The business concept behind the Suggestion restaurants is that user will be 
                able to view the restaurants according to his previous history. It will be decided on the 
                Cuisines. And according to the user s location user will be able to view the restaurant. The 
                Restaurant which have got highest number of rating and reviews that restaurant will be 
                displayed at the top
     * params:
     *      input:distance, latitude, longitude, user_id,is_all
     *      output: result array
     */

    public function get_suggesstions($distance = 50, $latitude = 0, $longitude = 0, $user_id, $is_all = 1)
    {
        $user_type = SEARCH_RESTAURANT_TYPE;
        $qry = "
                SELECT `user_id`,
                ACOS( SIN( RADIANS( `latitude` ) ) * SIN( RADIANS( $latitude ) ) + COS( RADIANS( `latitude` ) ) * COS( RADIANS( $latitude )) * COS( RADIANS( `longitude` ) - RADIANS( $longitude )) ) * 6380 AS `distance`
                FROM `tab_usermst`
                WHERE `user_type` = $user_type
                AND `user_status` = '". 1 . "'
                AND `is_deleted` = '". 0 . "'
                AND (ACOS( SIN( RADIANS( `latitude` ) ) * SIN( RADIANS( $latitude ) ) + COS( RADIANS( `latitude` ) ) * COS( RADIANS( $latitude )) * COS( RADIANS( `longitude` ) - RADIANS( $longitude )) ) * 6380 < $distance)
                ORDER BY `distance`
        ";

        $query = $this->db->query($qry);
        $final_suggested_restaurant_ids = $search_restaurant_ids = $booked_restaurant_ids = $booked_restaurant_cuisine_ids = $restaurant_ids = $suggested_restaurant_ids = [];
        if ($query->num_rows() > 0) {
            $search_restaurants = $query->result_array();
            //$search_restaurant_ids = are the ids which are search on the basis of users lat log
            foreach ($search_restaurants as $search_restaurant) {
                $search_restaurant_ids[] = $search_restaurant['user_id'];
            }

            $this->db->distinct();
            $this->db->select("restaurant_id");
            $this->db->from("tab_booking_request");
            $data = array(
                'customer_id' => $user_id
            );
            $this->db->where($data);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $booked_restaurants = $query->result_array();
                //$booked_restaurant_ids =  which are booked by users before
                foreach ($booked_restaurants as $booked_restaurant) {
                    $booked_restaurant_ids[] = $booked_restaurant['restaurant_id'];
                }

                //$restaurant_ids = are the suggested restro for user and which are not yet booked by user
                $restaurant_ids = array_diff($search_restaurant_ids, $booked_restaurant_ids);

                $booked_restaurant_ids = implode(', ', $booked_restaurant_ids);
                $sql = "SELECT DISTINCT rca_cuisine_ambience_id FROM tab_restaurant_cuisine_ambience WHERE rca_type = 1 AND user_id IN ($booked_restaurant_ids) ORDER BY rca_cuisine_ambience_id";
                $query = $this->db->query($sql);

                if ($query->num_rows() > 0) {
                    $booked_restaurant_cuisines = $query->result_array();
                    foreach ($booked_restaurant_cuisines as $booked_restaurant_cuisine) {
                        $booked_restaurant_cuisine_ids[] = $booked_restaurant_cuisine['rca_cuisine_ambience_id'];
                    }
                }
            } else {
                $restaurant_ids = $search_restaurant_ids;
            }

            $restaurant_ids = implode(', ', $restaurant_ids);
            if (!empty($booked_restaurant_cuisine_ids)) {
                $booked_restaurant_cuisine_ids = implode(', ', $booked_restaurant_cuisine_ids);
                $sql = "SELECT DISTINCT user_id FROM tab_restaurant_cuisine_ambience WHERE rca_type = 1 AND rca_cuisine_ambience_id IN ($booked_restaurant_cuisine_ids) AND user_id NOT IN ($booked_restaurant_ids) AND user_id IN($restaurant_ids) ORDER BY user_id";
                $query = $this->db->query($sql);

                if ($query->num_rows() > 0) {
                    $suggested_restaurants = $query->result_array();
                    foreach ($suggested_restaurants as $suggested_restaurant) {
                        $suggested_restaurant_ids[] = $suggested_restaurant['user_id'];
                    }

                    //$final_suggested_restaurant_ids = are the final suggested restro for user.
                    $final_suggested_restaurant_ids = implode(', ', $suggested_restaurant_ids);
                }
            } else {
                //$final_suggested_restaurant_ids = are the final suggested restro for user.
                $final_suggested_restaurant_ids = $restaurant_ids;
            }

            if (!empty($final_suggested_restaurant_ids)) {
                $sql = "
                SELECT restaurant_id, IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),2), 0) AS average_rating 
                FROM tab_rating 
                WHERE restaurant_id IN ($final_suggested_restaurant_ids) 
                GROUP BY restaurant_id 
                ORDER BY average_rating DESC
                ";
                $query = $this->db->query($sql);
                if ($query->num_rows() > 0) {
                    $final_restaurant_ids = $query->result_array();
                    if ($is_all == 0)
                        $final_restaurant_ids = array_slice($final_restaurant_ids, 0, 3);
                    else
                        $final_restaurant_ids = array_slice($final_restaurant_ids, 0, 20);

                    $restaurants = array();
                    foreach ($final_restaurant_ids as $final_restaurant_id) {
                        $restaurants[] = $this->usermodel->get_restaurant_user_details($final_restaurant_id['restaurant_id'], $user_type);
                    }
                    return $restaurants;
                } else {
                    return array();
                }
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
}

?>