<?php

/*
  Model that contains function related to user
 */

class UserModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Method Name: check_user_exists
     * Purpose: To verify if user exists in database
     * params:
     *      input: Email, password, $user_type
     *      output: array containing user details
     */
    public function check_user_exists($email, $password, $user_type)
    {
        $this->db->select("user_id, user_type, user_first_name, IF( user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', user_image) ) AS user_image, user_contact", FALSE);
        $this->db->from("usermst");
        $data = array(
            'user_email' => $email,
            'user_password' => $password,
            'user_type' => $user_type,
            'user_status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->row();
    }

    /*
     * Method Name: update_user_details
     * Purpose: To update user details in database
     * params:
     *      input: array - data to be updated, id of user for whom data is to be updated
     */
    public function update_user_details($update_data, $user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->update('usermst', $update_data);
    }

    /*
     * Method Name: update_device_data
     * Purpose: To update device data for user in database
     * params:
     *      input: user_id, device_id, device_type, device_id_live, access_token
     */
    public function update_device_data($user_id, $device_id, $device_type, $device_id_live, $access_token)
    {
        $this->db->select("dev_id");
        $this->db->from("user_devices");
        $this->db->where("user_id", $user_id);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();

        $date = date("Y-m-d H:i:s");
        $data = array(
            'user_id' => $user_id,
            'dev_device_id' => $device_id,
            'dev_type' => $device_type,
            'is_live_entry' => $device_id_live
        );

        if ($result) {
            $data['modified_on'] = $date;
            $this->db->where('dev_id', $result->dev_id);
            $this->db->update('user_devices', $data);
        } else {
            $data['created_on'] = $date;
            $this->db->insert('user_devices', $data);

            $user_id = $this->db->insert_id();
        }
        return $user_id;
    }

    /*
     * Method Name: get_slider_images
     * Purpose: To get slider images
     * params:
     *      output: return slider details
     */
    public function get_slider_images()
    {
        $this->db->select("sli_title, concat('" . base_url() . "', '" . SLIDER_IMAGE_PATH . "', sli_image) as slider_image", FALSE);
        $this->db->from("slidermst");
        $data = array(
            'sli_type' => 'Slider',
            'sli_status' => 1,
            'is_deleted' => 0
        );
        $this->db->where($data);
        $this->db->order_by('sli_sequence');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : array();
    }

    /*
     * Method Name: get_user_details_for_password
     * Purpose: To get user details
     * params:
     *      input: Email
     *      output: array containing user details
     */
    public function get_user_details_for_password($email)
    {
        $this->db->select("user_id, user_first_name, user_status, notification_setting");
        $this->db->from("usermst");
        $data = array(
            'user_email' => $email,
            'is_deleted' => '0'
        );
        $this->db->where($data);

        $where_clause = "(user_type = 2 OR user_type = 3)";
        $this->db->where($where_clause);
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->row();
    }

    /*
     * Method Name: update_forgot_password_token
     * Purpose: To update forgot password token for user in database
     * params:
     *      input: user_id, forgot_password_token
     *      output: user_id
     */
    public function update_forgot_password_token($user_id, $forgot_password_token)
    {
        if ($user_id && $forgot_password_token) {
            $update = array('forgot_password_hash' => $forgot_password_token);
            $this->db->where('user_id', $user_id);
            $this->db->update("usermst", $update);
            return $user_id;
        } else {
            return 0;
        }
    }

    /*
     * Method Name: get_countries
     * Purpose: To get all countries from database
     * params:
     *      input: -
     *      output: object of countries
     */
    public function get_countries()
    {
        $this->db->select("cou_id, cou_name, cou_abbreviation");
        $this->db->from("country");
        $data = array(
            'status' => '1',
            'cou_id' => '47', // as per requirement only country id is needed
            'cou_delete' => 0
        );
        $this->db->where($data);
        $this->db->order_by("cou_name");
        $query = $this->db->get();

        return $query->result();
    }

    /*
     * Method Name: get_state_by_country
     * Purpose: To get all states as per the country from database
     * params:
     *      input: country_id
     *      output: object of states
     */
    public function get_state_by_country($country_id)
    {
        $this->db->select("region_id as state_id, region_name as state_name");
        $this->db->from("region");
        $data = array(
            'cou_id' => $country_id,
            'status' => '1',
            'region_delete' => 0
        );
        $this->db->where($data);
        $this->db->order_by("region_name");
        $query = $this->db->get();

        return $query->result();
    }

    /*
     * Method Name: get_city_by_state
     * Purpose: To get all cities as per the state from database
     * params:
     *      input: state_id
     *      output: object of cities
     */
    public function get_city_by_state($state_id)
    {
        $this->db->select("city_id, city_name");
        $this->db->from("city");
        $data = array(
            'region_id' => $state_id,
            'status' => '1',
            'city_delete' => 0
        );
        $this->db->where($data);
        $this->db->order_by("city_name");
        $query = $this->db->get();

        return $query->result();
    }

    /*
     * Method Name: check_email_exists
     * Purpose: To check if user has already registerd with this email address
     * params:
     *      input: email
     *      output: TRUE Or FALSE
     */
    public function check_email_exists($email, $user_id = 0)
    {
        $this->db->select("user_id");
        $this->db->from("usermst");
        $data = array(
            'user_email' => $email,
            'is_deleted' => '0',
        );
        $this->db->where($data);
        if ($user_id > 0) {
            $this->db->where("user_id != $user_id");
        }
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row()->user_id : FALSE;
    }

    /*
     * Method Name: check_country_exists
     * Purpose: To check if country is valid
     * params:
     *      input: country
     *      output: TRUE Or FALSE
     */
    public function check_country_exists($country)
    {
        $this->db->select("cou_id");
        $this->db->from("country");
        $data = array(
            'cou_id' => $country,
            'status' => "1",
            'cou_delete' => 0
        );
        $this->db->where($data);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? TRUE : FALSE;
    }

    /*
     * Method Name: check_state_exists
     * Purpose: To check if state is valid
     * params:
     *      input: state
     *      output: TRUE Or FALSE
     */
    public function check_state_exists($state)
    {
        $this->db->select("region_id");
        $this->db->from("region");
        $data = array(
            'region_id' => $state,
            'status' => "1",
            'region_delete' => 0
        );
        $this->db->where($data);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? TRUE : FALSE;
    }

    /*
     * Method Name: check_city_exists
     * Purpose: To check if city is valid
     * params:
     *      input: city
     *      output: TRUE Or FALSE
     */
    public function check_city_exists($city)
    {
        $this->db->select("city_id");
        $this->db->from("city");
        $data = array(
            'city_id' => $city,
            'status' => "1",
            'city_delete' => 0
        );
        $this->db->where($data);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? TRUE : FALSE;
    }

    /*
     * Method Name: action
     * Purpose: To perform insert in user table
     * params:
     *      input: action, data array, edit_id
     *      output: id of record updated
     */
    public function action($action, $data = array(), $edit_id = 0)
    {
        switch ($action) {
            case 'insert':
                $this->db->insert('usermst', $data);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('user_id', $edit_id);
                $this->db->update('usermst', $data);
                return $edit_id;
                break;
        }
    }

    /*
     * Method Name: get_cuisines
     * Purpose: To get all cuisines from database
     * params:
     *      input: -
     *      output: object of cuisines
     */
    public function get_cuisines()
    {
        $this->db->select("cuisine_id, cuisine_name");
        $this->db->from("cuisine");
        $data = array(
            'status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->order_by("cuisine_name");
        $query = $this->db->get();

        return $query->result();
    }

    /*
     * Method Name: get_cuisines_by_restaurant_id
     * Purpose: To get all cuisines from database for that restaurant
     * params:
     *      input: -
     *      output: object of cuisines
     */
    public function get_cuisines_by_restaurant_id($restaurant_id)
    {
        $sql = "SELECT DISTINCT c.cuisine_id, c.cuisine_name, 
                 CASE WHEN rc.rca_id IS NOT NULL 
                       THEN 1
                       ELSE 0
                 END AS status
            FROM tab_cuisine c
            LEFT JOIN tab_restaurant_cuisine_ambience rc ON rc.rca_cuisine_ambience_id = c.cuisine_id AND rc.rca_type = '1' AND rc.user_id = '$restaurant_id'
            WHERE `c`.`status` = '1'
            AND `c`.`is_deleted` = '0'
            ORDER BY c.cuisine_name 
         ";

        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
     * Method Name: get_ambience
     * Purpose: To get all ambience from database
     * params:
     *      input: -
     *      output: object of ambience
     */
    public function get_ambience()
    {
        $this->db->select("ambience_id, ambience_name");
        $this->db->from("ambience");
        $data = array(
            'status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->order_by("ambience_name");
        $query = $this->db->get();

        return $query->result();
    }

    /*
      * Method Name: get_ambience_by_restaurant_id
      * Purpose: To get all ambience from database for that restaurant
      * params:
      *      input: -
      *      output: object of ambience
      */
    public function get_ambience_by_restaurant_id($restaurant_id)
    {
        $sql = "SELECT DISTINCT a.ambience_id, a.ambience_name, 
                CASE WHEN rc.rca_id IS NOT NULL 
                       THEN 1
                       ELSE 0
                END AS status

            FROM tab_ambience a
            LEFT JOIN tab_restaurant_cuisine_ambience rc ON rc.rca_cuisine_ambience_id = a.ambience_id AND rc.rca_type = '2' AND rc.user_id = '$restaurant_id'
            WHERE `a`.`status` = '1'
            AND `a`.`is_deleted` = '0'
            ORDER BY a.ambience_name 
         ";

        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
     * Method Name: get_dietary_preference
     * Purpose: to get list of dietary preference  from data base
     * params:
     *      input: -
     *      output: object of dietary preference
     */
    public function get_dietary_preference()
    {
        $this->db->select("diet_id, diet_preference");
        $this->db->from("dietary_preference");
        $data = array(
            'is_active' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->order_by("diet_preference");
        $query = $this->db->get();

        return $query->result();
    }

    /*
        * Method Name: get_dietary_preference_by_restaurant_id
        * Purpose: To get all dietary_preference from database for that restaurant
        * params:
        *      input: -
        *      output: object of dietary_preference
        */
    public function get_dietary_preference_by_restaurant_id($restaurant_id)
    {
        $sql = "SELECT DISTINCT d.diet_id, d.diet_preference, 
                CASE WHEN tdr.dra_id IS NOT NULL 
                       THEN 1
                       ELSE 0
                END AS status
        FROM tab_dietary_preference d
        LEFT JOIN tab_dietary_restaurant tdr ON tdr.diet_id = d.diet_id AND tdr.user_id ='$restaurant_id'
        WHERE d.is_active = '1'
        AND d.is_deleted = '0'
         ";
        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
     * Method Name: check_cuisine_exists
     * Purpose: To check if cuisine is valid
     * params:
     *      input: cuisine string
     *      output: TRUE Or FALSE
     */
    public function check_cuisine_exists($cuisine)
    {
        $arr = explode(",", $cuisine);
        $total_selected = count($arr);
        $comma_selected = "'" . implode("', '", $arr) . "'";

        $select = "COUNT(*) as total FROM tab_cuisine WHERE cuisine_id IN ( $comma_selected )";
        $this->db->select($select, FALSE);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->row()->total == $total_selected) ? TRUE : FALSE;
    }

    /*
     * Method Name: check_ambience_exists
     * Purpose: To check if ambience is valid
     * params:
     *      input: ambience string
     *      output: TRUE Or FALSE
     */
    public function check_ambience_exists($ambience)
    {
        $arr = explode(",", $ambience);
        $total_selected = count($arr);
        $comma_selected = "'" . implode("', '", $arr) . "'";

        $select = "COUNT(*) as total FROM tab_ambience WHERE ambience_id IN ( $comma_selected )";
        $this->db->select($select, FALSE);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->row()->total == $total_selected) ? TRUE : FALSE;
    }

    /*
     * Method Name: update_cuisine_ambience_data
     * Purpose: To update device data for user in database
     * params:
     *      input: user_id, cuisine string, ambience string
     */
    public function update_cuisine_ambience_data($user_id, $cuisine, $ambience)
    {
        // Delete all records for that restaurant and then insert new
        $this->db->where("user_id", $user_id);
        $this->db->delete('restaurant_cuisine_ambience');

        $cuisine_arr = explode(",", $cuisine);
        $ambience_arr = explode(",", $ambience);

        // Insert cuisine
        foreach ($cuisine_arr as $cuisine) {
            $data = array(
                'user_id' => $user_id,
                'rca_cuisine_ambience_id' => $cuisine,
                'rca_type' => '1'
            );
            $this->db->insert('restaurant_cuisine_ambience', $data);
        }

        // Insert ambience
        foreach ($ambience_arr as $ambience) {
            $data = array(
                'user_id' => $user_id,
                'rca_cuisine_ambience_id' => $ambience,
                'rca_type' => '2'
            );
            $this->db->insert('restaurant_cuisine_ambience', $data);
        }
    }


    /*
    * Method Name: update_cuisine_ambience_data
    * Purpose: To update device data for user in database
    * params:
    *      input: user_id, cuisine string, ambience string
    */
    public function update_dietary_preference_data($user_id, $dietary_preference)
    {
        // Delete all records for that restaurant and then insert new
        $this->db->where("user_id", $user_id);
        $this->db->delete('dietary_restaurant');

        $dietary_preference_arr = explode(",", $dietary_preference);

        // Insert cuisine
        foreach ($dietary_preference_arr as $dietary) {
            $data = array(
                'user_id' => $user_id,
                'diet_id' => $dietary
            );
            $this->db->insert('dietary_restaurant', $data);
        }


    }


    /*
    * Method Name: deleteTimeSlots
    * Purpose: delete time for each restaurant
    * params:
    *      input: user_id
    */
    public function deleteTimeSlots($user_id)
    {
        // check if slots are available
        $this->db->select("open_close_id");
        $this->db->where("user_id", $user_id);
        $aResult = $this->db->get("restaurant_open_close_time");
        if ($aResult->num_rows()) {
            // Delete all records for that restaurant and then insert new
            $this->db->where("user_id", $user_id);
            $this->db->delete('restaurant_open_close_time');
        }

    }

    /*
    * Method Name: insertTimeSlots
    * Purpose: insert time for each restaurant
    * params:
    *      input: insert array
    */
    public function insertTimeSlots($insertData, $user_id)
    {

        $this->db->insert('restaurant_open_close_time', $insertData);
        return $this->db->insert_id();

    }

    /*
    * Method Name: insertTimeSlots
    * Purpose: insert time for each restaurant
    * params:
    *      input: insert array
    */
    public function insertSubscriber($subData)
    {

        $this->db->insert('tab_subscribermst', $subData);
        return $this->db->insert_id();

    }

    /*
     * Method Name: get_country_name
     * Purpose: Get country name from database
     * params:
     *      input: country id
     *      output: country name
     */
    public function get_country_name($country)
    {
        $this->db->select("cou_name");
        $this->db->from("country");
        $data = array(
            'cou_id' => $country
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row()->cou_name : "";
    }

    /*
     * Method Name: get_state_name
     * Purpose: Get state name from database
     * params:
     *      input: state id
     *      output: state name
     */
    public function get_state_name($state)
    {
        $this->db->select("region_name");
        $this->db->from("region");
        $data = array(
            'region_id' => $state
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row()->region_name : "";
    }

    /*
     * Method Name: get_city_name
     * Purpose: Get city name from database
     * params:
     *      input: city id
     *      output: city name
     */
    public function get_city_name($city)
    {
        $this->db->select("city_name");
        $this->db->from("city");
        $data = array(
            'city_id' => $city
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row()->city_name : "";
    }

    /*
     * Method Name: logout
     * Purpose: Delete user device record
     * params:
     *      input: user_id
     */
    public function logout($user_id, $access_token)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete("user_devices");

        // delete access token\
        $this->db->where('user_id', $user_id);
        $this->db->where('user_access_token', $access_token);
        $this->db->delete("user_access_token");

    }

    /*
     * Method Name: get_user_details
     * Purpose: To get user details from database
     * params:
     *      input: user_id, user_type
     *      output: array containing user details
     */
    public function get_user_details($user_id, $user_type)
    {
        $this->db->select("IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.user_image) ) AS user_image,u.gender,u.notification_setting as notification_flag,u.mvg_points, u.date_of_birth, u.user_contact,u.user_email,u.user_type,u.user_id,reg.region_name as state_name, u.region_id,u.city_id,u.country_id,u.user_first_name as user_first_name,c.city_name, ", FALSE);
        $this->db->from("tab_usermst u");
        $this->db->join("city c", "c.city_id = u.city_id");
        $this->db->join("region reg", "reg.region_id = u.region_id");
        $this->db->join("country cou", "cou.cou_id = u.country_id");
        $data = array(
            'u.user_id' => $user_id,
            'u.user_type' => $user_type,
            'u.user_status' => '1',
            'u.is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        return ($query->num_rows() > 0) ? $query->row() : array();
    }

    /*
     * Method Name: get_restaurant_user_details
     * Purpose: To get user details from database
     * params:
     *      input: user_id, user_type
     *      output: array containing user details
     */
    public function get_restaurant_user_details($user_id, $user_type)
    {
        $sql = "select t.*,t1.*, t2.*,t3.*,t4.diet_id,t5.diet_preference
              from
                (  SELECT
                  `u`.`user_id`,`u`.`latitude`,`u`.`longitude`,restaurant_owner_name,  
                  `u`.`web_domain`,
                  IF( u.user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.	user_image) ) AS restaurant_profile_image,
                  IF( u.restaurant_hero_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', u.restaurant_hero_image) ) AS restaurant_image,
                  u.user_first_name as restaurant_name,                  
                  u.average_spend,
                  u.restaurant_detail_url,
                  u.user_email,
                  u.user_contact,
                  u.user_description as restaurant_description,
                  cou.cou_name as country,
                  reg.region_name as state,
                  c.city_name as city,
                  cou.cou_id as country_id,
                  reg.region_id as state_id,
                  c.city_id as city_id,
                  u.street_address1 as address
                FROM
                  (`tab_usermst` u)
                JOIN
                  `tab_city` c
                    ON `c`.`city_id` = `u`.`city_id`
                JOIN
                  `tab_region` reg
                    ON `reg`.`region_id` = `u`.`region_id`
                JOIN
                  `tab_country` cou
                    ON `cou`.`cou_id` = `u`.`country_id`
                WHERE
                  `u`.`user_id` =  '" . $user_id . "'
                  AND `u`.`user_type` =  '" . $user_type . "'
                  AND `u`.`user_status` =  '1'
                  AND `u`.`is_deleted` =  '0'
                GROUP BY
                  `u`.`user_id`) as t
              JOIN
                (
                  SELECT
                    `u`.`user_id`,
                    IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),
                    1),
                    0) AS average_rating,
                    COUNT( NULLIF( your_thoughts, '' ) ) AS average_review
                  FROM
                    (`tab_usermst` u)
                  LEFT JOIN
                    `tab_rating` r
                      ON `r`.`restaurant_id` = `u`.`user_id`
                      AND r.status = '1'
                      AND r.is_approved = '1'
                  WHERE
                    `u`.`user_id` =  '" . $user_id . "'
                    AND `u`.`user_type` =  '" . $user_type . "'
                    AND `u`.`user_status` =  '1'
                    AND `u`.`is_deleted` =  '0'
                  group by
                    r.`restaurant_id`
                ) as t1
                  ON t.`user_id` = t1.`user_id`                
                  
                LEFT JOIN
                (
                  SELECT
                  `u`.`user_id`,
                  GROUP_CONCAT(case rca.rca_type
                    when '2' then a.ambience_name
                  end ) as ambience,
                  GROUP_CONCAT(case rca.rca_type
                    when '2' then a.ambience_id
                  end ) as ambience_id
                FROM
                  (`tab_usermst` u)
                JOIN
                  `tab_restaurant_cuisine_ambience` rca
                    ON `rca`.`user_id` = `u`.`user_id`
                JOIN
                  `tab_ambience` a
                    ON `a`.`ambience_id` = `rca`.`rca_cuisine_ambience_id`
                WHERE
                  `u`.`user_id` =  '" . $user_id . "'
                  AND `u`.`user_type` =  '" . $user_type . "'
                  AND `u`.`user_status` =  '1'
                  AND `u`.`is_deleted` =  '0'
                GROUP BY
                  `rca`.`user_id`
                ) as t2
                  ON t1.`user_id` = t2.`user_id`
             LEFT JOIN
                (
                  SELECT
                  `u`.`user_id`,
                 GROUP_CONCAT(case rca.rca_type
                    when '1' then cu.cuisine_name
                  end ) as cuisine,
                  GROUP_CONCAT(case rca.rca_type
                    when '1' then cu.cuisine_id
                  end ) as cuisine_id
                FROM
                  (`tab_usermst` u)
                JOIN
                  `tab_restaurant_cuisine_ambience` rca
                    ON `rca`.`user_id` = `u`.`user_id`
                JOIN
                  `tab_cuisine` cu
                    ON `cu`.`cuisine_id` = `rca`.`rca_cuisine_ambience_id`
                WHERE
                  `u`.`user_id` =  '" . $user_id . "'
                  AND `u`.`user_type` =  '" . $user_type . "'
                  AND `u`.`user_status` =  '1'
                  AND `u`.`is_deleted` =  '0'
               GROUP BY
                  `rca`.`user_id`
                ) as t3
                  ON t1.`user_id` = t3.`user_id`  
                   
               LEFT JOIN
                (
				SELECT  `u`.`user_id`,GROUP_CONCAT(`tdr`.`diet_id`) as diet_id
				FROM (`tab_usermst` u) JOIN `tab_dietary_restaurant` tdr 
				ON u.user_id=tdr.user_id WHERE u.user_id = '" . $user_id . "' 
				GROUP BY tdr.user_id
                ) as t4
                ON t1.`user_id` = t4.`user_id`
				
				LEFT JOIN
                (
				SELECT  `u`.`user_id`,GROUP_CONCAT(`tdp`.`diet_preference`) as diet_preference
				FROM (`tab_usermst` u) 
				JOIN `tab_dietary_restaurant` tdr ON u.user_id=tdr.user_id 
				JOIN `tab_dietary_preference` tdp ON tdr.diet_id=tdp.diet_id 
				WHERE u.user_id = '" . $user_id . "' 
				GROUP BY tdr.user_id
                ) as t5
                ON t1.`user_id` = t5.`user_id`
    ";
        $query = $this->db->query($sql);
        return ($query->num_rows() > 0) ? $query->row() : array();
    }

    /*
     * Method Name: get_app_users
     * Purpose: To get list of halozi app users from emails provided
     * params:
     *      input: emails - array
     *      output: return user name, user image from database for matching records
     */
    public function get_app_users($emails)
    {
        $this->db->select("CONCAT( user_first_name, ' ', user_last_name ) as user_name, IF( user_image = '', '', CONCAT('" . base_url() . "', '" . MEMBER_IMAGE_PATH . "', user_image) ) AS user_image, user_email", FALSE);
        $this->db->from("usermst");
        $data = array(
            'user_status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->where_in('user_email', $emails);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result_array() : array();
    }

    /*
     * Method Name: is_valid_user
     * Purpose: To verify if user exists in database
     * params:
     *      input: user_id, user_type
     *
     *      output: TRUE/FALSE
     */
    public function is_valid_user($user_id, $user_type)
    {
        $this->db->select("user_id");
        $this->db->from("usermst");
        $data = array(
            'user_id' => $user_id,
            'user_type' => $user_type,
            'user_status' => '1',
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->row()) ? TRUE : FALSE;
    }

    /*
     * Method Name: get_default_loyalty_setting
     * Purpose: To get loyalty setting
     * params:
     *      input: -
     *      output: return loyalty settings
     */
    public function get_default_loyalty_setting()
    {
        $this->db->select("setting_value, setting_parameter");
        $this->db->from("settings");
        $data = array(
            'setting_name' => 'Loyalty'
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row_array() : array();
    }

    /*
     * Method Name: get_loyalty_points
     * Purpose: To get loyalty points
     * params:
     *      input: user_id
     *      output: return loyalty points
     */
    public function get_loyalty_points($user_id)
    {
        $this->db->select("loyalty_points");
        $this->db->from("loyalty");
        $data = array(
            'user_id' => $user_id
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row()->loyalty_points : 0;
    }

    /*
     * Method Name: get_user_device_details
     * Purpose: Get user device details
     * params:
     *      input: user_id
     *      output: object
     */
    public function get_user_device_details($user_id)
    {
        $this->db->select("u.user_id, u.user_email, d.dev_device_id, d.dev_type, d.is_live_entry");
        $this->db->from("usermst u");
        $this->db->join("user_devices d", "d.user_id = u.user_id");

        $data = array(
            'u.user_id' => $user_id,
            'u.notification_setting' => '1'
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row_array() : array();
    }

    /*
     * Method Name: insert_access_token
     * Purpose: To insert access token
     * params:
     *      input:
     *      output: array containing user details
     */
    public function insert_access_token($user_token, $user_id)
    {
        $insert_data = array('user_id' => $user_id, 'user_access_token' => $user_token);
        $this->db->insert("user_access_token", $insert_data);
    }

    public function restaurant_details_same_url_count($restaurant_details_url_string)
    {
        $this->db->select('user_id');
        $this->db->from('usermst');
        $this->db->where('user_type', '3');
        $this->db->like('restaurant_detail_url', $restaurant_details_url_string);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function is_wish_restaurant($restaurant_id, $user_id)
    {
        $this->db->select('wish_id');
        $this->db->from("tab_wishlist");
        $data = array(
            'restaurant_id' => $restaurant_id,
            'user_id' => $user_id
        );
        $this->db->where($data);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? 1 : 0;
    }
}

?>